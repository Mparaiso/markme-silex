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

Directives.directive("masonry",function(){
    return function(scope,element,attrs){
        var options = {};
        if(attrs['itemSelector'])options.itemSelector = attrs['itemSelector'];
        if(attrs['columnWidth'])options.columnWidth = 240 ; //
        if(attrs["isAnimated"])options.isAnimated = true;
        $(function(){
            element.imagesLoaded(function(){
                element.masonry(options);
            });
        });
    };
});

Directives.directive("openModal",function(){
    return function(scope,element,attrs){
        element.on("click",function(event){
            var modalSelector = attrs["openModal"];
            $(modalSelector).modal("show");
        });
    };
});

Directives.directive("preloadImage",function(){
    return function(scope,element,attrs) {
        // debugger;
        //console.log(attrs['preloadImage']);
        var imageToload = scope.$eval(attrs['preloadImage']);
        var lowSrc = attrs["lowSrc"];
        element.attr("src",lowSrc);
        var image = new Image();
        image.onload = function(){
            element.attr("src",image.src);
        };
        image.onerror = function(){
            console.log("error");
        };
        image.src = imageToload;
    };
});