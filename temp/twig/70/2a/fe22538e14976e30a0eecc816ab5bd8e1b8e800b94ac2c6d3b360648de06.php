<?php

/* layout.twig */
class __TwigTemplate_702afe22538e14976e30a0eecc816ab5bd8e1b8e800b94ac2c6d3b360648de06 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
            'head_css' => array($this, 'block_head_css'),
            'head_scripts' => array($this, 'block_head_scripts'),
            'content' => array($this, 'block_content'),
            'bottom_scripts' => array($this, 'block_bottom_scripts'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<!DOCTYPE HTML>
<html lang=\"en-US\">
    <head>
        <meta charset=\"UTF-8\">
        <meta name=\"title\" content=\"Markme\">
        <meta name=\"description\" content=\"online bookmark manager\">
        <meta name=\"keywords\"
              content=\"html5,web application,webapp,bookmark,bookmarks,application,utility,organizer,manager\">
        <meta name=\"author\" content=\"M.Paraiso\">
        <META HTTP-EQUIV=\"CACHE-CONTROL\" CONTENT=\"PUBLIC\">
        <title>Markme : Organize your bookmarks online</title>
        ";
        // line 12
        $this->displayBlock('head_css', $context, $blocks);
        // line 14
        echo "        ";
        $this->displayBlock('head_scripts', $context, $blocks);
        // line 16
        echo "    </head>
    <body data-ng-app=\"markme\">
        <noscript>
        <h1 class=\"alert alert-danger\" style=\"text-align: center;\">
            Please enable Javascript.
        </h1>
        </noscript>
        ";
        // line 23
        $this->displayBlock('content', $context, $blocks);
        // line 25
        echo "        ";
        $this->displayBlock('bottom_scripts', $context, $blocks);
        // line 27
        echo "        ";
        $this->env->loadTemplate("common/ga.twig")->display($context);
        // line 28
        echo "    </body>
</html>";
    }

    // line 12
    public function block_head_css($context, array $blocks = array())
    {
        // line 13
        echo "        ";
    }

    // line 14
    public function block_head_scripts($context, array $blocks = array())
    {
        // line 15
        echo "        ";
    }

    // line 23
    public function block_content($context, array $blocks = array())
    {
        // line 24
        echo "        ";
    }

    // line 25
    public function block_bottom_scripts($context, array $blocks = array())
    {
        // line 26
        echo "        ";
    }

    public function getTemplateName()
    {
        return "layout.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  87 => 26,  84 => 25,  80 => 24,  77 => 23,  73 => 15,  70 => 14,  66 => 13,  63 => 12,  58 => 28,  55 => 27,  52 => 25,  50 => 23,  41 => 16,  38 => 14,  36 => 12,  23 => 1,);
    }
}
