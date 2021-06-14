'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp:dashboardItem component
 * @description
 * # dashboardItem
 *
 * Creates a dashboard item which has multiple button were
 * each button has a label to put below the button and
 * an amount to put into the button.
 *
 * Example usage:
 *
 * <dashboard-button-group-item
 *   buttons='[
 *     {
 *       "label": "Elec fix up",
 *       "amount": "3",
 *       "action": {
 *         "id": "42",
 *         "recordId": "1337",
 *         "recordType": "elec"
 *       }
 *     },
 *     {
 *       "label": "Gas fix up",
 *       "amount": "3",
 *       "action": {
 *         "id": "43",
 *         "recordId": "1338",
 *         "recordType": "gas"
 *       }
 *     },
 *     {
 *       "label": "Gas tk up",
 *       "amount": "3"
 *       "action": {
 *         "id": "44",
 *         "recordId": "1339",
 *         "recordType": "tkup"
 *       }
 *     }
 *   ]'>
 * </dashboard-button-group-item>
 *
 * Component of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .component('dashboardButtonGroupItem', {
    templateUrl: 'es6/dashboard/items/dashboard-button-group-item/dashboard-button-group-item.component.html',
    bindings: {
      buttons: '< '
    },
    controllerAs: 'dashboardButtonGroupItemController',
    controller: function(actionDatasource) {
      const dashboardButtonGroupItemController = this;

      dashboardButtonGroupItemController.buttons = angular.fromJson(dashboardButtonGroupItemController.buttonsJson);

      dashboardButtonGroupItemController.buttonClicked = function(button) {
        actionDatasource.performAndHandle(button.action);
      };
    }
  });
