'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.list-checkbox-cell component
 * @description
 * # list-checkbox-cell
 *
 * Creates a cell with a checkbox in it.
 * As parameters should receive an id and the list key.
 *
 * Example usage:
 * <list-checkbox-cell id="123-456-789" list-key="accounts"></list-checkbox-cell>
 *
 * Component of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .component('listCheckboxCell', {
    templateUrl: 'es6/list/cells/list-checkbox-cell/list-checkbox-cell.component.html',
    bindings: {
      id: "@",
      listKey: "@"
    },
    controllerAs: 'listCheckboxCellController',
    controller: function (listObserver) {
      const listCheckboxCellController = this;
      listCheckboxCellController.isChecked = false;

      listCheckboxCellController.toggle = function () {
        listCheckboxCellController.isChecked = !listCheckboxCellController.isChecked;
        listObserver.toggleListRowSelection(listCheckboxCellController.listKey, listCheckboxCellController.id, listCheckboxCellController.isChecked);
      };

      let callbackDeregisterFunction = listObserver.registerToggleAllListRowsSelectionsCallback(listCheckboxCellController.listKey, function(itemSelected) {
        listCheckboxCellController.isChecked = itemSelected;
      });

      // When the listCheckboxCell is destroyed, clear the callback in the listObserver.
      listCheckboxCellController.$onDestroy = function() {
        callbackDeregisterFunction();
      };
    }
  });
