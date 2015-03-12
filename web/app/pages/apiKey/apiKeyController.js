/**
 * ApiKeyController.
 */
angular.module('KobaAdminApp').controller('ApiKeyController', ['$scope', 'ngOverlay', 'dataService',
  function($scope, ngOverlay, dataService) {
    'use strict';

    /**
     * Load API keys.
     */
    function loadApikeys() {
      // Get user/api key information form the backend.
      dataService.fetch('get', '/admin/apikeys').then(
        function (data) {
          $scope.apikeys = data;
        },
        function (reason) {
          $scope.message = reason.message;
          $scope.messageClass = 'alert-danger';
        }
      );
    }

    loadApikeys();

    /**
     * Remove API key.
     */
    $scope.remove = function remove(key) {
      var scope = $scope.$new(true);

      scope.title = 'Remove API key';
      scope.message = 'Remove the key "' + key + '". This can not be undone.';
      scope.okText = 'Remove';

      scope.confirmed = function confirmed() {
        dataService.fetch('delete', '/admin/apikeys/' + key).then(
          function (data) {
            $scope.message = data;
            $scope.messageClass = 'alert-success';

            // Update api key list.
            loadApikeys();

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
        template: "app/shared/confirm/confirm.html",
        scope: scope
      });
    };

    /**
     * Edit API key callback.
     */
    $scope.edit = function edit(key) {
      dataService.fetch('get', '/admin/apikeys/' + key).then(
        function (data) {
          var scope = $scope.$new(true);

          // Set API key information.
          scope.api = data;

          // Set key.
          scope.api.api_key = key;

          /**
           * Save API key callback.
           */
          scope.save = function save() {
            dataService.send('put', '/admin/apikeys/' + key, scope.api).then(
              function (data) {
                $scope.message = data;
                $scope.messageClass = 'alert-success';

                // Reload API key list.
                loadApikeys();

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
            template: "app/pages/apiKey/keyEdit.html",
            scope: scope
          });
        },
        function (reason) {
          $scope.message = reason.message;
          $scope.messageClass = 'alert-danger';
        }
      );
    };


    /**
     * Add API key callback.
     */
    $scope.add = function add() {
      var scope = $scope.$new(true);

      // Add default API key information.
      scope.api = {
        "api_key": '',
        "name": '',
        "configuration": {}
      };

      // Update api key.
      scope.$watch("api.name", function(newValue) {
        if (newValue.length > 0) {
          scope.api.api_key = CryptoJS.MD5(newValue + Math.random()).toString();
        }
        else {
          scope.api.api_key = '';
        }
      });

      /**
       * Save API key callback.
       */
      scope.save = function save() {
        dataService.send('post', '/admin/apikeys', scope.api).then(
          function (data) {
            $scope.message = data;
            $scope.messageClass = 'alert-success';

            // Reload API keys.
            loadApikeys();

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
        template: "app/pages/apiKey/keyAdd.html",
        scope: scope
      });
    };
  }
]);

