/**
 * ResourceController.
 */
angular.module('KobaAdminApp').controller('ResourceController', ['$scope', 'ngOverlay', 'dataService',
  function($scope, ngOverlay, dataService) {
    'use strict';

    /**
     * Load Resources.
     */
    function loadResources() {
      // Get user/api key information form the backend.
      dataService.fetch('get', '/admin/resources').then(
        function (data) {
          $scope.resources = data;
        },
        function (reason) {
          $scope.message = reason.message;
          $scope.messageClass = 'alert-danger';
        }
      );
    }

    loadResources();
  }
]);

