'use strict';

describe('Factory: blueSidebarDatasource - http requests', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(mockHelpers.logHeadersInterceptor);

  let $httpBackend;
  let blueSidebarDatasource;
  let API_PATH;
  let LOG_HEADERS_KEYS;

  beforeEach(inject(function ($state, _blueSidebarDatasource_, _$httpBackend_, _API_PATH_, _LOG_HEADERS_KEYS_) {
    mockHelpers.blockUIRouter($state);

    blueSidebarDatasource = _blueSidebarDatasource_;
    $httpBackend = _$httpBackend_;
    API_PATH = _API_PATH_;
    LOG_HEADERS_KEYS = _LOG_HEADERS_KEYS_;
  }));

  it('should send a correct request to retrieve a dashboard without id', function() {
    const mockResponse = {
      "status": 200,
      "message": "Success",
      "data": {
        "name": "Colruyt Group",
        "id": "123456789",
        "street": "Kroonstraat",
        "houseNumber": "81",
        "bus": "1",
        "addition": "b",
        "postalCode": "3020",
        "city": "Herent",
        "enterprise_number": "BE123456789",
        "gender": "Mrs.",
        "first_name": "Annemie",
        "last_name": "Van Den Broecke",
        "function": "Chief financial officer",
        "phone": "0032 15 316124",
        "mobile": "0032 486326874",
        "e-mail": "annemie@colruyt.be",
        "language": "Dutch",
        "contracts_elec": "3",
        "contracts_gas": "9",
        "arrowLink": {
          "title": "Dashboard",
          "linkTo": "dashboard",
          "params": {
            "mainMenuKey": "sales-marketing",
            "dashboardId": "leads"
          }
        },
        "buttons": [{
          "title": "Leads",
          "icon": "icon-bedrijf",
          "linkTo": "dashboard",
          "params": {
            "mainMenuKey": "sales-marketing",
            "dashboardId": "leads"
          }
        }, {
          "title": "Quotes",
          "icon": "icon-quote",
          "linkTo": "dashboard",
          "params": {
            "mainMenuKey": "sales-marketing",
            "dashboardId": "leads"
          }
        }, {
          "title": "Werkbakken",
          "icon": "icon-werkbakken",
          "linkTo": "dashboard",
          "params": {
            "mainMenuKey": "sales-marketing",
            "dashboardId": "leads"
          }
        }, {
          "title": "Log",
          "icon": "icon-log",
          "linkTo": "dashboard",
          "params": {
            "mainMenuKey": "sales-marketing",
            "dashboardId": "leads"
          }
        }],
        "messages": [
          "example message 1",
          "example message 2"
        ]
      }
    };

    $httpBackend.expectGET(API_PATH + 'sidebar/husky/1337', function(headers) {
      return headers[LOG_HEADERS_KEYS.DESCRIPTION] === 'get sidebar for husky:1337';
    }).respond(mockResponse);

    let promiseResolved = false;
    blueSidebarDatasource.get({ recordType: "husky", id: 1337 }).then(function(data) {
      expect(data).toEqual(mockResponse.data);
      promiseResolved = true;
    });

    $httpBackend.flush();

    expect(promiseResolved).toBe(true);
    $httpBackend.verifyNoOutstandingExpectation();
    $httpBackend.verifyNoOutstandingRequest();
  });
});
