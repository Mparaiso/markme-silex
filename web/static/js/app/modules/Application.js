Array.prototype.getIndexOf = Array.prototype.getIndexOf || function(callback) {
    for (var i = 0; i < this.length; i++) {
        if (callback(this[i]) === true) {
            return i;
        }
    }
    return -1;
};

Array.prototype.append = Array.prototype.append || function(arr) {
    if (!arr instanceof Array) {
        throw arr + " must be an instance of Array";
    }
    for (var i = 0; i < arr.length; i++) {
        this.push(arr[i]);
    }
    return this;
};

/**
 * EN : main module
 * FR : module principal.
 */
angular.module("markme",
        ["ApplicationDirectives", "ApplicationServices", "ApplicationFilters", 'ngRoute'],
        function($routeProvider) {

            $routeProvider.when("/tag/:tag", {
                templateUrl: "static/js/app/partials/bookmarks.html",
                controller: "BookmarkCtrl"
            })
                    .when("/bookmark/search/:search", {
                        templateUrl: "static/js/app/partials/bookmarks.html",
                        controller: "BookmarkCtrl"
                    })
                    .when("/bookmark", {
                        templateUrl: "static/js/app/partials/bookmarks.html",
                        controller: "BookmarkCtrl"
                    })
                    .when("/tag", {
                        templateUrl: "static/js/app/partials/tags.html",
                        controller: "TagCtrl"
                    })
                    .when("/account", {
                        templateUrl: "static/js/app/partials/account.html",
                        controller: "AccountController"
                    })
                    .otherwise({redirectTo: "/bookmark"});
        })
        .value('Alert', {})
        .value('Config', {
            editBookmarkModalId: 'bookmark-edit',
            bookmarksPerPage: 25,
            maxSizeUpload: '5M',
            autoCompleteParse: function(data) {
                var rows = [];
                for (var i = 0; i < data.tags.length; i++) {
                    var tag = data.tags[i];
                    rows[rows.length] = {data: [tag], value: tag, result: tag};
                }
                return rows;
            }})
        .controller("MainCtrl", function($scope, $window, UserService, Bookmarks, Alert, mpModalService, ThumbnailService, Config) {

            // initialization
            ThumbnailService.setService(ThumbnailService.services.ROBOTHUMB);
            $scope.Config = Config;
            $scope.Bookmarks = Bookmarks;
            $scope.Alert = Alert;
            Alert.info = "Application loaded successfully!";
            UserService.getCurrentUser(function success(data) {
                $scope.user = data.user;
            });
        })
        .controller("NavigationCtrl", function NavigationCtrl($scope, Bookmarks, mpModalService, Config, $routeParams, $location) {
            $scope.modal_id = "add_modal";
            $scope.search = $routeParams.search;
            $scope.find = function(search) {
                // @note @angular dynamicaly change the current page route without refreshing the page
                $location.path('/bookmark/search/' + search);
            };
            $scope.create = function() {
                Bookmarks.current = {};
                mpModalService.showModal(Config.editBookmarkModalId);
            };
        })
        .controller("BookmarkCtrl", function BookmarkCtrl($scope, mpModalService, $location, $routeParams, Alert, Bookmarks, Config, ThumbnailService) {
            $scope.getThumbnail = ThumbnailService.getThumbnail;
            $scope.Bookmarks = Bookmarks;
            Bookmarks.bookmarks = [];
            $scope.edit = function(bookmark) {
                Bookmarks.current = angular.copy(bookmark);
                Bookmarks.current.tags = Bookmarks.current.tags || [];
                mpModalService.showModal(Config.editBookmarkModalId);
            };
            $scope.remove = function(bookmark) {
                Alert.info = "Deleting %s ...".replace("%s", bookmark.title);
                return Bookmarks.remove(bookmark)
                        .then(function() {
                            Alert.info = "Bookmark %s removed.".replace("%s", bookmark.title);
                        })
                        .catch(function() {
                            Alert.danger = "Error removing bookmark %s .".replace("%s", bookmark.title);
                        });
            };
            $scope.nextBookmarkPage = function() {
                if ($scope.search) {
                    return $scope.searchBookmarks($scope.search, ++$scope.offset, $scope.limit);
                } else if ($scope.tag) {
                    $scope.searchBookmarksByTag($scope.tag++, $scope.offset, $scope.limit);
                } else {
                    return $scope.listBookmarks(++$scope.offset, $scope.limit);
                }
            };
            $scope.listBookmarks = function(offset, limit) {
                $scope.fetchingBookmarks = true;
                return $scope.Bookmarks.list(offset, limit)
                        .then(onBookmarkResponseOk)
                        .catch(onBookmarkResponseError)
                        .finally(onBookmarkResponseEnd);
            };
            $scope.searchBookmarks = function(search, offset, limit) {
                $scope.fetchingBookmarks = true;
                return Bookmarks.search(search, offset, limit)
                        .then(onBookmarkResponseOk)
                        .catch(onBookmarkResponseError)
                        .finally(onBookmarkResponseEnd);
            };
            $scope.searchBookmarksByTag = function(tag, offset, limit) {
                $scope.fetchingBookmarks = true;
                return Bookmarks.searchByTag(tag, offset, limit)
                        .then(onBookmarkResponseOk)
                        .catch(onBookmarkResponseError)
                        .finally(onBookmarkResponseEnd);
            };
            $scope.offset = $routeParams.offset || 0;
            $scope.limit = Config.bookmarksPerPage;
            $scope.fetchingBookmarks = true;
            if ($routeParams.search) {
                $scope.search = $routeParams.search;
                $scope.searchBookmarks($scope.search, $scope.offset, $scope.limit);
            } else if ($routeParams.tag) {
                $scope.tag = $routeParams.tag;
                $scope.searchBookmarksByTag($scope.tag, $scope.offset, $scope.limit);
            } else {
                $scope.listBookmarks($scope.offset, $scope.limit);
            }
            $scope.$watch('offset', function(current) {
                $location.search('offset', current);
            });
            /* event handlers */
            function onBookmarkResponseOk(lastBookmarkBatch) {
                console.log('lastBookmarkBatch', lastBookmarkBatch);
                Alert.info = "";
                $scope.lastBookmarkBatch = lastBookmarkBatch;
            }
            function onBookmarkResponseEnd() {
                $scope.fetchingBookmarks = false;
            }
            function onBookmarkResponseError(err) {
                Alert.danger = "Error fetching bookmarks.";
            }
        })
        .controller('BookmarkFormCtrl', function($scope, Bookmarks, Alert, $rootScope, mpModalService, Config) {
            $scope.Bookmarks = Bookmarks;
            $scope.Config = Config;
            $scope.save = function(bookmark) {
                Alert.info = "Saving bookmark " + bookmark.title + ", please wait...";
                Bookmarks.save(bookmark)
                        .then(function(bookmark) {
                            Alert.info = 'Bookmark "%s" edited'.replace("%s", bookmark.title);
                            if (!$rootScope.$$phase) {
                                $rootScope.$apply('Bookmarks.bookmarks');
                            }
                        })
                        .catch(function() {
                            Alert.danger = "Error saving bookmark";
                        }).finally(function() {
                    mpModalService.hideModal(Config.editBookmarkModalId)
                });
            }
        })
        .controller("TagCtrl", function TagController($scope, Alert, Tags) {
            $scope.Tags = Tags;
            Tags.get()
                    .then(function(tags) {
                        console.log(tags);
                        console.log($scope.Tags);
                    })
                    .catch(function() {
                        Alert.danger = "Error fetching tags.";
                    })
                    .finally(function() {
                        Alert.info = "";
                    });
        })
        .controller("LoginController", ["$scope", "$window", "UserService", function($scope, $window, UserService) {
                $scope.error = "";
                $scope.info = "";
                $scope.login = function(user) {
                    $scope.info = "login please wait";
                    UserService.login(user, function success(data) {
                        if (data.status === "ok") {
                            $scope.info = "redirecting to application";
                            $window.location = $scope.baseUrl + "/application";
                        } else {
                            $scope.error = data.message;
                        }
                    }, function error() {
                        console.log(arguments);
                        $scope.error = "Something went wrong";
                    });
                };
            }])
        .controller("RegisterController", function($scope, $window, UserService) {
            $scope.user = {};
            $scope.register = function(user) {
                if (user.password !== user.password_verify) {
                    $scope.error = "passwords dont match";
                } else {
                    $scope.error = "";
                    UserService.register(user, function success(data) {
                        if (data.status === "ok") {
                            $window.location = "/application";
                        } else {
                            $scope.error = data.message;
                        }
                    }, function error() {
                        console.log(arguments);
                    });
                }
            };
        })
        .controller("AccountController", ["$scope", "UserService", function AccountController($scope, UserService) {
                $scope.user_config = {};
                UserService.getCurrentUser(function(data) {
                    if (data.status === "ok") {
                        $scope.user_config = data.user;
                    } else {
                        $scope.alert.error = "Could not get user infos.";
                    }
                }, function() {
                    $scope.alert.error = "Could not get user infos.";
                });
            }]);