### FR : déclaration d'un module ###

window.baseUrl = window.baseUrl || ""

### FR : creation d'une application ###
app = angular.module("Application",["ApplicationDirectives"])

### FR : configuration des routes ###
app.config(["$routeProvider",
    ($routeProvider)->
        $routeProvider.when("/",{
            templateUrl:"#{baseUrl}/static/js/app/partials/home.html",
            controller:HomeController
        })
        ### FR : route par default ###
        $routeProvider.otherwise(redirectTo:"/")
        return
])