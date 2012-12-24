var ApplicationController = function($scope,$window,UserService,BookmarkService){

    UserService.getCurrentUser(function success(data){
        $scope.user = data.user ;
    });

    BookmarkService.get(function success(data){
        $scope.bookmarks = data.bookmarks;
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