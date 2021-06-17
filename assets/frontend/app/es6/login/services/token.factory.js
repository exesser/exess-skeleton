'use strict';

/**
 * @ngdoc service
 * @name digitalWorkplaceApp.tokenFactory
 * @description Keeps track of the current user.
 * Factory in the digitalWorkplaceApp.
 */
angular.module('digitalWorkplaceApp')
  .factory('tokenFactory', function ($window) {
    const LOGIN_TOKEN = 'login-token';

    return {
      getToken,
      hasToken,
      setToken,
      getUsername,
      removeToken
    };

    /**
     * Returns the current user's login token.
     * @return {[object]} The current user
     */
     function getToken() {
      return $window.localStorage.getItem(LOGIN_TOKEN);
    }

    /**
     * @return {boolean}
     */
     function hasToken() {
      return getToken() !== null;
    }

    /**
     * @return {string}
     */
     function getUsername() {
      return getDataFromToken().userId;
    }

    /**
     * Sets the current user's login token.
     * @param {string} token The token that needs to be saved.
     */
    function setToken(token) {
      $window.localStorage.setItem(LOGIN_TOKEN, token);
    }

    /**
     * Remove token.
     */
    function removeToken() {
      $window.localStorage.removeItem(LOGIN_TOKEN);
    }

    function getDataFromToken() {
      let data = {};
      const token = getToken();
      if (typeof token !== 'undefined') {
        data = JSON.parse(urlBase64Decode(token.split('.')[1]));
      }

      return data;
    }

    function urlBase64Decode(str) {
      var output = str.replace('-', '+').replace('_', '/');
      switch (output.length % 4) {
        case 0:
          break;
        case 2:
          output += '==';
          break;
        case 3:
          output += '=';
          break;
        default:
          throw 'Illegal base64url string!';
      }

      return window.atob(output);
    }
  });
