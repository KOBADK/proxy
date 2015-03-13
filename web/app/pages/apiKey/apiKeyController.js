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

          // @TODO: Replace with real locations.
          scope.availableLocations = [
            "location-1",
            "location-2",
            "location-3",
            "location-4",
            "location-5",
            "location-6",
            "location-7",
            "location-8",
            "location-9",
            "location-10",
            "location-11",
            "location-12",
            "location-13",
            "location-14",
            "location-15",
            "location-16",
            "location-17",
            "location-18",
            "location-19",
            "location-20",
            "location-21",
            "location-22",
            "location-23",
            "location-24"
          ];

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
              "locations": []
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
           * Add a location to a group.
           *
           * @param groupId
           *   Id of the group to add a location to.
           * @param location
           *   The location to add.
           */
          scope.addLocationToGroup = function addLocationToGroup(groupId, location) {
            for (var i = 0; i < scope.api.configuration.groups.length; i++) {
              if (scope.api.configuration.groups[i].id === groupId) {
                var locations = scope.api.configuration.groups[i].locations;

                var alreadyAdded = false;

                for (var j = 0; j < locations.length; j++) {
                  if (locations[j] === location) {
                    alreadyAdded = true;
                  }
                }

                if (!alreadyAdded) {
                  locations.push(location);
                }

                break;
              }
            }
          };

          /**
           * Remove location from a group.
           *
           * @param groupId
           *   Id of the group to add a location to.
           * @param location
           *   The location to add.
           */
          scope.removeLocationFromGroup = function removeLocationFromGroup(groupId, location) {
            for (var i = 0; i < scope.api.configuration.groups.length; i++) {
              if (scope.api.configuration.groups[i].id === groupId) {
                var locations = scope.api.configuration.groups[i].locations;

                for (var j = 0; j < locations.length; j++) {
                  if (locations[j] === location) {
                    locations.splice(j, 1);
                    return;
                  }
                }
              }
            }
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
          "groups": []
        }
      };

      // @TODO: Replace with real locations.
      scope.availableLocations = [
        "location-1",
        "location-2",
        "location-3",
        "location-4",
        "location-5",
        "location-6",
        "location-7",
        "location-8",
        "location-9",
        "location-10",
        "location-11",
        "location-12",
        "location-13",
        "location-14",
        "location-15",
        "location-16",
        "location-17",
        "location-18",
        "location-19",
        "location-20",
        "location-21",
        "location-22",
        "location-23",
        "location-24"
      ];

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
          "locations": []
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
       * Add a location to a group.
       *
       * @param groupId
       *   Id of the group to add a location to.
       * @param location
       *   The location to add.
       */
      scope.addLocationToGroup = function addLocationToGroup(groupId, location) {
        for (var i = 0; i < scope.api.configuration.groups.length; i++) {
          if (scope.api.configuration.groups[i].id === groupId) {
            var locations = scope.api.configuration.groups[i].locations;

            var alreadyAdded = false;

            for (var j = 0; j < locations.length; j++) {
              if (locations[j] === location) {
                alreadyAdded = true;
              }
            }

            if (!alreadyAdded) {
              locations.push(location);
            }

            break;
          }
        }
      };

      /**
       * Remove location from a group.
       *
       * @param groupId
       *   Id of the group to add a location to.
       * @param location
       *   The location to add.
       */
      scope.removeLocationFromGroup = function removeLocationFromGroup(groupId, location) {
        for (var i = 0; i < scope.api.configuration.groups.length; i++) {
          if (scope.api.configuration.groups[i].id === groupId) {
            var locations = scope.api.configuration.groups[i].locations;

            for (var j = 0; j < locations.length; j++) {
              if (locations[j] === location) {
                locations.splice(j, 1);
                return;
              }
            }
          }
        }
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

