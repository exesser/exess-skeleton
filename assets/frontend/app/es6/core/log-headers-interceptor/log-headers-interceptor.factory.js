'use strict';

/**
 * @ngdoc service
 * @name digitalWorkplaceApp.logHeadersInterceptor
 * @description
 * # logHeadersInterceptor
 *
 *
 * Factory in the digitalWorkplaceApp is a $http.interceptor.
 */
angular.module('digitalWorkplaceApp')
  .constant('LOG_HEADERS_KEYS', {
    ID: 'X-LOG-ID',
    DESCRIPTION: 'X-LOG-DESCRIPTION',
    COMPONENT: 'X-LOG-COMPONENT',
    MODE: 'X-LOG-MODE',
    DWP_FULL_PATH: 'X-DWP-FULL-PATH'
  })
  .factory('logHeadersInterceptor', function (rfc4122, LOG_HEADERS_KEYS, API_URL, $log, $injector, $location) {
    return {request};

    function request(config) {
      if (_.startsWith(config.url, API_URL) === false) {
        return config;
      }

      if (_.has(config.headers, LOG_HEADERS_KEYS.ID) === false) {
        config.headers[LOG_HEADERS_KEYS.ID] = rfc4122.v4();
      }

      if (_.has(config.headers, LOG_HEADERS_KEYS.DESCRIPTION) === false) {
        config.headers[LOG_HEADERS_KEYS.DESCRIPTION] = 'not added ... yet...';
        $log.log(`no header description for the call made to: ${config.url}`);
      }

      config.headers[LOG_HEADERS_KEYS.DESCRIPTION] += ` | URL: ${config.url}`;

      config.headers[LOG_HEADERS_KEYS.COMPONENT] = 'DWP';
      config.headers[LOG_HEADERS_KEYS.MODE] = $injector.get('$state').current.name;

      if (_.has(config.headers, LOG_HEADERS_KEYS.DWP_FULL_PATH) === false) {
        config.headers[LOG_HEADERS_KEYS.DWP_FULL_PATH] = $location.absUrl();
      }

      return config;
    }
  });
