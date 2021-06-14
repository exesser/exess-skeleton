'use strict';

describe('Form type: list-bold-top-two-liner-cell', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let scope;
  let element;

  let $rootScope;
  let $compile;

  const template = `<list-bold-top-two-liner-cell line-1="Exesser" line-2="ES12345"></list-bold-top-two-liner-cell>`;

  beforeEach(inject(function (_$rootScope_, _$compile_, $state) {
    $rootScope = _$rootScope_;
    $compile = _$compile_;
    mockHelpers.blockUIRouter($state);

    scope = $rootScope.$new();
    element = angular.element(template);
    element = $compile(element)(scope);

    $rootScope.$apply();
  }));

  it('should compile down to a directive with two headers inside (an h5 and a h6)', function() {
    expect($(element.find('h5')[0]).text()).toContain('Exesser');
    expect($(element.find('h6')[0]).text()).toContain('ES12345');
  });
});
