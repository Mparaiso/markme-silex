//service concernant la gestion d'un utilisateur

var ApplicationServices = angular.module("ApplicationServices",[]);

ApplicationServices.factory("UserService",['$http','$window',
    function($http,$window){
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