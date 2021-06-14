'use strict';

describe('Component: dashboard', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let scope;
  let element;

  let $rootScope;
  let $compile;
  let $q;
  let $timeout;
  let $window;

  let filtersObserver;
  let plusMenuObserver;
  let topSearchObserver;
  let dashboardDatasource;
  let menuDatasource;
  let slideAnimation;
  let sidebarState;
  let navigationHistoryContainer;

  const template = '<dashboard></dashboard>';

  // Mock the '$stateParams'
  beforeEach(module(function ($provide) {
    $provide.value('$stateParams', {
      dashboardId: 'profit-dashboard',
      recordId: '1337',
      query: 'searchTerm'
    });
  }));

  beforeEach(inject(function (_$rootScope_, _$compile_, _$timeout_, _$q_, $state, $stateParams, _filtersObserver_,
                              _topSearchObserver_, _plusMenuObserver_, _dashboardDatasource_, _slideAnimation_,
                              _menuDatasource_, _sidebarState_, _$window_, _navigationHistoryContainer_) {
    $rootScope = _$rootScope_;
    $window = _$window_;
    $compile = _$compile_;
    $timeout = _$timeout_;
    $q = _$q_;

    $window.innerWidth = '1000';

    filtersObserver = _filtersObserver_;
    topSearchObserver = _topSearchObserver_;
    plusMenuObserver = _plusMenuObserver_;
    dashboardDatasource = _dashboardDatasource_;
    menuDatasource = _menuDatasource_;
    slideAnimation = _slideAnimation_;
    sidebarState = _sidebarState_;
    navigationHistoryContainer = _navigationHistoryContainer_;

    mockHelpers.blockUIRouter($state);

    spyOn(filtersObserver, 'setFilterData');
    spyOn(menuDatasource, 'getMain').and.callFake(mockHelpers.resolvedPromise($q, {}));
    spyOn(menuDatasource, 'getSub').and.callFake(mockHelpers.resolvedPromise($q, [{
      "label": "Outbound",
      "link": "dashboard",
      "params": {
        "dashboardId": "tasks_outbound",
        "mainMenuKey": "tasks",
        "recordId": null
      }
    }, {
      "label": "Move",
      "link": "dashboard",
      "params": {
        "dashboardId": "tasks_move",
        "mainMenuKey": "tasks",
        "recordId": null
      }
    }, {
      "label": "Written",
      "link": "dashboard",
      "params": {
        "dashboardId": "tasks_written",
        "mainMenuKey": "tasks",
        "recordId": null
      }
    }, {
      "label": "Triage",
      "link": "dashboard",
      "params": {
        "dashboardId": "tasks_triage",
        "mainMenuKey": "tasks",
        "recordId": null
      }
    }, {
      "label": "Uncategorized customer care",
      "link": "dashboard",
      "params": {
        "dashboardId": "tasks_customer_care_delta",
        "mainMenuKey": "tasks",
        "recordId": null
      }
    }, {
      "label": "Uncategorized customer care",
      "link": "dashboard",
      "params": {
        "dashboardId": "tasks_customer_care_delta",
        "mainMenuKey": "tasks",
        "recordId": null
      }
    }, {
      "label": "Uncategorized customer care",
      "link": "dashboard",
      "params": {
        "dashboardId": "tasks_customer_care_delta",
        "mainMenuKey": "tasks",
        "recordId": null
      }
    }, {
      "label": "Uncategorized customer care",
      "link": "dashboard",
      "params": {
        "dashboardId": "tasks_customer_care_delta",
        "mainMenuKey": "tasks",
        "recordId": null
      }
    }]));
    spyOn(plusMenuObserver, 'setPlusMenuData');
    spyOn(topSearchObserver, 'setTopSearchData');
  }));

  afterEach(function () {
    $window.innerWidth = '400';
  });

  function compile({ dashboardResponse }) {
    spyOn(slideAnimation, 'close');
    spyOn(sidebarState, 'setShowSidebar');

    spyOn(dashboardDatasource, 'get').and.callFake(mockHelpers.resolvedPromise($q, dashboardResponse));

    scope = $rootScope.$new();

    element = angular.element(template);
    element = $compile(element)(scope);

    $rootScope.$apply();

    const topMenu = element.find('.top');
    expect(topMenu.hasClass('mobile-menu')).toBeFalsy();
    expect(topMenu.hasClass('hide-menu')).toBeTruthy();

    // Flush the 'dashboardDatasource.get' timeout;
    $timeout.flush();

    expect(dashboardDatasource.get).toHaveBeenCalledTimes(1);
    expect(dashboardDatasource.get).toHaveBeenCalledWith({
      dashboardId: 'profit-dashboard',
      recordId: '1337',
      queryParams: { query: 'searchTerm', recordType: undefined }
    });
  }

  it('should show a grid if the dashboard is fetched, set the sidebar state and close the slideAnimation on init.', function () {
    const dashboardResponse = {
      plusMenu: {
        display: false
      },
      filters: {
        display: false
      },
      search: {
        display: false
      },
      grid: {
        columns: [{
          size: "1-1",
          rows: [{
            size: "1-1",
            type: "awesome-thing",
            options: {
              title: "Awesome title!",
              description: "Awesome description!"
            }
          }]
        }]
      }
    };

    compile({ dashboardResponse });

    expect(element.find('awesome-thing').length).toBe(1);

    expect(slideAnimation.close).toHaveBeenCalledTimes(1);
    expect(sidebarState.setShowSidebar).toHaveBeenCalledTimes(1);
    expect(sidebarState.setShowSidebar).toHaveBeenCalledWith(true);

    const topMenu = element.find('.top');
    expect(topMenu.hasClass('mobile-menu')).toBeTruthy();
    expect(topMenu.hasClass('hide-menu')).toBeFalsy();

  });

  it('should inform the observers that data is available', function () {
    const dashboardResponse = {
      plusMenu: {
        display: true,
        buttonGroups: [
          {
            label: "Create Lead",
            buttons: [
              {
                enabled: true,
                label: "Business",
                icon: "icon-werkbakken",
                actionId: "guidance_to_create_lead",
                recordId: 42
              }
            ]
          }
        ],
        buttons: [
          {
            enabled: true,
            label: "Go to price screen",
            icon: "icon-werkbakken",
            actionId: "go_to_price_screen"
          }
        ]
      },
      filters: {
        display: true,
        filterKey: "accounts_filters_big",
        listKey: "Accounts"
      },
      search: {
        display: true,
        linkTo: "dashboard",
        params: {
          mainMenuKey: "sales-marketing",
          dashboardId: "search"
        }
      },
      grid: {
        columns: [{
          size: "1-1",
          rows: [{
            size: "1-1",
            type: "awesome-thing",
            options: {
              title: "Awesome title!",
              description: "Awesome description!"
            }
          }]
        }]
      }
    };

    compile({ dashboardResponse });

    expect(filtersObserver.setFilterData).toHaveBeenCalledTimes(1);
    expect(filtersObserver.setFilterData).toHaveBeenCalledWith('accounts_filters_big', 'Accounts');

    expect(plusMenuObserver.setPlusMenuData).toHaveBeenCalledTimes(1);
    expect(plusMenuObserver.setPlusMenuData).toHaveBeenCalledWith({
      buttonGroups: [
        {
          label: "Create Lead",
          buttons: [
            {
              enabled: true,
              label: "Business",
              icon: "icon-werkbakken",
              actionId: "guidance_to_create_lead",
              recordId: 42
            }
          ]
        }
      ],
      buttons: [
        {
          enabled: true,
          label: "Go to price screen",
          icon: "icon-werkbakken",
          actionId: "go_to_price_screen"
        }
      ]
    });

    expect(topSearchObserver.setTopSearchData).toHaveBeenCalledTimes(1);
    expect(topSearchObserver.setTopSearchData).toHaveBeenCalledWith(dashboardResponse.search);
  });

  it('should not inform the observers if nothing is displayed', function () {
    const dashboardResponse = {
      plusMenu: {
        display: false
      },
      filters: {
        display: false
      },
      search: {
        display: false
      },
      grid: {
        columns: [{
          size: "1-1",
          rows: [{
            size: "1-1",
            type: "awesome-thing",
            options: {
              title: "Awesome title!",
              description: "Awesome description!"
            }
          }]
        }]
      }
    };

    compile({ dashboardResponse });

    expect(filtersObserver.setFilterData).not.toHaveBeenCalled();

    expect(plusMenuObserver.setPlusMenuData).not.toHaveBeenCalled();

    expect(topSearchObserver.setTopSearchData).not.toHaveBeenCalled();
  });

  it('should inform navigationHistoryContainer if has a base bean', function () {
    spyOn(navigationHistoryContainer, 'addAction');

    const dashboardResponse = {
      plusMenu: {
        display: false
      },
      filters: {
        display: false
      },
      search: {
        display: false
      },
      dashboardBaseBean: "recordId (Field)",
      grid: {
        columns: [{
          size: "1-1",
          rows: [{
            size: "1-1",
            type: "awesome-thing",
            options: {
              title: "Awesome title!",
              description: "Awesome description!"
            }
          }]
        }]
      }
    };

    expect(navigationHistoryContainer.addAction).not.toHaveBeenCalled();

    compile({ dashboardResponse });

    expect(navigationHistoryContainer.addAction).toHaveBeenCalledTimes(1);
  });
});
