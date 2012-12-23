var Directives = angular.module("ApplicationDirectives", []);

/* FR : utilise le plugin bootstrap pop-over
*/


Directives.directive("popOver", function() {
    return function(scope, element, attrs) {
        var $target, options;
        if (element.attr("data-target")) {
            $target = $(attrs["target"]);
            $target.detach();
            options = {
                "html": true,
                "content": $target
            };
        }
        element.popover(options || {});
    };
});

/* FR : bascule l'élement en mode active en changant la classe
*/


Directives.directive("toggleActive", function() {
    return function(scope, element, attrs) {
        var _class = attrs.toggleActive;
        element.click(function() {
            if (element.hasClass(_class)) {
                element.removeClass(_class);
            } else {
                element.addClass(_class);
            }
        });
    };
});


/**
 * EN : 2 fields . if the origin field value
 * === current element value
 * current element value is valid
 */
 Directives.directive("passwordVerify",function(){
    return {
        require:"ngModel",
        link: function(scope,element,attrs,ctrl){
            // ajoute une fonction de vérification au parsers du controle
            ctrl.$parsers.unshift(function(viewValue){
                var origin = scope.$eval(attrs["passwordVerify"]);
                if(origin!==viewValue){
                    ctrl.$setValidity("passwordVerify",false);
                    return undefined;
                }else{
                    ctrl.$setValidity("passwordVerify",true);
                    return viewValue;
                }
            });

        }
    };
});
