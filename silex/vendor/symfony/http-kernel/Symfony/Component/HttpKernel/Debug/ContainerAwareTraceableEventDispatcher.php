<?php










namespace Symfony\Component\HttpKernel\Debug;

use Symfony\Component\HttpKernel\Debug\Stopwatch;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Profiler\Profile;
use Symfony\Component\HttpKernel\Profiler\Profiler;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher;
use Symfony\Component\EventDispatcher\Debug\TraceableEventDispatcherInterface;






class ContainerAwareTraceableEventDispatcher extends ContainerAwareEventDispatcher implements TraceableEventDispatcherInterface
{
private $logger;
private $called;
private $stopwatch;
private $priorities;
private $profiler;








public function __construct(ContainerInterface $container, Stopwatch $stopwatch, LoggerInterface $logger = null)
{
parent::__construct($container);

$this->stopwatch = $stopwatch;
$this->logger = $logger;
$this->called = array();
}




public function dispatch($eventName, Event $event = null)
{
switch ($eventName) {
case 'kernel.request':
$this->stopwatch->openSection();
break;
case 'kernel.view':
case 'kernel.response':

 try {
$this->stopwatch->stop('controller');
} catch (\LogicException $e) {
}
break;
case 'kernel.terminate':
$token = $event->getResponse()->headers->get('X-Debug-Token');
$this->stopwatch->openSection($token);
break;
}

$e1 = $this->stopwatch->start($eventName, 'section');

parent::dispatch($eventName, $event);

$e1->stop();

switch ($eventName) {
case 'kernel.controller':
$this->stopwatch->start('controller', 'section');
break;
case 'kernel.response':
$token = $event->getResponse()->headers->get('X-Debug-Token');
$this->stopwatch->stopSection($token);
if (HttpKernelInterface::MASTER_REQUEST === $event->getRequestType()) {

 
 $this->updateProfiles($token, true);
}
break;
case 'kernel.terminate':
$this->stopwatch->stopSection($token);

 
 
 $this->updateProfiles($token, false);
break;
}

return $event;
}






public function addListener($eventName, $listener, $priority = 0)
{
if (!is_callable($listener)) {
throw new \RuntimeException(sprintf('The given callback (%s) for event "%s" is not callable.', $this->getListenerAsString($listener), $eventName));
}

$this->priorities[$eventName.'_'.$this->getListenerAsString($listener)] = $priority;

parent::addListener($eventName, $listener, $priority);
}




protected function doDispatch($listeners, $eventName, Event $event)
{
foreach ($listeners as $listener) {
$info = $this->getListenerInfo($listener, $eventName);

if (null !== $this->logger) {
$this->logger->debug(sprintf('Notified event "%s" to listener "%s".', $eventName, $info['pretty']));
}

$this->called[$eventName.'.'.$info['pretty']] = $info;

$e2 = $this->stopwatch->start(isset($info['class']) ? substr($info['class'], strrpos($info['class'], '\\') + 1) : $info['type'], 'event_listener');

call_user_func($listener, $event);

$e2->stop();

if ($event->isPropagationStopped()) {
if (null !== $this->logger) {
$this->logger->debug(sprintf('Listener "%s" stopped propagation of the event "%s".', $info['pretty'], $eventName));

$skippedListeners = $this->getListeners($eventName);
$skipped = false;

foreach ($skippedListeners as $skippedListener) {
if ($skipped) {
$info = $this->getListenerInfo($skippedListener, $eventName);
$this->logger->debug(sprintf('Listener "%s" was not called for event "%s".', $info['pretty'], $eventName));
}

if ($skippedListener === $listener) {
$skipped = true;
}
}
}

break;
}
}
}




protected function lazyLoad($eventName)
{
$e = $this->stopwatch->start($eventName.'.loading', 'event_listener_loading');

parent::lazyLoad($eventName);

$e->stop();
}




public function getCalledListeners()
{
return $this->called;
}




public function getNotCalledListeners()
{
$notCalled = array();

foreach ($this->getListeners() as $name => $listeners) {
foreach ($listeners as $listener) {
$info = $this->getListenerInfo($listener, $name);
if (!isset($this->called[$name.'.'.$info['pretty']])) {
$notCalled[$name.'.'.$info['pretty']] = $info;
}
}
}

return $notCalled;
}









private function getListenerInfo($listener, $eventName)
{
$info = array(
'event' => $eventName,
'priority' => $this->priorities[$eventName.'_'.$this->getListenerAsString($listener)],
);
if ($listener instanceof \Closure) {
$info += array(
'type' => 'Closure',
'pretty' => 'closure'
);
} elseif (is_string($listener)) {
try {
$r = new \ReflectionFunction($listener);
$file = $r->getFileName();
$line = $r->getStartLine();
} catch (\ReflectionException $e) {
$file = null;
$line = null;
}
$info += array(
'type' => 'Function',
'function' => $listener,
'file' => $file,
'line' => $line,
'pretty' => $listener,
);
} elseif (is_array($listener) || (is_object($listener) && is_callable($listener))) {
if (!is_array($listener)) {
$listener = array($listener, '__invoke');
}
$class = is_object($listener[0]) ? get_class($listener[0]) : $listener[0];
try {
$r = new \ReflectionMethod($class, $listener[1]);
$file = $r->getFileName();
$line = $r->getStartLine();
} catch (\ReflectionException $e) {
$file = null;
$line = null;
}
$info += array(
'type' => 'Method',
'class' => $class,
'method' => $listener[1],
'file' => $file,
'line' => $line,
'pretty' => $class.'::'.$listener[1],
);
}

return $info;
}







private function updateProfiles($token, $updateChildren)
{
if (!$this->getContainer()->has('profiler')) {
return;
}

$this->profiler = $this->getContainer()->get('profiler');

if (!$profile = $this->profiler->loadProfile($token)) {
return;
}

$this->saveStopwatchInfoInProfile($profile, $updateChildren);
}







private function saveStopwatchInfoInProfile(Profile $profile, $updateChildren)
{
$profile->getCollector('time')->setEvents($this->stopwatch->getSectionEvents($profile->getToken()));
$this->profiler->saveProfile($profile);

if ($updateChildren) {
foreach ($profile->getChildren() as $child) {
$this->saveStopwatchInfoInProfile($child, true);
}
}
}

private function getListenerAsString($listener)
{
if (is_string($listener)) {
return '[string] '.$listener;
} elseif (is_array($listener)) {
return '[array] '.(is_object($listener[0]) ? get_class($listener[0]) : $listener[0]).'::'.$listener[1];
} elseif (is_object($listener)) {
return '[object] '.get_class($listener);
}

return '[?] '.var_export($listener, true);
}
}
