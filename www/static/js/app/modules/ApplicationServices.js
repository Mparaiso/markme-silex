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
        var config = {cache: false};
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
