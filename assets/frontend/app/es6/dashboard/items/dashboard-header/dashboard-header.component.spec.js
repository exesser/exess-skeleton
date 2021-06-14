'use strict';

describe('Dashboard item: dashboard-header', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let scope;
  let element;

  let $rootScope;
  let $compile;

  beforeEach(inject(function (_$rootScope_, _$compile_, $state) {
    $rootScope = _$rootScope_;
    $compile = _$compile_;
    mockHelpers.blockUIRouter($state);
  }));

  function compile(template) {
    scope = $rootScope.$new();
    element = angular.element(template);
    element = $compile(element)(scope);

    $rootScope.$apply();
  }

  it('should compile down a header for Sales with an "icon-wijzigen" icon.', function() {
    compile(`
      <dashboard-header label="Sales" icon="icon-wijzigen"></dashboard-header>
    `);

    expect(element.find('.icon-wijzigen').length).toBe(1);
    expect(element.find('h3').text()).toBe('Sales');
  });

  it('should compile down a header for Service with an "icon-previous" icon.', function() {
    compile(`
      <dashboard-header label="Service" icon="icon-previous"></dashboard-header>
    `);

    expect(element.find('.icon-previous').length).toBe(1);
    expect(element.find('h3').text()).toBe('Service');
  });
});
