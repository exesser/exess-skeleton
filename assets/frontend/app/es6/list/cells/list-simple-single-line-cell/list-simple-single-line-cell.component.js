'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.list-simple-single-line-cell component
 * @description
 * # list-simple-single-line-cell
 *
 * Creates a cell with one line with the text from the "text" parameter.
 *
 * Example usage:
 * <list-simple-single-line-cell text="Sloepstraat 22"></list-simple-single-line-cell>
 *
 * Component of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .component('listSimpleSingleLineCell', {
    templateUrl: 'es6/list/cells/list-simple-single-line-cell/list-simple-single-line-cell.component.html',
    bindings: {
      text: "@"
    },
    controllerAs: 'listSimpleSingleLineCellController',
    controller: _.noop
  });
