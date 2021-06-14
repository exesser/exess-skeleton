'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.factory:listStatus factory
 * @description
 * # listStatus
 *
 * The listStatus factory is responsible for saving in sessionStorage the list sort option and filters selected by user
 */
angular.module('digitalWorkplaceApp')
  .factory('listStatus', function ($window) {

    const sortKey = "LIST_SORT_KEY";
    const pageKey = "LIST_PAGE_KEY";
    const filterKey = "LIST_FILTER_KEY";
    const quickSearchKey = "LIST_QUICK_SEARCH_KEY";

    let sortStore = $window.sessionStorage.getItem(sortKey);
    if (_.isNull(sortStore)) {
      sortStore = {};
    } else {
      sortStore = angular.fromJson(sortStore);
    }

    let pageStore = $window.sessionStorage.getItem(pageKey);
    if (_.isNull(pageStore)) {
      pageStore = {};
    } else {
      pageStore = angular.fromJson(pageStore);
    }

    let filterStore = $window.sessionStorage.getItem(filterKey);
    if (_.isNull(filterStore)) {
      filterStore = {};
    } else {
      filterStore = angular.fromJson(filterStore);
    }

    let quickSearchStore = $window.sessionStorage.getItem(quickSearchKey);
    if (_.isNull(quickSearchStore)) {
      quickSearchStore = {};
    } else {
      quickSearchStore = angular.fromJson(quickSearchStore);
    }

    return {
      setSort,
      getSort,

      setPage,
      getPage,

      setQuickSearch,
      getQuickSearch,

      setFilters,
      getFilters
    };

    /**
     * Save the sort field for list.
     * @param listKey
     * @param sortValue
     */
    function setSort(listKey, sortValue) {
      sortStore[listKey] = sortValue;
      $window.sessionStorage.setItem(sortKey, angular.toJson(sortStore));
    }

    /**
     * Get the sort value for list
     * @param listKey
     */
    function getSort(listKey) {
      return sortStore[listKey];
    }

    /**
     * Save the page for list.
     * @param listKey
     * @param pageValue
     */
    function setPage(listKey, pageValue) {
      pageStore[listKey] = pageValue;
      $window.sessionStorage.setItem(pageKey, angular.toJson(pageStore));
    }

    /**
     * Get the page for list
     * @param listKey
     */
    function getPage(listKey) {
      return _.get(pageStore, listKey, 1);
    }

    /**
     * Save the filter values for list.
     * @param listKey
     * @param filterValue
     */
    function setFilters(listKey, filterValue) {
      filterStore[listKey] = filterValue;
      $window.sessionStorage.setItem(filterKey, angular.toJson(filterStore));
    }

    /**
     * Get the filter values for list
     * @param listKey
     */
    function getFilters(listKey) {
      return filterStore[listKey];
    }

    /**
     * Save the quickSearch values for list.
     * @param listKey
     * @param quickSearchValue
     */
    function setQuickSearch(listKey, quickSearchValue) {
      quickSearchStore[listKey] = quickSearchValue;
      $window.sessionStorage.setItem(quickSearchKey, angular.toJson(quickSearchStore));
    }

    /**
     * Get the quickSearch values for list
     * @param listKey
     */
    function getQuickSearch(listKey) {
      return quickSearchStore[listKey];
    }
  });
