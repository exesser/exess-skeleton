'use strict';

describe('Config: global-error-logger', function () {
  beforeEach(module('digitalWorkplaceApp'));

  let $rootScope;
  let $compile;

  let exceptionReporter;

  beforeEach(inject(function ($state, _$rootScope_, _$compile_, _exceptionReporter_) {
    mockHelpers.blockUIRouter($state);

    $rootScope = _$rootScope_;
    $compile = _$compile_;
    exceptionReporter = _exceptionReporter_;
  }));

  it('should when an error occurs send the error to the back-end', function (done) {
    spyOn(exceptionReporter, 'report');

    const template = `<div ng-class="{ 'error">`;

    const scope = $rootScope.$new();

    let element = angular.element(template);

    try {
      $compile(element)(scope);
    } catch (e) { // For some reason we must declare 'e' otherwise babel complains
      expect(exceptionReporter.report).toHaveBeenCalledTimes(1);

      const error = exceptionReporter.report.calls.argsFor(0)[0];
      expect(error.cause).toBe(`<div ng-class="{ 'error" class="ng-scope">`);

      // Cannot check for a reasonable value because jasmine also wraps error messages.
      expect(error.stack).not.toBe(undefined);

      done();
    }
  });
});
