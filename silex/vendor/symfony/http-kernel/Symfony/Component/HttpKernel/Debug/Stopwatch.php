<?php










namespace Symfony\Component\HttpKernel\Debug;






class Stopwatch
{
private $sections;
private $activeSections;

public function __construct()
{
$this->sections = $this->activeSections = array('__root__' => new Section('__root__'));
}








public function openSection($id = null)
{
$current = end($this->activeSections);

if (null !== $id && null === $current->get($id)) {
throw new \LogicException(sprintf('The section "%s" has been started at an other level and can not be opened.', $id));
}

$this->start('__section__.child', 'section');
$this->activeSections[] = $current->open($id);
$this->start('__section__');
}










public function stopSection($id)
{
$this->stop('__section__');

if (1 == count($this->activeSections)) {
throw new \LogicException('There is no started section to stop.');
}

$this->sections[$id] = array_pop($this->activeSections)->setId($id);
$this->stop('__section__.child');
}









public function start($name, $category = null)
{
return end($this->activeSections)->startEvent($name, $category);
}








public function stop($name)
{
return end($this->activeSections)->stopEvent($name);
}








public function lap($name)
{
return end($this->activeSections)->stopEvent($name)->start();
}








public function getSectionEvents($id)
{
return isset($this->sections[$id]) ? $this->sections[$id]->getEvents() : array();
}
}

class Section
{
private $events = array();
private $origin;
private $id;
private $children = array();






public function __construct($origin = null)
{
$this->origin = is_numeric($origin) ? $origin : null;
}








public function get($id)
{
foreach ($this->children as $child) {
if ($id === $child->getId()) {
return $child;
}
}

return null;
}








public function open($id)
{
if (null === $session = $this->get($id)) {
$session = $this->children[] = new self(microtime(true) * 1000);
}

return $session;
}




public function getId()
{
return $this->id;
}








public function setId($id)
{
$this->id = $id;

return $this;
}









public function startEvent($name, $category)
{
if (!isset($this->events[$name])) {
$this->events[$name] = new StopwatchEvent($this->origin ?: microtime(true) * 1000, $category);
}

return $this->events[$name]->start();
}










public function stopEvent($name)
{
if (!isset($this->events[$name])) {
throw new \LogicException(sprintf('Event "%s" is not started.', $name));
}

return $this->events[$name]->stop();
}










public function lap($name)
{
return $this->stop($name)->start();
}






public function getEvents()
{
return $this->events;
}
}
