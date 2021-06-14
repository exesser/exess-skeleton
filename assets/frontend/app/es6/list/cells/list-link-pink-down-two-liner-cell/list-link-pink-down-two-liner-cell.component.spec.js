'use strict';

describe('Form type: list-link-pink-down-two-liner-cell', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let scope;
  let element;

  let $rootScope;
  let $compile;
  let $state;

  const template = `
    <list-link-pink-down-two-liner-cell
      line-1="Ken Block"
      line-2="ken@block.ro"
      link="mailto:ken@block.ro">
    </list-link-pink-down-two-liner-cell>
  `;

  beforeEach(inject(function (_$rootScope_, _$compile_, _$state_) {
    $rootScope = _$rootScope_;
    $compile = _$compile_;
    $state = _$state_;
    mockHelpers.blockUIRouter($state);

    scope = $rootScope.$new();
    element = angular.element(template);
    element = $compile(element)(scope);

    $rootScope.$apply();
  }));

  it('should compile down to a directive with a two line paragraph and on the second should be a link', function() {
    const aHref = $(element.find('a')[0]);
    const paragraph = $(element.find('p')[0]);

    expect(aHref.attr('href')).toEqual('mailto:ken@block.ro');
    expect(aHref.attr('target')).toEqual('_blank');
    expect(aHref.text()).toEqual('ken@block.ro');
    expect(paragraph.text()).toContain('Ken Block');
    expect(paragraph.text()).toContain('ken@block.ro');
  });
});
