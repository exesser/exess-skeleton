'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp:listActionCell component
 * @description
 * # listActionCell
 *
 * Create a action button which, when clicked, informs the backend that an action has been requested.
 * The backend then responds by delivering a command for the frontend to execute.
 *
 * Example usage:
 *
 * <list-action-cell
 *   action='{ "id": "42"}'
 *   clickable='true'
 *   icon='icon-opportunity'
 *   label='Create opportunity'>
 * </list-action-cell>
 *
 * Component of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .component('listActionCell', {
    templateUrl: 'es6/list/cells/list-action-cell/list-action-cell.component.html',
    bindings: {
      action: '<',
      clickable: '<',
      icon: '@',
      label: '@'
    },
    controllerAs: 'listActionCellController',
    controller: function (actionDatasource) {
      const listActionCellController = this;

      listActionCellController.actionClicked = function() {
        if (listActionCellController.clickable) {
          actionDatasource.performAndHandle(listActionCellController.action);
        }
      };
    }
  });
