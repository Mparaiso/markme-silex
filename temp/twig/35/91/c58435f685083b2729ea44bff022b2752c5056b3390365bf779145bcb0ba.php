<?php

/* application.twig */
class __TwigTemplate_3591c58435f685083b2729ea44bff022b2752c5056b3390365bf779145bcb0ba extends Twig_Template
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
        echo "
";
    }

    // line 5
    public function block_content($context, array $blocks = array())
    {
        // line 6
        echo "    ";
        // line 13
        echo "
        <nav class=\"navbar navbar-default\" data-ng-controller=\"NavigationCtrl\" ng-include=\"'/static/js/app/partials/topnav.html'\"></nav>
        <main class='container' data-ng-controller=\"MainCtrl\">
            <section  ng-include=\"'/static/js/app/partials/alert.html'\"></section>    
            <section class='row main' ng-view>
            </section>
        </main>
    ";
        echo "
    <footer class=\"container\">
        <span>Copyright &copy; M.Paraiso 2014. <a href=\"mailto:mparaiso@online.fr\">mparaiso@online.fr</a> All rights reserved.</span>
        <span>Screenshots <a href=\"http://www.robothumb.com\">by Robothumb</a></span>
    </footer>
";
    }

    // line 19
    public function block_bottom_scripts($context, array $blocks = array())
    {
        // line 20
        echo "            ";
        $this->displayParentBlock("bottom_scripts", $context, $blocks);
        echo "

    <script type=\"text/javascript\" src=\"/bower_components/jquery/dist/jquery.min.js\"></script>
    <script type=\"text/javascript\" src=\"/static/js/jquery.transit.min.js\"></script>
    <script type=\"text/javascript\" src=\"/bower_components/jquery-masonry/dist/masonry.pkgd.min.js\"></script>
    <script type=\"text/javascript\" src=\"/bower_components/bootstrap/dist/js/bootstrap.min.js\"></script>
    <script type=\"text/javascript\" src=\"/bower_components/jquery.autocomplete/jquery.autocomplete.min.js\"></script>
    <script type=\"text/javascript\" src=\"/static/vendor/jquery-tagsinput/jquery.tagsinput.min.js\"></script>
    <script type=\"text/javascript\" src=\"/bower_components/lodash/dist/lodash.min.js\"></script>
    <script type=\"text/javascript\" src=\"/bower_components/angular/angular.min.js\"></script>
    <script type=\"text/javascript\" src=\"/bower_components/angular-route/angular-route.min.js\"></script>
    <script type=\"text/javascript\" src=\"/static/js/app/modules/ApplicationDirectives.js\"></script>
    <script type=\"text/javascript\" src=\"/static/js/app/modules/ApplicationFilters.js\"></script>
    <script type=\"text/javascript\" src=\"/static/js/app/modules/ApplicationServices.js\"></script>
    <script type=\"text/javascript\" src=\"/static/js/app/modules/Application.js\"></script>

    <link rel=\"stylesheet\" href=\"/static/vendor/jquery-tagsinput/jquery.tagsinput.css\"/>
    <link rel=\"stylesheet\" href=\"/bower_components/bootstrap/dist/css/bootstrap.min.css\">
    <link rel=\"stylesheet\" href=\"/static/vendor/jquery-autocomplete/jquery.autocomplete.css\"/>
    <link rel=\"stylesheet\" href=\"/static/css/style.css\">
";
    }

    public function getTemplateName()
    {
        return "application.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  63 => 20,  60 => 19,  43 => 13,  41 => 6,  38 => 5,  33 => 3,  30 => 2,);
    }
}
