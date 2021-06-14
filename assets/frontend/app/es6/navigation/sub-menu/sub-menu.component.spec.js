'use strict';

describe('Component: subMenu', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let scope;
  let element;

  let $rootScope;
  let $compile;
  let menuDatasource;
  let $q;

  const template = '<sub-menu></sub-menu>';

  beforeEach(inject(function (_$rootScope_, _$compile_, $state, _menuDatasource_, _$q_, $stateParams) {
    $rootScope = _$rootScope_;
    $compile = _$compile_;
    menuDatasource = _menuDatasource_;
    $q = _$q_;

    mockHelpers.blockUIRouter($state);

    $stateParams.dashboardId = "leads";

    spyOn(menuDatasource, 'getSub').and.callFake(mockHelpers.resolvedPromise($q, [
      {
        "label": "Leads",
        "link": "dashboard",
        "params": {
          "dashboardId": "leads",
          "mainMenuKey": "sales-marketing"
        }
      }, {
        "label": "Accounts",
        "link": "focus-mode",
        "params": {
          "dashboardId": "accounts",
          "mainMenuKey": "marketing-sales"
        }
      }
    ]));

    scope = $rootScope.$new();
    element = angular.element(template);
    element = $compile(element)(scope);

    $rootScope.$apply();
  }));

  it('should compile down to a number of sub-menu-links', function () {
    const mainMenuLinks = element.find('sub-menu-link');
    expect(mainMenuLinks.length).toBe(2);

    const salesElement = $(mainMenuLinks[0]);
    expect(salesElement.attr('label')).toBe('Leads');
    expect(salesElement.attr('link-to')).toBe('dashboard');
    expect(salesElement.attr('params')).toBe('{"dashboardId":"leads","mainMenuKey":"sales-marketing"}');
    expect(salesElement.attr('active')).toBe('true');

    const contractElement = $(mainMenuLinks[1]);
    expect(contractElement.attr('label')).toBe('Accounts');
    expect(contractElement.attr('link-to')).toBe('focus-mode');
    expect(contractElement.attr('params')).toBe('{"dashboardId":"accounts","mainMenuKey":"marketing-sales"}');
    expect(contractElement.attr('active')).toBe('false');

    expect(menuDatasource.getSub).toHaveBeenCalledTimes(1);
  });
});
