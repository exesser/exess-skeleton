'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.factory: navigateAwayWarning
 * @description
 *
 * This factory is responsible for showing a 'Are you sure you want
 * to navigate away' warning.
 */
angular.module('digitalWorkplaceApp')
  .factory('navigateAwayWarning', function($window, $translate) {

    return { enable, disable };

    /**
     * After 'enable' is called whenever the user navigates away
     * he is shown a navigate away warning, asking if the user is
     * sure that he wants to navigate away.
     */
    function enable() {
      $window.onbeforeunload = (event) => {
        // If we haven't been passed the event get the window.event
        event = event || $window.event;

        // Depending on the browser this message is shown.
        const message = $translate.instant('NAVIGATE_AWAY_WARNING');

        // For IE6-8 and Firefox prior to version 4
        event.returnValue = message;

        // For Chrome, Safari, IE8+ and Opera 12+
        return message;
      };
    }

    /**
     * After 'disable' is called the user is no longer shown a navigate
     * away warning.
     */
    function disable() {
      $window.onbeforeunload = null;
    }
  });
