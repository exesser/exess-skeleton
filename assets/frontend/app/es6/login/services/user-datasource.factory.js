'use strict';

/**
 * @ngdoc service
 * @name digitalWorkplaceApp.login
 * @description
 * # Login factory handles logging the user in and out.
 * Factory in the digitalWorkplaceApp.
 */
angular.module('digitalWorkplaceApp')
  .factory('userDatasource', function ($http, API_PATH, LOG_HEADERS_KEYS, $state, $location) {

    return { getUserPreferences };

    /**
     * Returns the current user.
     * @return {Promise} A promise which resolves to the current user.
     */
    function getUserPreferences(afterLoginState) {
      const headers = {};
      headers[LOG_HEADERS_KEYS.DESCRIPTION] = 'Current user';
      if (_.isUndefined(afterLoginState) === false
        && _.isUndefined(afterLoginState.name) === false
        && _.isUndefined(afterLoginState.params) === false
      ) {
        headers[LOG_HEADERS_KEYS.DWP_FULL_PATH] =
          _.head(_.split($location.absUrl(), '/#')) + '/' + $state.href(afterLoginState.name, afterLoginState.params);
      }

      return $http.get(API_PATH + 'user/current?' + _.random(1, 99999), {headers}).then(function(response) {
        return response.data.data;
      });
    }
  });
