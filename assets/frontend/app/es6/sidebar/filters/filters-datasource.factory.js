'use strict';

/**
 * @ngdoc service
 * @name digitalWorkplaceApp.filterDatasource
 * @description
 *
 * The filterDatasource is a factory whose job it is to provide
 * the filters for particular lists.
 *
 * Factory in the digitalWorkplaceApp.
 */
angular.module('digitalWorkplaceApp')
  .factory('filterDatasource', function (API_URL, $http, LOG_HEADERS_KEYS) {

    return { get };

    /**
     * Gets the filters based on the provided listKey and filterKey.
     * @param  {String} options.filterKey The filterKey you want the filters for.
     * @param  {String} options.listKey The listKey you want the filters for.
     * @return {Promise} A promise which resolves to the filters.
     */
    function get({ filterKey, listKey }) {
      const headers = {};
      headers[LOG_HEADERS_KEYS.DESCRIPTION] = `Filters: ${filterKey} | for list: ${listKey}`;

      return $http.get(API_URL + `Filter/${filterKey}/${listKey}`, {headers}).then(function(response) {
        return response.data.data;
      });
    }
  });
