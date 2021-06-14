'use strict';

describe('Component: sidebar', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let $rootScope;

  let sidebarAnimation;
  let sidebarState;
  let sidebarObserver;
  let SIDEBAR_ELEMENT;

  let element;
  let plusMenuOutlet;
  let filtersOutlet;
  let miniGuidanceOutlet;

  const template = '<sidebar></sidebar>';

  beforeEach(inject(function (_$rootScope_, $compile, $state, _sidebarAnimation_, _sidebarState_, _sidebarObserver_, _SIDEBAR_ELEMENT_) {
    $rootScope = _$rootScope_;
    sidebarAnimation = _sidebarAnimation_;
    sidebarState = _sidebarState_;
    sidebarObserver = _sidebarObserver_;
    SIDEBAR_ELEMENT = _SIDEBAR_ELEMENT_;

    mockHelpers.blockUIRouter($state);

    const scope = $rootScope.$new();

    element = angular.element(template);
    element = $compile(element)(scope);

    $rootScope.$apply();

    const outlets = element.find('div.sidebar > div');
    expect(outlets.length).toBe(3);

    plusMenuOutlet = $(outlets[0]);
    filtersOutlet = $(outlets[1]);
    miniGuidanceOutlet = $(outlets[2]);

    //We wish to inspect that the sideBar state changes but call through so we get the correct state when we call getActiveSidebarElement.
    spyOn(sidebarState, 'setActiveSidebarElement').and.callThrough();
  }));

  it('should compile down to three router outlets', function () {
    expect(plusMenuOutlet.hasClass('ng-hide')).toBe(true);
    expect(filtersOutlet.hasClass('ng-hide')).toBe(true);
    expect(miniGuidanceOutlet.hasClass('ng-hide')).toBe(true);
  });

  it('should know how to toggle a sidebar element between open and closed', function () {
    spyOn(sidebarAnimation, 'open');

    sidebarObserver.toggleSidebarElement(SIDEBAR_ELEMENT.PLUS_MENU);

    $rootScope.$apply();

    expect(plusMenuOutlet.hasClass('ng-hide')).toBe(false);
    expect(filtersOutlet.hasClass('ng-hide')).toBe(true);
    expect(miniGuidanceOutlet.hasClass('ng-hide')).toBe(true);

    expect(sidebarAnimation.open).toHaveBeenCalledTimes(1);

    expect(sidebarState.setActiveSidebarElement).toHaveBeenCalledTimes(1);
    expect(sidebarState.setActiveSidebarElement).toHaveBeenCalledWith(SIDEBAR_ELEMENT.PLUS_MENU);
  });

  it('should open a sidebarElement when the toggle callback occurs and it is currently closed', function () {
    spyOn(sidebarAnimation, 'open');

    sidebarObserver.toggleSidebarElement(SIDEBAR_ELEMENT.PLUS_MENU);

    $rootScope.$apply();

    expect(plusMenuOutlet.hasClass('ng-hide')).toBe(false);
    expect(filtersOutlet.hasClass('ng-hide')).toBe(true);
    expect(miniGuidanceOutlet.hasClass('ng-hide')).toBe(true);

    expect(sidebarAnimation.open).toHaveBeenCalledTimes(1);

    expect(sidebarState.setActiveSidebarElement).toHaveBeenCalledTimes(1);
    expect(sidebarState.setActiveSidebarElement).toHaveBeenCalledWith(SIDEBAR_ELEMENT.PLUS_MENU);

    spyOn(sidebarAnimation, 'close');

    sidebarObserver.toggleSidebarElement(SIDEBAR_ELEMENT.PLUS_MENU);

    $rootScope.$apply();

    expect(plusMenuOutlet.hasClass('ng-hide')).toBe(true);
    expect(filtersOutlet.hasClass('ng-hide')).toBe(true);
    expect(miniGuidanceOutlet.hasClass('ng-hide')).toBe(true);

    expect(sidebarState.setActiveSidebarElement).toHaveBeenCalledTimes(2);
    expect(sidebarState.setActiveSidebarElement).toHaveBeenCalledWith(null);
  });

  it('should open a sidebarElement when the callback occurs', function () {
    spyOn(sidebarAnimation, 'open');

    sidebarObserver.openSidebarElement(SIDEBAR_ELEMENT.PLUS_MENU);

    $rootScope.$apply();

    expect(plusMenuOutlet.hasClass('ng-hide')).toBe(false);
    expect(filtersOutlet.hasClass('ng-hide')).toBe(true);
    expect(miniGuidanceOutlet.hasClass('ng-hide')).toBe(true);

    expect(sidebarAnimation.open).toHaveBeenCalledTimes(1);

    expect(sidebarState.setActiveSidebarElement).toHaveBeenCalledTimes(1);
    expect(sidebarState.setActiveSidebarElement).toHaveBeenCalledWith(SIDEBAR_ELEMENT.PLUS_MENU);
  });

  it("should close all sidebarElement's when the callback occurs", function () {
    spyOn(sidebarAnimation, 'close');

    sidebarObserver.closeAllSidebarElements();

    $rootScope.$apply();

    expect(plusMenuOutlet.hasClass('ng-hide')).toBe(true);
    expect(filtersOutlet.hasClass('ng-hide')).toBe(true);
    expect(miniGuidanceOutlet.hasClass('ng-hide')).toBe(true);

    expect(sidebarAnimation.close).toHaveBeenCalledTimes(1);
  });
});
