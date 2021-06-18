'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp:login component
 * @description
 * # login
 *
 * The login component is responsible for login the user in.
 *
 * Example usage:
 *
 * <login></login>
 *
 * Component of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .component('login', {
    templateUrl: 'es6/login/login.component.html',
    controllerAs: 'loginController',
    controller: function (loginFactory, commandHandler, $state, STANDARD_USER, userDatasource,
                          currentUserFactory, tokenFactory, $translate, LANGUAGE, $analytics, googleTagManager, $timeout) {
      const loginController = this;

      // Bindings for username and password.
      loginController.username = STANDARD_USER.username;
      loginController.password = STANDARD_USER.password;

      loginController.loginFailed = false;
      loginController.failedMessage = 'LOGIN.ERROR';
      loginController.showLoginForm = false;

      loginController.$onInit = function () {

        /*
          Try to log the user in automatically by calling postAuthenticate() manually;
          When the token is not set this will fail and result in a 401 error,
          if it succeeds the user is logged in automatically.
        */
        postAuthenticate().catch(function () {
            loginController.showLoginForm = currentUserFactory.getDisplayLogin();
        });
      };

      /**
       * Login calls the loginFactory's login function with the entered username and password.
       * If it is successful, the postAuthenticate function is called.
       * If it is not successful, the 'loginFailed' property is set to true so the user sees an error.
       */
      loginController.login = function () {
        loginController.loginFailed = false;

        loginFactory.login(loginController.username, loginController.password).then(function (response) {
          const token = _.get(response, 'data.data.token', null);
          if (token) {
            tokenFactory.setToken(token);
          }
          postAuthenticate();
        }).catch(function (responseData) {
          loginController.loginFailed = true;
          loginController.failedMessage = _.get(responseData, 'data.message', 'LOGIN.ERROR');
        });
      };

      /**
       * Retrieves the currentUser from the back-end and stores it into the currentUserFactory.
       * @return {Promise} The promise that lets you know when the postAuthenticate is done.
       */
      function postAuthenticate() {
        return userDatasource.current(loginFactory.afterLoginState).then(function (user) {
          currentUserFactory.setUser({ username: user.username });
          setPreferredLanguage(user);
          setAnalyticsData(user);

          $timeout(function () {
            if (_.has(user, 'command')) {
              commandHandler.handle(user.command);
            }
          }, 500);

          $state.go(loginFactory.afterLoginState.name, loginFactory.afterLoginState.params);
        });
      }

      /**
       * Sets the preferred language for the user that authenticated
       *
       * @param {Object} user
       */
      function setPreferredLanguage(user) {
        const preferredLanguage =
          _.isEmpty(user.preferredLanguage) ? LANGUAGE.ENGLISH_BELGIUM : user.preferredLanguage;

        if ($translate.use() !== preferredLanguage && _.includes(LANGUAGE, preferredLanguage)) {
          $translate.use(preferredLanguage);
        }
      }

      /**
       * Sets data that need to be send to google tag manager
       *
       * @param {Object} user
       */
      function setAnalyticsData(user) {
        $analytics.setUsername(user.username);
      }
    }
  });
