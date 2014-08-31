<?php

/* index.twig */
class __TwigTemplate_b1633d6d12ecc0fa3fbbb0a91a751465fd44acf3aaa31fd2f7bf77ae7c3ba8cf extends Twig_Template
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
        echo "    <!--navbar -->
    ";
        // line 8
        $this->env->loadTemplate("include/navbar.twig")->display($context);
        // line 9
        echo "    <!-- header -->
    <div class=\"container\">
        <!-- homepage.hmtl -->
        <main class='row'>
            <div class=\"jumbotron col-md-8\">
                <h2 class='sans'>
                    Online bookmark manager.
                </h2>
                <ul>
                    <li>Bookmark sites fast</li>
                    <li>Add description and tags</li>
                    <li>Import and Export your bookmarks from and to popular browsers</li>
                    <li>Search and filter through your bookmarks</li>
                    <li>Access your bookmarks anywhere!</li>
                </ul>
                <form ";
        // line 24
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock((isset($context["form"]) ? $context["form"] : $this->getContext($context, "form")), 'enctype');
        echo " action=\"";
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : $this->getContext($context, "app")), "url_generator"), "generate", array(0 => "index"), "method"), "html", null, true);
        echo "\" method=\"POST\"
                                               class='form'>
                    <fieldset>
                        <legend>Register, it's fast and free !</legend>
                        ";
        // line 28
        $this->env->loadTemplate("include/form.twig")->display($context);
        // line 29
        echo "                        <div class=\"form-group\">
                                <input class=\"btn btn-default\" type=\"reset\" />
                                <input class='btn btn-success'
                                       type=\"submit\">
                        </div>
                    </fieldset>
                </form>
            </div>
            <div class=\"col-md-4\">
                <img class='thumbnail pull-right' data-ng-src='/static/img/abime.jpg' src=\"http://placehold.it/320x240\"
                     alt=\"\">
            </div>
        </main>
        <footer class=\"row\">
            ";
        // line 43
        $this->env->loadTemplate("include/footer.twig")->display($context);
        // line 44
        echo "        </footer>
    </div>
";
    }

    // line 47
    public function block_bottom_scripts($context, array $blocks = array())
    {
        // line 48
        echo "    <script type=\"text/javascript\" src=\"/bower_components/jquery/dist/jquery.js\"></script>
    <script type=\"text/javascript\" src=\"/bower_components/bootstrap/dist/js/bootstrap.min.js\"></script>
    <script type=\"text/javascript\" src=\"/bower_components/lodash/dist/lodash.min.js\"></script>
    <script type=\"text/javascript\" src=\"/bower_components/angular/angular.min.js\"></script>
    <script type=\"text/javascript\" src=\"/bower_components/angular-route/angular-route.min.js\"></script>
    <script type=\"text/javascript\" src=\"/static/js/app/modules/Application.js\"></script>
    <script type=\"text/javascript\" src=\"/static/js/app/modules/ApplicationFilters.js\"></script>
    <script type=\"text/javascript\" src=\"/static/js/app/modules/ApplicationDirectives.js\"></script>
    <script type=\"text/javascript\" src=\"/static/js/app/modules/ApplicationServices.js\"></script>
";
    }

    public function getTemplateName()
    {
        return "index.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  102 => 48,  99 => 47,  93 => 44,  91 => 43,  75 => 29,  73 => 28,  64 => 24,  47 => 9,  45 => 8,  42 => 7,  39 => 6,  33 => 3,  30 => 2,);
    }
}
