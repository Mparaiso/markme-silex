var RegisterController =function($scope,$http,UserService){
    $scope.user = {};
    $scope.register = function(user){
        if(user.password !== user.password_verify){
            console.log("error");
            $scope.error = "passwords dont match";
        }else{
            console.log("ok");
            $scope.error = "";
            UserService.register(user);
        }
    };
};