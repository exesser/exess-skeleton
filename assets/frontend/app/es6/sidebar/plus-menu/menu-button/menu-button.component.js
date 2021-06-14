 'use strict';

/**
 * This component represents a button inside the plus-menu.
 *
 * For example:
 *
 *  <button button="button"></button>
 */
angular.module('digitalWorkplaceApp')
  .component('menuButton', {
    replace: true,
    templateUrl: 'es6/sidebar/plus-menu/menu-button/menu-button.component.html',
    bindings: {
      button: '<'
    },
    controllerAs: 'menuButtonController'
  });
