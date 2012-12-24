window.baseUrl = window.baseUrl || "";

// FR : module principal
// EN : main module
var app = angular.module("Application",["ApplicationDirectives","ApplicationServices"]);

app.config(["$routeProvider","$locationProvider",
    function($routeProvider,$locationProvider){
    $routeProvider.when("/bookmark",{
        templateUrl:"static/js/app/partials/bookmarks.html",
        controller:"BookmarkController"
    });
    $routeProvider.when("/tag",{
        templateUrl:"static/js/app/partials/tags.html",
        controller:"TagController"
    });
}]);


app.controller("MainController",
    function($scope,$window,UserService,BookmarkService,TagService){

        UserService.getCurrentUser(function success(data){
            $scope.user = data.user ;
        });

        BookmarkService.get(function success(data){
            $scope.bookmarks = data.bookmarks;
        });

        $scope.getBookmarks = function(){
            BookmarkService.get(function success(data){
                $scope.bookmarks = data.bookmarks;
            });
        };

    /**
     * EN : get user tags
     * FR : obtenir les tags d'un utilisateur
     */
     $scope.getTags = function(){
        TagService.get(function success(data){
            console.log(data);
            $scope.tags = data.tags;
        },function error(){
            console.log("error",arguments);
        });
    };

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
});

app.controller("BookmarkController",
    function($scope){

    }
);

app.controller("TagController",
    function($scope){

    }
);

app.controller("HomeController",
    function($scope) {
      $scope.user = $scope.user || {};
      $scope.response = {};
      $scope.title = "Mark.me";
      $scope.year = new Date().getFullYear();
      $scope.login = function(user) {
        $scope.response.status = "error";
        $scope.response.message = "Username or password invalid";
        return console.log(user);
    };
});

app.controller("LoginController",
    function($scope,$window,UserService){
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
    });

app.controller("RegisterController",
    function($scope,$http,$window,UserService){
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
    });

