//service concernant la gestion d'un utilisateur

var ApplicationServices = angular.module("ApplicationServices",[]);

/** manage user API calls **/
ApplicationServices.factory("UserService",['$http','$window',
    function($http,$window){
        var config = {cache:true};
        return {
            getCurrentUser:function(success,error){
                $http.get("/json/user").success(success).error(error||function(){});
            },
            isLogged : function(){},
            register : function(user,success,error){
                $http.post("/json/register",user).success(success).error(error);
            },
            login : function(user,success,error){
                $http.post("/json/login",user).success(success).error(error);
            },
            logout:function(success,error){
                $http.post("/json/logout").success(success).error(error);
            }
        };
    }
    ]);

/** manage bookmark API calls **/
ApplicationServices.factory("BookmarkService",['$http',function($http){
    var config = {cache:true};
    return {
        get : function(success,error){
            $http.get("/json/bookmark",config).success(success).error(error);
        },
        post : function(bookmark,success,error){
            $http.put("/json/bookmark",bookmark).success(success).error(error);
        },
        put : function(bookmark,success,error){
            $http.post("/json/bookmark",bookmark).success(success).error(error);
        },
        "delete" : function(bookmark){
            $http['delete']("/json/bookmark",bookmark).success(success).error(error);

        },
        "getByTag":function(tagName,success,error){
            $http.get("/json/bookmark/tag/"+tagName,config).success(success).error(error);
        }
    };
}]);

/** manage tag API calls **/
ApplicationServices.factory("TagService",["$http",function($http){
    var config = {cache:true};
    return {
        get : function(success,error){
            $http.get("/json/tag",config).success(success).error(error);
        }
    };
}]);