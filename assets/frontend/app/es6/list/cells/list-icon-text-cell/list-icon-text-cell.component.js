'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.list-icon-text-cell component
 * @description
 * # list-icon-text-cell
 *
 * Creates a cell with an icon and a label.
 * The icon is displayed base on the parameters icon-type and icon-status.
 * The label is the string fill in the text parameter.
 *
 * Example usage:
 * <list-icon-text-cell icon-type="bedrijf" icon-status="prospect" text="prospect"></list-icon-text-cell>
 *
 * Component of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .component('listIconTextCell', {
    templateUrl: 'es6/list/cells/list-icon-text-cell/list-icon-text-cell.component.html',
    bindings: {
      text: "@",
      cssClasses: "@"
    },
    controllerAs: 'listIconTextCellController',
    controller: _.noop
  });
