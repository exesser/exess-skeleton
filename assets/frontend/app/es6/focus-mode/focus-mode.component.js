'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.component:focusMode
 * @description
 * # focusMode
 *
 * Creates a focus-mode page based on a definition.
 * It can contain filters, a plusMenu and top search bar.
 * These properties can all be set on the focusMode
 *
 * Example usage:
 *
 * <focus-mode definition="definition"></focus-mode>
 *
 * Component of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .component('focusMode', {
    templateUrl: 'es6/focus-mode/focus-mode.component.html',
    controllerAs: 'focusModeController',
    controller: function ($timeout, $stateParams, filtersObserver, plusMenuObserver, topSearchObserver,
                          previousState, dashboardDatasource, $window, sidebarState) {
      const focusModeController = this;

      focusModeController.$onInit = function() {
        sidebarState.setShowSidebar(true);
      };

      const params = {
        dashboardId: $stateParams.focusModeId,
        recordId: $stateParams.recordId,
        queryParams: { query: $stateParams.query }
      };

      focusModeController.loading = true;

      // give angular some time to render before we fetch the data
      $timeout(function () {
        dashboardDatasource.get(params).then((dashboard) => {
          const { filters, plusMenu, search, grid, title } = dashboard;

          focusModeController.title = title;
          focusModeController.grid = grid;
          focusModeController.loading = false;

          if (filters.display) {
            filtersObserver.setFilterData(filters.filterKey, filters.listKey);
          }

          if (plusMenu.display) {
            plusMenuObserver.setPlusMenuData({
              buttonGroups: plusMenu.buttonGroups,
              buttons: plusMenu.buttons
            });
          }

          if (search.display) {
            topSearchObserver.setTopSearchData(search);
          }
        });
      }, 100);

      focusModeController.backArrowClicked = function () {
        $window.history.back();
      };

      focusModeController.topArrowClicked = function () {
        previousState.navigateTo();
      };
    }
  });
