'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.factory: previousState
 * @description
 *
 * Keeps track of the previous state that ui-router had. This information
 * is then used to navigate back to the previous state.
 *
 * What the previous state is is determined by what the current state is.
 *
 * | current state  | behavior                                       |
 * =================|=============================================== |
 * | dashboard      | Go back to the previous state.                 |
 * | focus-mode     | Go back to the dashboard                       |
 * | guidance-mode  | Go back to the first not guidance mode         |
 * | other state    | Go back to the previous state                  |
 *
 * We store the 'previous states' in a stack.
 *
 * Whenever the state goes to a dashboard the stack is cleared, and the
 * previous state is then put on the stack. The reason for this is that
 * the dashboard doesn't have a back-arrow button, so the 'chain' breaks
 * on a dashboard.
 *
 * Note that we only store the states for 'dashboard', 'guidance-mode' and 'focus-mode'
 * pages all other pages are ignored.
 */
angular.module('digitalWorkplaceApp')
  .factory('previousState', function ($state, flashMessageContainer) {
    // Start the state at a dashboard called 'home'.
    const baseState = { name: 'dashboard', params: { mainMenuKey: 'start', dashboardId: 'home' } };

    // Stores the previous states in an array.
    let stateStore = [baseState];

    // The current active ui-router state's name;
    let currentState = baseState.name;

    return { navigateTo, registerStateChange };

    /**
     * Navigates the ui-router to the previous state.
     */
    function navigateTo() {

      // Every time we navigate to previews state we have to clear the flash messages
      flashMessageContainer.clearMessages();

      switch (currentState) {
        case 'focus-mode':
          navigateFromFocusMode();
          break;
        case 'guidance-mode':
          navigateFromGuidanceMode();
          break;
        default:
          navigateToClosest();
          break;
      }
    }

    /**
     * Handle a state change by registering the previous state to the store,
     * and storing the current state.
     *
     * @param {Object{name: String, params: Object}} previousState The previous state.
     * @param {String} currentState The current ui-router state
     */
    function registerStateChange(previousState, current) {
      currentState = current;

      switch (previousState.name) {
        case 'dashboard':
          stateStore = [previousState];
          break;
        case 'focus-mode':
        case 'guidance-mode':
          stateStore.push(previousState);
          break;
      }
    }

    /* ===== Helper functions ===== */

    // From a focus-mode we always go to the closest dashboard.
    function navigateFromFocusMode() {
      // First state is always a dashboard.
      const dashboardState = stateStore.shift();

      // Reset the store to the first dashboard state.
      stateStore = [dashboardState];

      // Go to the dashboard
      $state.go(dashboardState.name, dashboardState.params);
    }

    // From a guidance-mode we go to the closest non guidance-mode.
    function navigateFromGuidanceMode() {
      stateStore = _(stateStore)
                   .dropRightWhile({ name: 'guidance-mode' })
                   .value();
      navigateToClosest();
    }

    // Navigate to the closest state in the stateStore
    function navigateToClosest() {
      // First we only 'peek' what the last state is so we can attempt navigating to it.
      const { name, params } = _.last(stateStore);

      /*
       * Then we attempt to navigate to the previous state.
       * If this is successful, the last state is removed from the stateStore.
       */
      $state.go(name, params).then(function () {
        stateStore.pop();
      });
    }
  }).run(function ($rootScope, previousState) {
    $rootScope.$on('$stateChangeSuccess', function (event, toState, toParams, fromState, fromParams) { //eslint-disable-line angular/on-watch
      previousState.registerStateChange({ name: fromState.name, params: fromParams }, toState.name);
    });
  });
