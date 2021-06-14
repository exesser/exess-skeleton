'use strict';

(function (jquery) {
  /**
   * @ngdoc service
   * @name digitalWorkplaceApp.factory:sidebarAnimation
   * @description
   *
   * The sidebar animation opens and closes the sidebar via an animation.
   *
   * Factory in the digitalWorkplaceApp.
   */
  angular.module('digitalWorkplaceApp')
    .factory('sidebarAnimation', function ($timeout) {
      const sidebarAnimationTime = 500; // As defined in the CSS.

      return {
        toggle,
        close,
        open
      };

      /**
       * Opens the menu when it is closed, closes the menu when
       * it is currently opened. Both are done via an animation.
       *
       * @return {Promise} A promise for when the animation is completed.
       */
      function toggle() {
        if (isSideBarOpen()) {
          return close();
        } else {
          return open();
        }
      }

      /**
       * Opens the menu bar via an animation.
       *
       * Note that the completion promise ignores the animations for the
       * individual links inside the menu.
       *
       * @return {Promise} A promise for when the animation is completed.
       */
      function open() {
        if (isSideBarOpen() === false) {
          jquery('body').addClass('sidebar-is-open');
          jquery('.sidebar').addClass('is-open');

          jquery('.sidebar ul a').each(function (i, element) {
            jquery(element).addClass('is-visible');
          });

          return $timeout(_.noop, sidebarAnimationTime);
        }

        return $timeout(_.noop, 1);
      }

      /**
       * Closes the menu bar via an animation.
       *
       * @return {Promise} A promise for when the animation is completed.
       */
      function close() {
        if (isSideBarOpen()) {
          jquery('body').removeClass('sidebar-is-open');
          jquery('.sidebar').removeClass('is-open');
          jquery('.sidebar ul a').removeClass('is-visible');
          return $timeout(_.noop, sidebarAnimationTime);
        }

        return $timeout(_.noop, 1);
      }

      function isSideBarOpen() {
        return jquery('.sidebar').hasClass('is-open');
      }
    });
})(window.$); //eslint-disable-line angular/window-service
