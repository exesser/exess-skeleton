'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp:mainMenu component
 * @description
 * # mainMenu
 *
 * Creates a sub menu by fetching the menus from the back-end.
 *
 * <sub-menu></sub-menu>
 *
 * Component of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .component('subMenu', {
    templateUrl: 'es6/navigation/sub-menu/sub-menu.component.html',
    bindings: {
      isLoaded: "="
    },
    controllerAs: 'submenuController',
    controller: function (menuDatasource, $stateParams, $timeout) {
      const submenuController = this;

      const dashboardId = $stateParams.dashboardId;

      menuDatasource.getSub($stateParams.mainMenuKey).then((subMenus) => {
        submenuController.subMenus = _.map(subMenus, (subMenu) => {
          subMenu.active = subMenu.params.dashboardId === dashboardId;
          return subMenu;
        });

        //give some time to render
        $timeout(function () {
          submenuController.isLoaded = true;
        }, 100);

      });
    }
  });
