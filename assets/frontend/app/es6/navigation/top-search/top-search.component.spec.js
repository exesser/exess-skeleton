'use strict';

describe('Component: topSearch', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let element;
  let topSearchObserver;
  let $state;
  let $rootScope;

  const template = "<top-search></top-search>";
  let inputElement;

  beforeEach(inject(function (_$state_, _$rootScope_, $compile, _topSearchObserver_) {
    topSearchObserver = _topSearchObserver_;
    $state = _$state_;
    $rootScope = _$rootScope_;
    const scope = $rootScope.$new(true);

    spyOn($state, 'transitionTo');

    element = angular.element(template);
    element = $compile(element)(scope);

    $rootScope.$apply();
    inputElement = element.find("input");
  }));

  it('should render and input with type `search`', function () {
    expect(inputElement.attr('type')).toBe('search');
  });

  it('should navigate to new page when `linkTo` and `params` are NOT null', function () {
    spyOn($state, 'go');

    const searchData = {
      display: true,
      linkTo: "dashboard",
      params: {
        mainMenuKey: "sales-marketing",
        dashboardId: "search"
      }
    };
    topSearchObserver.setTopSearchData(searchData);

    inputElement.val('wky').change();
    inputElement.triggerHandler({ type: 'keyup', keyCode: 13 });

    $rootScope.$apply();

    expect($state.go).toHaveBeenCalledTimes(1);
    expect($state.go).toHaveBeenCalledWith(searchData.linkTo, searchData.params);
  });

  it('should not navigate if `linkTo` or `params` is null', function () {
    spyOn($state, 'go');

    topSearchObserver.setTopSearchData({
      linkTo: null,
      params: null
    });

    inputElement.val('wky').change();
    inputElement.triggerHandler({ type: 'keyup', keyCode: 13 });

    $rootScope.$apply();

    expect($state.go).not.toHaveBeenCalled();
  });
});
