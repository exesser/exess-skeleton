'use strict';

/**
 * @ngdoc service
 * @name digitalWorkplaceApp.blueSidebarDatasource
 * @description
 *
 * The blueSidebarDatasource is a factory whose job it is to provide
 * the data for the blue side bar.
 *
 * Factory in the digitalWorkplaceApp.
 */
angular.module('digitalWorkplaceApp')
  .factory('blueSidebarDatasource', function (API_URL, $http, LOG_HEADERS_KEYS) {

    return { get };

    /**
     * Returns the data for the BlueSidebar.
     * @param  {String} options.recordType The recordType you want the data for.
     * @param  {String} options.id         The id you want the data for.
     * @return {Promise} A promise which resolves the blue side bar data.
     */
    function get({recordType, id}) {
      const headers = {};
      headers[LOG_HEADERS_KEYS.DESCRIPTION] = `get BlueSidebar for ${recordType}:${id}`;

      return $http.get(API_URL + `BlueSidebar/${recordType}/${id}`, {headers}).then(function(response) {
        return response.data.data;
      });
    }
  });
