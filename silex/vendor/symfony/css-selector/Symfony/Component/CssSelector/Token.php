<?php










namespace Symfony\Component\CssSelector;









class Token
{
private $type;
private $value;
private $position;








public function __construct($type, $value, $position)
{
$this->type = $type;
$this->value = $value;
$this->position = $position;
}






public function __toString()
{
return (string) $this->value;
}








public function isType($type)
{
return $this->type == $type;
}






public function getPosition()
{
return $this->position;
}
}
