'use strict';

describe('Factory: sideBarObserver', function () {

  beforeEach(module('digitalWorkplaceApp'));

  let sidebarObserver;
  let SIDEBAR_ELEMENT;

  beforeEach(inject(function (_sidebarObserver_, _SIDEBAR_ELEMENT_) {
    sidebarObserver = _sidebarObserver_;
    SIDEBAR_ELEMENT = _SIDEBAR_ELEMENT_;
  }));

  it('should be able to open a sidebar element', function () {
    const observer = jasmine.createSpy('observer');

    sidebarObserver.registerOpenSidebarElementCallback(observer);
    sidebarObserver.openSidebarElement(SIDEBAR_ELEMENT.PLUS_MENU);

    expect(observer).toHaveBeenCalledTimes(1);
    expect(observer).toHaveBeenCalledWith(SIDEBAR_ELEMENT.PLUS_MENU);
  });

  it('should be able to toggle a sidebar element', function () {
    const observer = jasmine.createSpy('observer');

    sidebarObserver.registerToggleSidebarElementCallback(observer);
    sidebarObserver.toggleSidebarElement(SIDEBAR_ELEMENT.PLUS_MENU);

    expect(observer).toHaveBeenCalledTimes(1);
    expect(observer).toHaveBeenCalledWith(SIDEBAR_ELEMENT.PLUS_MENU);
  });

  it('should be able to close all side bar elements', function () {
    const observer = jasmine.createSpy('observer');

    sidebarObserver.registerCloseAllSideBarElementsCallback(observer);
    sidebarObserver.closeAllSidebarElements();

    expect(observer).toHaveBeenCalledTimes(1);
  });
});
