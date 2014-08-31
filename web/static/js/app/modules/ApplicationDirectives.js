var Directives = angular.module("ApplicationDirectives", ["ApplicationServices"])
        .directive("popOver", function() {
            /* FR : utilise le plugin bootstrap pop-over
             */
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
        })
        .directive("toggleActive", function() {
            /* FR : bascule l'élement en mode active en changant la classe
             */
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
        })
        .directive("preloadImage", function() {
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
        })
        .directive("bstTooltip", ["$timeout", function($timeout) {
                return function(scope, element, attrs) {
                    $timeout(function() {
                        return element.tooltip();
                    }, 10);
                };
            }])
        .directive("tagsInput", ["$timeout", "Url", function tagsInput($timeout, Url) {
                /** allow jquery.tags-input plugin use **/
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
                            autocomplete_url: '/json/autocomplete',
                            autocomplete: {selectFirst: true, width: "auto", autoFill: true, highlight: false,
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
            }])
        .directive('masonry', function($timeout) {
            return {
                scope: {
                    columnWidth: '@',
                    itemSelector: '@',
                    transitionDuration: '@',
                    gutter: '@',
                    masonry: "="
                },
                link: function($scope, element, attrs, ctrl) {
                    $timeout(function() {
                        var container,
                                update = function() {
                                    $timeout(function() {
                                        container.masonry('reloadItems');
                                        container.masonry();
                                    }, 5);
                                },
                                getOptions = function() {
                                    return {
                                        columnWidth: parseInt($scope.columnWidth, 10) || 200,
                                        itemSelector: $scope.itemSelector || ">*",
                                        gutter: parseInt($scope.gutter, 10) || 10,
                                        transitionDuration: $scope.transitionDuration || '0.5s',
                                    };
                                };
                        container = element.masonry(getOptions());
                        container.masonry();
                        $scope.$watch('masonry', update, true);
                    });
                }
            };
        })
        .service('mpModalService', function() {
            this._modalList = [];
            this.register = function(name, el) {
                this._modalList.push({
                    name: name,
                    el: el
                });
            };
            this.unregister = function(name) {
                return this._modalList.splice(this._modalList.indexOf(this.findModalByName(name)), 1);
            };
            this.findModalByName = function(name) {
                return this._modalList.filter(function(modal) {
                    return modal.name == name;
                })[0];
            };
            this.showModal = function(name) {
                var modal = this.findModalByName(name);
                console.log('modal', modal);
                if (modal) {
                    return modal.el.modal('toggle');
                }
            };
            this.hideModal = function(name) {
                var modal = this.findModalByName(name);
                if (modal) {
                    return modal.el.modal('hide');
                }
            };
        })
        .directive('mpModalFooter', function() {
            return {
                restrict: 'EAC',
                template: '<div class="modal-footer" ng-transclude></div>',
                transclude: true,
                replace: true,
                link: angular.noop
            };
        })
        .directive('mpModalBody', function() {
            return {
                restrict: 'EAC',
                template: '<div class="modal-body" ng-transclude></div>',
                transclude: true,
                replace: true,
                link: angular.noop
            };
        })
        .directive('mpModalHeader', function($timeout) {
            return {
                restrict: 'EAC',
                require: '^mpModal',
                template: '<div class="modal-header">' +
                        '<button type="button" class="close" ng-click="hide()" data-dismiss="{{modalId}}"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>' +
                        '<div ng-transclude></div>' +
                        '</div>',
                transclude: true,
                scope: true,
                link: function($scope, el, attrs, controller) {
                    $scope.modalId = controller.modalId;
                    $scope.hide = controller.hide.bind(controller);
                }
            };
        })
        .directive('mpModal', function($timeout, mpModalService) {
            return {
                restrict: 'EAC',
                transclude: true,
                template: '<div class="modal-dialog">' +
                        '	   		<div class="modal-content" ng-transclude></div>' +
                        '		</div><!-- /.modal-dialog -->',
                scope: {
                    modalId: '@'
                },
                controller: function($scope, $element) {
                    this.modalId = $scope.modalId;
                    this.hide = function() {
                        $element.modal('hide');
                    };
                },
                link: function($scope, element, attributes, controller) {
                    element.attr({
                        tabindex: '-1',
                        role: 'dialog',
                        'aria-hidden': 'true',
                        id: $scope.modalId,
                        'aria-labelledby': attributes.ariaLabelledBy
                    });
                    element.addClass('modal');
                    element.addClass('fade');
                    $timeout(function() {
                        mpModalService.register($scope.modalId, element);
                        $scope.$on('$destroy', function() {
                            mpModalService.unregister($scope.modalId);
                        });
                    });

                }
            };
        });