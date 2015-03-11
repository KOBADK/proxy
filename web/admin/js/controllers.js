/**
 * @file
 * The applications controllers.
 */

/**
 * Main application controller.
 */
app.controller('MainController', [
  function() {
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
        .success(function (data) {
          // Store token in session.
          $window.sessionStorage.token = data.token;

          $location.path('users');
        })
        .error(function () {
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
      dataService.fetch('get', '/admin/users').then(
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
     * Load Groups
     */
    function loadGroups() {
      // Get user/api key information form the backend.
      dataService.fetch('get', '/admin/groups').then(
        function (data) {
          $scope.groups = data;
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
      dataService.fetch('get', '/admin/users/' + id).then(
        function (data) {
          var scope = $scope.$new(true);
          scope.groups = $scope.groups;

          // Set User information.
          scope.user = data;

          // Get user groups.
          dataService.fetch('get', '/admin/users/' + scope.user.id + '/groups').then(
            function (data) {
              scope.user.groups = data;

              /**
               * Save User callback.
               */
              scope.save = function save() {
                dataService.send('put', '/admin/users/' + scope.user.id + '/status', { "status": scope.user.status }).then(
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

              // Open the overlay.
              var overlay = ngOverlay.open({
                template: 'admin/views/userEdit.html',
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
    loadGroups();
  }
]);


/**
 * Groups page.
 */
app.controller('GroupsController', ['$scope', '$window', '$location', 'ngOverlay', 'dataService',
  function($scope, $window, $location, ngOverlay, dataService) {
    'use strict';

    // Check that the user is logged in.
    if (!$window.sessionStorage.token) {
      $location.path('');
    }

    /**
     * Load Groups.
     */
    function loadGroups() {
      // Get user/api key information form the backend.
      dataService.fetch('get', '/admin/groups').then(
        function (data) {
          $scope.groups = data;
        },
        function (reason) {
          $scope.message = reason.message;
          $scope.messageClass = 'alert-danger';
        }
      );
    }

    /**
     * Edit Group callback.
     */
    $scope.edit = function edit(id) {
      dataService.fetch('get', '/admin/groups/' + id).then(
        function (data) {
          var scope = $scope.$new(true);

          // Set Group information.
          scope.group = data;

          /**
           * Save group callback.
           */
          scope.save = function save() {
            dataService.send('put', '/admin/groups/' + scope.group.id, scope.group).then(
              function (data) {
                $scope.message = data;
                $scope.messageClass = 'alert-success';

                // Reload API keys.
                loadGroups();

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
            template: 'admin/views/groupEdit.html',
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
    loadGroups();
  }
]);
