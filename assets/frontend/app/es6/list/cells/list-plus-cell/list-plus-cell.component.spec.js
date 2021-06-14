'use strict';

describe('Form type: list-plus-cell', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let scope;
  let element;
  let listObserver;
  let aHref;
  let $rootScope;
  let $compile;

  const listKey = 'accounts';
  const template = `<list-plus-cell id="123-123-345" grid-key="action-bar" list-key="accounts"></list-plus-cell>`;

  beforeEach(inject(function (_$rootScope_, _$compile_, $state, _listObserver_) {
    $rootScope = _$rootScope_;
    $compile = _$compile_;
    listObserver = _listObserver_;
    mockHelpers.blockUIRouter($state);

    spyOn(listObserver, 'registerToggleAllListRowsSelectionsCallback');

    scope = $rootScope.$new();
    element = angular.element(template);
    element = $compile(element)(scope);

    $rootScope.$apply();
    aHref = $(element.find("a")[0]);
  }));

  it('should compile down to a directive with a link', function () {
    expect(aHref.hasClass('show-actions')).toBe(true);
    expect(aHref.hasClass('icon-plus')).toBe(true);
  });

  it('should call listObserver.toggleExtraRowContentPlaceholder when the link is accessed', function () {
    spyOn(listObserver, 'toggleExtraRowContentPlaceholder');

    aHref.click();

    expect(listObserver.toggleExtraRowContentPlaceholder).toHaveBeenCalledTimes(1);
    expect(listObserver.toggleExtraRowContentPlaceholder).toHaveBeenCalledWith(listKey, 'action-bar', '123-123-345');
  });
});
