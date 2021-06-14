'use strict';

/*
  Decorates Angular's $exceptionHandler, which is responsible for
  logging errors that occur to the console. This decorator adds one
  extra behavior: sending the errors to the back-end.
*/
angular.module('digitalWorkplaceApp')
  .decorator("$exceptionHandler", function($delegate, exceptionReporter) {
    return function(exception, cause) {
      const error = { stack: exception.stack, cause };
      exceptionReporter.report(error);

      /*
        For some reason it is not possible to properly unit test this
        call to $delegate. The reason for this is that $exceptionHandler
        is mocked by ngMock by default, and for some reason this prevents
        us from using our normal mocking strategies.

        I've already sunken 3 hours of my life into the hellhole which
        is unit testing $exceptionHandler decorators. Since I failed
        miserably, I can only ask you not to remove the next line of
        code, so the default behavior of $exceptionHandler is still
        executed.

                                                -- Signed Maarten Hus
      */
      $delegate(exception, cause);
    };
  });
