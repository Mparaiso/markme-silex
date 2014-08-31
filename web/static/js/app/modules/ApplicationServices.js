//service concernant la gestion d'un utilisateur

angular.module("ApplicationServices", [])
        .factory("Url", function() {
            return{
                getBase: function() {
                    return ($("meta[name=base_url]").attr("content")) || "";
                }
            };
        })
        .factory("UserService", function UserService($http, Url) {
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
        })
        .factory("Tags", function TagService($http) {
            var config = {cache: true};
            return {
                tags: [],
                get: function() {
                    return $http.get("/json/tag", config)
                            .then(function(result) {
                                this.tags = result.data.tags;
                                return this.tags;
                            }.bind(this));
                }
            };
        })
        .factory("ThumbnailService", function() {
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
        })
        .factory("AlertManager", function() {
            return{
                info: "",
                error: "",
                success: ""
            };
        })
        .factory("Bookmarks", function Bookmarks($http) {
            return {
                bookmarks: [],
                offset: 0,
                limit: 35,
                count: 0,
                search: function(search, offset, limit) {
                    offset = offset || 0;
                    limit = limit || 25;
                    return $http.get('/json/bookmark/search', {
                        params: {q: search, offset: offset, limit: limit}
                    })
                            .then(function(result) {
                                var bookmarks = result.data.bookmarks || [];
                                if (offset == 0) {
                                    this.bookmarks = bookmarks;
                                } else {
                                    bookmarks.forEach(function(b) {
                                        this.bookmarks.push(b);
                                    }.bind(this));
                                }
                                return bookmarks;
                            }.bind(this))
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
                searchByTag: function(tag, offset, limit) {
                    offset = offset || 0;
                    limit = limit || 25;
                    return $http.get('/json/tag/' + tag, {cache: true, params: {offset: offset, limit: limit}})
                            .then(function(result) {
                                var bookmarks = result.data.bookmarks || [];
                                if (offset == 0) {
                                    this.bookmarks = bookmarks;
                                } else {
                                    bookmarks.forEach(function(b) {
                                        this.bookmarks.push(b);
                                    }.bind(this));
                                }
                                return bookmarks;
                            }.bind(this))

                },
                save: function(bookmark) {
                    if (bookmark.id) {
                        //edit
                        return $http.put('/json/bookmark/' + bookmark.id, bookmark)
                                .then(function(result) {
                                    var bookmark = result.data.bookmark;
                                    this.bookmarks.splice(this.bookmarks.indexOf(this.bookmarks.filter(function(b) {
                                        return b.id == bookmark.id;
                                    })[0]), 1, bookmark);
                                    return bookmark;
                                }.bind(this));
                    } else {
                        //create
                        return $http.post('/json/bookmark/', bookmark)
                                .then(function(result) {
                                    var bookmark = result.data.bookmark;
                                    this.bookmarks.push(bookmark);
                                    return bookmark;
                                }.bind(this));
                    }
                }
            };
        });
