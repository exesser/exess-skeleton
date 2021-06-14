'use strict';

/**
 * @ngdoc service
 * @name digitalWorkplaceApp.hashtagDatasource
 * @description
 *
 * The hashtagDatasource is a factory whose job it is to provide
 * the data for the hashtag autocompletion.
 *
 * Factory in the digitalWorkplaceApp.
 */
angular.module('digitalWorkplaceApp')
  .factory('hashtagDatasource', function (API_URL, $http, LOG_HEADERS_KEYS) {

    return { search };

    /**
     * Sends a query and gets back a list of tags and text which can
     * be added to the hashtag-text-form-element.
     *
     * @param  {String} datasourceName The name of the datasource we need tags and text for.
     * @param  {String} query The query we need tags and text for.
     * @return {Promise} A promise which resolves to the data containing text and hashtags.
     */
    function search(datasourceName, query) {
      let url = API_URL + `hashtags/${datasourceName}`;
      const headers = {};
      headers[LOG_HEADERS_KEYS.DESCRIPTION] = `hashtag : ${datasourceName} | query: ${encodeURIComponent(query)}`;

      return $http.get(url, { params: { query: query }, headers }).then(function(response) {
        return response.data.data;
      });
    }
  });
