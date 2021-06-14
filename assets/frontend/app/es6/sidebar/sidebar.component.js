'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.sidebar component
 * @description
 * # sidebar
 *
 * This component will create a placeholder for the content of the
 * top-action modules (plus-menu, filters and guidance-mode)
 *
 * Example usage:
 * <sidebar></sidebar>
 *
 * Component of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .constant('SIDEBAR_ELEMENT', {
    FILTERS: "FILTERS",
    PLUS_MENU: "PLUS_MENU",
    MINI_GUIDANCE: "MINI_GUIDANCE"
  })
  .component('sidebar', {
    templateUrl: 'es6/sidebar/sidebar.component.html',
    controllerAs: 'sidebarController',
    controller: function (SIDEBAR_ELEMENT, sidebarObserver, sidebarAnimation, sidebarState) {
      const sidebarController = this;

      sidebarController.SIDEBAR_ELEMENT = SIDEBAR_ELEMENT;

      sidebarController.isSidebarElementOpen = function(sideBarElement) {
        return sidebarState.getActiveSidebarElement() === sideBarElement;
      };

      sidebarObserver.registerOpenSidebarElementCallback(function(sideBarElement) {
        setActiveSideBarElement(sideBarElement);
      });

      sidebarObserver.registerToggleSidebarElementCallback(function(sideBarElement) {
        toggleActiveSideBarElement(sideBarElement);
      });

      sidebarObserver.registerCloseAllSideBarElementsCallback(function() {
        reset();
      });

      function toggleActiveSideBarElement(sideBarElement) {
        if (sidebarState.getActiveSidebarElement() === sideBarElement) {
          reset();
        } else {
          setActiveSideBarElement(sideBarElement);
        }
      }

      function setActiveSideBarElement(sideBarElement) {
        sidebarState.setActiveSidebarElement(sideBarElement);
        sidebarAnimation.open();
      }

      function reset() {
        sidebarAnimation.close();
        sidebarState.setActiveSidebarElement(null);
      }
    }
  });
