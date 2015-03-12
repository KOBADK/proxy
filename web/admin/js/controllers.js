/**
 * @file
 * The applications controllers.
 */

/**
 * Main application controller.
 */
angular.module('KobaAdminApp').controller('MainController', [
  function() {
    'use strict';

  }
]);

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
        template: "admin/views/keyAdd.html",
        scope: scope
      });
    };
  }
]);

/**
 * Login page.
 */
angular.module('KobaAdminApp').controller('LoginController', ['$scope', '$http', '$window', '$location',
  function($scope, $http, $window, $location) {
    'use strict';

    $scope.login = function login() {
      $location.path("/apikeys");
    };
  }
]);

/**
 * Logout page.
 */
angular.module('KobaAdminApp').controller('LogoutController', ['$scope', '$window',
  function($scope, $window) {
    'use strict';

    // Remove the token from login.
    delete $window.sessionStorage.token;
  }
]);


/**
 * Navigation helpers.
 */
angular.module('KobaAdminApp').controller('NavigationController', ['$scope', '$location',
  function($scope, $location) {
    'use strict';

    $scope.isActive = function (viewLocation) {
      return viewLocation === $location.path();
    };
  }
]);


