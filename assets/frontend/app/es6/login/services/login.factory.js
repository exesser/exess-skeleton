'use strict';

/**
 * @ngdoc service
 * @name digitalWorkplaceApp.login
 * @description
 * # Login factory handles logging the user in and out.
 * Factory in the digitalWorkplaceApp.
 */
angular.module('digitalWorkplaceApp')
  .factory('loginFactory', function ($http, API_PATH, LOG_HEADERS_KEYS) {
    // Which state the user should goto after the login.
    const defaultAfterLoginState = { name: 'dashboard', params: { mainMenuKey: 'start', dashboardId: 'home' } };

    return {
      login,
      logout,
      afterLoginState: defaultAfterLoginState
    };

    function login(username, password) {
      var req = {
        method: 'POST',
        url: API_PATH + 'login',
        headers: {
          'Content-Type': 'application/json;charset=utf-8'
        },
        data: `{ "username": "${username}", "password": "${password}" }`
      };
      req.headers[LOG_HEADERS_KEYS.DESCRIPTION] = 'login';

      return $http(req);
    }

    function logout() {
      const headers = {};
      headers[LOG_HEADERS_KEYS.DESCRIPTION] = 'logout';

      return $http.get(API_PATH + 'logout', {}, {headers}).then(function(response) {
          return response;
      });
    }
  });
