'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.factory:topSearchObserver factory
 * @description
 * # topSearchObserver
 *
 * ## Responsibility
 *
 * In the application a dashboard or focus mode can contain a search bar.
 * These pages are dynamically defined, and the location where the user
 * needs to go when he types in a search query is as well. In the backend
 * response for the request to retrieve the page this data is obtained.
 *
 * The top search component registers to this:
 *
 * ```javascript
 * topSearchObserver.registerSetTopSearchDataCallback(function(searchData) {
 *   topSearchController.linkTo = searchData.linkTo;
 *   topSearchController.params = searchData.params;
 * });
 * ```
 *
 * When the dashboard component (or similarly the focus mode component)
 * receives this data, it sends it out via the top search observer:
 *
 * ```javascript
 * topSearchObserver.setTopSearchData(dashboardController.search);
 * ```
 *
 * The callback in the topSearchController is then invoked, and now
 * when the user types in a search query there the application routes
 * to the page specified in the linkTo and params with the query the
 * user entered.
 *
 * ## Lifespan and cardinality
 *
 * The lifespan of the topSearchObserver is unbounded. It is created
 * when the application starts up and it remains alive during its entire
 * life span.
 *
 * The cardinality of the search observer is 1-to-1, on one side we have
 * a top search component and on the other side we have either a dashboard
 * or a focus mode sending out the top search data.
 *
 * The top search component is recreated every time the user browses
 * to another dashboard or focus mode. It overwrites the current
 * setTopSearchDataCallback. The new dashboard or focus mode then
 * sets the new top search data if the backend indicates that there
 * should be a search bar on that page.
 */
angular.module('digitalWorkplaceApp')
  .factory('topSearchObserver', function() {

    let setTopSearchDataCallback = _.noop;

    return {
      setTopSearchData,
      registerSetTopSearchDataCallback
    };

    function setTopSearchData(searchData) {
      return setTopSearchDataCallback(searchData);
    }

    function registerSetTopSearchDataCallback(callback) {
      setTopSearchDataCallback = callback;
    }
  });
