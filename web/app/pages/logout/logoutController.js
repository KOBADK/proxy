/**
 * LogoutController.
 */
angular.module('KobaAdminApp').controller('LogoutController', ['$scope', '$window',
  function($scope, $window) {
    'use strict';

    // Remove the token from login.
    delete $window.sessionStorage.token;
  }
]);