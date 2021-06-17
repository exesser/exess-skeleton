'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp:logout component
 * @description
 * # logout
 *
 * The logout component logs the user out and redirects him back to the
 * login state.
 *
 * Example usage:
 *
 * <logout
 *  location="menu"> <!-- Place of usage. It can be menu or sidebar -->
 * </logout>
 *
 * Component of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .component('logout', {
    templateUrl: 'es6/logout/logout.component.html',
    bindings: {
      location: "@"
    },
    controllerAs: 'logoutController',
    controller: function (loginFactory, tokenFactory, $state, commandHandler) {
      const logoutController = this;

      logoutController.isLoggedIn = function() {
        return tokenFactory.hasToken();
      };

      logoutController.logout = function () {
          return loginFactory.logout().then(function (logoutData) {
              tokenFactory.removeToken();

              if (_.has(logoutData, 'command')) {
                  commandHandler.handle(logoutData.command);
              } else {
                  $state.transitionTo('login');
              }
          });
      };
    }
  });
