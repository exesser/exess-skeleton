'use strict';

describe('Component: navigationHistoryRenderer', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let $rootScope;

  let element;
  let navigationHistoryAction;

  let navigationHistoryContainer;

  const template = "<navigation-history-renderer></navigation-history-renderer>";

  beforeEach(inject(function (_$rootScope_, $compile, _navigationHistoryContainer_) {
    $rootScope = _$rootScope_;
    navigationHistoryContainer = _navigationHistoryContainer_;

    spyOn(navigationHistoryContainer, 'getActions').and.returnValue([
      {
        label: "recordId (Guidance field)",
        stateName: "dashboard.",
        stateParams: { dashboardId: 'view_crud', recordId: 12345678 }
      },
      {
        label: "recordType (Guidance field)",
        stateName: "dashboard.",
        stateParams: { dashboardId: 'view_crud', recordId: 87654321 }
      }
    ]);

    spyOn(navigationHistoryContainer, 'getShowActions').and.returnValue(true);
    spyOn(navigationHistoryContainer, 'setShowActions');

    const scope = $rootScope.$new();
    element = angular.element(template);
    element = $compile(element)(scope);
    $rootScope.$apply();

    expect(navigationHistoryContainer.getActions).toHaveBeenCalledTimes(1);
    expect(navigationHistoryContainer.getShowActions).toHaveBeenCalledTimes(1);
    expect(navigationHistoryContainer.setShowActions).not.toHaveBeenCalled();

    navigationHistoryAction = element.find("navigation-history-action");
  }));

  it('should render two actions', function () {
    expect(navigationHistoryAction.length).toBe(2);
    expect($(navigationHistoryAction[0]).find("a").text()).toContain("recordType (Guidance field)");
    expect($(navigationHistoryAction[1]).find("a").text()).toContain("recordId (Guidance field)");
  });

  it('should hide the actions', function () {
    const containerDiv = $(element.find('div.navigation-history-container'));
    const aHrefElement = $(element.find('a.button'));

    expect(containerDiv.hasClass('ng-hide')).toBe(false);
    expect(navigationHistoryContainer.setShowActions).not.toHaveBeenCalled();

    aHrefElement.click();

    expect(containerDiv.hasClass('ng-hide')).toBe(true);
    expect(navigationHistoryContainer.setShowActions).toHaveBeenCalledTimes(1);
    expect(navigationHistoryContainer.setShowActions).toHaveBeenCalledWith(false);

    aHrefElement.click();

    expect(containerDiv.hasClass('ng-hide')).toBe(false);
    expect(navigationHistoryContainer.setShowActions).toHaveBeenCalledTimes(2);
    expect(navigationHistoryContainer.setShowActions).toHaveBeenCalledWith(true);
  });

});
