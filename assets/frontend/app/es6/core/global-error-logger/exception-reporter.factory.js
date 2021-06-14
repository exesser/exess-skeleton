'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.factory:exceptionReporter
 * @description
 * # exceptionReporter
 *
 * The exceptionReporter factory is used to report exceptions that occurred
 * in Angular to the back-end. This reporter enriches the error that
 * is reported with the current url of the browser, and the $stateParams
 * of ui-router.
 *
 * Factory of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .constant('BACK_END_LOG_URL', 'log/error')
  .factory('exceptionReporter', function(ENV, API_URL, $injector, $stateParams, BACK_END_LOG_URL, LOG_HEADERS_KEYS) {
    /*
      Cannot inject $http and $location directly because of
      circular dependency injection errors.
    */
    let $http = null;
    let $location = null;

    // Tracks what the last error was that was sent to the back-end.
    let lastReport = null;

    /*
      We want to make sure that this request does not trigger the loading bar
      see: https://github.com/chieffancypants/angular-loading-bar.
    */
    const config = { ignoreLoadingBar: true, headers: {} };

    return { report };

    /**
     * Reports an error to the back-end by sending a HTTP Post request
     * with the relevant error info that was available
     * @param  {Object} error The error object which needs to be sent to the back-end.
     * @param  {String} name The name of the error which needs to be sent to the back-end.
     * @return {Void}
     */
    function report(error, name) {
      // Only report errors when in production.
      if (ENV.name !== 'production') {
        return;
      }

      // If $http is null get it once via the injector manually.
      if (_.isNull($http)) {
        $http = $injector.get("$http");
      }

      // If $location is null get it once via the injector manually.
      if (_.isNull($location)) {
        $location = $injector.get("$location");
      }

      const stateInfo = { url: $location.absUrl(), state: $stateParams };

      const errorName = { name };

      // If there is a stacktrace use the first sentence of the stack.
      if (_.isEmpty(error.stack) === false) {
        errorName.name = _(error.stack).split('\n').first();
      }

      const report = _.merge({}, error, stateInfo, errorName);

      /*
        If the reported error is the same as the last sent error we do
        not send it again twice in a row. This also fixes a bug in
        Internet Explorer that caused it to go into an infinite loop.
      */
      if (_.isEqual(lastReport, report) === false) {
        const headers = { [LOG_HEADERS_KEYS.DESCRIPTION]: `Report error: ${report.name}` };
        const newConfig = _.merge({}, config, { headers });

        $http.post(API_URL + BACK_END_LOG_URL, report, newConfig);
      }

      lastReport = report;
    }
  });
