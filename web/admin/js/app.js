/**
 * @file
 * Defines the Angular JS application the run the administration frontend.
 */

// Define the angular application.
var app = angular.module('KobaAdminApp', [ 'ngRoute', 'ngOverlay', 'appMessage' ]);

/**
 * Add authentication header to all AJAX requests.
 */
angular.module('KobaAdminApp').factory('authInterceptor', ['$rootScope', '$q', '$window', '$location',
  function ($rootScope, $q, $window, $location) {
    'use strict';

    return {
      request: function (config) {
        config.headers = config.headers || {};
        if ($window.sessionStorage.token) {
          config.headers.Authorization = 'Bearer ' + $window.sessionStorage.token;
        }
        return config;
      },
      responseError: function (response) {
        if (response.status === 401) {
          // Handle auth error by redirect to front page.
          $location.path('');
        }
        return response || $q.when(response);
      }
    };
  }
]);

/**
 * Configure routes and add auth interceptor.
 */
angular.module('KobaAdminApp').config(['$routeProvider', '$locationProvider', '$httpProvider',
  function ($routeProvider, $locationProvider, $httpProvider) {
    'use strict';

    $routeProvider
      .when('/', {
        templateUrl: 'admin/views/login.html',
        controller: 'LoginController'
      })
      .when('/apikeys', {
        templateUrl: 'admin/views/apikeys.html',
        controller: 'ApiKeyController'
      })
      .when('/logout', {
        templateUrl: 'admin/views/logout.html',
        controller: 'LogoutController'
      });

    $httpProvider.interceptors.push('authInterceptor');
  }
]);
