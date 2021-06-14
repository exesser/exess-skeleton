'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.factory:sidebarObserver factory
 * @description
 * # sidebarObserver
 *
 * ## Responsibility
 *
 * The sidebar observer is responsible for hiding or showing the possible
 * contents of the sidebar: the mini-guidance, filters or plus menu.
 *
 * It contains some events to trigger these:
 *
 * **Open sidebar element**
 *
 * The openSidebarElement event takes a SIDEBAR_ELEMENT enum value as
 * an argument. The callback is registered by the sidebarComponent and
 * used to set the SIDEBAR_ELEMENT given as an argument as the currently
 * active element. There can only be one openSideBar callback registered.
 *
 * **Toggle sidebar element**
 *
 * The toggleSideBarElement event takes a SIDEBAR_ELEMENT enum value
 * as an argument. It is similar to the openSidebarElement event except
 * in the sense that it opens the element if it is closed and vice versa.
 * There can be only one toggleSideBar callback registered.
 *
 * **Close all sidebar elements**
 *
 * The closeAllSidebarElements callback is registered by the sidebarComponent.
 * When fired all the sidebar elements are closed.
 * There can be only one toggleSideBar callback registered.
 *
 * ## Lifespan and cardinality ##
 *
 * The lifespan of the sidebarObserver is unbounded. It is created when
 * the application starts up and it remains alive during its entire lifespan.
 *
 * The sidebarComponent is the only subscriber to the events, it sets
 * all the callbacks. This sidebar component also remains alive during
 * the lifecycle of the application.
 */
angular.module('digitalWorkplaceApp')
  .factory('sidebarObserver', function() {

    let openSideBarElementCallback = _.noop;
    let toggleSideBarElementCallback = _.noop;
    let closeAllSideBarElementsCallback = _.noop;

    return {
      openSidebarElement,
      registerOpenSidebarElementCallback,

      toggleSidebarElement,
      registerToggleSidebarElementCallback,

      closeAllSidebarElements,
      registerCloseAllSideBarElementsCallback
    };

    /**
     * Open a SIDEBAR_ELEMENT on the sidebar. All other sideBarElements
     * will close, only one sideBarElement can be opened at one time.
     *
     * @param {SIDEBAR_ELEMENT} sideBarElement The sideBarElement which needs to open.
     */
    function openSidebarElement(sideBarElement) {
      openSideBarElementCallback(sideBarElement);
    }

    /**
     * Register a callback for when a sidebarElement is opened.
     * The callback function will receive the sideBarElement which
     * needs to be opened.
     *
     * @param {Function(SIDEBAR_ELEMENT)} callback The callback function that needs to be called.
     */
    function registerOpenSidebarElementCallback(callback) {
      openSideBarElementCallback = callback;
    }

    /**
     * Toggle a SIDEBAR_ELEMENT on the sidebar. If the SLIDEBAR_ELEMENT
     * is closed it will open. If it is currently opened it will close.
     *
     * @param {SIDEBAR_ELEMENT} sideBarElement The sideBarElement which needs to toggle.
     */
    function toggleSidebarElement(sideBarElement) {
      toggleSideBarElementCallback(sideBarElement);
    }

    /**
     * Register a callback for when a sidebarElement is toggled.
     * The callback function will receive the sideBarElement which
     * needs to be toggled.
     *
     * @param {Function(SIDEBAR_ELEMENT)} callback The callback function that needs to be called.
     */
    function registerToggleSidebarElementCallback(callback) {
      toggleSideBarElementCallback = callback;
    }

    /**
     * Close all SIDEBAR_ELEMENT on the sidebar.
     */
    function closeAllSidebarElements() {
      closeAllSideBarElementsCallback();
    }

    /**
     * Register a callback for when the closeAllSidebarElements is called.
     * @param {Function} callback The callback function that needs to be called.
     */
    function registerCloseAllSideBarElementsCallback(callback) {
      closeAllSideBarElementsCallback = callback;
    }
  });
