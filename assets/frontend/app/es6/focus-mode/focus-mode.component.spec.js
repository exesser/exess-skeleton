'use strict';

describe('Component: focusMode', function () {
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
  let previousState;
  let sidebarState;

  const template = '<focus-mode></focus-mode>';

  // Mock the '$stateParams'
  beforeEach(module(function ($provide) {
    $provide.value('$stateParams', {
      focusModeId: 'profit-dashboard',
      recordId: '1337',
      query: 'searchTerm'
    });
  }));

  beforeEach(inject(function (_$rootScope_, _$compile_, _$timeout_, _$q_, $state, $stateParams, _filtersObserver_,
                              _topSearchObserver_, _previousState_, _plusMenuObserver_, _dashboardDatasource_,
                              _$window_, _sidebarState_) {
    $rootScope = _$rootScope_;
    $compile = _$compile_;
    $timeout = _$timeout_;
    $window = _$window_;
    $q = _$q_;

    filtersObserver = _filtersObserver_;
    topSearchObserver = _topSearchObserver_;
    previousState = _previousState_;
    plusMenuObserver = _plusMenuObserver_;
    dashboardDatasource = _dashboardDatasource_;
    sidebarState = _sidebarState_;

    mockHelpers.blockUIRouter($state);

    spyOn(filtersObserver, 'setFilterData');
    spyOn(plusMenuObserver, 'setPlusMenuData');
    spyOn(topSearchObserver, 'setTopSearchData');
  }));

  function compile({ dashboardResponse }) {
    spyOn(dashboardDatasource, 'get').and.callFake(mockHelpers.resolvedPromise($q, dashboardResponse));
    spyOn(sidebarState, 'setShowSidebar');

    scope = $rootScope.$new();

    element = angular.element(template);
    element = $compile(element)(scope);

    $rootScope.$apply();

    // Flush the 'dashboardDatasource.get' timeout;
    $timeout.flush();

    expect(dashboardDatasource.get).toHaveBeenCalledTimes(1);
    expect(dashboardDatasource.get).toHaveBeenCalledWith({
      dashboardId: 'profit-dashboard',
      recordId: '1337',
      queryParams: { query: 'searchTerm' }
    });
  }

  it('should show a grid if the dashboard is fetched, set the sidebar state  and show a title.', function () {
    const dashboardResponse = {
      title: "Colyt Group",
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
    expect(element.find("h4").html()).toBe('Colyt Group');

    expect(sidebarState.setShowSidebar).toHaveBeenCalledTimes(1);
    expect(sidebarState.setShowSidebar).toHaveBeenCalledWith(true);
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

  it('should navigate to the previous state/dashboard', function () {
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

    spyOn(previousState, 'navigateTo');
    spyOn($window.history, 'back');
    compile({ dashboardResponse });

    const buttons = element.find('.top > a');
    const backArrowButton = $(buttons[0]);
    const topArrowButton = $(buttons[1]);

    topArrowButton.click();
    $rootScope.$apply();

    expect(previousState.navigateTo).toHaveBeenCalledTimes(1);
    expect($window.history.back).not.toHaveBeenCalled();

    backArrowButton.click();
    $rootScope.$apply();

    expect(previousState.navigateTo).toHaveBeenCalledTimes(1);
    expect($window.history.back).toHaveBeenCalledTimes(1);
  });
});
