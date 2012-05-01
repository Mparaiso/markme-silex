<?php










namespace Symfony\Component\Routing;






class RouteCompiler implements RouteCompilerInterface
{
const REGEX_DELIMITER = '#';








public function compile(Route $route)
{
$pattern = $route->getPattern();
$len = strlen($pattern);
$tokens = array();
$variables = array();
$pos = 0;
preg_match_all('#.\{([\w\d_]+)\}#', $pattern, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
foreach ($matches as $match) {
if ($text = substr($pattern, $pos, $match[0][1] - $pos)) {
$tokens[] = array('text', $text);
}
$seps = array($pattern[$pos]);
$pos = $match[0][1] + strlen($match[0][0]);
$var = $match[1][0];

if ($req = $route->getRequirement($var)) {
$regexp = $req;
} else {
if ($pos !== $len) {
$seps[] = $pattern[$pos];
}
$regexp = sprintf('[^%s]+?', preg_quote(implode('', array_unique($seps)), self::REGEX_DELIMITER));
}

$tokens[] = array('variable', $match[0][0][0], $regexp, $var);

if (in_array($var, $variables)) {
throw new \LogicException(sprintf('Route pattern "%s" cannot reference variable name "%s" more than once.', $route->getPattern(), $var));
}

$variables[] = $var;
}

if ($pos < $len) {
$tokens[] = array('text', substr($pattern, $pos));
}


 $firstOptional = INF;
for ($i = count($tokens) - 1; $i >= 0; $i--) {
$token = $tokens[$i];
if ('variable' === $token[0] && $route->hasDefault($token[3])) {
$firstOptional = $i;
} else {
break;
}
}


 $regexp = '';
for ($i = 0, $nbToken = count($tokens); $i < $nbToken; $i++) {
$regexp .= $this->computeRegexp($tokens, $i, $firstOptional);
}

return new CompiledRoute(
$route,
'text' === $tokens[0][0] ? $tokens[0][1] : '',
self::REGEX_DELIMITER.'^'.$regexp.'$'.self::REGEX_DELIMITER.'s',
array_reverse($tokens),
$variables
);
}










private function computeRegexp(array $tokens, $index, $firstOptional)
{
$token = $tokens[$index];
if('text' === $token[0]) {

 return preg_quote($token[1], self::REGEX_DELIMITER);
} else {

 if (0 === $index && 0 === $firstOptional && 1 == count($tokens)) {

 return sprintf('%s(?<%s>%s)?', preg_quote($token[1], self::REGEX_DELIMITER), $token[3], $token[2]);
} else {
$regexp = sprintf('%s(?<%s>%s)', preg_quote($token[1], self::REGEX_DELIMITER), $token[3], $token[2]);
if ($index >= $firstOptional) {

 
 
 $regexp = "(?:$regexp";
$nbTokens = count($tokens);
if ($nbTokens - 1 == $index) {

 $regexp .= str_repeat(")?", $nbTokens - $firstOptional);
}
}

return $regexp;
}
}
}
}
