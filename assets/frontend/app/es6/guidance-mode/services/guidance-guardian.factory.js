"use strict";

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.flow:guidanceGuardian
 * @description
 * # guidanceGuardian
 *
 * The guidanceGuardian keeps track whether or not a guidance
 * is currently in progress. A guidance is in progress when
 * the user has typed in information in a form.
 *
 * When the guidanceGuardian is on guard it won't allow the user
 * to navigate to another 'state' via the ui.router.
 *
 * The guidanceGuardian can also be told to step aside and let
 * 'state' changes happen.
 *
 * Factory of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .factory('guidanceGuardian', function() {

    // The guidances the guidanceGuardian is currently guarding.
    let guidances = [];

    return { startGuard, endGuard, isGuarding, resetGuardian };

    /**
     * Start guarding a guidance, by its guidanceFormObserver.
     *
     * @param  {Object} guidance The guidanceFormObserver to guard.
     */
    function startGuard(guidance) {
      if (_.includes(guidances, guidance) === false) {
        guidances.push(guidance);
      }
    }

    /**
     * Ends the guard on a guidance, by its guidanceFormObserver.
     *
     * @param  {[type]} guidance The guidanceControllr to stop guarding.
     */
    function endGuard(guidance) {
      guidances = _.without(guidances, guidance);
    }

    /**
     * Stops guarding all guidances that it is currently guarding.
     */
    function resetGuardian() {
      guidances = [];
    }

    /**
     * Whether or not the guardian is currently guarding one or more
     * guidances.
     *
     * @return {Boolean} Whether or not the guardian is currently on duty.
     */
    function isGuarding() {
      return guidances.length > 0;
    }
  }).run(function($rootScope, guidanceGuardian, $translate, $window, actionDatasource) {
    $rootScope.$on('$stateChangeStart', function(event) { //eslint-disable-line angular/on-watch
      // If the guardian is active.
      if (guidanceGuardian.isGuarding()) {
        const leaveMessage = $translate.instant('NAVIGATE_AWAY_WARNING_DATA_LOSS');

        // Warn the user that he is navigating away.
        if ($window.confirm(leaveMessage)) {
          /*
            If the user really wants to navigate away we must clear
            the guidanceGuardian. Otherwise the things it 'guards'
            will be kept in memory.
          */
          guidanceGuardian.resetGuardian();

          // Delete also the guidance recovery data for user, we have a special action for that.
          actionDatasource.performAndHandle({id: 'remove_recovery_guidance_data'});
        } else {
          // Prevent the navigation when the user prefers to stay.
          event.preventDefault();
        }
      }
    });
  });
