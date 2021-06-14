'use strict';

/**
 * @ngdoc service
 * @name digitalWorkplaceApp.exceptionInterceptor
 * @description
 * # exceptionInterceptor
 *
 * Whenever an error occurs during a network request this is reported
 * back to the server.
 *
 * Factory in the digitalWorkplaceApp is a $http.interceptor.
 */
angular.module('digitalWorkplaceApp')
  .factory('exceptionInterceptor', function (exceptionReporter, $q, BACK_END_LOG_URL) {
    return { responseError };

    /**
     * Whenever a HTTP request fails for some reason this error is then
     * reported back to the back-end. This way we can make sure that
     * we can detect configuration errors more quickly.
     * @param  {Object} rejection The rejection object explaining what went wrong.
     */
    function responseError(rejection) {
      /*
        When the back-end is down we will get stuck in a loop when we
        try to report an error, because reporting an error will cause
        a new error, because the server is down.

        To prevent this we will not report on errors which happen
        when reporting an error.

        If the 'rejection.config' which contains the url is undefined
        we do not report the error. In Internet Explorer the config
        is sometimes not available.

        When the response is 401 is not an error, is just Unauthorized
      */
      if (_.isUndefined(rejection.config) === false) {
        if (_.endsWith(rejection.config.url, BACK_END_LOG_URL) === false && rejection.status !== 401) {
          exceptionReporter.report({}, `HTTP error: ${rejection.status}`);
        }
      }

      return $q.reject(rejection);
    }
  });
