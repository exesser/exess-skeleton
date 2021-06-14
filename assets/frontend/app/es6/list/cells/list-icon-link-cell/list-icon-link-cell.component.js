'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.list-icon-link-cell component
 * @description
 * # list-icon-link-cell
 *
 * Creates a cell with an icon and a link on the icon.
 *
 * Example usage:
 * <list-icon-link-cell icon="bedrijf" link="http://ginder"></list-icon-link-cell>
 *
 * Component of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .component('listIconLinkCell', {
    templateUrl: 'es6/list/cells/list-icon-link-cell/list-icon-link-cell.component.html',
    bindings: {
      icon: "@",
      link: "@"
    },
    controllerAs: 'listIconLinkCellController',
    controller: _.noop
  });
