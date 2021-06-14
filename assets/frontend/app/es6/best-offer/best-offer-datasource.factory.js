'use strict';

/**
 * @ngdoc service
 * @name digitalWorkplaceApp.bestOfferDatasource
 * @description
 *
 * The bestOfferDatasource is a factory whose job it is to provide
 * the data for best offer tool.
 *
 * Factory in the digitalWorkplaceApp.
 */
angular.module('digitalWorkplaceApp')
  .factory('bestOfferDatasource', function (API_URL, $http, LOG_HEADERS_KEYS) {

    return {
      getBestOffers
    };

    /**
     * Gets the contents for a list based on the provided listKey.
     * @param  {String} recordId The account id.
     * @return {Promise} A promise which resolves to the data for the bestOffer tool.
     */
    function getBestOffers(recordId) {
      const headers = {};
      headers[LOG_HEADERS_KEYS.DESCRIPTION] = `Best offer for: ${recordId}`;

      return $http.get(API_URL + `BestOffer/${recordId}`, { headers }).then(function (response) {
        return response.data.data;
      });
    }
  });
