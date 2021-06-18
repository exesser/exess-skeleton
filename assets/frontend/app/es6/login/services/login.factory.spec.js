'use strict';

describe('Service: loginFactory', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(mockHelpers.logHeadersInterceptor);

  // instantiate service
  let loginFactory;

  let $httpBackend;
  let API_URL;
  let LOG_HEADERS_KEYS;

  beforeEach(inject(function (_loginFactory_, _$httpBackend_, $state, _API_URL_, _LOG_HEADERS_KEYS_) {
    mockHelpers.blockUIRouter($state);

    loginFactory = _loginFactory_;
    API_URL = _API_URL_;
    LOG_HEADERS_KEYS = _LOG_HEADERS_KEYS_;

    $httpBackend = _$httpBackend_;
  }));

  it('should set the correct after login state', function () {
    expect(loginFactory.afterLoginState.name).toBe('dashboard');
    expect(loginFactory.afterLoginState.params).toEqual({ mainMenuKey: 'start', dashboardId: 'home' });
  });

  it('should send the correct login request, and handle a successful response.', function () {
    $httpBackend.expectPOST('/Api/login', function(data) {
      return data === '{ "username": "kristof", "password": "vc" }';
    }, function(headers) {
      return headers['Content-Type'] === 'application/json;charset=utf-8' && headers[LOG_HEADERS_KEYS.DESCRIPTION] === 'login';
    }).respond({ data: "token" });

    loginFactory.login('kristof', 'vc');

    $httpBackend.flush();
  });

  it('should send the correct login logout', function () {
      $httpBackend.expectGET('/Api/logout', function() {
          return '{}';
      }, function(headers) {
          return headers['Content-Type'] === 'application/json;charset=utf-8' && headers[LOG_HEADERS_KEYS.DESCRIPTION] === 'logout';
      }).respond({ data: "token" });
    loginFactory.logout();

    $httpBackend.flush();
  });

  afterEach(function() {
    $httpBackend.verifyNoOutstandingExpectation();
    $httpBackend.verifyNoOutstandingRequest();
  });

});
