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
ApplicationServices.factory("Bookmarks", function Bookmarks($http) {
    return {
        "bookmarks": [],
        "offset": 0,
        "limit": 35,
        "count": 0,
        search: function(keyword, success, error) {
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
        remove: function(bookmark) {
            return $http.delete('/json/bookmark/' + bookmark.id)
                    .then(function(result) {
                        this.bookmarks.splice(this.bookmarks.indexOf(this.bookmarks.filter(function(_bookmark) {
                            return _bookmark.id === bookmark.id
                        })[0]), 1);
                        return result.data;
                    }.bind(this));
        },
        list: function(offset, limit) {
            offset = offset || 0;
            limit = limit || 25;
            return $http.get('/json/bookmark', {params: {offset: offset, limit: limit}})
                    .then(function(result) {
                        if (offset === 0) {
                            this.bookmarks = result.data.bookmarks;
                        } else {
                            result.data.bookmarks.forEach(function(bookmark) {
                                this.bookmarks.push(bookmark);
                            }.bind(this));
                        }
                        return result.data.bookmarks;
                    }.bind(this));
        },
        getByTag: function(tagName, success, error) {
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
        save: function(bookmark, success, error) {
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
});
