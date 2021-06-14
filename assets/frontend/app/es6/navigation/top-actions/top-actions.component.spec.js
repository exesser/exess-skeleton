'use strict';

describe('Component: topActions', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let element;
  let miniGuidanceButton;
  let filtersButton;
  let plusMenuButton;
  let primaryButton;
  let topActionState;
  let sidebarState;
  let sidebarObserver;
  let primaryButtonObserver;
  let guidanceModeBackendState;
  let SIDEBAR_ELEMENT;

  let $rootScope;
  const template = "<top-actions></top-actions>";

  beforeEach(inject(function ($state, _$rootScope_, $compile, _topActionState_, _sidebarState_, _sidebarObserver_,
                              _primaryButtonObserver_, _SIDEBAR_ELEMENT_, _guidanceModeBackendState_) {
    $rootScope = _$rootScope_;
    topActionState = _topActionState_;
    sidebarState = _sidebarState_;
    sidebarObserver = _sidebarObserver_;
    primaryButtonObserver = _primaryButtonObserver_;
    guidanceModeBackendState = _guidanceModeBackendState_;
    SIDEBAR_ELEMENT = _SIDEBAR_ELEMENT_;

    mockHelpers.blockUIRouter($state);

    const scope = $rootScope.$new(true);
    element = angular.element(template);
    element = $compile(element)(scope);

    $rootScope.$apply();

    const buttons = element.find('a');
    expect(buttons.length).toBe(4);

    miniGuidanceButton = $(buttons[0]);
    filtersButton = $(buttons[1]);
    plusMenuButton = $(buttons[2]);
    primaryButton = $(buttons[3]);
  }));

  it('should render four options', function () {
    expect(miniGuidanceButton.hasClass('icon-log')).toBe(true);
    expect(filtersButton.hasClass('icon-filters')).toBe(true);
    expect(plusMenuButton.hasClass('icon-plus')).toBe(true);

    expect(filtersButton.hasClass('ng-hide')).toBe(true);
    expect(miniGuidanceButton.hasClass('ng-hide')).toBe(true);
    expect(plusMenuButton.hasClass('ng-hide')).toBe(true);
    expect(primaryButton.hasClass('ng-hide')).toBe(true);

    expect(filtersButton.hasClass('is-active')).toBe(false);
    expect(miniGuidanceButton.hasClass('is-active')).toBe(false);
    expect(plusMenuButton.hasClass('is-active')).toBe(false);

    expect(primaryButton.attr("id")).toBe('primaryButton'); //Used in external tests
  });

  describe("What actions should happen when the buttons are clicked", function () {
    it('should open the filters when the filters button is clicked', function () {
      spyOn(sidebarObserver, 'toggleSidebarElement');

      filtersButton.click();
      $rootScope.$apply();

      expect(sidebarObserver.toggleSidebarElement).toHaveBeenCalledTimes(1);
      expect(sidebarObserver.toggleSidebarElement).toHaveBeenCalledWith(SIDEBAR_ELEMENT.FILTERS);
    });

    it('should open the mini guidance when the mini guidance button is clicked', function () {
      spyOn(sidebarObserver, 'toggleSidebarElement');

      miniGuidanceButton.click();
      $rootScope.$apply();

      expect(sidebarObserver.toggleSidebarElement).toHaveBeenCalledTimes(1);
      expect(sidebarObserver.toggleSidebarElement).toHaveBeenCalledWith(SIDEBAR_ELEMENT.MINI_GUIDANCE);
    });

    it('should open the plus menu when the plus menu button is clicked', function () {
      spyOn(sidebarObserver, 'toggleSidebarElement');

      plusMenuButton.click();
      $rootScope.$apply();

      expect(sidebarObserver.toggleSidebarElement).toHaveBeenCalledTimes(1);
      expect(sidebarObserver.toggleSidebarElement).toHaveBeenCalledWith(SIDEBAR_ELEMENT.PLUS_MENU);
    });

    it('should notify the primaryButton observer when the primary button is clicked', function () {
      spyOn(primaryButtonObserver, 'primaryButtonClicked');

      primaryButton.click();
      $rootScope.$apply();

      expect(primaryButtonObserver.primaryButtonClicked).toHaveBeenCalledTimes(1);
    });

    it('should NOT notify the primaryButton observer the guidance is invalid', function () {
      spyOn(primaryButtonObserver, 'primaryButtonClicked');
      spyOn(topActionState, 'getPrimaryButtonData').and.returnValue({ disabled: true });

      primaryButton.click();
      $rootScope.$apply();

      expect(primaryButtonObserver.primaryButtonClicked).not.toHaveBeenCalled();
    });

    it('should NOT notify the primaryButton observer when the backend is busy', function () {
      spyOn(primaryButtonObserver, 'primaryButtonClicked');
      spyOn(guidanceModeBackendState, 'getBackendIsBusy').and.returnValue(true);

      primaryButton.click();
      $rootScope.$apply();

      expect(primaryButtonObserver.primaryButtonClicked).not.toHaveBeenCalled();
    });
  });

  describe("When the buttons should be active", function () {
    it('should hide the filters button when the filters cannot be opened', function () {
      spyOn(sidebarState, 'getActiveSidebarElement').and.returnValue(SIDEBAR_ELEMENT.FILTERS);

      $rootScope.$apply();

      expect(filtersButton.hasClass('is-active')).toBe(true);
      expect(sidebarState.getActiveSidebarElement).toHaveBeenCalled();
    });

    it('should hide the mini guidance button when the mini guidance cannot be opened', function () {
      spyOn(sidebarState, 'getActiveSidebarElement').and.returnValue(SIDEBAR_ELEMENT.MINI_GUIDANCE);

      $rootScope.$apply();

      expect(miniGuidanceButton.hasClass('is-active')).toBe(true);
      expect(sidebarState.getActiveSidebarElement).toHaveBeenCalled();
    });

    it('should hide the plus menu button when the plus menu cannot be opened', function () {
      spyOn(sidebarState, 'getActiveSidebarElement').and.returnValue(SIDEBAR_ELEMENT.PLUS_MENU);

      $rootScope.$apply();

      expect(plusMenuButton.hasClass('is-active')).toBe(true);
      expect(sidebarState.getActiveSidebarElement).toHaveBeenCalled();
    });
  });

  describe('When the buttons should be shown', function () {
    it('should show the filters button when the filters can be opened', function () {
      spyOn(topActionState, 'filtersCanBeOpened').and.returnValue(true);

      $rootScope.$apply();

      expect(filtersButton.hasClass('ng-hide')).toBe(false);

      expect(topActionState.filtersCanBeOpened).toHaveBeenCalled();
    });

    it('should show the mini guidance button when the mini guidance can be opened', function () {
      spyOn(topActionState, 'miniGuidanceCanBeOpened').and.returnValue(true);

      $rootScope.$apply();

      expect(miniGuidanceButton.hasClass('ng-hide')).toBe(false);

      expect(topActionState.miniGuidanceCanBeOpened).toHaveBeenCalled();
    });

    it('should show the plus menu button when the plus menu can be opened', function () {
      spyOn(topActionState, 'plusMenuCanBeOpened').and.returnValue(true);

      $rootScope.$apply();

      expect(plusMenuButton.hasClass('ng-hide')).toBe(false);

      expect(topActionState.plusMenuCanBeOpened).toHaveBeenCalled();
    });

    it('should show the primary button when the topActionState property is not null', function () {
      spyOn(topActionState, 'getPrimaryButtonData').and.returnValue({ data: 42 });

      $rootScope.$apply();

      expect(primaryButton.hasClass('ng-hide')).toBe(false);

      expect(topActionState.getPrimaryButtonData).toHaveBeenCalled();
    });
  });
});
