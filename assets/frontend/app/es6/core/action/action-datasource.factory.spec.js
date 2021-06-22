'use strict';

describe('Factory: actionDatasource - http requests', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(mockHelpers.logHeadersInterceptor);

  let $httpBackend;
  let actionDatasource;
  let commandHandler;
  let API_PATH;
  let LOG_HEADERS_KEYS;

  beforeEach(inject(function ($state, _actionDatasource_, _commandHandler_, _$httpBackend_, _API_PATH_, _LOG_HEADERS_KEYS_) {
    mockHelpers.blockUIRouter($state);

    actionDatasource = _actionDatasource_;
    commandHandler = _commandHandler_;
    $httpBackend = _$httpBackend_;
    API_PATH = _API_PATH_;
    LOG_HEADERS_KEYS = _LOG_HEADERS_KEYS_;
  }));

  it('should know how to send an action request', function () {
    const mockResponse = {
      "status": 200,
      "message": "Success",
      "data": {
        "command": "navigate",
        "arguments": {
          "linkTo": "dashboard",
          "params": {
            "mainMenuKey": "sales-marketing",
            "dashboardId": "leads"
          }
        }
      }
    };

    const postBody = { id: "1337", age: "28", name: "Maarten" };

    $httpBackend.expectPOST(API_PATH + 'action/1337', postBody, function (headers) {
      return headers[LOG_HEADERS_KEYS.DESCRIPTION] === 'Perform action: 1337';
    }).respond(mockResponse);

    let promiseResolved = false;
    actionDatasource.perform(postBody).then(function (data) {
      expect(data).toEqual(mockResponse.data);
      promiseResolved = true;
    });

    $httpBackend.flush();

    expect(promiseResolved).toBe(true);
    $httpBackend.verifyNoOutstandingExpectation();
    $httpBackend.verifyNoOutstandingRequest();
  });

  it('should throw an error when no id parameter is provided', function () {
    expect(function () {
      actionDatasource.perform({});
    }).toThrow(new Error("actionDatasource: 'perform' must have an 'id' property defined in the postBody."));
  });

  it('should know how to send an action request and handle it via the commandHandler', function () {
    spyOn(commandHandler, 'handle');

    const mockResponse = {
      "status": 200,
      "message": "Success",
      "data": {
        "command": "navigate",
        "arguments": {
          "linkTo": "dashboard",
          "params": {
            "mainMenuKey": "sales-marketing",
            "dashboardId": "leads"
          }
        }
      }
    };

    const postBody = { id: "1337", age: "28", name: "Maarten" };

    $httpBackend.expectPOST(API_PATH + 'action/1337', postBody).respond(mockResponse);

    actionDatasource.performAndHandle(postBody);

    $httpBackend.flush();

    $httpBackend.verifyNoOutstandingExpectation();
    $httpBackend.verifyNoOutstandingRequest();

    expect(commandHandler.handle).toHaveBeenCalledTimes(1);
    expect(commandHandler.handle).toHaveBeenCalledWith(mockResponse.data);

    $httpBackend.expectPOST(API_PATH + 'action/1337', postBody).respond(mockResponse);
    actionDatasource.performAndHandle(postBody, true);

    $httpBackend.flush();

    expect(commandHandler.handle).toHaveBeenCalledTimes(2);
    expect(commandHandler.handle).toHaveBeenCalledWith(mockResponse.data);
  });
});
