'use strict';

describe('Form type: list-link-bold-bottom-two-liner-cell', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let scope;
  let element;
  let guidanceModalObserver;

  let $rootScope;
  let $compile;
  let $state;
  let $q;

  const template = `
    <list-link-bold-bottom-two-liner-cell
       line-1="ES12345"
       line-2="Exesser"
       link-to="dashboard"
       params='{"mainMenuKey":"sales-marketing","dashboardId":"account","recordId":"a8d6fbfd-58ce-4f9c-0afe-5735d87a01eb"}'>
    </list-link-bold-bottom-two-liner-cell>
  `;

  beforeEach(inject(function (_$rootScope_, _$compile_, _$state_, _$q_, _guidanceModalObserver_) {
    $rootScope = _$rootScope_;
    $compile = _$compile_;
    $state = _$state_;
    $q = _$q_;
    guidanceModalObserver = _guidanceModalObserver_;
    spyOn($state, 'transitionTo');

    scope = $rootScope.$new();
    element = angular.element(template);
    element = $compile(element)(scope);

    $rootScope.$apply();
  }));

  it('should compile down to a directive with two headers inside (an h5 and a h6) and a link over the h5', function () {
    expect($(element.find('a')[0]).text()).toEqual('Exesser');
    expect($(element.find('h5')[0]).text()).toEqual('Exesser');
    expect($(element.find('h6')[0]).text()).toEqual('ES12345');
  });

  it('should compile down to a link with a class active and icon.', function () {
    spyOn($state, 'go').and.callFake(mockHelpers.resolvedPromise($q));
    spyOn(guidanceModalObserver, 'resetModal');
    $(element.find('a')[0]).click();

    expect(guidanceModalObserver.resetModal).toHaveBeenCalledTimes(1);
    expect($state.go).toHaveBeenCalledTimes(1);
    expect($state.go).toHaveBeenCalledWith('dashboard', {
      mainMenuKey: 'sales-marketing',
      dashboardId: 'account',
      recordId: 'a8d6fbfd-58ce-4f9c-0afe-5735d87a01eb' }
    );
  });
});
