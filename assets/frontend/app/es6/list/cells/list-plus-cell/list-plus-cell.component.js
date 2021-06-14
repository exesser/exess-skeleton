'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.list-plus-cell component
 * @description
 * # list-plus-cell
 *
 * Creates a cell with a plus icon.
 * The icon is a toggle which display/hide an extra row on the list, in this new row
 * will be render a new grid base on the grid-key, id and list-key parameters.
 *
 * Example usage:
 * <list-plus-cell id="account__123-123-345" grid-key="action-bar" list-key="accounts"></list-plus-cell>
 *
 * Component of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .component('listPlusCell', {
    templateUrl: 'es6/list/cells/list-plus-cell/list-plus-cell.component.html',
    bindings: {
      id: "@",
      gridKey: "@",
      listKey: "@",
      params: "<"
    },
    controllerAs: 'listPlusCellController',
    controller: function(listObserver) {
      const listPlusCellController = this;

      listPlusCellController.isOpen = false;
      listPlusCellController.iconClose = _.get(listPlusCellController, 'params.icon-close', 'plus');
      listPlusCellController.iconOpen = _.get(listPlusCellController, 'params.icon-open', 'plus');

      listPlusCellController.toggleExtraRowContentPlaceholder = function() {
        listPlusCellController.isOpen = !listPlusCellController.isOpen;
        listObserver.toggleExtraRowContentPlaceholder(listPlusCellController.listKey, listPlusCellController.gridKey, listPlusCellController.id);
      };
    }
  });
