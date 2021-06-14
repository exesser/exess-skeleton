'use strict';

/**
 * @ngdoc service
 * @name digitalWorkplaceApp.crudConfigHelperDatasource
 * @description
 *
 * The crudConfigHelperDatasource is a factory whose job it is to provide
 * the data for crud config helper tool.
 *
 * Factory in the digitalWorkplaceApp.
 */
angular.module('digitalWorkplaceApp')
  .factory('crudConfigHelperDatasource', function (API_URL, $http, LOG_HEADERS_KEYS) {

    return {
      getRecordsInformation
    };

    /**
     * @return {Promise} A promise which resolves to the data for backend.
     */
    function getRecordsInformation() {
      const headers = {};
      headers[LOG_HEADERS_KEYS.DESCRIPTION] = `Crud records information`;

      return $http.get(API_URL + `CRUD/records-information`, { headers }).then(function (response) {
        return response.data.data;
      });
    }
  });
