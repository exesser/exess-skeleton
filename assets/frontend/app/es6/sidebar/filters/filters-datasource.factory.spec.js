'use strict';

describe('Factory: filterDatasource - resource http requests', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(mockHelpers.logHeadersInterceptor);

  let $httpBackend;
  let filterDatasource;
  let API_URL;
  let LOG_HEADERS_KEYS;

  beforeEach(inject(function ($state, _filterDatasource_, _$httpBackend_, _API_URL_, _LOG_HEADERS_KEYS_) {
    mockHelpers.blockUIRouter($state);

    $httpBackend = _$httpBackend_;
    filterDatasource = _filterDatasource_;
    API_URL = _API_URL_;
    LOG_HEADERS_KEYS = _LOG_HEADERS_KEYS_;
  }));

  it('should sent a correct request when retrieving the filters', function() {
    const mockResponse = {
      status: 200,
      data: {
        "model": {
          "account_status": "active",
          "person_firstname": "Bogdan",
          "gender": "MALE",
          "legal_form_c": "bvba"
        },
        "fieldGroups": [
          {
            "fields": [
              {
                "id": "account_type",
                "label": "B2B",
                "type": "bool"
              },
              {
                "id": "account_type2",
                "label": "B2C",
                "type": "bool"
              },
              {
                "id": "legal_form_c",
                "label": "Legal Form",
                "type": "enum",
                "generateByServer": "done",
                "module": "Leads",
                "moduleField": "legal_form_c",
                "enumValues": [
                  {
                    "key": "",
                    "value": ""
                  },
                  {
                    "key": "bvba",
                    "value": "bvba"
                  },
                  {
                    "key": "nv",
                    "value": "nv"
                  },
                  {
                    "key": "ebvba",
                    "value": "ebvba"
                  },
                  {
                    "key": "cvba",
                    "value": "cvba"
                  },
                  {
                    "key": "s-bvba",
                    "value": "s-bvba"
                  },
                  {
                    "key": "vof",
                    "value": "vof"
                  },
                  {
                    "key": "gcv",
                    "value": "gcv"
                  },
                  {
                    "key": "cva",
                    "value": "cva"
                  },
                  {
                    "key": "vzw",
                    "value": "vzw"
                  },
                  {
                    "key": "ivzw",
                    "value": "ivzw"
                  }
                ]
              }
            ]
          },
          {
            "fields": [
              {
                "id": "person_firstname",
                "label": "First name",
                "type": "varchar"
              },
              {
                "id": "person_lastname",
                "label": "Last name",
                "type": "varchar"
              }
            ]
          },
          {
            "fields": [
              {
                "id": "gender",
                "label": "Gender",
                "type": "enum",
                "enumValues": [{
                  "key": "UNKNOWN",
                  "value": "Unknown"
                }, {
                  "key": "FEMALE",
                  "value": "FEMALE"
                }, {
                  "key": "MALE",
                  "value": "MALE"
                }]
              }
            ]
          }
        ]
      },
      message: "Success"
    };

    $httpBackend.expectGET(API_URL + 'Filter/accounts_filter_big/accounts_big', function(headers) {
      return headers[LOG_HEADERS_KEYS.DESCRIPTION] === 'Filters: accounts_filter_big | for list: accounts_big';
    }).respond(mockResponse);


    let promiseResolved = false;
    filterDatasource.get({ filterKey: "accounts_filter_big", listKey: "accounts_big" }).then(function(data) {
      expect(data).toEqual(mockResponse.data);
      promiseResolved = true;
    });

    $httpBackend.flush();

    expect(promiseResolved).toBe(true);
    $httpBackend.verifyNoOutstandingExpectation();
    $httpBackend.verifyNoOutstandingRequest();
  });
});
