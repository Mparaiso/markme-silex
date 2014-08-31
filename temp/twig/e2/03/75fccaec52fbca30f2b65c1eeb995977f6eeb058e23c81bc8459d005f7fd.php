<?php

/* include/navbar.twig */
class __TwigTemplate_e20375fccaec52fbca30f2b65c1eeb995977f6eeb058e23c81bc8459d005f7fd extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 4
        echo "<div class=\"navbar navbar-default\">
    <div class=\"container\">
        <div class='navbar-header'>
            <a class='navbar-brand brand' href='/'>Mark.me</a>
        </div>
        <ul class=\"navbar-nav navbar-right nav\">
            <li>
                <a href=\"";
        // line 11
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : $this->getContext($context, "app")), "url_generator"), "generate", array(0 => "login"), "method"), "html", null, true);
        echo "\">Login</a>
            </li>
        </ul>
    </div>
</div>";
    }

    public function getTemplateName()
    {
        return "include/navbar.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  28 => 11,  19 => 4,  80 => 25,  71 => 15,  68 => 14,  61 => 12,  56 => 28,  54 => 27,  52 => 25,  50 => 23,  41 => 16,  38 => 14,  36 => 12,  23 => 1,  102 => 48,  99 => 47,  93 => 44,  91 => 43,  75 => 23,  73 => 28,  64 => 13,  47 => 9,  45 => 8,  42 => 7,  39 => 6,  33 => 3,  30 => 2,);
    }
}
