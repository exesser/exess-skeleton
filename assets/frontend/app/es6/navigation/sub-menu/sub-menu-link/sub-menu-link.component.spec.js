'use strict';

describe('Component: subMenuLink', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let $rootScope;
  let $state;
  let $q;
  let $window;

  let element;
  let menuLink;
  let aHref;

  const template = `<a class="active">Accounts</a>`;

  const menuLinkTemplate = `
    <sub-menu-link
              label="Accounts"
              link-to="dashboard"
              params='{"dashboardId": "accounts", "mainMenuKey": "sales-marketing"}'
              active="true">
    </sub-menu-link>
  `;

  beforeEach(inject(function (_$rootScope_, $compile, _$state_, _$q_, _$window_) {
    $q = _$q_;
    $state = _$state_;
    $window = _$window_;

    spyOn($state, 'transitionTo');

    $rootScope = _$rootScope_;

    const scope = $rootScope.$new();

    element = angular.element(template);

    menuLink = angular.element(menuLinkTemplate);
    menuLink = $compile(menuLink)(scope);

    element.prepend(menuLink);

    $rootScope.$apply();
  }));

  it('should compile down to a link with a class active and icon. It opens in a new window on control-click', function () {
    aHref = $(element.find('a')[0]);

    spyOn($state, 'go').and.callFake(mockHelpers.resolvedPromise($q));
    aHref.click();

    expect(aHref.hasClass('active')).toBe(true);
    expect(aHref.text().trim()).toBe('Accounts');

    expect($state.go).toHaveBeenCalledTimes(1);
    expect($state.go).toHaveBeenCalledWith('dashboard', { dashboardId: "accounts", mainMenuKey: "sales-marketing", query: undefined });

    spyOn($window, 'open');
    const preventDefaultSpy = jasmine.createSpy('preventDefault');
    aHref.trigger({ type: 'contextmenu', ctrlKey: true, preventDefault: preventDefaultSpy });
    $rootScope.$apply();

    expect(preventDefaultSpy).toHaveBeenCalledTimes(1);

    expect($window.open).toHaveBeenCalledTimes(1);
    expect($window.open).toHaveBeenCalledWith('#/sales-marketing/dashboard/accounts/', '_blank');
  });
});
