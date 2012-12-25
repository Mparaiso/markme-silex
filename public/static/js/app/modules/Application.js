window.baseUrl = window.baseUrl || "";

// FR : module principal
// EN : main module
var app = angular.module("Application",
    ["ApplicationDirectives","ApplicationServices","ApplicationFilters"]);

app.controller("MainController",
    function($scope,$window,UserService,BookmarkService,TagService){

        $scope.bookmarks =[];

        $scope.user ={};

        $scope.tags = {};

        $scope.alert = {};

        $scope.alert.info = "Application loaded successfully!";

        UserService.getCurrentUser(function success(data){
            $scope.user = data.user ;
        });

        $scope.getBookmarks = function(){
            BookmarkService.get(function success(data){
                $scope.bookmarks = data.bookmarks;
            });
        };

        $scope.getByTag = function(tagName){
            BookmarkService.getByTag(tagName,function success(data){
                $scope.bookmarks = data.bookmarks;
            },function error(){
                console.log(arguments);
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

app.controller("NavigationController",["$scope","$route",
    function($scope,$route){
    }
]);

app.controller("BookmarkController",
    function($scope,$routeParams,BookmarkService){

        var successCallback = function success(data){
            if(data.status === "ok"){
                $scope.bookmarks = data.bookmarks;
            }else{
                $scope.alert.info = data.message;
            }
        };

        $scope['delete'] = function(id,index){
            BookmarkService['delete'](id,function success(data){
                if(data.status === "ok"){
                    $scope.alert.info = "Bookmark "+$scope.bookmarks[index].title+" deleted successfully!";
                    $scope.bookmarks.splice(index,1);
                }else{
                    $scope.alert.error = data.message;
                }
            },function error(){
                console.log("error",arguments);
            });
        };

        // initialization
        if($routeParams.tagName){
            BookmarkService.getByTag($routeParams.tagName,successCallback);
        }else{
            BookmarkService.get(successCallback);
        }
    });

app.controller("TagController",
    function($scope,$routeParams,TagService){

        var successCallback = function success(data){
            if(data.status === "ok"){
                $scope.tags = data.tags;
            }else{
                $scope.alert.info = data.message;
            }
        };

        TagService.get(successCallback);
    });

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

app.config(['$routeProvider',
    function($routeProvider){
        $routeProvider.when("/bookmark",{
            templateUrl:"static/js/app/partials/bookmarks.html",
            controller:"BookmarkController"
        });
        $routeProvider.when("/bookmark/tag/:tagName",{
            templateUrl:"static/js/app/partials/bookmarks.html",
            controller:"BookmarkController"
        });
        $routeProvider.when("/tag",{
            templateUrl:"static/js/app/partials/tags.html",
            controller:"TagController"
        });
        $routeProvider.otherwise({redirectTo :"/bookmark"});
    }]);

