'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.list-bold-top-two-liner-cell component
 * @description
 * # list-bold-top-two-liner-cell
 *
 * Creates a cell with two lines, first line is bold and second line is normal.
 *
 * Example usage:
 * <list-bold-top-two-liner-cell line-1="Exesser" line-2="ES12345"></list-bold-top-two-liner-cell>
 *
 * Component of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .component('listBoldTopTwoLinerCell', {
    templateUrl: 'es6/list/cells/list-bold-top-two-liner-cell/list-bold-top-two-liner-cell.component.html',
    bindings: {
      line1: "@",
      line2: "@"
    },
    controllerAs: 'listBoldTopTwoLinerCellController',
    controller: _.noop
  });
