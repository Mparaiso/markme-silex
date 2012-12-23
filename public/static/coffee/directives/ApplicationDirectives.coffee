### FR : module regroupant les directives ###
directives = angular.module("ApplicationDirectives",[]);

### FR : utilise le plugin bootstrap pop-over ###
directives.directive("popOver",()->
    return (scope,element,attrs)->
        if element.attr("data-target")
            $target = $(attrs["target"])
            $target.detach()
            options = 
                "html":true,
                "content":$target
        element.popover(options || {});
        return
);

### FR : bascule l'élement en mode active en changant la classe ###
directives.directive("toggleActive",()->
    return (scope,element,attrs)->
        element.click(()-> 
            if element.hasClass("active")
                element.removeClass("active")
            else
                element.addClass("active")
            return
        );
        return
);
