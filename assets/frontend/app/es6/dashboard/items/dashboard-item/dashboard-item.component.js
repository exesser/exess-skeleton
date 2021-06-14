'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp:dashboardItem component
 * @description
 * # dashboardItem
 *
 * Creates a dashboard item with a label an amount and a number of
 * textual lines that need to be displayed in the bottom. This item
 * can optionally be shown in bold, and can display a checkmark icon
 * optionally.
 *
 * Example usage:
 *
 * <dashboard-item
 *   label="Open opportunities"
 *   amount="3"
 *   lines='["1 opportunity is due today", "2 consumptions received"]'
 *   has-warning="false"
 *   show-checkmark="false"
 *   action='{"id": "42", "recordId": "1337", "recordType": "elec"}'>
 * </dashboard-item>
 *
 * Component of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .component('dashboardItem', {
    templateUrl: 'es6/dashboard/items/dashboard-item/dashboard-item.component.html',
    bindings: {
      label: '@',
      amount: '@',
      lines: '<',
      hasWarning: '<',
      showCheckmark: '<',
      action: "<"
    },
    controllerAs: 'dashboardItemController',
    controller: function(actionDatasource) {
      const dashboardItemController = this;

      dashboardItemController.doAction = function() {
        actionDatasource.performAndHandle(dashboardItemController.action);
      };
    }
  });
