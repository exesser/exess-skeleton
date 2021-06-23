'use strict';

describe('Factory: listDatasource - http requests', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(mockHelpers.logHeadersInterceptor);

  let $httpBackend;
  let listDatasource;
  let API_PATH;
  let LOG_HEADERS_KEYS;

  beforeEach(inject(function ($state, _listDatasource_, _$httpBackend_, _API_PATH_, _LOG_HEADERS_KEYS_) {
    mockHelpers.blockUIRouter($state);

    $httpBackend = _$httpBackend_;
    listDatasource = _listDatasource_;
    API_PATH = _API_PATH_;
    LOG_HEADERS_KEYS = _LOG_HEADERS_KEYS_;
  }));

  it('should send a correct request when retrieving a list', function() {
    const mockResponse = {
      status: 200,
      data: {
        settings: {
          title: "Accounts",
          displayFooter: true
        },
        topBar: {
          selectAll: true,
          buttons: [
            {
              label: "delete",
              type: "CALLBACK",
              icon: "icon-remove",
              action: {
                id: "backend-action-delete"
              },
              enabled: true
            },
            {
              label: "merge",
              icon: "icon-merge",
              type: "CALLBACK",
              action: {
                id: "backend-action-merge"
              },
              enabled: false
            }
          ],
          filters: [
            {
              label: "Only Active",
              key: "ACTIVE"
            },
            {
              label: "Only New",
              key: "NEW"
            }
          ],
          sortingOptions: [
            {
              label: "company name",
              key: "COMPANY_NAME"
            },
            {
              label: "firstname,lastname",
              key: "NAME"
            }
          ]
        },
        headers: [
          {
            label: ""
          },
          {
            label: "Status & type",
            colSize: "1-4"
          },
          {
            label: "company & vat",
            colSize: "1-4"
          },
          {
            label: "Contact",
            colSize: "1-4"
          },
          {
            label: "billing address",
            colSize: "1-4"
          },
          {
            label: ""
          }
        ],
        rows: [
          {
            id: "account__123-123-345",
            class: "test",
            cells: [
              {
                type: "list-checkbox-cell",
                options: {
                  id: "account__123-123-345"
                },
                class: "cell__checkbox"
              },
              {
                type: "list-icon-text-cell",
                options: {
                  iconType: "bedrijf",
                  iconStatus: "prospect",
                  text: "prospect"
                },
                class: "cell__text"
              },
              {
                type: "list-link-bold-top-two-liner-cell",
                options: {
                  line1: "WKY 2",
                  line2: "BE012345678",
                  linkTo: "dashboard",
                  params: {mainMenuKey: "sales-marketing", dashboardId: "account", recordId: "a8d6fbfd-58ce-4f9c-0afe-5735d87a01eb"}
                },
                class: "cell__text"
              },
              {
                type: "list-link-pink-down-two-liner-cell",
                options: {
                  line1: "Bogdan Terzea",
                  line2: "bogdan@terzea.ro",
                  link: "mailto:bogdan@terzea.ro"
                },
                class: "cell__text"
              },
              {
                type: "list-simple-two-liner-cell",
                options: {
                  line1: "Vredestraat 22",
                  line2: "2220 Heist op den berg"
                },
                class: "cell__text"
              },
              {
                type: "list-plus-cell",
                options: {
                  id: "account__123-123-345",
                  gridKey: "more-info"
                },
                class: "cell__action"
              }
            ]
          },
          {
            id: "lead__1234567-1234-234553",
            class: "test",
            cells: [
              {
                type: "list-checkbox-cell",
                options: {
                  id: "lead__1234567-1234-234553"
                },
                class: "cell__checkbox"
              },
              {
                type: "list-icon-text-cell",
                options: {
                  iconType: "particulier",
                  iconStatus: "old-customer",
                  text: "old"
                },
                class: "cell__text"
              },
              {
                type: "list-link-bold-top-two-liner-cell",
                options: {
                  line1: "Hansen RX",
                  line2: "BE04781234",
                  linkTo: "dashboard",
                  params: {mainMenuKey: "sales-marketing", dashboardId: "account", recordId: "a8d6fbfd-58ce-4f9c-0afe-5735d87a01eb"}
                },
                class: "cell__text"
              },
              {
                type: "list-link-pink-down-two-liner-cell",
                options: {
                  line1: "Timmy Hansen",
                  line2: "timmy@hansen.com",
                  link: "mailto:timmy@hansen.com"
                },
                class: "cell__text"
              },
              {
                type: "list-simple-two-liner-cell",
                options: {
                  line1: "Street 22",
                  line2: "3456vv City"
                },
                class: "cell__text"
              },
              {
                type: "list-plus-cell",
                options: {
                  id: "lead__1234567-1234-234553",
                  gridKey: "action-bar"
                },
                class: "cell__action"
              }
            ]
          }
        ],
        pagination: {
          page: 1,
          sortBy: "NAME",
          size: 10
        },
        emptyButton: {
          label: "Add company",
          action: {
            id: "backend-action-delete"
          }
        }
      },
      message: "Success"
    };

    $httpBackend.expectPOST(API_PATH + 'list/1337', { data: "suhnetraam" }, function(headers) {
      return headers[LOG_HEADERS_KEYS.DESCRIPTION] === 'List: 1337';
    }).respond(mockResponse);

    let promiseResolved = false;
    listDatasource.getList({ listKey: '1337', params: { data: "suhnetraam" } }).then(function(data) {
      expect(data).toEqual(mockResponse.data);
      promiseResolved = true;
    });

    $httpBackend.flush();

    expect(promiseResolved).toBe(true);
    $httpBackend.verifyNoOutstandingExpectation();
    $httpBackend.verifyNoOutstandingRequest();
  });

  it('should retrieve the actions when the get method is called', function() {
    const mockResponse = {
      status: 200,
      data: {
        buttons: [
          {
            enabled: true,
            label: "create qoute",
            icon: "icon-quote",
            action: {
              id: "1"
            }
          },
          {
            enabled: false,
            label: "create opportunity",
            icon: "icon-opportunity",
            action: {
              id: "1"
            }
          },
          {
            enabled: true,
            label: "log case",
            icon: "icon-log",
            action: {
              id: "1"
            }
          }
        ]
      },
      message: "Success"
    };

    const actionData = { parentId: "mockId" };
    $httpBackend.expectPOST(API_PATH + 'list/Account/row/bar/1337', { actionData }, function(headers) {
      return headers[LOG_HEADERS_KEYS.DESCRIPTION] === 'List | getActionButtons | recordId: 1337 | recordType: Account';
    }).respond(mockResponse);

    let promiseResolved = false;
    listDatasource.getActionButtons({ recordType: "Account", recordId: "1337", actionData: actionData }).then(function(buttons) {
      expect(buttons).toEqual(mockResponse.data.buttons);
      promiseResolved = true;
    });

    $httpBackend.flush();

    expect(promiseResolved).toBe(true);
    $httpBackend.verifyNoOutstandingExpectation();
    $httpBackend.verifyNoOutstandingRequest();
  });

  it('should sent a correct request when retrieving extra row content', function() {
    const mockResponse = {
      status: 200,
      data: {
        grid: {
          cssClasses: ["cols"],
          columns: [
            {
              size: "1-1",
              hasMargin: false,
              rows: [
                {
                  type: "listRowActions",
                  size: "1-1",
                  options: {
                    listRowId: "12345"
                  }
                }
              ]
            }
          ]
        }
      },
      message: "Success"
    };

    const actionData = { parentId: "mockId" };
    $httpBackend.expectPOST(API_PATH + 'list/list/row/grid/grid/1337', { actionData }, function(headers) {
      return headers[LOG_HEADERS_KEYS.DESCRIPTION] === 'List: list | getExtraRow: grid | recordId: 1337';
    }).respond(mockResponse);

    let promiseResolved = false;
    listDatasource.getExtraRowContent({ gridKey: 'grid', listKey: 'list', itemId: '1337', actionData }).then(function(data) {
      expect(data).toEqual(mockResponse.data);
      promiseResolved = true;
    });

    $httpBackend.flush();

    expect(promiseResolved).toBe(true);
    $httpBackend.verifyNoOutstandingExpectation();
    $httpBackend.verifyNoOutstandingRequest();
  });

  it('should sent a correct request when retrieving extra row content with parentId', function() {
    const mockResponse = {
      status: 200,
      data: {
        grid: {
          cssClasses: ["cols"],
          columns: [
            {
              size: "1-1",
              hasMargin: false,
              rows: [
                {
                  type: "listRowActions",
                  size: "1-1",
                  options: {
                    listRowId: "12345"
                  }
                }
              ]
            }
          ]
        }
      },
      message: "Success"
    };

    const actionData = { parentId: "mockId" };
    $httpBackend.expectPOST(API_PATH + 'list/list/row/grid/grid/1337', { actionData }, function(headers) {
      return headers[LOG_HEADERS_KEYS.DESCRIPTION] === 'List: list | getExtraRow: grid | recordId: 1337';
    }).respond(mockResponse);

    let promiseResolved = false;
    listDatasource.getExtraRowContent({ gridKey: 'grid', listKey: 'list', itemId: '1337',  actionData }).then(function(data) {
      expect(data).toEqual(mockResponse.data);
      promiseResolved = true;
    });

    $httpBackend.flush();

    expect(promiseResolved).toBe(true);
    $httpBackend.verifyNoOutstandingExpectation();
    $httpBackend.verifyNoOutstandingRequest();
  });

  it('should sent a correct response when asking for a CSV export', function() {
    const mockResponse = {
      status: 200,
      data: {
        command: "openModal",
        arguments: { name: "Ken Block" }
      },
      message: "Success"
    };

    $httpBackend.expectPOST(API_PATH + 'list/1337/export/csv', { data: "suhnetraam", recordIds: [1, 2, 3] }, function(headers) {
      return headers[LOG_HEADERS_KEYS.DESCRIPTION] === 'List: 1337 | exportToCSV';
    }).respond(mockResponse);


    let promiseResolved = false;
    listDatasource.exportToCSV({ listKey: '1337', params: {data: "suhnetraam", recordIds: [1, 2, 3] }}).then(function(data) {
      expect(data).toEqual(mockResponse.data);
      promiseResolved = true;
    });

    $httpBackend.flush();

    expect(promiseResolved).toBe(true);
    $httpBackend.verifyNoOutstandingExpectation();
    $httpBackend.verifyNoOutstandingRequest();
  });
});
