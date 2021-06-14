'use strict';

describe('Form type: list-icon-link-cell', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let scope;
  let element;

  let $rootScope;
  let $compile;

  const template = `<list-icon-link-cell icon="bedrijf" link="http://ginder"></list-icon-link-cell>`;

  beforeEach(inject(function (_$rootScope_, _$compile_, $state) {
    $rootScope = _$rootScope_;
    $compile = _$compile_;
    mockHelpers.blockUIRouter($state);

    scope = $rootScope.$new();
    element = angular.element(template);
    element = $compile(element)(scope);

    $rootScope.$apply();
  }));

  it('should compile down to a directive with a link and an icon', function() {
    const spans = element.find('span');
    const span1 = $(spans[1]);
    expect(span1.hasClass('icon-bedrijf')).toBe(true);
    expect((element.find('a')[0]).href).toEqual('http://ginder/');
    });
  });
