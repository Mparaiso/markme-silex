


var HomeController = function($scope) {
  $scope.user = $scope.user || {};
  $scope.response = {};
  $scope.title = "Mark.me";
  $scope.year = new Date().getFullYear();
  $scope.login = function(user) {
    $scope.response.status = "error";
    $scope.response.message = "Username or password invalid";
    return console.log(user);
  };
};