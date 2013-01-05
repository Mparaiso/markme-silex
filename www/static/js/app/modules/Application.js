window.baseUrl = window.baseUrl || "";

// FR : module principal
// EN : main module
var app = angular.module("Application",
        ["ApplicationDirectives", "ApplicationServices", "ApplicationFilters"]);

app.controller("MainController",
        function($scope, $window, UserService, BookmarkService, TagService) {

            // initialization
            $scope.baseUrl = $("meta[name=base_url]").attr("content");

            $scope.bookmarks = [];

            $scope.user = {};

            $scope.tags = {};

            $scope.alert = {};

            $scope.alert.info = "Application loaded successfully!";

            UserService.getCurrentUser(function success(data) {
                $scope.user = data.user;
            });
            // end init.

            var success = function(data) {
                if (data.status === "ok") {
                    $scope.alert.info = "Bookmark saved successfully";
                } else {
                    $scope.alert.error = data.message;
                }

            };

            var error = function() {
                console.log(arguments);
                $scope.alert.error = "Something went wrong bookmark could not be saved";
            };

            $scope.save = function(bookmark) {
                // EN : save bookmark currently edited
                console.log(bookmark);
                bookmark.tags = bookmark.tags.split(",").filter(function(x) {
                    return x !== "";
                });
                if (bookmark.id) {
                    BookmarkService.put(bookmark, success, error);
                } else {
                    BookmarkService.post(bookmark, success, error);
                }
                $scope.alert.info = "Saving bookmark " + bookmark.title + ", please wait...";
            };

            $scope.addBookmark = function() {
                // edit new bookmark
                $scope.bookmark = {};
            };

            $scope.getBookmarks = function() {
                // EN : get user bookmarks
                BookmarkService.get(function success(data) {
                    $scope.bookmarks = data.bookmarks;
                });
            };

            $scope.getByTag = function(tagName) {
                // EN : get user bookmarks by tag
                BookmarkService.getByTag(tagName, function success(data) {
                    $scope.bookmarks = data.bookmarks;
                }, function error() {
                    console.log(arguments);
                });
            };


            $scope.getTags = function() {
                // EN : get user tags
                // FR : obtenir les tags d'un utilisateur
                TagService.get(function success(data) {
                    console.log(data);
                    $scope.tags = data.tags;
                }, function error() {
                    console.log("error", arguments);
                });
            };

            $scope.logout = function() {
                $scope.info = "Logout user ...";
                UserService.logout(function success(data) {
                    if (data.status === "ok") {
                        $scope.info = "User logged out";
                        $window.location = "/";
                    }
                }, function error() {
                    console.log(arguments);
                    $scope.error = "Something went wrong";
                });
            };
        });

app.controller("NavigationController", ["$scope", "$route",
    function NavigationController($scope, $route) {
        $scope.modal_id = "add_modal";
    }
]);

app.controller("BookmarkFormController", ["$scope", "BookmarkService",
    function($scope, BookmarkService) {

    }]);

app.controller("BookmarkController",
        function BookmarkController($scope, $routeParams, BookmarkService, ThumbnailService) {

            // configure le service

            ThumbnailService.setService(ThumbnailService.services.ROBOTHUMB);

            $scope.modal_id = "edit_modal";

            $scope.getThumbnail = ThumbnailService.getThumbnail;

            var successCallback = function success(data) {
                if (data.status === "ok") {
                    $scope.bookmarks = data.bookmarks;
                } else {
                    $scope.alert.info = data.message;
                }
            };

            $scope.editBookmark = function(bookmark) {
                // edit selected bookmark
                $scope.bookmark = angular.copy(bookmark);
            };

            $scope.delete = function(id, index) {
                $scope.alert.info = "Deleting " + $scope.bookmarks[index].title + "...";
                BookmarkService.delete(id, function success(data) {
                    if (data.status === "ok") {
                        $scope.alert.info = "Bookmark " + $scope.bookmarks[index].title + " deleted successfully!";
                        $scope.bookmarks.splice(index, 1);
                    } else {
                        $scope.alert.error = data.message;
                    }
                }, function error() {
                    console.log("error", arguments);
                    $scope.alert.error = "Something went wrong during bookmark deletion.";
                });
            };

            // initialization
            if ($routeParams.tagName) {
                BookmarkService.getByTag($routeParams.tagName, successCallback);
            } else {
                BookmarkService.get(successCallback);
            }
        });

app.controller("TagController",
        function TagController($scope, $routeParams, TagService) {

            var successCallback = function success(data) {
                if (data.status === "ok") {
                    $scope.tags = data.tags;
                } else {
                    $scope.alert.info = data.message;
                }
            };

            TagService.get(successCallback);
        });

app.controller("HomeController",
        function($scope,Url) {
            $scope.baseUrl = Url.getBase();
            $scope.user = $scope.user || {};
            $scope.response = {};
            $scope.title = "Mark.me";
            $scope.year = new Date().getFullYear();
            $scope.login = function(user) {
                $scope.response.status = "error";
                $scope.response.message = "Username or password invalid";
                return console.log(user);
            };
        });

app.controller("LoginController",["$scope", "$window", "UserService",
        function($scope, $window, UserService) {
            $scope.error = "";
            $scope.info = "";
            $scope.login = function(user) {
                $scope.info = "login please wait";
                UserService.login(user, function success(data) {
                    if (data.status === "ok") {
                        $scope.info = "redirecting to application";
                        $window.location = $scope.baseUrl+"/application";
                    } else {
                        $scope.error = data.message;
                    }
                }, function error() {
                    console.log(arguments);
                    $scope.error = "Something went wrong";
                });
            };
        }]);

app.controller("RegisterController",
        function($scope, $http, $window, UserService) {
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
        });

app.controller("AccountController", ["$scope", "UserService",
    function AccountController($scope, UserService) {
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

app.config(['$routeProvider',
    function($routeProvider) {
        $routeProvider.when("/bookmark", {
            templateUrl: "static/js/app/partials/bookmarks.html",
            controller: "BookmarkController"
        });
        $routeProvider.when("/bookmark/tag/:tagName", {
            templateUrl: "static/js/app/partials/bookmarks.html",
            controller: "BookmarkController"
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

