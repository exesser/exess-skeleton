'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp:dashboard component
 * @description
 * # dashboard
 *
 * Creates a dashboard by fetching the relevant data from the back-end
 * such as the grid, filters, plusMenu and search.
 *
 * <dashboard></dashboard>
 *
 * Component of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .component('dashboard', {
    templateUrl: 'es6/dashboard/dashboard.component.html',
    controllerAs: 'dashboardController',
    controller: function ($timeout, filtersObserver, slideAnimation, plusMenuObserver, topSearchObserver,
                          dashboardDatasource, navigationHistoryContainer, $state, $stateParams, sidebarState, $element,
                          $window) {
      const dashboardController = this;
      dashboardController.subMenuIsLoaded = false;

      dashboardController.$onInit = function () {
        sidebarState.setShowSidebar(true);
        slideAnimation.close();
      };

      dashboardController.loading = true;
      dashboardController.displayMobileMenu = false;
      dashboardController.hideMenu = true;

      const params = {
        dashboardId: $stateParams.dashboardId,
        recordId: $stateParams.recordId,
        queryParams: { query: $stateParams.query, recordType: $stateParams.recordType }
      };

      // give angular some time to render before we fetch the data
      $timeout(function () {
        dashboardDatasource.get(params).then((dashboard) => {
          const { filters, plusMenu, search, grid, baseEntity} = dashboard;

          dashboardController.grid = grid;
          dashboardController.loading = false;

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

          if (baseEntity) {
            navigationHistoryContainer.addAction(baseEntity, $state);
          }
        });
      }, 100);

      dashboardController.showMobileMenu = function () {
        if ($window.innerWidth < 960 || dashboardController.subMenuIsLoaded === false) {
          dashboardController.displayMobileMenu = false;
          return dashboardController.displayMobileMenu;
        }

        if (dashboardController.displayMobileMenu) {
          return dashboardController.displayMobileMenu;
        }

        const top = $element.find('.top');
        const menus = $element.find('.top-menu a');
        const width = _.sumBy(menus, (o) => {return o.offsetWidth;});

        dashboardController.displayMobileMenu = (top[0].offsetWidth - width) < 350;
        dashboardController.hideMenu = false;
        return dashboardController.displayMobileMenu;
      };
    }
  });
