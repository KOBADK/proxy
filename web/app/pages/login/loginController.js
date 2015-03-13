/**
 * LoginController.
 */
angular.module('KobaAdminApp').controller('LoginController', ['$scope', '$http', '$window', '$location',
  function($scope, $http, $window, $location) {
    'use strict';

    $scope.login = function login() {
      $location.path('/apikeys');
    };
  }
]);
