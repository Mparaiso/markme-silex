### Controller de la route par défault ###
HomeController = ($scope)->
    $scope.user = $scope.user || {}
    $scope.response = {}
    $scope.title = "Mark.me"
    $scope.year  = new Date().getFullYear()
    $scope.login = (user)->
        $scope.response.status = "error"
        $scope.response.message = "Username or password invalid"
        console.log(user)
    return