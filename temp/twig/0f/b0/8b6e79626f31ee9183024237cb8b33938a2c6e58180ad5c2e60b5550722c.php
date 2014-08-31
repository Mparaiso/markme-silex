<?php

/* login.twig */
class __TwigTemplate_0fb08b6e79626f31ee9183024237cb8b33938a2c6e58180ad5c2e60b5550722c extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("layout.twig");

        $this->blocks = array(
            'head_css' => array($this, 'block_head_css'),
            'content' => array($this, 'block_content'),
            'bottom_scripts' => array($this, 'block_bottom_scripts'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "layout.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 2
    public function block_head_css($context, array $blocks = array())
    {
        // line 3
        echo "    <link rel=\"stylesheet\" href=\"/bower_components/bootstrap/dist/css/bootstrap.min.css\">
    <link rel=\"stylesheet\" href=\"/static/css/style.css\">
";
    }

    // line 6
    public function block_content($context, array $blocks = array())
    {
        // line 7
        echo "    ";
        // line 8
        echo "    ";
        $this->env->loadTemplate("include/navbar.twig")->display($context);
        // line 9
        echo "    ";
        // line 10
        echo "    <div class='container'>
        <main class=\"row jumbotron\">
            <div class=\"col-sm-6\">
                <form method=\"POST\" action=\"";
        // line 13
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : $this->getContext($context, "app")), "url_generator"), "generate", array(0 => "login-check"), "method"), "html", null, true);
        echo "\" role=\"form\">
                    <fieldset>
                        <legend>Please sign in.</legend>
                        ";
        // line 16
        if ((isset($context["error"]) ? $context["error"] : $this->getContext($context, "error"))) {
            // line 17
            echo "                            <div class=\"alert alert-danger\">
                                ";
            // line 18
            echo twig_escape_filter($this->env, (isset($context["error"]) ? $context["error"] : $this->getContext($context, "error")), "html", null, true);
            echo "
                            </div>
                        ";
        }
        // line 21
        echo "                        ";
        $this->env->loadTemplate("include/form.twig")->display($context);
        // line 22
        echo "                        <div class=\"form-group\">
                            <input type=\"reset\" class=\"btn btn-default\"/>
                            <input type=\"submit\" class=\"btn btn-success\"/>
                        </div>
                    </fieldset>
                </form>
            </div>
        </main>
        <footer class=\"row\">
            ";
        // line 31
        $this->env->loadTemplate("include/footer.twig")->display($context);
        // line 32
        echo "        </footer>
    </div>
";
    }

    // line 35
    public function block_bottom_scripts($context, array $blocks = array())
    {
        // line 36
        echo "    ";
        $this->displayParentBlock("bottom_scripts", $context, $blocks);
        echo "
    ";
    }

    public function getTemplateName()
    {
        return "login.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  96 => 36,  93 => 35,  87 => 32,  85 => 31,  74 => 22,  71 => 21,  65 => 18,  62 => 17,  60 => 16,  54 => 13,  49 => 10,  47 => 9,  44 => 8,  42 => 7,  39 => 6,  33 => 3,  30 => 2,);
    }
}
