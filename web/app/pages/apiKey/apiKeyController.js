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
           * Load Resources.
           */
          function loadResources() {
            // Get user/api key information form the backend.
            dataService.fetch('get', '/admin/resources').then(
              function (data) {
                scope.resources = data;
              },
              function (reason) {
                $scope.message = reason.message;
                $scope.messageClass = 'alert-danger';
              }
            );
          }

          loadResources();

          /**
           * Add a group
           * @param groupId
           *   The id of the group to add.
           */
          scope.addGroup = function addGroup(groupId) {
            if (!groupId || groupId === '') {
              return;
            }

            for (var i = 0; i < scope.api.configuration.groups.length; i++) {
              if (scope.api.configuration.groups[i].id === groupId) {
                return;
              }
            }
            scope.api.configuration.groups.push({
              "id": groupId,
              "resources": []
            });
          };

          /**
           * Remove a group
           * @param groupId
           *   The id of the group to remove.
           */
          scope.removeGroup = function removeGroup(groupId) {
            for (var i = 0; i < scope.api.configuration.groups.length; i++) {
              if (scope.api.configuration.groups[i].id === groupId) {
                scope.api.configuration.groups.splice(i, 1);
              }
            }
          };

          /**
           * Add a resource to a group.
           *
           * @param groupId
           *   Id of the group to add a resource to.
           * @param resource
           *   The resource to add.
           */
          scope.addResourceToGroup = function addResourceToGroup(groupId, resource) {
            for (var i = 0; i < scope.api.configuration.groups.length; i++) {
              if (scope.api.configuration.groups[i].id === groupId) {
                var resources = scope.api.configuration.groups[i].resources;

                var alreadyAdded = false;

                for (var j = 0; j < resources.length; j++) {
                  if (resources[j].mail === resource.mail) {
                    alreadyAdded = true;
                  }
                }

                if (!alreadyAdded) {
                  resources.push(resource);
                }

                break;
              }
            }
          };

          /**
           * Remove resource from a group.
           *
           * @param groupId
           *   Id of the group to add a resource to.
           * @param resource
           *   The resource to add.
           */
          scope.removeResourceFromGroup = function removeResourceFromGroup(groupId, resource) {
            for (var i = 0; i < scope.api.configuration.groups.length; i++) {
              if (scope.api.configuration.groups[i].id === groupId) {
                var resources = scope.api.configuration.groups[i].resources;

                for (var j = 0; j < resources.length; j++) {
                  if (resources[j].mail === resource.mail) {
                    resources.splice(j, 1);
                    return;
                  }
                }
              }
            }
          };

          /**
           * Is the group selected.
           *
           * @param groupId
           * @param resource
           * @returns {boolean}
           */
          scope.resourceSelected = function resourceSelected(groupId, resource) {
            for (var i = 0; i < scope.api.configuration.groups.length; i++) {
              if (scope.api.configuration.groups[i].id === groupId) {
                var resources = scope.api.configuration.groups[i].resources;

                for (var j = 0; j < resources.length; j++) {
                  if (resources[j].mail === resource.mail) {
                    return true;
                  }
                }
              }
            }
            return false;
          };

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
        "configuration": {
          "groups": [
            {
              "id": "default",
              "resources": []
            },
            {
              "id": "display",
              "resources": []
            }
          ]
        }
      };

      /**
       * Load Resources.
       */
      function loadResources() {
        // Get user/api key information form the backend.
        dataService.fetch('get', '/admin/resources').then(
          function (data) {
            scope.resources = data;
          },
          function (reason) {
            $scope.message = reason.message;
            $scope.messageClass = 'alert-danger';
          }
        );
      }

      loadResources();

      /**
       * Add a group
       * @param groupId
       *   The id of the group to add.
       */
      scope.addGroup = function addGroup(groupId) {
        if (!groupId || groupId === '') {
          return;
        }

        for (var i = 0; i < scope.api.configuration.groups.length; i++) {
          if (scope.api.configuration.groups[i].id === groupId) {
            return;
          }
        }
        scope.api.configuration.groups.push({
          "id": groupId,
          "resources": []
        });
      };

      /**
       * Remove a group
       * @param groupId
       *   The id of the group to remove.
       */
      scope.removeGroup = function removeGroup(groupId) {
        for (var i = 0; i < scope.api.configuration.groups.length; i++) {
          if (scope.api.configuration.groups[i].id === groupId) {
            scope.api.configuration.groups.splice(i, 1);
          }
        }
      };

      /**
       * Add a resource to a group.
       *
       * @param groupId
       *   Id of the group to add a resource to.
       * @param resource
       *   The resource to add.
       */
      scope.addResourceToGroup = function addResourceToGroup(groupId, resource) {
        for (var i = 0; i < scope.api.configuration.groups.length; i++) {
          if (scope.api.configuration.groups[i].id === groupId) {
            var resources = scope.api.configuration.groups[i].resources;

            var alreadyAdded = false;

            for (var j = 0; j < resources.length; j++) {
              if (resources[j].mail === resource.mail) {
                alreadyAdded = true;
              }
            }

            if (!alreadyAdded) {
              resources.push(resource);
            }

            break;
          }
        }
      };

      /**
       * Remove resource from a group.
       *
       * @param groupId
       *   Id of the group to add a resource to.
       * @param resource
       *   The resource to add.
       */
      scope.removeResourceFromGroup = function removeResourceFromGroup(groupId, resource) {
        for (var i = 0; i < scope.api.configuration.groups.length; i++) {
          if (scope.api.configuration.groups[i].id === groupId) {
            var resources = scope.api.configuration.groups[i].resources;

            for (var j = 0; j < resources.length; j++) {
              if (resources[j].mail === resource.mail) {
                resources.splice(j, 1);
                return;
              }
            }
          }
        }
      };

      /**
       * Is the group selected.
       *
       * @param groupId
       * @param resource
       * @returns {boolean}
       */
      scope.resourceSelected = function resourceSelected(groupId, resource) {
        for (var i = 0; i < scope.api.configuration.groups.length; i++) {
          if (scope.api.configuration.groups[i].id === groupId) {
            var resources = scope.api.configuration.groups[i].resources;

            for (var j = 0; j < resources.length; j++) {
              if (resources[j].mail === resource.mail) {
                return true;
              }
            }
          }
        }
        return false;
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
        if (scope.api.name === '') {
          scope.message = 'Set a name.';
          scope.messageClass = 'alert-danger';
          return;
        }

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

