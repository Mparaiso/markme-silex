/*global angular*/
/**
 * @copyrights 2014 mparaiso <mparaiso@online.fr>
 */
angular.module("ApplicationServices", [])
        .constant('ApiEndpoints', {
            BOOKMARK_IMPORT: '/json/bookmark/import',
            BOOKMARK_EXPORT: '/json/bookmark/export',
            BOOKMARK_SUGGEST: '/json/bookmark/suggest',
            BOOKMARK_FAVORITES: '/json/bookmark/favorites',
            BOOKMARK_TOGGLE_FAVORITE: '/json/bookmark/:id/favorite'
        })
        .factory('Alert', function() {
            var Alert = {
                message: {},
                info: function(message) {
                    this.reset();
                    this.message.info = message;
                },
                success: function(message) {
                    this.reset();
                    this.message.success = message;
                },
                warning: function(message) {
                    this.reset();
                    this.message.warning = message;
                },
                danger: function(message) {
                    this.reset();
                    this.message.danger = message;
                },
                reset: function() {
                    this.message.info = '';
                    this.message.danger = '';
                    this.message.success = '';
                    this.message.warning = '';
                }};
            Object.seal(Alert);
            return Alert;
        })
        .factory("Users", function Users($http) {
            return {
                getCurrent: function() {
                    return $http.get("/json/user")
                            .then(function(result) {
                                this.current = result.data.user;
                                return this.current;
                            }.bind(this));
                }
            };
        })
        .factory("Tags", function Tags($http) {
            var config = {cache: false};
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
        .factory("Thumbnails", function() {
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
        .factory("Bookmarks", function Bookmarks($http, $q, ApiEndpoints, Config) {
            return {
                bookmarks: [],
                offset: 0,
                limit: 35,
                count: 0,
                search: function(search, offset, limit) {
                    offset = offset || 0;
                    limit = limit || 25;
                    return $http.get('/json/bookmark/search', {
                        cache: false,
                        params: {q: search, offset: offset, limit: limit}
                    })
                            .then(function(result) {
                                var bookmarks = result.data.bookmarks || [];
                                if (offset == 0) {
                                    this.bookmarks = bookmarks.slice();
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
                    return $http.get('/json/bookmark', {cache: false, params: {offset: offset, limit: limit}})
                            .then(function(result) {
                                if (offset === 0) {
                                    this.bookmarks = result.data.bookmarks.slice();
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
                    return $http.get('/json/tag/' + tag, {cache: false, params: {offset: offset, limit: limit}})
                            .then(function(result) {
                                var bookmarks = result.data.bookmarks || [];
                                if (offset === 0) {
                                    // first page 
                                    this.bookmarks = bookmarks.slice();
                                } else {
                                    bookmarks.forEach(function(b) {
                                        this.bookmarks.push(b);
                                    }.bind(this));
                                }
                                return bookmarks;
                            }.bind(this))

                },
                save: function(bookmark) {
                    bookmark.tags = bookmark.tags || [];
                    if (bookmark.id) {
                        //edit
                        return $http.put('/json/bookmark/' + bookmark.id, bookmark)
                                .then(function(result) {
                                    var bookmark = result.data.bookmark;
                                    // force the view to consider this bookmark as a new object.
                                    bookmark.timestamp = Date.now();
                                    this.bookmarks.splice(this.bookmarks.indexOf(this.bookmarks.filter(function(b) {
                                        return b.id == bookmark.id;
                                    })[0]), 1, bookmark);
                                    return bookmark;
                                }.bind(this));
                    } else {
                        //create
                        return $http.post('/json/bookmark', bookmark)
                                .then(function(result) {
                                    var bookmark = result.data.bookmark;
                                    bookmark.timestamp = Date.now();
                                    this.bookmarks.unshift(bookmark);
                                    return bookmark;
                                }.bind(this));
                    }
                },
                import: function(file) {
                    var fileReader = new FileReader
                            , domParser = new DOMParser
                            , deferred = $q.defer();
                    fileReader.onloadend = function(ev) {
                        try {
                            var links = [].slice.call(
                                    domParser.parseFromString(fileReader.result, "text/html")
                                    .getElementsByTagName('A'))
                                    .map(function(link) {
                                        return {url: link.getAttribute('HREF'), title: link.textContent};
                                    }).slice(0, Config.importLimit);
                            deferred.resolve(this._doImport(links));
                        } catch (error) {
                            deferred.reject(error);
                        }
                    }.bind(this);
                    fileReader.onerror = function(ev) {
                        console.log('error');
                        deferred.reject(fileReader.error);
                    };
                    fileReader.readAsText(file);
                    return deferred.promise;
                },
                _doImport: function(links) {
                    return $http.post(ApiEndpoints.BOOKMARK_IMPORT, {bookmarks: links.splice(0, 100)})
                            .then(function() {
                                console.log('done', links.length, 'to go');
                                if (links.length > 0) {
                                    return this._doImport(links);
                                }
                            }.bind(this));
                },
                export: function() {
                    return $http.get(ApiEndpoints.BOOKMARK_EXPORT)
                            .then(function(result) {
                                return result.data.export;
                            });
                },
                suggest: function(url) {
                    return $http.get(ApiEndpoints.BOOKMARK_SUGGEST, {params: {url: url}})
                            .then(function(result) {
                                return result.data;
                            });
                },
                toggleFavorite: function(bookmark) {
                    return $http.post(ApiEndpoints.BOOKMARK_TOGGLE_FAVORITE.replace(':id', bookmark.id));
                },
                getFavorites: function(offset, limit) {
                    offset = offset || 0;
                    limit = limit || 25;
                    return $http.get(ApiEndpoints.BOOKMARK_FAVORITES, {cache: false, params: {offset: offset, limit: limit}})
                            .then(function(result) {
                                if (offset === 0) {
                                    this.bookmarks = result.data.bookmarks.slice();
                                } else {
                                    result.data.bookmarks.forEach(function(bookmark) {
                                        this.bookmarks.push(bookmark);
                                    }.bind(this));
                                }
                                return result.data.bookmarks;
                            }.bind(this));
                }
            };
        });
