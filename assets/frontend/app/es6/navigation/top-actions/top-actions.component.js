'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.top-actions component
 * @description
 * # top-actions
 *
 * This component will display the icons (buttons) for the top-action modules (plus-menu, filters and guidance-mode)
 *
 * Example usage:
 * <top-actions></top-actions>
 *
 * Component of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .component('topActions', {
    templateUrl: 'es6/navigation/top-actions/top-actions.component.html',
    controllerAs: 'topActionsController',
    controller: function (sidebarObserver, primaryButtonObserver, SIDEBAR_ELEMENT, topActionState, sidebarState, guidanceModeBackendState) {
      const topActionsController = this;

      topActionsController.toggleMiniGuidance = function () {
        sidebarObserver.toggleSidebarElement(SIDEBAR_ELEMENT.MINI_GUIDANCE);
      };

      topActionsController.toggleFilters = function () {
        sidebarObserver.toggleSidebarElement(SIDEBAR_ELEMENT.FILTERS);
      };

      topActionsController.togglePlusMenu = function () {
        sidebarObserver.toggleSidebarElement(SIDEBAR_ELEMENT.PLUS_MENU);
      };

      topActionsController.primaryButtonClicked = function () {
        if (topActionsController.primaryButtonIsDisabled() === false) {
          primaryButtonObserver.primaryButtonClicked();
        }
      };

      topActionsController.miniGuidanceIsOpen = function () {
        return sidebarState.getActiveSidebarElement() === SIDEBAR_ELEMENT.MINI_GUIDANCE;
      };

      topActionsController.filtersIsOpen = function () {
        return sidebarState.getActiveSidebarElement() === SIDEBAR_ELEMENT.FILTERS;
      };

      topActionsController.plusMenuIsOpen = function () {
        return sidebarState.getActiveSidebarElement() === SIDEBAR_ELEMENT.PLUS_MENU;
      };

      topActionsController.filtersCanBeOpened = function () {
        return topActionState.filtersCanBeOpened();
      };

      topActionsController.plusMenuCanBeOpened = function () {
        return topActionState.plusMenuCanBeOpened();
      };

      topActionsController.miniGuidanceCanBeOpened = function () {
        return topActionState.miniGuidanceCanBeOpened();
      };

      topActionsController.getPrimaryButtonData = function () {
        return topActionState.getPrimaryButtonData();
      };

      topActionsController.primaryButtonIsVisible = function () {
        return _.isEmpty(topActionsController.getPrimaryButtonData()) === false;
      };

      topActionsController.primaryButtonIsDisabled = function () {
        if (guidanceModeBackendState.getBackendIsBusy()) {
          return true;
        }

        return _.result(topActionsController.getPrimaryButtonData(), 'disabled', false);
      };
    }
  });
