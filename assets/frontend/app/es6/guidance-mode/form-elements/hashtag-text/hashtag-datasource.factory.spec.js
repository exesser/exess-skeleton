'use strict';

describe('Factory: hashtagDatasource - http requests', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(mockHelpers.logHeadersInterceptor);

  let $httpBackend;
  let hashtagDatasource;
  let API_URL;
  let LOG_HEADERS_KEYS;

  beforeEach(inject(function ($state, _hashtagDatasource_, _$httpBackend_, _API_URL_, _LOG_HEADERS_KEYS_) {
    mockHelpers.blockUIRouter($state);

    $httpBackend = _$httpBackend_;
    hashtagDatasource = _hashtagDatasource_;
    API_URL = _API_URL_;
    LOG_HEADERS_KEYS = _LOG_HEADERS_KEYS_;
  }));

  it('should sent a correct request searching for hashtagas', function() {
    const mockResponse = {
      "status": 200,
      "data": [{
        "id": "46c15ec6-7c30-a1b7-3e0a-564efd6363ef",
        "label": "Betaling",
        "hashtag": "Payment",
        "replacement": "The Payment"
      }, {
        "id": "122141541-7c30-a1b7-3e0a-564efd6363ef",
        "label": "Betaaltelefoon",
        "hashtag": "Payphone",
        "replacement": "The Payment"
      }],
      "message": "Success"
    };

    $httpBackend.expectGET(API_URL + 'hashtags/blaat?query=hello+world', function(headers) {
      return headers[LOG_HEADERS_KEYS.DESCRIPTION] === 'hashtag : blaat | query: hello%20world';
    }).respond(mockResponse);

    let promiseResolved = false;
    hashtagDatasource.search('blaat', 'hello world').then(function (data) {
      expect(data).toEqual(mockResponse.data);
      promiseResolved = true;
    });

    $httpBackend.flush();

    expect(promiseResolved).toBe(true);

    $httpBackend.verifyNoOutstandingExpectation();
    $httpBackend.verifyNoOutstandingRequest();
  });
});
