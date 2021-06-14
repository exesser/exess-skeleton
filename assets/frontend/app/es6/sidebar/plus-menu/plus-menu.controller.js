'use strict';

/**
 * This controller renders the plus-menu when you open a page that supports it.
 * It is listening to the plusMenuObserver.setPlusMenuData() method.
 * When plusMenuObserver.resetPlusMenuData() is invoked the contents are cleared again.
 */
angular.module('digitalWorkplaceApp')
  .controller('PlusMenuController', function($scope, plusMenuObserver, sidebarObserver, topActionState) {
    var plusMenuController = this;

    topActionState.setPlusMenuCanBeOpened(false);

    plusMenuController.plusMenu = {};

    plusMenuObserver.registerSetPlusMenuDataCallback(function(plusMenu) {
      plusMenuController.plusMenu = plusMenu;
      topActionState.setPlusMenuCanBeOpened(true);
    });

    $scope.$on("$destroy", function() {
      sidebarObserver.closeAllSidebarElements();
      topActionState.setPlusMenuCanBeOpened(false);
    });
  });
