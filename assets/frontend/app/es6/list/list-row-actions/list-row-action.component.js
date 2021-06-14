'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp:listRowAction component
 * @description
 * # listRowAction
 *
 * Create a black action button which, when clicked, informs the backend that an action has been requested.
 * The backend then responds by delivering a command for the frontend to execute.
 *
 * Example usage:
 *
 * <list-row-action
 *   action='{ "id": "42"}'
 *   clickable='true'
 *   icon='icon-opportunity'
 *   label='Create opportunity'>
 * </list-row-actions>
 *
 * Component of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .component('listRowAction', {
    templateUrl: 'es6/list/list-row-actions/list-row-action.component.html',
    bindings: {
      action: '<',
      clickable: '<',
      icon: '@',
      label: '@'
    },
    controllerAs: 'listRowActionController',
    controller: function (actionDatasource) {
      const listRowActionController = this;

      listRowActionController.actionClicked = function() {
        if (listRowActionController.clickable) {
          actionDatasource.performAndHandle(listRowActionController.action);
        }
      };
    }
  });
