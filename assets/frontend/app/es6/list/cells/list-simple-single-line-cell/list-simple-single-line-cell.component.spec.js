'use strict';

describe('Form type: list-simple-single-line-cell', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let scope;
  let element;

  let $rootScope;
  let $compile;

  const template = `<list-simple-single-line-cell text="Your text could be here!"></list-simple-single-line-cell>`;

  beforeEach(inject(function (_$rootScope_, _$compile_, $state) {
    $rootScope = _$rootScope_;
    $compile = _$compile_;
    mockHelpers.blockUIRouter($state);

    scope = $rootScope.$new();
    element = angular.element(template);
    element = $compile(element)(scope);

    $rootScope.$apply();
  }));

  it('should compile down to a directive with a paragraph inside', function() {
    const p = element.find('p');
    expect($(p[0]).text()).toContain('Your text could be here!');
  });
});
