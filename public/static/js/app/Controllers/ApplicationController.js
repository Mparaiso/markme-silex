var ApplicationController = function($scope,$window,UserService){

    UserService.getCurrentUser(function success(data){
        $scope.user = data.user ;
    });
    
    $scope.logout=function(){
        $scope.info = "Logout user ...";
        UserService.logout(function success(data){
            if(data.status==="ok"){
                $scope.info = "User logged out";
                $window.location = "/";
            }
        },function error(){
            console.log(arguments);
            $scope.error ="Something went wrong";
        });
    };
};