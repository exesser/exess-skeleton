'use strict';

describe('Component: paragraph', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let element;

  const template = "<paragraph text='This is a nice paragraph.'></paragraph>";

  beforeEach(inject(function ($state, $rootScope, $compile) {
    mockHelpers.blockUIRouter($state);

    const scope = $rootScope.$new(true);
    element = angular.element(template);
    element = $compile(element)(scope);
    $rootScope.$apply();
  }));

  it('should compile down to a paragraph with a text', function() {
    const innerParagraph = element.find("p");
    expect(innerParagraph.length).toBe(1);
    expect($(innerParagraph[0]).text()).toBe('This is a nice paragraph.');
  });
});
