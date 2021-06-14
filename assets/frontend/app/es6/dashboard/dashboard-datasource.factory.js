'use strict';

/**
 * @ngdoc service
 * @name digitalWorkplaceApp.dashboardDatasource
 * @description
 *
 * The dashboardDatasource is a factory whose job it is to provide
 * the data for dashboards.
 *
 * Factory in the digitalWorkplaceApp.
 */
angular.module('digitalWorkplaceApp')
  .factory('dashboardDatasource', function (API_URL, $http, LOG_HEADERS_KEYS, $timeout, commandHandler) {

    return { get };

    /**
     * Returns the data, such as the grid, for the dashboard.
     * @param  {String} options.dashboardId The dashboardId you want the plusMenu for.
     * @param  {String} options.recordId    The recordId you want the plusMenu for.
     * @param  {String} options.queryParams       The query parameters to pass along.
     * @return {Promise} A promise which resolves to the data for the dashboard.
     */
    function get({ dashboardId, recordId, queryParams = {} }) {
      let url = API_URL + `Dashboard/${dashboardId}`;

      if (_.isEmpty(recordId) === false) {
        url += `/${recordId}`;
      }

      const headers = {};
      headers[LOG_HEADERS_KEYS.DESCRIPTION] = `Dashboard: ${dashboardId} | recordId: ${recordId}`;

      return $http.get(url, { params: queryParams, headers }).then(
        function(response) {
          return response.data.data;
        }, function(data) {
          $timeout(function () {
            if (_.has(data, 'data.data.command')) {
              commandHandler.handle(data.data.data.command);
            }
          }, 500);
        }
      );
    }
  });
