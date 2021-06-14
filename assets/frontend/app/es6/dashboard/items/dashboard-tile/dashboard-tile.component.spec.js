'use strict';

describe('Dashboard item: dashboard-tile', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let scope;
  let element;

  let $rootScope;
  let $compile;
  let actionDatasource;

  beforeEach(inject(function (_$rootScope_, _$compile_, $state, _actionDatasource_) {
    $rootScope = _$rootScope_;
    $compile = _$compile_;
    actionDatasource = _actionDatasource_;

    mockHelpers.blockUIRouter($state);
  }));

  function compile(template, action) {
    scope = $rootScope.$new();
    scope.action = action;

    element = angular.element(template);
    element = $compile(element)(scope);

    $rootScope.$apply();
  }

  it('should render correct the template and perform the action', function () {
    spyOn(actionDatasource, 'performAndHandle');

    compile(`
      <dashboard-tile
        class="m-tile"
        icon="icon-finance"
        title="Finance"
        value="1"
        text="1 invoice"
        button="more info"
        action="action">
      </dashboard-tile>
    `, {
      id: "42",
      recordId: "1337"
    });

    const icon = element.find('.m-tile__icon');

    expect(icon.hasClass('icon-finance')).toBe(true);
    expect(icon.hasClass('icon__big')).toBe(false);

    expect(element.find('.m-tile__title').text()).toBe('Finance');
    expect(element.find('.m-tile__value').text()).toBe('1');
    expect(element.find('p').text()).toBe('1 invoice');

    const buttonElement = element.find('.m-tile__button');

    expect(buttonElement.text()).toBe('more info');

    expect(actionDatasource.performAndHandle).not.toHaveBeenCalled();

    buttonElement.click();

    expect(actionDatasource.performAndHandle).toHaveBeenCalledTimes(1);
    expect(actionDatasource.performAndHandle).toHaveBeenCalledWith({
      id: "42",
      recordId: "1337"
    });

  });

  it('should hide the empty fields', function () {
    spyOn(actionDatasource, 'performAndHandle');

    compile(`
      <dashboard-tile
        class="m-tile"
        icon="icon-finance"
        title=""
        value=""
        text=""
        button="more info"
        action="action">
      </dashboard-tile>
    `, {
      noId: "42",
      recordId: "1337"
    });

    const icon = element.find('.m-tile__icon');

    expect(icon.hasClass('icon-finance')).toBe(true);
    expect(icon.hasClass('icon__big')).toBe(true);

    expect(element.find('.m-tile__title').hasClass('ng-hide')).toBe(true);
    expect(element.find('.m-tile__value').hasClass('ng-hide')).toBe(true);
    expect(element.find('.m-tile__button').hasClass('ng-hide')).toBe(true);
    expect(element.find('p').hasClass('ng-hide')).toBe(true);
  });

});
