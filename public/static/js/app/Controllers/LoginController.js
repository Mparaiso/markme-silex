/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
var LoginController = function($scope,$window,UserService){
    $scope.error = "";
    $scope.info = "";
    $scope.login=function(user){
        $scope.info = "login please wait";
        UserService.login(user,function success(data){
            if(data.status ==="ok"){
                $scope.info = "redirecting to application";
                $window.location = "/application";
            }else{
                $scope.error = data.message;
            }
        },function error(){
            console.log(arguments);
            $scope.error = "Something went wrong";
        });
    };
};

