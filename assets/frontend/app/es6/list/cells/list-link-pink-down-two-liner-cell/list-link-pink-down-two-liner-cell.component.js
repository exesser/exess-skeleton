'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.list-link-pink-down-two-liner-cell component
 * @description
 * # list-link-pink-down-two-liner-cell
 *
 * Creates a cell with two lines, first line is normal and second line is a pink link.
 *
 * Example usage:
 * <list-link-pink-down-two-liner-cell
 *    line-1="Ken Block"
 *    line-2="ken@block.ro"
 *    link="mailto:ken@block.ro">
 * </list-link-pink-down-two-liner-cell>
 *
 * Component of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .component('listLinkPinkDownTwoLinerCell', {
    templateUrl: 'es6/list/cells/list-link-pink-down-two-liner-cell/list-link-pink-down-two-liner-cell.component.html',
    bindings: {
      line1: "@",
      line2: "@",
      link: "@"
    },
    controllerAs: 'listLinkPinkDownTwoLinerCellController',
    controller: _.noop
  });
