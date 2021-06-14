'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp:titleContainingGrid component
 * @description
 * # titleContainingGrid
 *
 * TitleContainingGrid takes a grid, titleExpression and an optional defaultTitle as input.
 * It renders the grid in a card, evaluates the given expression against the current form
 * model and puts the result into the card title. If the outcome of the expression is empty,
 * the default value is shown if specified.
 *
 * Component of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .component('titleContainingGrid', {
    templateUrl: 'es6/guidance-mode/grid-wrappers/title-containing-grid/title-containing-grid.component.html',
    require: {
      guidanceObserversAccessor: '^guidanceObserversAccessor'
    },
    bindings: {
      grid: "<",
      defaultTitle: '@',
      titleExpression: '@'
    },
    controllerAs: 'titleContainingGridController',
    controller: function() {
      const titleContainingGridController = this;

      let stepChangeDeregisterFunction = _.noop;
      titleContainingGridController.model = {};

      titleContainingGridController.$onInit = function() {
        const guidanceFormObserver = titleContainingGridController.guidanceObserversAccessor.getGuidanceFormObserver();
        stepChangeDeregisterFunction = guidanceFormObserver.addStepChangeOccurredCallback(function({ model }) {
          titleContainingGridController.model = model;
        });
      };

      titleContainingGridController.$onDestroy = function() {
        stepChangeDeregisterFunction();
      };
    }
  });
