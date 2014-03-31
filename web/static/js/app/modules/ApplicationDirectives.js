var Directives = angular.module("ApplicationDirectives", ["ApplicationServices"]);

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
Directives.directive("passwordVerify", function() {
    return {
        require: "ngModel",
        link: function(scope, element, attrs, ctrl) {
            // ajoute une fonction de vérification au parsers du controle
            ctrl.$parsers.unshift(function(viewValue) {
                var origin = scope.$eval(attrs["passwordVerify"]);
                if (origin !== viewValue) {
                    ctrl.$setValidity("passwordVerify", false);
                    return undefined;
                } else {
                    ctrl.$setValidity("passwordVerify", true);
                    return viewValue;
                }
            });
        }
    };
});

/**
 * EN : Masonry directive.
 * attributes : 
 * reload-on = will reload masonry when the model is updates , the model 
 * must be accessible in the scope
 * item-selector = masonry will apply on selected items
 * is-animated = animation turned on or off
 */
Directives.directive("masonry", function($timeout) {
    return function(scope, element, attrs) {
        var options = {};
        var init = false;
        var reloadOn = null;
        var oldDisplay = element.css("display");
        element.css({"display": "none"});
        if (attrs['itemSelector'])
            options.itemSelector = attrs['itemSelector'];
        if (attrs['columnWidth'])
            options.columnWidth = attrs["columnWidth"];
        if (attrs["isAnimated"])
            options.isAnimated = true;
        // reload when given model is updated
        if (attrs["reloadOn"]) {
            reloadOn = attrs["reloadOn"];
            scope.$watch(reloadOn, function(_new, _old) {
                if (init === true) {
                    $timeout(function() {
                        element.masonry("reload");
                    },100);
                }
            }, true);
        }
        // init masonry
        element.ready(
                function() {
                    element.css({"display": oldDisplay});
                    element.masonry(options);
                    init = true;
                });
        scope.$on("$destroy", function() {
            init = false;
            console.log("destroy",arguments);
            return false;
        });
    };
});

Directives.directive("openModal", function() {
    return function(scope, element, attrs) {
        element.on("click", function(event) {
            var modalSelector = attrs["openModal"];
            $(modalSelector).modal("show");
        });
    };
});

Directives.directive("preloadImage", function() {
    return function(scope, element, attrs) {
        var imageToload = scope.$eval(attrs['preloadImage']);
        var lowSrc = attrs["lowSrc"];
        element.attr("src", lowSrc);
        var image = new Image();
        image.onload = function() {
            element.hide();
            element.attr("src", image.src);
            element.fadeIn(300);
        };
        image.onerror = function(error) {
            console.log("error", error);
        };
        image.src = imageToload;
    };
});

Directives.directive("bstTooltip", ["$timeout", function($timeout) {
        return function(scope, element, attrs) {
            $timeout(function() {
                return element.tooltip();
            }, 10);
        };
    }]);

/** allow jquery.tags-input plugin use **/
Directives.directive("tagsInput", ["$timeout", "Url", function tagsInput($timeout, Url) {
        return function($scope, element, attrs) {
            var tagsInput = null;
            var model = attrs["ngModel"];
            var applyCallback = function() {
                $scope.$apply(model + "='" + element.val() + "'");
            };
            $scope.$watch(model, function(_new, _old) {
                $timeout(function() {
                    if (_new && _new.split) {
                        // force tagsInput value to _new
                        tagsInput = element.importTags(_new);
                    } else {
                        // force tagsInput to be empty
                        tagsInput = element.importTags("");
                    }
                }, true);
            });
            $timeout(function() {
                element.tagsInput({
                    "onAddTag": applyCallback,
                    "onRemoveTag": applyCallback,
                    autocomplete_url: Url.getBase() + '/json/autocomplete',
                    autocomplete: {selectFirst: true, width: '100px', autoFill: true, highlight: false,
                        dataType: "json",
                        parse: function(data) {
                            var rows = [];
                            for (var i = 0; i < data.tags.length; i++) {
                                var tag = data.tags[i].tag;
                                rows[rows.length] = {data: [tag], value: tag, result: tag};
                            }
                            return rows;
                        }
                    }
                });
            });
        };
    }]);