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

    $scope.refreshResources = function refreshResources() {
      dataService.fetch('get', '/admin/resources/refresh').then(
        function () {
          loadResources();
        },
        function (reason) {
          $scope.message = reason.message;
          $scope.messageClass = 'alert-danger';
        }
      );
    };

    /**
     * Edit resource callback.
     */
    $scope.edit = function edit(resource) {
      var scope = $scope.$new(true);

      // Set resource information.
      scope.resource = resource;

      /**
       * Save resource callback.
       */
      scope.save = function save() {
        dataService.send('put', '/admin/resources/' + scope.resource.mail + '/alias', scope.resource).then(
          function (data) {
            $scope.message = data;
            $scope.messageClass = 'alert-success';

            // Reload API key list.
            loadResources();

            // Close overlay.
            overlay.close();
          },
          function (reason) {
            $scope.message = reason.message;
            $scope.messageClass = 'alert-danger';
          }
        );
      };

      // Open the overlay.
      var overlay = ngOverlay.open({
        template: "app/pages/resources/resourceEdit.html",
        scope: scope
      });
    }
  }
]);

