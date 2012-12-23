/**
 * service concernant la gestion d'un utilisateur
 */

var ApplicationServices = angular.module("ApplicationServices",[]);

ApplicationServices.factory("UserService",['$http','$window',function($http,$window){
         
    return {
        getCurrentUser:function(){
            
        },
        isLogged : function(){
        // retourne si un user est connecté
        },
        register : function(user){
         $http.post("/json/register",user).success(function(data){
             if(data.status==="ok"){
                 $window.location("/application");
             }else{
                console.log("error",JSON.stringify(data));
             }
         });
        },
        login : function(user){
            
        },
        logout:function(){
            
        }
    }
}
]);