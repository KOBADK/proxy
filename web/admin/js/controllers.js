/**
 * @file
 * The applications controllers.
 */

/**
 * Main application controller.
 */
app.controller('MainController', ['$scope', '$route', '$routeParams', '$location',
  function($scope, $route, $routeParams, $location) {
    'use strict';

  }
]);

/**
 * Login page.
 */
app.controller('LoginController', ['$scope', '$http', '$window', '$location',
  function($scope, $http, $window, $location) {
    'use strict';

    $scope.login = function login() {
      $http.post('/login', $scope.user)
        .success(function (data, status, headers, config) {
          // Store token in session.
          $window.sessionStorage.token = data.token;

          $location.path('users');
        })
        .error(function (data, status, headers, config) {
          // Erase the token if the user fails to log in
          delete $window.sessionStorage.token;

          // Handle login errors here
          $scope.message = 'Error: Invalid user or password';
        }
      );
    };
  }
]);

/**
 * Logout page.
 */
app.controller('LogoutController', ['$scope', '$window',
  function($scope, $window) {
    'use strict';

    // Remove the token from login.
    delete $window.sessionStorage.token;
  }
]);


/**
 * Navigation helpers.
 */
app.controller('NavigationController', ['$scope', '$location',
  function($scope, $location) {
    'use strict';

    $scope.isActive = function (viewLocation) {
      return viewLocation === $location.path();
    };
  }
]);

/**
 * Users page.
 */
app.controller('UsersController', ['$scope', '$window', '$location', 'ngOverlay', 'dataService',
  function($scope, $window, $location, ngOverlay, dataService) {
    'use strict';

    // Check that the user is logged in.
    if (!$window.sessionStorage.token) {
      $location.path('');
    }

    /**
     * Load Users.
     */
    function loadUsers() {
      // Get user/api key information form the backend.
      dataService.fetch('get', '/api/users').then(
        function (data) {
          $scope.users = data;
        },
        function (reason) {
          $scope.message = reason.message;
          $scope.messageClass = 'alert-danger';
        }
      );
    }

    /**
     * Load Roles.
     */
    function loadRoles() {
      // Get user/api key information form the backend.
      dataService.fetch('get', '/api/roles').then(
        function (data) {
          $scope.roles = data;
        },
        function (reason) {
          $scope.message = reason.message;
          $scope.messageClass = 'alert-danger';
        }
      );
    }

    /**
     * Edit User callback.
     */
    $scope.edit = function edit(id) {
      dataService.fetch('get', '/api/users/' + id).then(
        function (data) {
          var scope = $scope.$new(true);
          scope.roles = $scope.roles;

          // Set User information.
          scope.user = data;

          // Get user roles.
          dataService.fetch('get', '/api/users/' + scope.user.id + '/roles').then(
            function (data) {
              scope.user.roles = data;

              /**
               * Save User callback.
               */
              scope.save = function save() {
                dataService.send('put', '/api/users/' + scope.user.id + '/status', { "status": scope.user.status }).then(
                  function (data) {
                    $scope.message = data;
                    $scope.messageClass = 'alert-success';

                    // Reload API key list.
                    loadUsers();

                    // Close overlay.
                    overlay.close();
                  },
                  function (reason) {
                    $scope.message = reason.message;
                    $scope.messageClass = 'alert-danger';
                    console.log(reason);
                  }
                );
              };

              // helper method to get selected fruits
              scope.selectedRoles = function selectedRoles() {
                return filterFilter(scope.fruits, { selected: true });
              };

              // watch fruits for changes
              $scope.$watch('fruits|filter:{selected:true}', function (nv) {
                $scope.selection = nv.map(function (fruit) {
                  return fruit.name;
                });
              }, true);

              // Open the overlay.
              var overlay = ngOverlay.open({
                template: 'views/userEdit.html',
                scope: scope
              });
            },
            function (reason) {
              $scope.message = reason.message;
              $scope.messageClass = 'alert-danger';
            }
          );
        },
        function (reason) {
          $scope.message = reason.message;
          $scope.messageClass = 'alert-danger';
        }
      );
    };

    // Get the controller up and running.
    loadUsers();
    loadRoles();
  }
]);


/**
 * Roles page.
 */
app.controller('RolesController', ['$scope', '$window', '$location', 'ngOverlay', 'dataService',
  function($scope, $window, $location, ngOverlay, dataService) {
    'use strict';

    // Check that the user is logged in.
    if (!$window.sessionStorage.token) {
      $location.path('');
    }

    /**
     * Load Roles.
     */
    function loadRoles() {
      // Get user/api key information form the backend.
      dataService.fetch('get', '/api/roles').then(
        function (data) {
          $scope.roles = data;
        },
        function (reason) {
          $scope.message = reason.message;
          $scope.messageClass = 'alert-danger';
        }
      );
    }

    /**
     * Edit Role callback.
     */
    $scope.edit = function edit(id) {
      dataService.fetch('get', '/api/roles/' + id).then(
        function (data) {
          var scope = $scope.$new(true);

          // Set User information.
          scope.role = data;

          /**
           * Save role callback.
           */
          scope.save = function save() {
            dataService.send('put', '/api/roles/' + scope.role.id, scope.role).then(
              function (data) {
                $scope.message = data;
                $scope.messageClass = 'alert-success';

                // Reload API keys.
                loadRoles();

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
            template: 'views/roleEdit.html',
            scope: scope
          });
        },
        function (reason) {
          $scope.message = reason.message;
          $scope.messageClass = 'alert-danger';
        }
      );
    };

    // Get the controller up and running.
    loadRoles();
  }
]);
