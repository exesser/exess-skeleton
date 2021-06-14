'use strict';

describe('Form type: list-simple-two-liner-cell', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let scope;
  let element;

  let $rootScope;
  let $compile;

  const template = `<list-simple-two-liner-cell line-1="Sloepstraat 22" line-2="2584VV Den Haag"></list-simple-two-liner-cell>`;

  beforeEach(inject(function (_$rootScope_, _$compile_, $state) {
    $rootScope = _$rootScope_;
    $compile = _$compile_;
    mockHelpers.blockUIRouter($state);

    scope = $rootScope.$new();
    element = angular.element(template);
    element = $compile(element)(scope);

    $rootScope.$apply();
  }));

  it('should compile down to a directive with two paragraphs inside', function() {
    const p = element.find('p');
    expect($(p[0]).text()).toContain('Sloepstraat 22');
    expect($(p[0]).text()).toContain('2584VV Den Haag');
  });
});
