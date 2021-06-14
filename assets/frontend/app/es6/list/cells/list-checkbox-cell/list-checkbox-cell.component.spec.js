'use strict';

describe('Form type: list-checkbox-cell', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let scope;
  let element;
  let listObserver;
  let checkBoxElement;
  let deregisterSpy;

  let $rootScope;
  let $q;
  let $compile;
  const listKey = 'accounts';
  const template = `<list-checkbox-cell id="123-456-789" list-key="accounts"></list-checkbox-cell>`;

  beforeEach(inject(function (_$rootScope_, _$compile_, $state, _$q_, _listObserver_) {
    $rootScope = _$rootScope_;
    $compile = _$compile_;
    $q = _$q_;
    listObserver = _listObserver_;
    mockHelpers.blockUIRouter($state);

    deregisterSpy = jasmine.createSpy("deregisterSpy");
    spyOn(listObserver, 'registerToggleAllListRowsSelectionsCallback').and.returnValue(deregisterSpy);

    scope = $rootScope.$new();
    element = angular.element(template);
    element = $compile(element)(scope);

    $rootScope.$apply();
    checkBoxElement = $(element.find("input")[0]);
  }));

  it('should compile down to a directive with a input type checkbox', function () {
    expect(checkBoxElement.attr('type')).toBe('checkbox');
  });

  it('should call listObserver.toggleListRowSelection when the checkbox is checked', function () {
    spyOn(listObserver, 'toggleListRowSelection').and.callFake(mockHelpers.resolvedPromise($q));
    checkBoxElement.click();

    expect(listObserver.toggleListRowSelection).toHaveBeenCalledTimes(1);
    expect(listObserver.toggleListRowSelection).toHaveBeenCalledWith(listKey, '123-456-789', true);

    checkBoxElement.click();

    expect(listObserver.toggleListRowSelection).toHaveBeenCalledTimes(2);
    expect(listObserver.toggleListRowSelection).toHaveBeenCalledWith(listKey, '123-456-789', false);
  });

  it('should check and uncheck the checkbox when the observer is called', function () {
    expect(checkBoxElement.attr('checked')).toBeUndefined();
    expect(listObserver.registerToggleAllListRowsSelectionsCallback).toHaveBeenCalledTimes(1);

    const registerToggleAllListRowsSelectionsCallback = listObserver.registerToggleAllListRowsSelectionsCallback.calls.argsFor(0)[1];

    registerToggleAllListRowsSelectionsCallback(true);
    $rootScope.$apply();
    expect(checkBoxElement.attr('checked')).toBe('checked');

    registerToggleAllListRowsSelectionsCallback(false);
    $rootScope.$apply();
    expect(checkBoxElement.attr('checked')).toBeUndefined();
  });

  it('should call the callbackDeregister function when the scope is destroyed', function () {
    expect(deregisterSpy).not.toHaveBeenCalled();
    scope.$destroy();
    expect(deregisterSpy).toHaveBeenCalledTimes(1);
  });
});
