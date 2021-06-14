'use strict';

describe('Factory: uploadDatasource - http requests', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(mockHelpers.logHeadersInterceptor);

  let $httpBackend;
  let uploadDatasource;
  let API_URL;
  let LOG_HEADERS_KEYS;

  beforeEach(inject(function ($state, _uploadDatasource_, _$httpBackend_, _API_URL_, _LOG_HEADERS_KEYS_) {
    mockHelpers.blockUIRouter($state);

    $httpBackend = _$httpBackend_;
    uploadDatasource = _uploadDatasource_;
    API_URL = _API_URL_;
    LOG_HEADERS_KEYS = _LOG_HEADERS_KEYS_;
  }));

  it('should sent a correct request for deleting file', function() {
    const postBody = {
      docGuid: "123",
      model: {
        "dwp|storeDoc|miscellaneous|tariff_sheet_id": "cae094ac-fd8e-c691-12f9-59b6e5af5dda",
        "dwp|storeDoc|miscellaneous|language": "nl_BE"
      }
    };
    const mockResponse = {
      status: 200,
      data: {
        docGuid: "123",
        customerId: null,
        miscProps: [{
          "key": "tariff_sheet_id",
          "value": "cae094ac-fd8e-c691-12f9-59b6e5af5dda",
          "operator": "DELETE"
        }, {
          "key": "language",
          "value": "nl_BE",
          "operator": "DELETE"
        }]
      },
      message: "Success"
    };

    $httpBackend.expectPOST(API_URL + 'filedelete', postBody, function (headers) {
        return headers[LOG_HEADERS_KEYS.DESCRIPTION] === 'Remove file';
    }).respond(mockResponse);

    let promiseResolved = false;
    uploadDatasource.removeFile(postBody).then(function (data) {
        expect(data).toEqual(mockResponse.data);
        promiseResolved = true;
    });

    $httpBackend.flush();

    expect(promiseResolved).toBe(true);
    $httpBackend.verifyNoOutstandingExpectation();
    $httpBackend.verifyNoOutstandingRequest();
  });
});
