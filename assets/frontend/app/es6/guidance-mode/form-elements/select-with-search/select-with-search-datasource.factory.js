'use strict';

/**
 * @ngdoc service
 * @name digitalWorkplaceApp.selectWithSearchDatasource
 * @description
 *
 * The selectWithSearchDatasource is a factory whose job it is to provide
 * the data for select with search form element.
 *
 * Factory in the digitalWorkplaceApp.
 */
angular.module('digitalWorkplaceApp')
  .factory('selectWithSearchDatasource', function (API_URL, $http, LOG_HEADERS_KEYS) {

    return {getSelectOptions};

    /**
     * Gets the select items based on the provided datasourceName.
     *
     * @param  {String} datasourceName  The name of the select-with-search.
     * @param  {Object} params          The params object contains the query, the page number and the full model.
     * @return {Promise} A promise which resolves to the data for the selectWithSearch.
     */
    function getSelectOptions(datasourceName, params) {
      const headers = {};
      headers[LOG_HEADERS_KEYS.DESCRIPTION] = `Select with search: ${datasourceName}`;

      return $http.post(
        API_URL + "SelectWithSearch/" + datasourceName,
        params,
        {headers}
      ).then(function (response) {
        return response.data.data;
      });
    }
  });