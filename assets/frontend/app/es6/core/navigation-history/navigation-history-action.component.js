'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.navigationHistoryAction component
 * @description
 * # navigationHistoryAction
 *
 * The navigationHistoryAction component represents a single history state.
 *
 * Example usage:
 *
 * <navigation-history-action action="{label:"Home (Dashboard)", stateName:"dashboard", stateParams:{}}">
 * </navigation-history-action>
 *
 * Component of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .component('navigationHistoryAction', {
    templateUrl: 'es6/core/navigation-history/navigation-history-action.component.html',
    bindings: {
      action: "<"
    },
    controllerAs: 'navigationHistoryActionController',
    controller: function ($state) {
      const navigationHistoryActionController = this;

      navigationHistoryActionController.go = function() {
        $state.go(navigationHistoryActionController.action.stateName, navigationHistoryActionController.action.stateParams);
      };
    }
  });
