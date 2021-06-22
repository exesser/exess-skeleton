'use strict';

describe('Factory: dashboardDatasource - http requests', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(mockHelpers.logHeadersInterceptor);

  let $httpBackend;
  let $timeout;
  let dashboardDatasource;
  let commandHandler;
  let API_PATH;
  let LOG_HEADERS_KEYS;

  beforeEach(inject(function ($state, _dashboardDatasource_, _$httpBackend_, _API_PATH_, _LOG_HEADERS_KEYS_, _$timeout_, _commandHandler_) {
    mockHelpers.blockUIRouter($state);

    dashboardDatasource = _dashboardDatasource_;
    commandHandler = _commandHandler_;
    $httpBackend = _$httpBackend_;
    $timeout = _$timeout_;
    API_PATH = _API_PATH_;
    LOG_HEADERS_KEYS = _LOG_HEADERS_KEYS_;
  }));

  it('should send a correct request to retrieve a dashboard without id', function() {
    const mockResponse = {
      "status": 200,
      "data": {
        "columns": [{
          "size": "1-1",
          "hasMargin": false,
          "rows": [{
            "size": "1-1",
            "type": "leadsList",
            "options": {}
          }]
        }]
      },
      "message": "Success"
    };

    $httpBackend.expectGET(API_PATH + 'dashboard/leads').respond(mockResponse);

    let promiseResolved = false;
    dashboardDatasource.get({ dashboardId: "leads" }).then(function(data) {
      expect(data).toEqual(mockResponse.data);
      promiseResolved = true;
    });

    $httpBackend.flush();

    expect(promiseResolved).toBe(true);
    $httpBackend.verifyNoOutstandingExpectation();
    $httpBackend.verifyNoOutstandingRequest();
  });

  it('should send execute command when dashboard errors', function() {
    const mockCommand = {
      command: "navigate",
      arguments: {
        linkTo: "dashboard"
      }
    };

    const mockResponse = {
      status: 404,
      data: {
        command: mockCommand
      },
      message: "Success"
    };

    spyOn(commandHandler, 'handle');
    $httpBackend.expectGET(API_PATH + 'dashboard/leads').respond(function () {
      return [404, mockResponse, {}, 'Not found'];
    });

    let promiseResolved = false;
    dashboardDatasource.get({ dashboardId: "leads" }).then(function() {
      promiseResolved = true;
    });

    $httpBackend.flush();
    $timeout.flush();

    expect(promiseResolved).toBe(true);
    $httpBackend.verifyNoOutstandingExpectation();
    $httpBackend.verifyNoOutstandingRequest();

    expect(commandHandler.handle).toHaveBeenCalledTimes(1);
    expect(commandHandler.handle).toHaveBeenCalledWith(mockCommand);
  });

  it('should not execute command when dashboard errors but no command found', function() {
    const mockResponse = {
      status: 404,
      message: "Success"
    };

    spyOn(commandHandler, 'handle');
    $httpBackend.expectGET(API_PATH + 'dashboard/leads').respond(function () {
      return [404, mockResponse, {}, 'Not found'];
    });

    let promiseResolved = false;
    dashboardDatasource.get({ dashboardId: "leads" }).then(function() {
      promiseResolved = true;
    });

    $httpBackend.flush();
    $timeout.flush();

    expect(promiseResolved).toBe(true);
    $httpBackend.verifyNoOutstandingExpectation();
    $httpBackend.verifyNoOutstandingRequest();

    expect(commandHandler.handle).toHaveBeenCalledTimes(0);
  });

  it('should send a correct request when it has query params', function () {
    $httpBackend.expectGET(API_PATH + 'dashboard/leads?query=yolo').respond(true);

    dashboardDatasource.get({ dashboardId: "leads", queryParams: { query: 'yolo'} });

    $httpBackend.flush();

    $httpBackend.verifyNoOutstandingExpectation();
    $httpBackend.verifyNoOutstandingRequest();
  });

  it('should send a correct request to retrieve a dashboard with id', function() {
    const mockResponse = {
      "status": 200,
      "data": {
        "columns": [{
          "size": "1-4",
          "hasMargin": false,
          "cssClasses": ["card", "blue", "contact-info"],
          "rows": [{
            "size": "1-1",
            "type": "detailInfo",
            "options": {
              "recordId": "e0998a48-1137",
              "dashboardName": "lead",
              "cardName": "lead"
            }
          }]
        }, {
          "size": "3-4",
          "hasMargin": false,
          "rows": [{
            "size": "1-2",
            "type": "opportunityInfo",
            "cssClasses": ["card", "light"],
            "options": {
              "recordId": "e0998a48-1137",
              "dashboardName": "lead",
              "cardName": "opportunities"
            }
          }, {
            "size": "1-2",
            "type": "quoteInfo",
            "cssClasses": ["card"],
            "options": {
              "recordId": "e0998a48-1137",
              "dashboardName": "lead",
              "cardName": "quotes"
            }
          }]
        }]
      },
      "message": "Success"
    };

    $httpBackend.expectGET(API_PATH + 'dashboard/lead/e0998a48-1137?query=yolo', function(headers) {
      return headers[LOG_HEADERS_KEYS.DESCRIPTION] === 'Dashboard: lead | recordId: e0998a48-1137';
    }).respond(mockResponse);

    let promiseResolved = false;
    dashboardDatasource.get({ dashboardId: "lead", recordId: "e0998a48-1137", queryParams: { query: 'yolo'} }).then(function(data) {
      expect(data).toEqual(mockResponse.data);
      promiseResolved = true;
    });

    $httpBackend.flush();

    expect(promiseResolved).toBe(true);
    $httpBackend.verifyNoOutstandingExpectation();
    $httpBackend.verifyNoOutstandingRequest();
  });
});
