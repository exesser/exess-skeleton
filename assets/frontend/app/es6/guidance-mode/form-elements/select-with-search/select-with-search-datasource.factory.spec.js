'use strict';

describe('Factory: selectWithSearchDatasource - http requests', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(mockHelpers.logHeadersInterceptor);

  let $httpBackend;
  let selectWithSearchDatasource;
  let API_URL;
  let LOG_HEADERS_KEYS;

  beforeEach(inject(function ($state, _selectWithSearchDatasource_, _$httpBackend_, _API_URL_, _LOG_HEADERS_KEYS_) {
    mockHelpers.blockUIRouter($state);

    $httpBackend = _$httpBackend_;
    selectWithSearchDatasource = _selectWithSearchDatasource_;
    API_URL = _API_URL_;
    LOG_HEADERS_KEYS = _LOG_HEADERS_KEYS_;
  }));

  it('should sent a correct request when retrieving a list of items', function() {
    const mockResponse = {
      status: 200,
      data: {
        rows: [
          {
            key: "6a58d159-c726-3d12-c554-565d68c43da3",
            label: "a.delpriore - Adaline Delpriore"
          },
          {
            key: "31875a77-81ad-66dd-5633-565d695eccb0",
            label: "a.jetter - Agueda Jetter"
          },
          {
            key: "dd520364-4c6c-ca81-c854-565d67f83dcf",
            label: "a.kwong - Antonina Kwong"
          },
          {
            key: "d16dafa0-8611-d334-5c9a-575eb05d755a",
            label: "api_servicemix - Api ServiceMix"
          },
          {
            key: "6529497e-f660-c2bf-9a2d-565d6c1cd7a1",
            label: "c.lesher - Charlyn Lesher"
          },
          {
            key: "8f93ae3f-96ea-247f-244d-565d6824a4c7",
            label: "c.see - Cayla See"
          },
          {
            key: "c4cf9c48-e802-6fd0-903c-565d6ccabbff",
            label: "d.current - Dorthea Current"
          },
          {
            key: "1",
            label: "superadmin -  Administrator"
          },
          {
            key: "437c323f-535a-fcc9-5a92-565d661f9f3a",
            label: "k.bartee - Kathline Bartee"
          },
          {
            key: "db089354-39a6-74c0-7b6f-565d6aaa9f5b",
            label: "k.bechtel - Kaylene Bechtel"
          }
        ],
        pagination: {
          page: 1,
          pages: 2,
          pageSize: 10,
          total: 18
        }
      },
      message: "Success"
    };

    $httpBackend.expectPOST(API_URL + 'SelectWithSearch/Users', {page: 1, query: ''}, function(headers) {
      return headers[LOG_HEADERS_KEYS.DESCRIPTION] === 'Select with search: Users';
    }).respond(mockResponse);

    let promiseResolved = false;
    selectWithSearchDatasource.getSelectOptions('Users', {query: '', page: 1}).then(function (data) {
      expect(data).toEqual(mockResponse.data);
      promiseResolved = true;
    });

    $httpBackend.flush();

    expect(promiseResolved).toBe(true);
    $httpBackend.verifyNoOutstandingExpectation();
    $httpBackend.verifyNoOutstandingRequest();
  });
});
