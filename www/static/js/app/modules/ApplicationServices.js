//service concernant la gestion d'un utilisateur

var ApplicationServices = angular.module("ApplicationServices", []);
// manage root url configuration
ApplicationServices.factory("Url", function() {
    return{
        getBase: function() {
            return ($("meta[name=base_url]").attr("content")) || "";
        }
    };
});
// manage users business logic
ApplicationServices.factory("UserService", ['$http', 'Url',
    function UserService($http, Url) {
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
// Manage bookmarks API calls
ApplicationServices.factory("BookmarkProvider", ['$http', "Url", function BookmarkProvider($http, Url) {
        var config = {};
        var baseUrl = Url.getBase();
        var BookmarkProvider = {
            "search": function(keyword) {
                return $http.get(baseUrl + "/json/bookmark/search?&query=" + keyword);
            },
            "get": function(offset, limit, success, error) {
                $http.get(baseUrl + "/json/bookmark?offset=" + offset + "&limit=" + limit, config).success(success).error(error);
            },
            "put": function(bookmark, success, error) {
                $http.put(baseUrl + "/json/bookmark", bookmark).success(success).error(error);
            },
            "post": function(bookmark, success, error) {
                $http.post(baseUrl + "/json/bookmark", bookmark).success(success).error(error);
            },
            "remove": function(id, success, error) {
                $http['delete'](baseUrl + "/json/bookmark/" + id).success(success).error(error);
            },
            "getByTag": function(tagName, success, error) {
                $http.get(baseUrl + "/json/bookmark/tag/" + tagName, config).success(success).error(error);
            },
            "count": function(success, error) {
                $http.post(baseUrl + "/json/bookmark/count").success(success).error(error);
            }
        };
        return BookmarkProvider;
    }]);
// manage tag API calls
ApplicationServices.factory("TagService", ["$http", "Url", function TagService($http, Url) {
        var config = {cache: true};
        var baseUrl = Url.getBase();
        return {
            get: function(success, error) {
                $http.get(baseUrl + "/json/tag", config).success(success).error(error);
            }
        };
    }]);
// manage thumbnail generation
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
// manage notifications and alerts @todo implement
ApplicationServices.factory("AlertManager", function() {
    return{
        info: "",
        error: "",
        success: ""
    };
});
// manage  Bookmarks business logic
ApplicationServices.factory("BookmarkManager", ["BookmarkProvider", "$log",
    function BookmarkManager(BookmarkProvider, $log) {
        // creating a bookmark was successfull
        var successCreate = function(data) {
            if (data.status === "ok") {
                // replace the old bookmark with the new one.
                if (data.bookmark) {
                    data.bookmark.tags = data.bookmark.tags.join(",");
                    BookmarkManager.bookmarks.unshift(data.bookmark);
                }
            } else {
                console.log("error creating bookmark status err", data);
            }
        };
        // updating a bookmark was successfull
        var successSave = function(data) {
            // creating or updating a bookmark was successfull
            if (data.status === "ok") {
                // replace the old bookmark with the new one.
                if (data.bookmark) {
                    var index = BookmarkManager.bookmarks.getIndexOf(function(bookmark) {
                        // convert any string to number (+n)
                        return (+bookmark.id) === (+data.bookmark.id);
                    });
                    if (index >= 0) {
                        console.log("bookmark found");
                        BookmarkManager.bookmarks[index] = data.bookmark;
                        if (data.bookmark.tags.join) {
                            BookmarkManager.bookmarks[index].tags = data.bookmark.tags.join(",");
                        }
                    }
                }
            } else {
                console.log("error saving , status err ", data);
            }
        };
        // error saving a bookmark
        var errorSave = function(data) {
            console.log("request error saving", data);
        };
        // success getting bookmarks
        var successGet = function(data) {
            // get bookmarks request is successfull
            if (data.status === "ok") {
                $log.info("success");
                BookmarkManager.bookmarks = data.bookmarks;
                if (data.count) {
                    BookmarkManager.count = parseInt(data.count, 10);
                }
            } else {
                $log.info("get status = error", data.message);
            }
        };
        // error getting bookmarks
        var errorGet = function(data) {
            console.log("request error fetching bookmarks", data);
        };
        var BookmarkManager = {
            "bookmarks": [],
            "offset": 0,
            "limit": 35,
            "count": 0,
            "search": function(keyword, success, error) {
                var self = this;
                self.bookmarks = [];
                return BookmarkProvider.search(keyword).success(
                        function _success(data) {
                            successGet(data);
                            success(data);
                        }).error(
                        function _error(data) {
                            errorGet(data);
                            error(data);
                        }
                );
            },
            "get": function(offset, limit, success, error) {
                var self = this;
                return BookmarkProvider.get(offset, limit,
                        function _success(data) {
                            successGet.call(self, data);
                            if (success)
                                success(data);
                        },
                        function _error(data) {
                            if (error)
                                error(data);
                        }
                );
            },
            "getByTag": function(tagName, success, error) {
                var self = this;
                self.offset = 0;
                self.bookmarks = [];
                return BookmarkProvider.getByTag(tagName, function _success(data) {
                    successGet(data);
                    if (success)
                        success(data);
                }, function _success(data) {
                    errorGet(data);
                    if (error)
                        error(data);
                });
            },
            "save": function(bookmark, success, error) {
                if (bookmark.id) {
                    //edit
                    BookmarkProvider.put(bookmark,
                            function _successSave(data) {
                                // continuation (goto)
                                successSave(data);
                                if (success)
                                    success(data);
                            },
                            function _errorSave(data) {
                                // continuation (goto)
                                errorSave(data);
                                if (error)
                                    error(data);
                            });
                } else {
                    //create
                    BookmarkProvider.post(bookmark,
                            function _successPost(data) {
                                successCreate(data);
                                if (success)
                                    success(data);
                            },
                            function _errorPost(data) {
                                errorSave(data);
                                if (error)
                                    error(data);
                            });
                }
            }
        };
        return BookmarkManager;
    }]);
