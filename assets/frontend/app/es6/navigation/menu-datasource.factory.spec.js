'use strict';

describe('Factory: menuDatasource - http requests', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(mockHelpers.logHeadersInterceptor);


  let $httpBackend;
  let menuDatasource;
  let API_PATH;
  let LOG_HEADERS_KEYS;

  beforeEach(inject(function ($state, _menuDatasource_, _$httpBackend_, _API_PATH_, _LOG_HEADERS_KEYS_) {
    mockHelpers.blockUIRouter($state);

    $httpBackend = _$httpBackend_;
    menuDatasource = _menuDatasource_;
    API_PATH = _API_PATH_;
    LOG_HEADERS_KEYS = _LOG_HEADERS_KEYS_;
  }));

  it('should sent a correct request when retrieving a list of available menus', function() {
    const mockResponse = {
      "status": 200,
      "data": [
        {
          "name": "sales-marketing",
          "link": "dashboard",
          "params": {
              "mainMenuKey": "sales-marketing",
              "dashboardId": "sales-marketing"
          },
          "icon": "icon-winkelwagen"
        },
        {
          "name": "contracting-switching",
          "link": "dashboard",
          "params": {
              "mainMenuKey": "contracting-switching",
              "dashboardId": "contracting-switching"
          },
          "icon": "icon-contract"
        },
        {
          "name": "billing",
          "link": "dashboard",
          "params": {
              "mainMenuKey": "billing",
              "dashboardId": "billing"
          },
          "icon": "icon-euro"
        },
        {
          "name": "credit-collection",
          "link": "dashboard",
          "params": {
              "mainMenuKey": "credit-collection",
              "dashboardId": "credit-collection"
          },
          "icon": "icon-credit"
        },
        {
          "name": "finance",
          "link": "dashboard",
          "params": {
              "mainMenuKey": "finance",
              "dashboardId": "finance"
          },
          "icon": "icon-finance"
        },
        {
          "name": "service-complaint",
          "link": "dashboard",
          "params": {
              "mainMenuKey": "service-complaint",
              "dashboardId": "service-complaint"
          },
          "icon": "icon-agent"
        },
        {
          "name": "admin",
          "link": "dashboard",
          "params": {
              "mainMenuKey": "admin",
              "dashboardId": "admin"
          },
          "icon": "icon-mappen"
        },
        {
          "name": "test",
          "link": "dashboard",
          "params": {
              "mainMenuKey": "test",
              "dashboardId": "test"
          },
          "icon": "icon-instellingen"
        }
      ],
      "message": "Success"
    };

    $httpBackend.expectGET(API_PATH + 'menu', function(headers) {
      return headers[LOG_HEADERS_KEYS.DESCRIPTION] === 'Main menu';
    }).respond(mockResponse);

    let promiseResolved = false;
    menuDatasource.getMain().then(function(mainMenus) {
      expect(mainMenus).toEqual(mockResponse.data);
      promiseResolved = true;
    });

    $httpBackend.flush();

    expect(promiseResolved).toBe(true);
    $httpBackend.verifyNoOutstandingExpectation();
    $httpBackend.verifyNoOutstandingRequest();
  });

  it('should sent a correct request when retrieving a list of submenus', function() {
    const mockResponse = {
      status: 200,
      data: [{
        "label": "Leads",
        "link": "dashboard",
        "params": {
          "dashboardId": "leads",
          "mainMenuKey": "sales-marketing"
        }
      }, {
        "label": "Accounts",
        "link": "dashboard",
        "params": {
          "dashboardId": "accounts",
          "mainMenuKey": "sales-marketing"
        }
      }, {
        "label": "Contracts",
        "link": "dashboard",
        "params": {
          "dashboardId": "contracts",
          "mainMenuKey": "sales-marketing"
        }
      }],
      message: "Success"
    };

    $httpBackend.expectGET(API_PATH + 'menu/sales-marketing', function(headers) {
      return headers[LOG_HEADERS_KEYS.DESCRIPTION] === 'Submenu for: sales-marketing';
    }).respond(mockResponse);

    let promiseResolved = false;
    menuDatasource.getSub("sales-marketing").then(function(subMenus) {
      expect(subMenus).toEqual(mockResponse.data);
      promiseResolved = true;
    });

    $httpBackend.flush();

    expect(promiseResolved).toBe(true);
    $httpBackend.verifyNoOutstandingExpectation();
    $httpBackend.verifyNoOutstandingRequest();
  });
});
