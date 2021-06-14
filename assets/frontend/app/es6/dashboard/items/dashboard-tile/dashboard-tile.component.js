'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp:dashboardTile component
 * @description
 * # dashboardTile
 *
 * Creates a dashboard item with a label an amount and a number of
 * textual lines that need to be displayed in the bottom. This item
 * can optionally be shown in bold, and can display a checkmark icon
 * optionally.
 *
 * Example usage:
 *
 * <dashboard-tile
 *  "class": "m-tile",
 *  "icon": "icon-finance",
 *  "title": "Finance",
 *  "value": "1",
 *  "text": "1 invoice",
 *  "button": "more info",
 *  "action": {
 *    "id": "navigate_to_account_finance",
 *    "recordId": "a77e6656-309b-5307-ed23-5a8d0788ea72"
 *  }
 * </dashboard-tile>
 *
 * Component of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .component('dashboardTile', {
    templateUrl: 'es6/dashboard/items/dashboard-tile/dashboard-tile.component.html',
    bindings: {
      icon: '@',
      title: '@',
      value: '@',
      text: '@',
      button: '@',
      action: "<"
    },
    controllerAs: 'dashboardTileController',
    controller: function(actionDatasource) {
      const dashboardTileController = this;

      dashboardTileController.showActionButton = function() {
        return _.has(dashboardTileController, 'action.id');
      };

      dashboardTileController.doAction = function() {
        actionDatasource.performAndHandle(dashboardTileController.action);
      };
    }
  });
