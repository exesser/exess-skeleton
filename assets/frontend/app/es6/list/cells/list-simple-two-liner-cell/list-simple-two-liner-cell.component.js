'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.list-simple-two-liner-cell component
 * @description
 * # list-simple-two-liner-cell
 *
 * Creates a cell with two simple (no bold, no link, black) lines.
 *
 * Example usage:
 * <list-simple-two-liner-cell line-1="Sloepstraat 22" line-2="2584VV Den Haag"></list-simple-two-liner-cell>
 *
 * Component of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .component('listSimpleTwoLinerCell', {
    templateUrl: 'es6/list/cells/list-simple-two-liner-cell/list-simple-two-liner-cell.component.html',
    bindings: {
      line1: "@",
      line2: "@"
    },
    controllerAs: 'listSimpleTwoLinerCellController',
    controller: _.noop
  });
