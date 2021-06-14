'use strict';

describe('httpInterceptor: exceptionInterceptor', function () {
  beforeEach(module('digitalWorkplaceApp'));

  // instantiate service
  let exceptionInterceptor;

  let $q;
  let exceptionReporter;
  let BACK_END_LOG_URL;

  beforeEach(inject(function (_exceptionInterceptor_, _$q_, _exceptionReporter_, _BACK_END_LOG_URL_) {
    exceptionInterceptor = _exceptionInterceptor_;

    $q = _$q_;
    exceptionInterceptor = _exceptionInterceptor_;
    exceptionReporter = _exceptionReporter_;
    BACK_END_LOG_URL = _BACK_END_LOG_URL_;

    spyOn($q, 'reject');
    spyOn(exceptionReporter, 'report');
  }));

  it('should report an error when the request failed', function () {
    const rejection = { status: 403, config: { url: 'bla/bla/bla' } };

    exceptionInterceptor.responseError(rejection);

    expect(exceptionReporter.report).toHaveBeenCalledTimes(1);
    expect(exceptionReporter.report).toHaveBeenCalledWith({}, 'HTTP error: 403');

    expect($q.reject).toHaveBeenCalledTimes(1);
    expect($q.reject).toHaveBeenCalledWith(rejection);
  });

  it('should not report errors when the url is BACK_END_LOG_URL', function () {
    const rejection = { status: 403, config: { url: BACK_END_LOG_URL } };

    exceptionInterceptor.responseError(rejection);

    expect(exceptionReporter.report).not.toHaveBeenCalled();

    expect($q.reject).toHaveBeenCalledTimes(1);
    expect($q.reject).toHaveBeenCalledWith(rejection);
  });

  it('should not report errors when the status is 401', function () {
    const rejection = { status: 401, config: { url: 'bla/bla/bla' } };

    exceptionInterceptor.responseError(rejection);

    expect(exceptionReporter.report).not.toHaveBeenCalled();

    expect($q.reject).toHaveBeenCalledTimes(1);
    expect($q.reject).toHaveBeenCalledWith(rejection);
  });

  it('should not report errors when there is no config object in the rejection', function () {
    const rejection = { status: 403 };

    exceptionInterceptor.responseError(rejection);

    expect(exceptionReporter.report).not.toHaveBeenCalled();

    expect($q.reject).toHaveBeenCalledTimes(1);
    expect($q.reject).toHaveBeenCalledWith(rejection);
  });
});
