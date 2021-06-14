 'use strict';

/**
 * This component represents a link inside the plus-menu.
 *
 * A link has a action (base on this is navigating to a different page or open a model),
 * a clickable(true|false - active|inactive), a label and an icon.
 *
 * For example:
 *
 *  <menu-link
 *    action-id="navigate_to_create_lead_guidance"
 *    clickable="true"
 *    label="business"
 *    icon="icon-werkbakken">
 *  </menu-link>
 */
angular.module('digitalWorkplaceApp')
  .component('menuLink', {
    replace: true,
    templateUrl: 'es6/sidebar/plus-menu/menu-link/menu-link.component.html',
    bindings: {
      action: '<',
      clickable: '<',
      label: '@',
      icon: '@'
    },
    controllerAs: 'menuLinkController',
    controller: function (actionDatasource) {
      const menuLinkController = this;

      menuLinkController.actionClicked = function() {
        if (menuLinkController.clickable) {
          actionDatasource.performAndHandle(menuLinkController.action);
        }
      };
    }
  });
