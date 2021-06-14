'use strict';

// Routes for the login functionality.
angular.module('digitalWorkplaceApp')
  .config(function ($stateProvider) {
    $stateProvider.state('login', {
      parent: 'base',
      url: '/',
      views: {
        'modal@': {
          template: '<login></login>'
        }
      }
    });
  }).run(function ($rootScope, currentUserFactory, loginFactory, $state) {
  /*
   If the user is logged in and tries to navigate to the login page, navigate him away
   to the home page.

   Redirect user to login page if he tries to go to any URL when he is not logged in.

   Prevent access to pages the current users role cannot see, if the user is not authorized
   to see a page route redirect him to the home page.
   */
  $rootScope.$on('$stateChangeStart', function (event, toState, toParams) { //eslint-disable-line angular/on-watch
    var isLoggedIn = currentUserFactory.isLoggedIn();

    if (isLoggedIn && toState.name === 'login') {
      event.preventDefault();
      $state.transitionTo('dashboard', { mainMenuKey: 'start', dashboardId: 'home' });
    } else if (!isLoggedIn && toState.name !== 'login') {
      event.preventDefault();

      // After login navigate the user back to the state he tried to access.
      loginFactory.afterLoginState = { name: toState.name, params: toParams };

      $state.transitionTo('login');
    }
  });
});
