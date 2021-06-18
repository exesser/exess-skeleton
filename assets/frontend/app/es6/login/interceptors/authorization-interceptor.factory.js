'use strict';

/**
 * @ngdoc service
 * @name digitalWorkplaceApp.authorizationInterceptor
 * @description Redirects the user to the loginPage when 403 or 401 is received.
 * Factory in the digitalWorkplaceApp is a $http.interceptor.
 */
angular.module('digitalWorkplaceApp')
  .factory('authorizationInterceptor', function ($injector, $q, tokenFactory) {

    /*
      A Note about injecting the $state via the $injector manually.

      Unfortunately $state also requires $http somewhere in its
      dependencies. This causes a circular dependency:

      Circular dependency found: $http <- $templateFactory <- $view <- $state <- authorizationInterceptor <- $http <- loginFactory

      See:

      http://stackoverflow.com/questions/20230691/injecting-state-ui-router-into-http-interceptor-causes-circular-dependency
    */
    return { request, responseError };

    function request(request) {
      if (tokenFactory.getToken() !== null) {
        request.headers['Authorization'] = 'Bearer ' + tokenFactory.getToken();
      }

      return request;
    }

    /**
    * Whenever a HTTP request fails with a 401 (UNAUTHORIZED)
    * send the user back to the login page and reset the current user.
    * @param {Object} rejection The rejection object explaining what went wrong.
    */
    function responseError(rejection) {
      if (rejection.status === 401) {
        tokenFactory.removeToken();

        if (_.has(rejection.data, 'command')) {
          // Inject 'commandHandler' manually to prevent the circular dependency bug.
          $injector.get('commandHandler').handle(rejection.data.command);
          return $q.reject(rejection);
        }

        // Inject '$state' manually to prevent the circular dependency bug.
        $injector.get('$state').go('login');
      }

      return $q.reject(rejection);
    }
  });
