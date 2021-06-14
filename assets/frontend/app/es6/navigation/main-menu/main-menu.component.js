'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp:mainMenu component
 * @description
 * # mainMenu
 *
 * Creates a main menu by fetching the menu's from the back-end.
 *
 * <main-menu></main-menu>
 *
 * Component of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .component('mainMenu', {
    templateUrl: 'es6/navigation/main-menu/main-menu.component.html',
    controllerAs: 'mainMenuController',
    controller: function(menuDatasource, $stateParams) {
      const mainMenuController = this;

      menuDatasource.getMain().then((mainMenus) => {
        mainMenuController.mainMenus = _.map(mainMenus, (mainMenu) => {
          mainMenu.active = mainMenu.params.mainMenuKey === $stateParams.mainMenuKey;
          return mainMenu;
        });
      });
    }
  });
