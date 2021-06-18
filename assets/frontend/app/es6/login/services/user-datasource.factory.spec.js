'use strict';

describe('Factory: userDatasource - http requests', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(mockHelpers.logHeadersInterceptor);

  let $httpBackend;
  let userDatasource;
  let LOG_HEADERS_KEYS;

  beforeEach(inject(function ($state, _userDatasource_, _$httpBackend_, _LOG_HEADERS_KEYS_, $location) {
    mockHelpers.blockUIRouter($state);

    $httpBackend = _$httpBackend_;
    userDatasource = _userDatasource_;
    LOG_HEADERS_KEYS = _LOG_HEADERS_KEYS_;

    spyOn($location, 'absUrl').and.returnValue('exesscms.local/dwp/#/start/dashboard/home/');
  }));

  it('should sent a correct request when retrieving the current user ', function() {
    const mockResponse = {
      "status": 200,
      "data": {
        "username": "Tony",
      },
      "message": "Success"
    };

    $httpBackend.expectGET(/^(.*)user\/current\?(.*)/, function(headers) {
      return headers[LOG_HEADERS_KEYS.DESCRIPTION] === 'User preferences'
        && headers[LOG_HEADERS_KEYS.DWP_FULL_PATH] === 'exesscms.local/dwp/#/menu/dashboard/test-dwp/';
    }).respond(mockResponse);

    let promiseResolved = false;

    userDatasource.getUserPreferences({name: 'dashboard', params: {mainMenuKey: 'menu', dashboardId: 'test-dwp'}}).then(function(user) {
      expect(user).toEqual(mockResponse.data);
      promiseResolved = true;
    });

    $httpBackend.flush();

    expect(promiseResolved).toBe(true);
    $httpBackend.verifyNoOutstandingExpectation();
    $httpBackend.verifyNoOutstandingRequest();
  });

  it('should sent a correct request when the current call doesn`t have parameters', function() {
    const mockResponse = {
      "status": 200,
      "data": {
        "username": "Tony",
      },
      "message": "Success"
    };

    $httpBackend.expectGET(/^(.*)user\/current\?(.*)/, function(headers) {
      return headers[LOG_HEADERS_KEYS.DESCRIPTION] === 'User preferences'
        && _.isUndefined(headers[LOG_HEADERS_KEYS.DWP_FULL_PATH]);
    }).respond(mockResponse);

    let promiseResolved = false;

    userDatasource.getUserPreferences().then(function(user) {
      expect(user).toEqual(mockResponse.data);
      promiseResolved = true;
    });

    $httpBackend.flush();

    expect(promiseResolved).toBe(true);
    $httpBackend.verifyNoOutstandingRequest();
    $httpBackend.verifyNoOutstandingExpectation();
  });
});
