'use strict';

describe('Form type: list-icon-text-cell', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let scope;
  let element;

  let $rootScope;
  let $compile;

  const template = `<list-icon-text-cell css-classes="icon-edit status-star" text="prospect"></list-icon-text-cell>`;

  beforeEach(inject(function (_$rootScope_, _$compile_, $state) {
    $rootScope = _$rootScope_;
    $compile = _$compile_;
    mockHelpers.blockUIRouter($state);

    scope = $rootScope.$new();
    element = angular.element(template);
    element = $compile(element)(scope);

    $rootScope.$apply();
  }));

  it('should compile down to a directive with three div`s inside', function() {
    const divs = element.find('div');
    const div0 = $(divs[0]);
    const div1 = $(divs[1]);
    const div2 = $(divs[2]);

    expect(div0.hasClass('customer__status')).toBe(true);
    expect(div0.hasClass('icon-edit')).toBe(true);
    expect(div0.hasClass('status-star')).toBe(true);
    expect(div1.hasClass('status__badge')).toBe(true);
    expect(div2.hasClass('status__label')).toBe(true);
  });
});
