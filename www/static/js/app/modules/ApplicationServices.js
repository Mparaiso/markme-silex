//service concernant la gestion d'un utilisateur

var ApplicationServices = angular.module("ApplicationServices", []);
ApplicationServices.factory("Url", function() {
    return{
        getBase: function() {
            return ($("meta[name=base_url]").attr("content")) || "";
        }
    };
});
/** manage user API calls **/
ApplicationServices.factory("UserService", ['$http', '$window', 'Url',
    function UserService($http, $window, Url) {
        var config = {cache: true};
        var baseUrl = Url.getBase();
        return {
            getCurrentUser: function(success, error) {
                $http.get(baseUrl + "/json/user").success(success).error(error || function() {
                });
            },
            isLogged: function() {
            },
            register: function(user, success, error) {
                $http.post(baseUrl + "/json/register", user).success(success).error(error);
            },
            login: function(user, success, error) {
                $http.post(baseUrl + "/json/login", user).success(success).error(error);
            },
            logout: function(success, error) {
                $http.post(baseUrl + "/json/logout").success(success).error(error);
            }
        };
    }
]);
/** manage bookmark API calls **/
ApplicationServices.factory("BookmarkService", ['$http', "Url", function BookmarkService($http, Url) {
        var config = {cache: true};
        var baseUrl = Url.getBase();
        return {
            "get": function(offset, limit, success, error) {
                $http.get(baseUrl + "/json/bookmark?offset=" + offset + "&limit=" + limit, config).success(success).error(error);
            },
            "put": function(bookmark, success, error) {
                $http.put(baseUrl + "/json/bookmark", bookmark).success(success).error(error);
            },
            "post": function(bookmark, success, error) {
                $http.post(baseUrl + "/json/bookmark", bookmark).success(success).error(error);
            },
            "delete": function(id, success, error) {
                $http['delete'](baseUrl + "/json/bookmark/" + id).success(success).error(error);
            },
            "getByTag": function(tagName, success, error) {
                $http.get(baseUrl + "/json/bookmark/tag/" + tagName, config).success(success).error(error);
            },
            "count": function(success, error) {
                $http.post(baseUrl + "/json/bookmark/count").success(success).error(error);
            }
        };
    }]);
/** manage tag API calls **/
ApplicationServices.factory("TagService", ["$http", "Url", function TagService($http, Url) {
        var config = {cache: true};
        var baseUrl = Url.getBase();
        return {
            get: function(success, error) {
                $http.get(baseUrl + "/json/tag", config).success(success).error(error);
            }
        };
    }]);
ApplicationServices.factory("ThumbnailService", function() {
    return {
        setService: function(serviceCallback) {
            this.getThumbnail = function() {
                return serviceCallback.apply(null, [].slice.call(arguments));
            };
        },
        services: {
            WIMG: function(url) {
                return "http://wimg.ca/http://" + url;
            },
            THUMBALIZR: function(url, width) {
                if (width === undefined) {
                    width = 200;
                }
                return "http://api.thumbalizr.com/?url=" + url + "&width=" + width;
            },
            ROBOTHUMB: function(url, width, height) {
                if (width === undefined)
                    width = 240;
                if (height === undefined)
                    height = 180;
                return "http://www.robothumb.com/src/?url=" + url + "&size=" + width + "x" + height;
            }
        }
    };
});
// Bookmarks business logic
ApplicationServices.factory("BookmarkManager", function BookmarkManager(BookmarkService) {
    var bookmark = {};
    var bookmarks = [];
    var offset = 0;
    var limit = 20;
    var count = 0;
    var fetchingBookmarks = false;
    var alert = {info: "", error: ""};
    var errorSave = function() {
        alert.error = "Something went wrong bookmark could not be saved";
    };
    var successSave = function(data) {
// creating or updating a bookmark was successfull
        if (data.status === "ok") {
            alert.info = "Bookmark saved successfully";
            // replace the old bookmark with the new one.
            var index = bookmarks.getIndexOf(function(bookmark) {
                return bookmark.id == data.bookmark.id;
            });
            if (index >= 0) {
                bookmarks[index] = data.bookmark;
                if (data.bookmark.tags.join) {
                    bookmarks[index].tags = data.bookmark.tags.join(",");
                }
            }
        } else {
            alert.error = data.message;
        }

    };
    var successFetch = function success(data) {
        if (data.status === "ok") {
            if (bookmarks.length <= 0) {
                bookmarks = data.bookmarks;
            } else {
                bookmarks = bookmarks.concat(data.bookmarks);
            }
            if (data.count) {
                count = data.count;
            }
            offset += 1;
            alert.info = "";
        } else {
            alert.info = data.message;
        }
        fetchingBookmarks = false;
    };
    var BookmarkManager = {
// EN : save bookmark currently edited
        "save": function(bookmark) {
            bookmark.tags = bookmark.tags.split(",").filter(function(x) {
                return x !== "";
            });
            if (bookmark.id) {
                BookmarkService.put(bookmark, successSave, errorSave);
            } else {
                BookmarkService.post(bookmark, successSave, errorSave);
            }
            alert.info = "Saving bookmark " + bookmark.title + ", please wait...";
        },
        "delete": function(id, index) {
            alert.info = "Deleting " + bookmarks[index].title + "...";
            BookmarkService.delete(id, function success(data) {
                if (data.status === "ok") {
                    alert.info = "Bookmark " + bookmarks[index].title + " deleted successfully!";
                    bookmarks.splice(index, 1);
                    bookmarks = bookmarks.slice();
                } else {
                    alert.error = data.message;
                }
            }, function error() {
                console.log("error", arguments);
                alert.error = "Something went wrong during bookmark deletion.";
            });
        },
        "addBookmark": function() {
// edit new bookmark
            bookmark = {};
        },
        "fetch": function(tagName) {
// EN : get user bookmarks
            alert.info = "Fetching bookmarks";
            fetchingBookmarks = true;
            if (tagName) {
                BookmarkService.getByTag(tagName, successCallback);
            } else {
                BookmarkService.get(offset, limit, successCallback);
            }
        },
        "fetchByTag": function(tagName) {
// EN : get user bookmarks by tag
            BookmarkService.getByTag(tagName, function success(data) {
                bookmarks = data.bookmarks;
            }, function error() {
                console.log(arguments);
            });
        },
        "getBookmarks": function(tagName) {
            // get loaded bookmarks;
            if (bookmarks.length <= 0) {
                this.fetch(tagName);
            }
            return bookmarks;
        },
        "getCount": function() {
            return count;
        },
        "getLimit": function() {
            return limit;
        },
        "getOffset": function() {
            return offset;
        },
        "getAlert": function() {
            return alert;
        }

    };
    return BookmarkManager;
});