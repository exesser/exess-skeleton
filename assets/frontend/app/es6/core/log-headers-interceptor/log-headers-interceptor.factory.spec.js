'use strict';

describe('httpInterceptor: logHeadersInterceptor', function () {
  beforeEach(module('digitalWorkplaceApp'));

  // instantiate service
  let logHeadersInterceptor;
  let LOG_HEADERS_KEYS;
  let API_URL;
  let rfc4122;

  beforeEach(inject(function (_logHeadersInterceptor_, _LOG_HEADERS_KEYS_, _API_URL_, _rfc4122_, $location) {
    rfc4122 = _rfc4122_;
    API_URL = _API_URL_;
    LOG_HEADERS_KEYS = _LOG_HEADERS_KEYS_;
    logHeadersInterceptor = _logHeadersInterceptor_;

    spyOn(rfc4122, 'v4').and.returnValue('123-456-789');
    spyOn($location, 'absUrl').and.returnValue('exesscms.local/dwp/#/start/dashboard/home/');
  }));

  it('should not changes the config if the request is not to API', function () {
    const config = { url: '/img/test', headers: {}};
    const response = logHeadersInterceptor.request(config);

    expect(response).toEqual(config);
  });

  it('should add the headers if they are not available', function () {
    const config = { url: API_URL + 'getList', headers: {}};
    const response = logHeadersInterceptor.request(config);
    const expectedResponse = angular.copy(config);

    expectedResponse.headers[LOG_HEADERS_KEYS.ID] = "123-456-789";
    expectedResponse.headers[LOG_HEADERS_KEYS.DESCRIPTION] = `not added ... yet... | URL: ${config.url}`;
    expectedResponse.headers[LOG_HEADERS_KEYS.COMPONENT] = "DWP";
    expectedResponse.headers[LOG_HEADERS_KEYS.MODE] = "";
    expectedResponse.headers[LOG_HEADERS_KEYS.DWP_FULL_PATH] = "exesscms.local/dwp/#/start/dashboard/home/";

    expect(response).toEqual(expectedResponse);
  });

  it('should NOT replace the headers if they are available', function () {
    const headers = {};
    headers[LOG_HEADERS_KEYS.ID] = "123-456-789-123";
    headers[LOG_HEADERS_KEYS.DESCRIPTION] = "List: accounts";
    headers[LOG_HEADERS_KEYS.DWP_FULL_PATH] = "exesscms.local/dwp/#/original/";

    const config = { url: API_URL + 'getList', headers};
    const response = logHeadersInterceptor.request(config);
    const expectedResponse = angular.copy(config);

    expectedResponse.headers[LOG_HEADERS_KEYS.ID] = "123-456-789-123";
    expectedResponse.headers[LOG_HEADERS_KEYS.DESCRIPTION] = `List: accounts | URL: ${config.url}`;
    expectedResponse.headers[LOG_HEADERS_KEYS.COMPONENT] = "DWP";
    expectedResponse.headers[LOG_HEADERS_KEYS.MODE] = "";
    expectedResponse.headers[LOG_HEADERS_KEYS.DWP_FULL_PATH] = "exesscms.local/dwp/#/original/";

    expect(response).toEqual(expectedResponse);
  });
});
