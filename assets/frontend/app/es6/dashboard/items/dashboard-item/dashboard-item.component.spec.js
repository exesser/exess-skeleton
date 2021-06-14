'use strict';

describe('Dashboard item: dashboard-item', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let scope;
  let element;

  let $rootScope;
  let $compile;
  let actionDatasource;
  let $q;

  beforeEach(inject(function (_$rootScope_, _$compile_, $state, _actionDatasource_, _$q_) {
    $rootScope = _$rootScope_;
    $compile = _$compile_;
    actionDatasource = _actionDatasource_;
    $q = _$q_;

    mockHelpers.blockUIRouter($state);
  }));

  function compile(template, action) {
    scope = $rootScope.$new();
    scope.action = action;

    element = angular.element(template);
    element = $compile(element)(scope);

    $rootScope.$apply();
  }

  it('should compile down to an amount of three "Open opportunities" with one opportunity and two consumptions.', function () {
    compile(`
      <dashboard-item
        label="Open opportunities"
        amount="3"
        lines='["1 opportunity is due today", "2 consumptions received"]'
        has-warning="false"
        show-checkmark="false">
      </dashboard-item>
    `);

    const dashboardItemElement = element.find('.dashboard__item');
    expect(dashboardItemElement.hasClass('warning')).toBe(false);
    expect(dashboardItemElement.hasClass('cleared')).toBe(false);

    expect(element.find('h5').text()).toBe('Open opportunities');
    expect(element.find('.amount').text()).toBe('3');

    const smalls = element.find('small');
    expect(smalls.length).toBe(2);

    const opportunitySmallElement = $(smalls[0]);
    const consumptionsSmallElement = $(smalls[1]);

    expect(opportunitySmallElement.text()).toBe('1 opportunity is due today');
    expect(consumptionsSmallElement.text()).toBe('2 consumptions received');
  });

  it('should compile down to a warning saying: amount of six "Active quotes" which as four expiring quotes.', function () {
    compile(`
      <dashboard-item
        label="Active quotes"
        amount="6"
        lines='["4 expiring today"]'
        has-warning="true"
        show-checkmark="false">
      </dashboard-item>
    `);

    const dashboardItemElement = element.find('.dashboard__item');
    expect(dashboardItemElement.hasClass('warning')).toBe(true);
    expect(dashboardItemElement.hasClass('cleared')).toBe(false);

    expect(element.find('h5').text()).toBe('Active quotes');
    expect(element.find('.amount').text()).toBe('6');

    const smalls = element.find('small');
    expect(smalls.length).toBe(1);

    const expiringTodaySmallElement = $(smalls[0]);

    expect(expiringTodaySmallElement.text()).toBe('4 expiring today');
  });

  it('should compile down to zero Complaints with a done checkmark.', function () {
    compile(`
      <dashboard-item
        label="Complaints"
        amount="0"
        lines='[]'
        has-warning="false"
        show-checkmark="true">
      </dashboard-item>
    `);

    const dashboardItemElement = element.find('.dashboard__item');
    expect(dashboardItemElement.hasClass('warning')).toBe(false);
    expect(dashboardItemElement.hasClass('cleared')).toBe(true);

    expect(element.find('h5').text()).toBe('Complaints');
    expect(element.find('.amount').text()).toBe('0');

    const smalls = element.find('small');
    expect(smalls.length).toBe(0);
  });

  it('should call the back-end when dashboard-item is clicked', function () {
    // The result of an Action POST to the backend is a command to execute for the frontend.
    const navigateCommand = {
      command: "navigate",
      arguments: {
        route: "dashboard",
        params: {
          mainMenuKey: "sales-marketing",
          dashboardId: "leads"
        }
      }
    };
    spyOn(actionDatasource, 'performAndHandle').and.callFake(mockHelpers.resolvedPromise($q, navigateCommand));

    const template = `
      <dashboard-item
        label="Complaints"
        amount="0"
        lines='[]'
        has-warning="false"
        show-checkmark="true"
        action="action">
      </dashboard-item>
    `;

    const action = {
      id: "42",
      recordId: "1337",
      recordType: "centered-item"
    };

    compile(template, action);

    element.find('.dashboard__item').click();

    expect(actionDatasource.performAndHandle).toHaveBeenCalledTimes(1);
    expect(actionDatasource.performAndHandle).toHaveBeenCalledWith({
      id: "42",
      recordId: "1337",
      recordType: "centered-item"
    });
  });
});
