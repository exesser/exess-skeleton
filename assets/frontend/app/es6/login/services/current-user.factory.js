'use strict';

/**
 * @ngdoc service
 * @name digitalWorkplaceApp.currentUserFactory
 * @description Keeps track of the current user.
 * Factory in the digitalWorkplaceApp.
 */
angular.module('digitalWorkplaceApp')
  .factory('currentUserFactory', function () {
    var currentUser = null;
    var displayLogin = true;

    return {
      getUser,
      setUser,
      isLoggedIn,
      getDisplayLogin,
      setDisplayLogin
    };

    /**
     * Returns the current user.
     * @return {[object]} The current user
     */
    function getUser() {
      return currentUser;
    }

    /**
     * Sets the current user.
     * @param {[type]} u The user you want to set the current user to.
     */
    function setUser(u) {
      currentUser = u;
    }

    /**
     * Returns whether or not the user is logged in.
     * @return {Boolean} Whether or not the user is logged in.
     */
    function isLoggedIn() {
      return currentUser !== null;
    }

    /**
     * Sets if the login form should be shown
     * @param {Boolean} flag
     */
    function setDisplayLogin(flag) {
        displayLogin = flag;
    }

    /**
     * Returns whether or not to show the login form for the user.
     * @return {Boolean} Whether or not to show the loginform for the user.
     */
    function getDisplayLogin() {
      return displayLogin;
    }
  });
