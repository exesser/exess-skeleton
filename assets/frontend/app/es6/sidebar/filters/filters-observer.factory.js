'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.factory:filtersObserver factory
 * @description
 * # filtersObserver
 *
 * ## Responsibility
 *
 * On some dashboards and focus mode pages lists are displayed on
 * which you can filter to display fewer results. There is a filters component
 * that is created every time we open a dashboard or focus mode page.
 * The filter data consists of a filter key and list key. This data
 * comes back with the backend response for the dashboard or focus mode
 * page. The filters component then uses this data to retrieve the
 * filter fields and model.
 *
 * The filters component registers to receive the filter data like this:
 *
 * ```javascript
 * filtersObserver.registerSetFilterDataCallback(function(filterKey, listKey) {
 *   //Request filter data and set filters
 *   ...
 * });
 * ```
 *
 * This data is sent by the dashboard controller (and similarly the focus mode controller) like this:
 *
 * ```javascript
 * filtersObserver.setFilterData(filters.filterKey, filters.listKey);
 * ```
 *
 * After opening it in the sidebar the fields retrieved by the filterKey
 * and listKey are shown. When the user then fills in some filters,
 * the filters components sends out the filtersHaveChanged event:
 *
 * ```javascript
 * //When the filters model changes (with a debounce) ...
 * $scope.$watch('filtersController.model', _.debounce(function(newValue, oldValue) {
 *   if (_.isEqual(oldValue, newValue) === false) {
 *     filtersObserver.filtersHaveChanged(listKey, newValue);
 *     listStatus.setFilters(listKey, newValue);
 *   }
 * }, DEBOUNCE_TIME), true);
 * ```
 *
 * To which the list component is registered for a specific listKey,
 * so it knows to perform a new request in the backend and set the new
 * list data:
 *
 * ```javascript
 * filtersObserver.registerFiltersHaveChangedCallback(listController.listKey, function (filters) {
 *   listController.filters = filters;
 *   listController.page = 1;
 *   updateList();
 * });
 * ```
 *
 * ## Lifespan and cardinality
 *
 * The lifespan of the filtersObserver is unbounded. It is created when
 * the application starts up and it remains alive during its entire life
 * span.
 *
 * The cardinality of the filters observer is 1-to-1, on one side we have
 * a filters component and on the other side we have either a dashboard
 * or a focus mode sending out the filters data.
 *
 * The filters component is recreated every time the user browses to another
 * dashboard or focus mode. It overwrites the current setFilterDataCallback.
 *
 * The new dashboard or focus mode then sets the new filters data if
 * the backend indicates that that page can contain filters.
 */
angular.module('digitalWorkplaceApp')
  .factory('filtersObserver', function() {

    let filtersHaveChangedCallbacks = {};
    let setFilterDataCallback = _.noop;

    return {
      setFilterData,
      registerSetFilterDataCallback,

      filtersHaveChanged,
      registerFiltersHaveChangedCallback
    };

    /**
     * Inform the observer that there is filter data available.
     * The filter data is the data which allows the observer to get
     * the actual filters from the backend.
     *
     * @param filterKey The filterKey of the filters which are
     * @param listKey   The listKey for which the filters would apply.
     */
    function setFilterData(filterKey, listKey) {
      setFilterDataCallback(filterKey, listKey);
    }

    function registerSetFilterDataCallback(callback) {
      setFilterDataCallback = callback;
    }

    /**
     * Inform the observer that the filters have changed for a particular
     * list.
     *
     * @param listKey The list key.
     * @param filters The new filters.
     */
    function filtersHaveChanged(listKey, filters) {
      if (_.has(filtersHaveChangedCallbacks, listKey)) {
        filtersHaveChangedCallbacks[listKey](filters);
      }
    }

    /**
     * Register a callback method to invoke when the filterHasChanged
     * function is invoked.
     *
     * @param listKey The list key.
     * @param callback A function which takes a object(filters model) as a parameter.
     */
    function registerFiltersHaveChangedCallback(listKey, callback) {
      filtersHaveChangedCallbacks[listKey] = callback;
    }
  });
