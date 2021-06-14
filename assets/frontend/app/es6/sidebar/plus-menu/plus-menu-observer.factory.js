'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.factory:plusMenuObserver factory
 * @description
 * # plusMenuObserver
 *
 * ## Responsibility
 *
 * On some dashboards and focus mode pages there is an option to press
 * a 'plus button' on the top right of the page.This plus menu contains
 * certain operations that can be triggered that are dependent of the
 * page you are on. There is a plus menu component that is created
 * every time we open a dashboard or focus mode page.
 *
 * The plus menu data consists of a hierarchical list of actions possibly
 * nested in action groups. This data is requested in the dashboard or
 * focus mode page and sent to the plus menu component using the
 * plus menu observer. The plus menu components then uses this data to
 * show the specified action (groups).
 *
 * The plus menu component registers to receive the plus menu data like this:
 *
 * ```javascript
 * plusMenuObserver.registerSetPlusMenuDataCallback(function(plusMenu) {
 *   plusMenuController.plusMenu = plusMenu;
 *   ...
 * });
 * ```
 *
 * This data is sent by the dashboard controller (and similarly the focus mode controller) like this:
 *
 * ```javascript
 * plusMenuObserver.setPlusMenuData(plusMenuData);
 * ```
 *
 * ## Lifespan and cardinality
 *
 * The lifespan of the plusMenuObserver is unbounded. It is created
 * when the application starts up and it remains alive during its
 * entire life span.
 *
 * The cardinality of the plus menu observer is 1-to-1, on one side we
 * have a plus menu component and on the other side we have either a
 * dashboard or a focus mode sending out the plus menu data.
 *
 * The plus menu component is recreated every time the user browses to
 * another dashboard or focus mode. It overwrites the current setPlusMenuDataCallback.
 *
 * The new dashboard or focus mode then sets the new plus menu data if
 * the backend indicates that that page can contain a plus menu.
 */
angular.module('digitalWorkplaceApp')
  .factory('plusMenuObserver', function() {

    let setPlusMenuDataCallback = _.noop;

    return {
      setPlusMenuData,
      registerSetPlusMenuDataCallback
    };

    /**
     * Tell the observer that new data for the plusMenu is available.
     *
     * @param plusMenu The plus menu data.
     */
    function setPlusMenuData(plusMenu) {
      setPlusMenuDataCallback(plusMenu);
    }

    /**
     * Register a callback method to invoke when the setPlusMenuData function
     * is invoked.
     *
     * @param callback function to invoke with the plusMenu as an argument
     */
    function registerSetPlusMenuDataCallback(callback) {
      setPlusMenuDataCallback = callback;
    }
  });
