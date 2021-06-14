'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp:dashboardCenteredItem component
 * @description
 * # dashboardCenteredItem
 *
 * Creates a dashboard item with a centered icon, a text and
 * a bold text.
 *
 * Example usage:
 *
 * <dashboard-centered-item
 *   icon='icon-bedrijf'
 *   line='Birthday on'
 *   bold-line='Tomorrow'
 *   action='{ "id": "42", "recordId": "1337", "recordType": "elec"}'>
 * </dashboard-centered-item>
 *
 * Component of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .component('dashboardCenteredItem', {
    templateUrl: 'es6/dashboard/items/dashboard-centered-item/dashboard-centered-item.component.html',
    bindings: {
      icon: '@',
      line: '@',
      boldLine: '@',
      action: "<"
    },
    controllerAs: 'dashboardCenteredItemController',
    controller: function(actionDatasource) {
      const dashboardCenteredItemController = this;

      dashboardCenteredItemController.doAction = function() {
        actionDatasource.performAndHandle(dashboardCenteredItemController.action);
      };
    }
  });
