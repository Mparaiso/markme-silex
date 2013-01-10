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
        function($scope, $window, UserService, BookmarkService, TagService, Url) {

            // initialization
            $scope.baseUrl = Url.getBase();
            $scope.maxSizeUpload = "5M";
            $scope.bookmarks = [];
            $scope.offset = 0;
            $scope.limit = 20;
            $scope.count = 0;
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
                    // replace the old bookmark with the new one.
                    if (data.bookmark) {
                        //$scope.bookmarks.push(data.bookmark);
                    }
                } else {
                    $scope.alert.error = data.message;
                }

            };

            var errorSave = function() {
                console.log(arguments);
                $scope.alert.error = "Something went wrong bookmark could not be saved";
            };

            $scope.save = function(bookmark) {
                // EN : save bookmark currently edited
                console.log(bookmark);
                bookmark.tags = bookmark.tags.split(",").filter(function(x) {
                    return x !== "";
                });
                BookmarkService.post(bookmark, successSave, errorSave);
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
                        $window.location = $scope.baseUrl + "/";
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

            $scope.fetchingBookmarks = false;

            $scope.getThumbnail = ThumbnailService.getThumbnail;

            var successSave = function(data) {
                // creating or updating a bookmark was successfull
                if (data.status === "ok") {
                    $scope.alert.info = "Bookmark saved successfully";
                    // replace the old bookmark with the new one.
                    if (data.bookmark) {
                        var index = $scope.bookmarks.getIndexOf(function(bookmark) {
                            // convert any string to number (+n)
                            return (+bookmark.id) === (+data.bookmark.id);
                        });
                        if (index >= 0) {
                            console.log("bookmark found");
                            $scope.bookmarks[index] = data.bookmark;
                            if (data.bookmark.tags.join) {
                                $scope.bookmarks[index].tags = data.bookmark.tags.join(",");
                            }

                        }
                    }
                } else {
                    $scope.alert.error = data.message;
                }

            };

            var errorSave = function(data) {
                $scope.alert.error = data.message;
            };

            var successGet = function success(data) {
                if (data.status === "ok") {
                    if ($scope.bookmarks.length === 0) {
                        $scope.bookmarks = data.bookmarks;

                    } else {
                        $scope.bookmarks.append(data.bookmarks);
                    }

                    if (data.count) {
                        $scope.count = data.count;
                    }
                    $scope.offset += 1;
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
                    BookmarkService.getByTag($routeParams.tagName, successGet);
                } else {
                    BookmarkService.get(offset, limit, successGet);
                }
            };

            $scope.editBookmark = function(bookmark) {
                // edit selected bookmark
                $scope.bookmark = angular.copy(bookmark);
            };

            $scope.save = function(bookmark) {
                bookmark.tags = bookmark.tags.split(",").filter(function(x) {
                    return x !== "";
                });
                BookmarkService.put(bookmark, successSave, errorSave);
                $scope.alert.info = "Saving bookmark " + bookmark.title + ", please wait...";
            };

            $scope.delete = function(id, index) {
                $scope.alert.info = "Deleting " + $scope.bookmarks[index].title + "...";
                BookmarkService.delete(id, function success(data) {
                    if (data.status === "ok") {
                        $scope.alert.info = "Bookmark " + $scope.bookmarks[index].title + " deleted successfully!";
                        $scope.bookmarks.splice(index, 1);
                        $scope.bookmarks = $scope.bookmarks.slice();
                    } else {
                        $scope.alert.error = data.message;
                    }
                }, function error() {
                    console.log("error", arguments);
                    $scope.alert.error = "Something went wrong during bookmark deletion.";
                });
            };

            // initialization
            $scope.getBookmarks($scope.offset, $scope.limit);
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

