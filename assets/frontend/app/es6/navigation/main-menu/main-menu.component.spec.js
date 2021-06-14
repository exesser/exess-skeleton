'use strict';

describe('Component: mainMenu', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let scope;
  let element;

  let $rootScope;
  let $compile;
  let menuDatasource;
  let $q;

  const template = '<main-menu></main-menu>';

  beforeEach(inject(function (_$rootScope_, _$compile_, $state, _menuDatasource_, _$q_, $stateParams) {
    $rootScope = _$rootScope_;
    $compile = _$compile_;
    menuDatasource = _menuDatasource_;
    $q = _$q_;

    mockHelpers.blockUIRouter($state);

    $stateParams.mainMenuKey = "sales-marketing";

    spyOn(menuDatasource, 'getMain').and.callFake(mockHelpers.resolvedPromise($q, [
      {
        "name": "Sales & Marketing",
        "link": "dashboard",
        "params": {
          "mainMenuKey": "sales-marketing",
          "dashboardId": "sales-marketing"
        },
        "icon": "icon-winkelwagen"
      },
      {
        "name": "Contract & Switching",
        "link": "focus-mode",
        "params": {
          "mainMenuKey": "contracting-switching",
          "dashboardId": "contracting-switching"
        },
        "icon": "icon-contract"
      }
    ]));

    scope = $rootScope.$new();
    element = angular.element(template);
    element = $compile(element)(scope);

    $rootScope.$apply();
  }));

  it('should compile down to a number of main-menu-links', function () {
    const mainMenuLinks = element.find('main-menu-link');
    expect(mainMenuLinks.length).toBe(2);

    const salesElement = $(mainMenuLinks[0]);
    expect(salesElement.attr('link-to')).toBe('dashboard');
    expect(salesElement.attr('params')).toBe('mainMenu.params');
    expect(salesElement.attr('icon')).toBe('icon-winkelwagen');
    expect(salesElement.attr('active')).toBe('mainMenu.active');
    expect(salesElement.attr('name')).toBe('Sales & Marketing');

    const contractElement = $(mainMenuLinks[1]);
    expect(contractElement.attr('link-to')).toBe('focus-mode');
    expect(contractElement.attr('params')).toBe('mainMenu.params');
    expect(contractElement.attr('icon')).toBe('icon-contract');
    expect(contractElement.attr('active')).toBe('mainMenu.active');
    expect(contractElement.attr('name')).toBe('Contract & Switching');

    expect(menuDatasource.getMain).toHaveBeenCalledTimes(1);
  });
});
