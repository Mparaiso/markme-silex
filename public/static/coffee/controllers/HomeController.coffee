### Controller de la route par défault ###
HomeController = ($scope)->
    $scope.title = "Mark.me"
    $scope.year  = new Date().getFullYear()
    return