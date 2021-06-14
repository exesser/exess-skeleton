'use strict';

describe('Directive: dwpCtrlClick', function () {
  beforeEach(module('digitalWorkplaceApp'));

  let $rootScope;
  let scope;

  let element;

  let template = `
    <a dwp-ctrl-click="testFunction()"></a>
  `;

  beforeEach(inject(function (_$rootScope_, $compile) {

    $rootScope = _$rootScope_;

    scope = $rootScope.$new();
    scope.testFunction = jasmine.createSpy('testFunction');

    element = angular.element(template);
    element = $compile(element)(scope);

    $rootScope.$apply();
  }));

  it('should call testFunction', function () {

    element.click();

    expect(scope.testFunction).not.toHaveBeenCalled();

    element.trigger({ type: 'contextmenu', ctrlKey: false });

    $rootScope.$apply();

    expect(scope.testFunction).not.toHaveBeenCalled();

    const preventDefaultSpy = jasmine.createSpy('preventDefault');
    element.trigger({ type: 'contextmenu', ctrlKey: true, preventDefault: preventDefaultSpy });
    $rootScope.$apply();

    expect(preventDefaultSpy).toHaveBeenCalledTimes(1);
    expect(scope.testFunction).toHaveBeenCalledTimes(1);
  });
});
