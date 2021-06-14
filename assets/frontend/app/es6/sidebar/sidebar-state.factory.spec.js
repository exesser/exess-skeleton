'use strict';

describe('Factory: sidebarState', function () {
  beforeEach(module('digitalWorkplaceApp'));

  let sidebarState;
  let SIDEBAR_ELEMENT;

  beforeEach(inject(function (_sidebarState_, _SIDEBAR_ELEMENT_) {
    sidebarState = _sidebarState_;
    SIDEBAR_ELEMENT = _SIDEBAR_ELEMENT_;
  }));

  it('getActiveSidebarElement should initially return null', function() {
    expect(sidebarState.getActiveSidebarElement()).toBe(null);
  });

  it('getActiveSidebarElement should return the new value after it has been set', function() {
    sidebarState.setActiveSidebarElement(SIDEBAR_ELEMENT.PLUS_MENU);
    expect(sidebarState.getActiveSidebarElement()).toBe(SIDEBAR_ELEMENT.PLUS_MENU);
  });

  it('getShowSidebar should initially return true', function() {
    expect(sidebarState.getShowSidebar()).toBe(true);
  });

  it('getShowSidebar should return the new value after it has been set', function() {
    sidebarState.setShowSidebar(false);
    expect(sidebarState.getShowSidebar()).toBe(false);
  });
});
