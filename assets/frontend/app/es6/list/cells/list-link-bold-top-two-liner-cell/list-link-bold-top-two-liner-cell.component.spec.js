'use strict';

describe('Form type: list-link-bold-top-two-liner-cell', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let scope;
  let element;
  let guidanceModalObserver;

  let $rootScope;
  let $window;
  let $compile;
  let $state;
  let $q;

  const template = `
    <list-link-bold-top-two-liner-cell
       line-1="Exesser"
       line-2="ES12345"
       link-to="dashboard"
       params='{"mainMenuKey":"sales-marketing","dashboardId":"account","recordId":"a8d6fbfd-58ce-4f9c-0afe-5735d87a01eb"}'>
    </list-link-bold-top-two-liner-cell>
  `;

  beforeEach(inject(function (_$rootScope_, _$compile_, _$state_, _$q_, _guidanceModalObserver_, _$window_) {
    $rootScope = _$rootScope_;
    $compile = _$compile_;
    $state = _$state_;
    $q = _$q_;
    $window = _$window_;
    guidanceModalObserver = _guidanceModalObserver_;
    spyOn($state, 'transitionTo');

    scope = $rootScope.$new();
    element = angular.element(template);
    element = $compile(element)(scope);

    $rootScope.$apply();
  }));

  it('should compile down to a directive with two headers inside (an h5 and a h6) and a link over the h5', function () {
    expect($(element.find('a')[0]).text()).toContain('Exesser');
    expect($(element.find('h5')[0]).text()).toEqual('Exesser');
    expect($(element.find('h6')[0]).text()).toEqual('ES12345');
  });

  it('should compile down to a link with a class active and icon.', function () {
    spyOn($state, 'go').and.callFake(mockHelpers.resolvedPromise($q));
    spyOn(guidanceModalObserver, 'resetModal');
    spyOn($window, 'open');

    const aHref = $(element.find('a')[0]);
    aHref.click();

    expect(guidanceModalObserver.resetModal).toHaveBeenCalledTimes(1);
    expect($state.go).toHaveBeenCalledTimes(1);
    expect($state.go).toHaveBeenCalledWith('dashboard', {
      mainMenuKey: 'sales-marketing',
      dashboardId: 'account',
      recordId: 'a8d6fbfd-58ce-4f9c-0afe-5735d87a01eb' }
    );

    $(element.find('a')[0]).click();

    const preventDefaultSpy = jasmine.createSpy('preventDefault');
    aHref.trigger({ type: 'contextmenu', ctrlKey: true, preventDefault: preventDefaultSpy });
    $rootScope.$apply();

    expect(preventDefaultSpy).toHaveBeenCalledTimes(1);

    expect($window.open).toHaveBeenCalledTimes(1);
    expect($window.open).toHaveBeenCalledWith('#/sales-marketing/dashboard/account/a8d6fbfd-58ce-4f9c-0afe-5735d87a01eb', '_blank');
  });

});
