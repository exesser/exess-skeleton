'use strict';

describe('Factory: bestOfferDatasource - http requests', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(mockHelpers.logHeadersInterceptor);

  let $httpBackend;
  let bestOfferDatasource;
  let API_URL;
  let LOG_HEADERS_KEYS;

  beforeEach(inject(function ($state, _bestOfferDatasource_, _$httpBackend_, _API_URL_, _LOG_HEADERS_KEYS_) {
    mockHelpers.blockUIRouter($state);

    bestOfferDatasource = _bestOfferDatasource_;
    $httpBackend = _$httpBackend_;
    API_URL = _API_URL_;
    LOG_HEADERS_KEYS = _LOG_HEADERS_KEYS_;
  }));

  it('should send a correct request to retrieve a dashboard without id', function() {
    const mockResponse = {
      "status": 200,
      "data": {
        "addresses": [],
        "scripting": "",
        "accountLabel": ""
      },
      "message": "Success"
    };

    $httpBackend.expectGET(API_URL + 'BestOffer/accountId', function(headers) {
      return headers[LOG_HEADERS_KEYS.DESCRIPTION] === 'Best offer for: accountId';
    }).respond(mockResponse);

    let promiseResolved = false;
    bestOfferDatasource.getBestOffers('accountId').then(function(data) {
      expect(data).toEqual(mockResponse.data);
      promiseResolved = true;
    });

    $httpBackend.flush();

    expect(promiseResolved).toBe(true);
    $httpBackend.verifyNoOutstandingExpectation();
    $httpBackend.verifyNoOutstandingRequest();
  });
});
