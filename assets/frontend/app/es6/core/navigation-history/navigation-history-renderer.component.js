'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.navigationHistoryRenderer component
 * @description
 * # navigationHistoryRenderer
 *
 * The navigationHistoryRenderer component renders the history that is
 * available on navigationHistoryContainer.
 *
 * <navigation-history-renderer></navigation-history-renderer>
 *
 * Component of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .component('navigationHistoryRenderer', {
    templateUrl: 'es6/core/navigation-history/navigation-history-renderer.component.html',
    controllerAs: 'navigationHistoryRendererController',
    controller: function (navigationHistoryContainer) {
      const navigationHistoryRendererController = this;

      navigationHistoryRendererController.actions = _.take(_.reverse(angular.copy(navigationHistoryContainer.getActions())), 10);
      navigationHistoryRendererController.isOpen = navigationHistoryContainer.getShowActions();
      navigationHistoryRendererController.showEditIcon = navigationHistoryContainer.getShowEditIcon();

      navigationHistoryRendererController.showNavigation = function() {
        return !_.isEmpty(navigationHistoryRendererController.actions);
      };

      navigationHistoryRendererController.toggleDisplayHistory = function() {
        navigationHistoryRendererController.isOpen = !navigationHistoryRendererController.isOpen;
        navigationHistoryContainer.setShowActions(navigationHistoryRendererController.isOpen);
      };

      navigationHistoryRendererController.showEditIconChanged = function () {
        navigationHistoryContainer.setShowEditIcon(navigationHistoryRendererController.showEditIcon);
      };
    }
  });
