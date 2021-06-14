'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp:centeredGuidanceGrid component
 * @description
 * # centeredGuidanceGrid
 *
 * CenteredGuidanceGrid takes a grid as input and renders it vertically aligned
 * in the middle of the grid it is drawn in itself.
 *
 * Component of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .component('centeredGuidanceGrid', {
    templateUrl: 'es6/guidance-mode/grid-wrappers/centered-guidance-grid/centered-guidance-grid.component.html',
    bindings: {
      grid: "<"
    },
    controllerAs: 'centeredGuidanceGridController',
    controller: _.noop
  });
