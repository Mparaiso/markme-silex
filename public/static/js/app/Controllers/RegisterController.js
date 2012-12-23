var RegisterController =function($scope,$http,$window,UserService){
    $scope.user = {};
    $scope.register = function(user){
        if(user.password !== user.password_verify){
            $scope.error = "passwords dont match";
        }else{
            $scope.error = "";
            UserService.register(user,function success(data){
                if(data.status==="ok"){
                    $window.location = "/application";
                }else{
                    $scope.error = data.message;
                }
            },function error(){
                console.log(arguments);
            });
        }
    };
};