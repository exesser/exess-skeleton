"use strict";

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.factory:sidebarState factory
 * @description
 * # sidebarState
 * in this factory we are keeping the state(activeElement: SIDEBAR_ELEMENT) of the sidebar.
 */
angular.module('digitalWorkplaceApp')
  .factory('sidebarState', function() {

    let activeSidebarElement = null;
    let showSidebar = true;

    return {
      getActiveSidebarElement,
      setActiveSidebarElement,
      setShowSidebar,
      getShowSidebar
    };

    function setShowSidebar(hasSidebar) {
      showSidebar = hasSidebar;
    }

    function getShowSidebar() {
      return showSidebar;
    }

    /**
     * Returns the currently active sidebar element.
     * @returns {SIDEBAR_ELEMENT} current state
     */
    function getActiveSidebarElement() {
      return activeSidebarElement;
    }

    /**
     * Sets the currently active sidebar element.
     * @param {SIDEBAR_ELEMENT} sidebarElement new state
     */
    function setActiveSidebarElement(sidebarElement) {
      activeSidebarElement = sidebarElement;
    }
  });
