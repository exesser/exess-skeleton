 'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp:subMenuLink component
 * @description
 * # subMenuLink
 *
 * This Component represents a link inside the sub menu on the top
 * of the screen.
 *
 * For example:
 *
 *  <sub-menu-link
 *    label="Accounts"
 *    link-to="dashboard"
 *    params='{"dashboardId": "accounts", "mainMenuKey": "sales-marketing"}'
 *    active="true">
 *  </sub-menu-link>
 */
angular.module('digitalWorkplaceApp')
  .component('subMenuLink', {
    templateUrl: 'es6/navigation/sub-menu/sub-menu-link/sub-menu-link.component.html',
    bindings: {
      linkTo: '@',
      paramsJson: '@params',
      label: '@',
      activeString: '@active'
    },
    controllerAs: 'submenuLinkController',
    controller: function ($state, $window) {
      const submenuLinkController = this;

      submenuLinkController.active = submenuLinkController.activeString === 'true';

      const params = angular.fromJson(submenuLinkController.paramsJson);
      submenuLinkController.key = params.dashboardId;

      // Make search 'query' empty when the menu is clicked.
      params.query = undefined;

      submenuLinkController.linkClicked = function(linkTo, newWindow) {
        if (newWindow) {
          $window.open($state.href(linkTo, params), '_blank');
          return;
        }
        $state.go(linkTo, params);
      };
    }
  });
