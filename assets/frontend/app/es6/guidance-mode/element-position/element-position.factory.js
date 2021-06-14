'use strict';

(function (jquery) {
  /**
   * @ngdoc service
   * @name digitalWorkplaceApp.elementPosition
   * @description
   *
   * The elementPosition can tell you information about the position
   * of an element on the screen.
   *
   * Factory in the digitalWorkplaceApp.
   */
  angular.module('digitalWorkplaceApp')
    .factory('elementPosition', function($window) {

      return { isAboveFold };

      /**
       * Given a angular element returns whether or not the
       * the element is above the fold.
       *
       * +--------------------+ < Top of page
       * |                    |
       * |   Above the fold   |
       * |                    |
       * +--------------------+ < Fold
       * |                    |
       * |   Below the fold   |
       * |                    |
       * +--------------------+ < Bottom of page
       *
       * @param  {[type]}  element [description]
       * @return {Boolean}         [description]
       */
      function isAboveFold(element) {
        const heightOfScreen = jquery($window).height();
        const positionOfElement = jquery(element).offset().top;

        return positionOfElement <= (heightOfScreen / 2);
      }
    });
})(window.$); //eslint-disable-line angular/window-service
