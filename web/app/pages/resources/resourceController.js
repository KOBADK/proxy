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

    /**
     * Refresh resources.
     */
    $scope.refreshResources = function refreshResources() {
      dataService.send('post', '/admin/resources/refresh').then(
        function () {
          $scope.message = 'Resources refreshed.';
          $scope.messageClass = 'alert-success';
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
      dataService.fetch('get', '/admin/resources/' + resource.mail).then(
        function success(data) {
          var scope = $scope.$new(true);

          // Set resource information.
          scope.resource = data;

          /**
           * Save resource callback.
           */
          scope.save = function save() {
            dataService.send('put', '/admin/resources/' + scope.resource.mail + '/alias', scope.resource).then(
              function () {
                $scope.message = 'Resource save complete.';
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
        },
        function error(reason) {
          $scope.message = reason.message;
          $scope.messageClass = 'alert-danger';
        }
      );
    }
  }
]);

