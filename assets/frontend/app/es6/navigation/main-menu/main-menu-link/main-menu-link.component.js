'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp:mainMenuLink component
 * @description
 * # mainMenuLink
 *
 * This Component represents a link inside the main menu on the
 * left side of the screen.
 *
 * For example:
 *
 *  <main-menu-link
 *    link-to="sales"
 *    params='{"dashboardId": "accounts", "mainMenuKey": "sales-marketing"}'
 *    name="Sales & Marketing"
 *    icon="icon-werkbakken"
 *    active="false">
 *  </main-menu-link>
 */
angular.module('digitalWorkplaceApp')
  .component('mainMenuLink', {
    templateUrl: 'es6/navigation/main-menu/main-menu-link/main-menu-link.component.html',
    bindings: {
      linkTo: '@',
      params: '<',
      name: '@',
      icon: '@',
      active: '<'
    },
    controllerAs: 'mainMenuLinkController',
    controller: function controller($state, $window) {
      const mainMenuLinkController = this;

      mainMenuLinkController.linkClicked = function (linkTo, newWindow) {
          if (newWindow) {
              $window.open($state.href(mainMenuLinkController.linkTo, mainMenuLinkController.params), '_blank');
              return;
          }
          $state.go(linkTo, mainMenuLinkController.params);
      };
    }
  });
