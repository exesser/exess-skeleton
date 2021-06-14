'use strict';

describe('Dashboard item: dashboard-centered-item', function () {
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

  it('should compile down to "icon-bedrijf" with a birthday on tomorrow.', function () {
    compile(`
      <dashboard-centered-item
        icon="icon-bedrijf"
        line="Birthday on"
        bold-line="Tomorrow">
      </dashboard-centered-item>
    `);

    expect(element.find('.icon-bedrijf').length).toBe(1);
    expect($(element.find('p')[0]).text()).toBe('Birthday on');
    expect(element.find('strong').text()).toBe('Tomorrow');
  });

  it('should compile down to "icon-previous" with a next invoice and a date.', function () {
    compile(`
      <dashboard-centered-item
        icon="icon-previous"
        line="Next invoice"
        bold-line="01/05/2016">
      </dashboard-centered-item>
    `);

    expect(element.find('.icon-previous').length).toBe(1);
    expect($(element.find('p')[0]).text()).toBe('Next invoice');
    expect(element.find('strong').text()).toBe('01/05/2016');
  });

  it('should call the back-end when centered-item is clicked', function () {
    // The result of an Action POST to the backend is a command to execute for the frontend.
    const navigateCommand = {
      "command": "navigate",
      "arguments": {
        "route": "dashboard",
        "params": {
          "mainMenuKey": "sales-marketing",
          "dashboardId": "leads"
        }
      }
    };
    spyOn(actionDatasource, 'performAndHandle').and.callFake(mockHelpers.resolvedPromise($q, navigateCommand));

    const template = `
      <dashboard-centered-item
        icon="icon-bedrijf"
        line="Birthday on"
        bold-line="Tomorrow"
        action="action">
      </dashboard-centered-item>
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
