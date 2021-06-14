'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp:dashboardImageItem component
 * @description
 * # dashboardImageItem
 *
 * Creates a dashboard item which displays an image. The image which
 * is displayed can come from anywhere, as long as it is an URL,
 * in the following example it is retrieved from www.42.nl:
 *
 * Example usage:
 *
 * <dashboard-image-item
 *   src='http://www.42.nl/images/42-logo.svg'
 *   action='{"id": "42", "recordId": "1337", "recordType": "elec"}'>
 * </dashboard-image-item>
 *
 * Component of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .component('dashboardImageItem', {
    templateUrl: 'es6/dashboard/items/dashboard-image-item/dashboard-image-item.component.html',
    bindings: {
      src: '@',
      action: "<"
    },
    controllerAs: 'dashboardImageItemController',
    controller: function(actionDatasource) {
      const dashboardImageItemController = this;

      dashboardImageItemController.doAction = function() {
        actionDatasource.performAndHandle(dashboardImageItemController.action);
      };
    }
  });
