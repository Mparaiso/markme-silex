window.baseUrl = window.baseUrl || "";
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
angular.module("markme", ["ApplicationDirectives", "ApplicationServices", "ApplicationFilters", 'ngRoute'])
        .value('Alert', {})
        .controller("MainCtrl", function($scope, $window, UserService, Bookmarks, Alert, ThumbnailService) {

            // initialization
            ThumbnailService.setService(ThumbnailService.services.ROBOTHUMB);
            $scope.maxSizeUpload = "5M";
            $scope.Bookmarks = Bookmarks;

            $scope.user = {};
            $scope.tags = {};
            $scope.Alert = Alert;
            Alert.info = "Application loaded successfully!";


            UserService.getCurrentUser(function success(data) {
                $scope.user = data.user;
            });
            // end init.

            var successSave = function(data) {
                // creating or updating a bookmark was successfull
                if (data.status === "ok") {
                    $scope.alert.info = "Bookmark saved successfully";
                } else {
                    $scope.alert.error = data.message;
                }

            };

            var errorSave = function() {
                $scope.alert.error = "Something went wrong bookmark could not be saved";
            };

            $scope.addBookmark = function() {
                // edit new bookmark
                $scope.bookmark = {};
            };

            $scope.logout = function() {
                $scope.info = "Logout user ...";
                UserService.logout(function success(data) {
                    if (data.status === "ok") {
                        $scope.info = "User logged out";
                        $window.location = $scope.baseUrl + "/";
                    }
                }, function error() {
                    console.log(arguments);
                    $scope.error = "Something went wrong";
                });
            };
        })
        .controller("NavigationCtrl", ["$scope", "$routeParams", "$location", function NavigationController($scope, $routeParams, $location) {
                $scope.modal_id = "add_modal";
                $scope.search = $routeParams.search;
                $scope.find = function(search) {
                    // @note @angular dynamicaly change the current page route without refreshing the page
                    $location.path('/bookmark/search/' + search);
                };
            }
        ])
        .controller("BookmarkCtrl", function BookmarkCtrl($scope, mpModalService, $routeParams, Alert, Bookmarks, ThumbnailService) {
            $scope.getThumbnail = ThumbnailService.getThumbnail;
            $scope.editBookmarkModalId = 'bookmark-edit';
            $scope.Bookmarks = Bookmarks;
            $scope.edit = function(bookmark) {
                Bookmarks.current = angular.copy(bookmark);
                Bookmarks.current.tags = Bookmarks.current.tags || [];
                mpModalService.showModal('bookmark-edit');
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
                $scope.fetchingBookmarks = true;
                return $scope.listBookmarks(++$scope.offset, $scope.limit);
            };
            $scope.listBookmarks = function(offset, limit) {
                $scope.fetchingBookmarks = true;
                return $scope.Bookmarks.list(offset, limit)
                        .then(function(lastBookmarkBatch) {
                            Alert.info = "";
                            $scope.lastBookmarkBatch = lastBookmarkBatch;
                        })
                        .catch(function() {
                            Alert.error = "Something went wrong!";
                        })
                        .finally(function() {
                            $scope.fetchingBookmarks = false;
                        });
            };
            $scope.offset = 0;
            $scope.limit = 25;
            $scope.fetchingBookmarks = true;
            $scope.listBookmarks($scope.offset, $scope.limit);
        })
        .controller('BookmarkFormCtrl', function($scope, Bookmarks, Alert) {
            $scope.Bookmarks = Bookmarks;
            $scope.save = function(bookmark) {
                Alert.info = "Saving bookmark " + Bookmarks.current.title + ", please wait...";
                Bookmarks.save(bookmark)
                        .then(function(bookmark) {
                            Alert.info = "";
                        })
                        .catch(function() {
                            Alert.danger = "Error saving bookmark";
                        })
                        .finally(function() {

                        });
            }
        })
        .controller("TagController", function TagController($scope, $log, TagService) {
            $log.info("TagController init");
            var successCallback = function success(data) {
                if (data.status === "ok") {
                    $scope.tags = data.tags;
                } else {
                    $scope.alert.info = data.message;
                }
            };

            TagService.get(successCallback);
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
            }])
        .config(['$routeProvider',
            function($routeProvider) {

                $routeProvider.when("/bookmark/tag/:tagName", {
                    templateUrl: "static/js/app/partials/bookmarks.html",
                    controller: "BookmarkCtrl"
                });
                $routeProvider.when("/bookmark/search/:search", {
                    templateUrl: "static/js/app/partials/bookmarks.html",
                    controller: "BookmarkCtrl"
                });
                $routeProvider.when("/bookmark", {
                    templateUrl: "static/js/app/partials/bookmarks.html",
                    controller: "BookmarkCtrl"
                });
                $routeProvider.when("/tag", {
                    templateUrl: "static/js/app/partials/tags.html",
                    controller: "TagController"
                });

                $routeProvider.when("/account", {
                    templateUrl: "static/js/app/partials/account.html",
                    controller: "AccountController"
                });

                $routeProvider.otherwise({redirectTo: "/bookmark"});
            }]);
