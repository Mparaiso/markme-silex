window.baseUrl = window.baseUrl || "";
Array.prototype.getIndexOf = function(callback) {
    for (var i = 0; i < this.length; i++) {
        if (callback(this[i]) === true) {
            return i;
        }
    }
    return -1;
};

Array.prototype.append = function(arr) {
    if (!arr instanceof Array) {
        throw arr + " must be an instance of Array";
    }
    for (var i = 0; i < arr.length; i++) {
        this.push(arr[i]);
    }
    return this;
};

// FR : module principal
// EN : main module
var app = angular.module("Application",
        ["ApplicationDirectives", "ApplicationServices", "ApplicationFilters"]);
app.controller("MainController",
        function($scope, $window, UserService, BookmarkManager, Url) {

            // initialization
            $scope.baseUrl = Url.getBase();
            $scope.maxSizeUpload = "5M";
            $scope.BookmarkManager = BookmarkManager;

            $scope.user = {};
            $scope.tags = {};
            $scope.alert = {};
            $scope.alert.info = "Application loaded successfully!";


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

            $scope.save = function(bookmark) {
                // EN : save bookmark currently edited
                console.log(bookmark);
                bookmark.tags = bookmark.tags.split(",").filter(function(x) {
                    return x !== "";
                });
                BookmarkManager.save(bookmark, successSave, errorSave);
                $scope.alert.info = "Saving bookmark " + bookmark.title + ", please wait...";
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
        });

app.controller("NavigationController", ["$scope", "$routeParams", "$location",
    function NavigationController($scope, $routeParams, $location) {
        $scope.modal_id = "add_modal";
        $scope.search = $routeParams.search;
        $scope.find = function(search) {
            // @note @angular dynamicaly change the current page route without refreshing the page
            $location.path('/bookmark/search/' + search);
        };
    }
]);

app.controller("BookmarkFormController", ["$scope", "BookmarkProvider", function($scope, BookmarkProvider) {
    }]);

app.controller("BookmarkController",
        function BookmarkController($scope, $routeParams, BookmarkManager, BookmarkProvider, ThumbnailService) {

            // configure le service
            ThumbnailService.setService(ThumbnailService.services.ROBOTHUMB);
            $scope.getThumbnail = ThumbnailService.getThumbnail;
            $scope.BookmarkManager = BookmarkManager;
            $scope.modal_id = "edit_modal";
            $scope.fetchingBookmarks = false;
            var successGet = function success(data) {
                if (data.status === "ok") {
                    console.log("success get from BookmarkController", data);
                    $scope.alert.info = "";
                } else {
                    $scope.alert.info = data.message;
                }
                $scope.fetchingBookmarks = false;
            };
            $scope.splitTagString = function(bookmark) {
                if (typeof(bookmark.tags) === "string")
                    return bookmark.tags.split(",").filter(function(i) {
                        return i !== null;
                    });
            };
            $scope.getBookmarks = function(offset, limit) {
                // initialization
                $scope.alert.info = "Fetching bookmarks";
                $scope.fetchingBookmarks = true;
                if ($routeParams.tagName) {
                    BookmarkManager.getByTag($routeParams.tagName, successGet);
                } else if ($routeParams.search) {
                    BookmarkManager.search($routeParams.search, successGet);
                } else {
                    BookmarkManager.get(offset, limit, successGet);
                }
            };
            $scope.editBookmark = function(bookmark) {
                // edit selected bookmark
                console.log(bookmark);
                if (!bookmark.tags) {
                    bookmark.tags = [];
                }
                $scope.bookmark = angular.copy(bookmark);

            };
            $scope.save = function(bookmark) {
                bookmark.tags = $scope.splitTagString(bookmark);
                $scope.BookmarkManager.save(bookmark,
                        function success(data) {
                            if (data.status === "ok") {
                                $scope.alert.info = "Bookmark saved successfully";
                            } else {
                                $scope.alert.error = data.message;
                            }
                        },
                        function error(data) {
                            $scope.alert.error = data.message;
                        });
                $scope.alert.info = "Saving bookmark " + bookmark.title + ", please wait...";
            };
            $scope.remove = function(id, index) {
                $scope.alert.info = "Deleting " + $scope.BookmarkManager.bookmarks[index].title + "...";
                BookmarkProvider.remove(id, function success(data) {
                    if (data.status === "ok") {
                        $scope.alert.info = "Bookmark " + $scope.BookmarkManager.bookmarks[index].title + " deleted successfully!";
                        $scope.BookmarkManager.bookmarks.splice(index, 1);
                        $scope.BookmarkManager.bookmarks = $scope.BookmarkManager.bookmarks.slice();
                    } else {
                        $scope.alert.error = data.message;
                    }
                }, function error() {
                    console.log("error", arguments);
                    $scope.alert.error = "Something went wrong during bookmark deletion.";
                });
            };
            // initialization
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
        function($scope, Url) {
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

app.controller("LoginController", ["$scope", "$window", "UserService",
    function($scope, $window, UserService) {
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

        $routeProvider.when("/bookmark/tag/:tagName", {
            templateUrl: "static/js/app/partials/bookmarks.html",
            controller: "BookmarkController"
        });
        $routeProvider.when("/bookmark/search/:search", {
            templateUrl: "static/js/app/partials/bookmarks.html",
            controller: "BookmarkController"
        });
        $routeProvider.when("/bookmark", {
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

