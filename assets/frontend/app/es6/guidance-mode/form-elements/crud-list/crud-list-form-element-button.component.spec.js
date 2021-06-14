'use strict';

describe('Component: crudListFormElementButton', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let $rootScope;
  let $compile;

  let element;
  let anchorTag;
  let scope;

  const template = `
    <crud-list-form-element-button
      icon-class="{{ iconClass }}"
      row-index="42"
      button-clicked="buttonClicked(index)">
    </crud-list-form-element-button>
  `;

  beforeEach(inject(function ($state, _$rootScope_, _$compile_) {
    mockHelpers.blockUIRouter($state);

    $rootScope = _$rootScope_;
    $compile = _$compile_;
  }));

  function compile(iconClass = "icon-edit") {
    scope = $rootScope.$new();

    scope.buttonClicked = jasmine.createSpy('buttonClickedSpy');
    scope.iconClass = iconClass;

    element = angular.element(template);
    element = $compile(element)(scope);
    scope.$apply();

    anchorTag = $(element.find("a"));
  }

  it('should contain an anchor tag with an icon-edit class', function () {
    compile('icon-edit');
    expect(anchorTag.length).toBe(1);
    expect(anchorTag.hasClass("icon-edit")).toBe(true);
  });

  it('should contain an anchor tag with an icon-remove class', function () {
    compile('icon-remove');
    expect(anchorTag.length).toBe(1);
    expect(anchorTag.hasClass("icon-remove")).toBe(true);
  });

  it('should call the removeRow function when clicking the link', function () {
    compile();
    expect(scope.buttonClicked).not.toHaveBeenCalled();
    anchorTag.click();
    expect(scope.buttonClicked).toHaveBeenCalledTimes(1);
    expect(scope.buttonClicked).toHaveBeenCalledWith(42);
  });
});
