'use strict';

describe('Factory: exceptionReporter', function () {
  beforeEach(module('digitalWorkplaceApp'));

  const ENV = {
    name: 'production'
  };

  beforeEach(module('digitalWorkplaceApp', function ($provide) {
    $provide.constant('ENV', ENV);
  }));

  let $http;
  let $httpBackend;
  let exceptionReporter;
  let API_URL;
  let LOG_HEADERS_KEYS;

  let expectedConfig;

  beforeEach(inject(function ($state, _exceptionReporter_, _$http_, _$httpBackend_, _API_URL_, _LOG_HEADERS_KEYS_) {
    mockHelpers.blockUIRouter($state);

    expectedConfig = { ignoreLoadingBar: true, headers: {} };

    exceptionReporter = _exceptionReporter_;
    $http = _$http_;
    $httpBackend = _$httpBackend_;
    API_URL = _API_URL_;
    LOG_HEADERS_KEYS = _LOG_HEADERS_KEYS_;
  }));

  describe('ENV: production', () => {
    it('should send the errors to the back-end', function () {
      const report = {
        name: "Error between desktop and keyboard",
        cause: "developer stupidity",
        url: "http://server/",
        state: {}
      };

      $httpBackend.expectPOST(API_URL + 'log/error', report).respond(200);
      spyOn($http, 'post').and.callThrough();

      const error = { cause: "developer stupidity" };
      const name = 'Error between desktop and keyboard';

      expectedConfig.headers[LOG_HEADERS_KEYS.DESCRIPTION] = `Report error: ${name}`;

      exceptionReporter.report(error, name);

      $httpBackend.flush();

      $httpBackend.verifyNoOutstandingExpectation();
      $httpBackend.verifyNoOutstandingRequest();

      expect($http.post).toHaveBeenCalledTimes(1);
      expect($http.post.calls.argsFor(0)[2]).toEqual(expectedConfig);
    });

    it('should when there is a stack use the first sentence of the stack as a name', function () {
      const report = {
        name: "one big stack",
        stack: "one big stack\nit is really big",
        cause: "developer stupidity",
        url: "http://server/",
        state: {}
      };

      $httpBackend.expectPOST(API_URL + 'log/error', report).respond(200);
      spyOn($http, 'post').and.callThrough();

      const error = { stack: "one big stack\nit is really big", cause: "developer stupidity" };
      const name = 'Error between desktop and keyboard';

      exceptionReporter.report(error, name);

      expectedConfig.headers[LOG_HEADERS_KEYS.DESCRIPTION] = `Report error: ${report.name}`;

      $httpBackend.flush();

      $httpBackend.verifyNoOutstandingExpectation();
      $httpBackend.verifyNoOutstandingRequest();

      expect($http.post).toHaveBeenCalledTimes(1);
      expect($http.post.calls.argsFor(0)[2]).toEqual(expectedConfig);
    });

    it('should not report the same error twice in a row', function () {
      const report = {
        name: "Error between desktop and keyboard",
        cause: "developer stupidity",
        url: "http://server/",
        state: {}
      };

      $httpBackend.expectPOST(API_URL + 'log/error', report).respond(200);
      spyOn($http, 'post').and.callThrough();

      const error = { cause: "developer stupidity" };
      const name = 'Error between desktop and keyboard';

      // Report it twice
      exceptionReporter.report(error, name);
      exceptionReporter.report(error, name);

      expect($http.post).toHaveBeenCalledTimes(1);
    });
  });

  describe('ENV: development', () => {
    it('should not send the errors to the back-end', () => {
      ENV.name = 'development';

      spyOn($http, 'post').and.callThrough();

      const error = { cause: "developer stupidity" };
      const name = 'Error between desktop and keyboard';

      exceptionReporter.report(error, name);

      expect($http.post).not.toHaveBeenCalled();
    });
  });
});
