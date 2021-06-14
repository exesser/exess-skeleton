'use strict';

(function (jquery) {
  /**
   * @ngdoc service
   * @name digitalWorkplaceApp.factory:slideAnimation
   * @description
   *
   * The slide animation knows how to transition to a detail page
   * from a main page, and vice versa.
   *
   * The slide animation itself is currently disabled, but we still
   * need to set CSS classes on the body the digital workplace to
   * work.
   *
   * By adding the 'focus-is-open' CSS class on the body the div.focus-mode
   * will be rendered in front of the main content.
   *
   * Factory in the digitalWorkplaceApp.
   */
  angular.module('digitalWorkplaceApp')
    .factory('slideAnimation', function () {
      return {
        close,
        open
      };

      /**
       * Moves the detail page in from the left.
       */
      function open() {
        if (isBodyOpen() === false) {
          jquery('body').addClass('focus-is-open');
        }
      }

      /**
       * Moves the main page in from the right.
       */
      function close() {
        if (isBodyOpen()) {
          jquery('body').removeClass('focus-is-open');
        }
      }

      function isBodyOpen() {
        return jquery('body').hasClass('focus-is-open');
      }
    });
})(window.$); //eslint-disable-line angular/window-service
