'use strict';

describe('Component: navigationHistoryAction', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let $state;

  let element;
  let aHrefElement;

  const template = `<navigation-history-action action="action"></navigation-history-action>`;

  beforeEach(inject(function ($rootScope, $compile, _$state_) {
    $state = _$state_;
    const scope = $rootScope.$new(true);
    _.extend(scope, {
      action: {
        label: "recordType (Guidance field)",
        stateName: "dashboard",
        stateParams: { dashboardId: 'view_crud', recordId: 87654321 }
      }
    });
    element = angular.element(template);
    element = $compile(element)(scope);
    $rootScope.$apply();

    spyOn($state, 'go');
    aHrefElement = $(element.find('a'));
  }));

  it('should render the action and navigate on click', function () {
    expect(aHrefElement.text()).toContain('recordType (Guidance field)');
    expect($state.go).toHaveBeenCalledTimes(0);

    aHrefElement.click();

    expect($state.go).toHaveBeenCalledTimes(1);
    expect($state.go).toHaveBeenCalledWith('dashboard', { dashboardId: 'view_crud', recordId: 87654321 });
  });
});
