'use strict';

describe('Component: mainMenuLink', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let $rootScope;
  let $state;
  let $q;
  let $window;

  let element;
  let aHref;

  const template = `
    <main-menu-link
      link-to="dashboard"
      params='controller.params'
      icon="icon-instellingen"
      active="controller.active"
      name="Sales & Marketing">
    </main-menu-link>
  `;

  beforeEach(inject(function (_$rootScope_, $compile, _$state_, _$q_,  _$window_) {
    $q = _$q_;
    $state = _$state_;
    $window =  _$window_;

    spyOn($state, 'transitionTo');

    $rootScope = _$rootScope_;

    const scope = $rootScope.$new();

    scope.controller = {
      params: {
        "mainMenuKey": "sales-marketing",
        "dashboardId": "sales-marketing"
      },
      active: false
    };

    element = angular.element(template);
    element = $compile(element)(scope);

    $rootScope.$apply();

    aHref = $(element.find('a')[0]);
  }));

  it('should compile down to a link with a tooltip text and an icon', function () {
    expect(aHref.hasClass('active')).toBe(false);

    const spanElements = element.find('span');
    expect(spanElements.length).toBe(2);

    const iconSpan = $(spanElements[0]);
    expect(iconSpan.hasClass('icon-instellingen')).toBe(true);

    const toolTipSpan = $(spanElements[1]);
    expect(toolTipSpan.text()).toEqual('Sales & Marketing');
  });

  it('should compile down to a link with a class active, a tooltip text, and icon. It opens in a new window on control-click', function () {
    spyOn($state, 'go').and.callFake(mockHelpers.resolvedPromise($q));

    aHref.click();
    $rootScope.$apply();

    expect($state.go).toHaveBeenCalledTimes(1);
    expect($state.go).toHaveBeenCalledWith('dashboard', {
      "mainMenuKey": "sales-marketing",
      "dashboardId": "sales-marketing"
    });

    spyOn($window, 'open');
    const preventDefaultSpy = jasmine.createSpy('preventDefault');
    aHref.trigger({ type: 'contextmenu', ctrlKey: true, preventDefault: preventDefaultSpy });
    $rootScope.$apply();

    expect(preventDefaultSpy).toHaveBeenCalledTimes(1);

    expect($window.open).toHaveBeenCalledTimes(1);
    expect($window.open).toHaveBeenCalledWith('#/sales-marketing/dashboard/sales-marketing/', '_blank');
  });
});
