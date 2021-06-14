'use strict';

/**
 * @ngdoc service
 * @name digitalWorkplaceApp.currentUserFactory
 * @description Keeps track of the current user.
 * Factory in the digitalWorkplaceApp.
 */
angular.module('digitalWorkplaceApp')
  .factory('tokenFactory', function ($window) {
    const LOGIN_TOKEN = 'login-token';

    return {
      getToken,
      setToken,
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
     * Sets the current user's login token.
     * @param {[type]} t The token that needs to be saved.
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
  });
