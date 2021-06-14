'use strict';

describe('Component: best-offerAccordionWrapper', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let $rootScope;
  let $compile;

  let element;

  let aHref;
  let span;
  let divElements;

  beforeEach(inject(function (_$rootScope_, _$compile_, $state) {
    $rootScope = _$rootScope_;
    $compile = _$compile_;

    mockHelpers.blockUIRouter($state);
  }));

  function compile(isOpen = false) {
    const template = `
      <best-offer-accordion-wrapper label="Hello World" is-open="${isOpen}">
        content
      </best-offer-accordion-wrapper>
    `;

    const scope = $rootScope.$new();

    element = angular.element(template);
    element = $compile(element)(scope);

    $rootScope.$apply();

    aHref = $(element.find('a')[0]);
    span = $(element.find('span')[0]);
    divElements = element.find('div');
  }

  it('should compile down to a title and a arrow to toggle the content', function () {
    compile();

    expect(span.hasClass('icon-arrow-down')).toBe(true);
    expect(aHref.text()).toContain('Hello World');

    expect($(divElements[1]).hasClass('ng-hide')).toBe(true);

    // Test the transclusion
    expect($(divElements[1]).text()).toContain('content');
  });

  it('should display/hide the content when clicking the link ', function() {
    compile();

    expect(span.hasClass('icon-arrow-down')).toBe(true);
    expect(span.hasClass('icon-arrow-up')).toBe(false);
    expect($(divElements[1]).hasClass('ng-hide')).toBe(true);

    // The first click should open the accordion
    aHref.click();
    expect(span.hasClass('icon-arrow-down')).toBe(false);
    expect(span.hasClass('icon-arrow-up')).toBe(true);
    expect($(divElements[1]).hasClass('ng-hide')).toBe(false);

    // The second click should close it again
    aHref.click();
    expect(span.hasClass('icon-arrow-down')).toBe(true);
    expect(span.hasClass('icon-arrow-up')).toBe(false);
    expect($(divElements[1]).hasClass('ng-hide')).toBe(true);
  });

  it('should be able to start the best-offer-accordion opened by default', function() {
    compile(true);

    expect(span.hasClass('icon-arrow-down')).toBe(false);
    expect(span.hasClass('icon-arrow-up')).toBe(true);
    expect($(divElements[1]).hasClass('ng-hide')).toBe(false);

    // The first click should open the accordion
    aHref.click();
    expect(span.hasClass('icon-arrow-down')).toBe(true);
    expect(span.hasClass('icon-arrow-up')).toBe(false);
    expect($(divElements[1]).hasClass('ng-hide')).toBe(true);

    // The second click should close it again
    aHref.click();
    expect(span.hasClass('icon-arrow-down')).toBe(false);
    expect(span.hasClass('icon-arrow-up')).toBe(true);
    expect($(divElements[1]).hasClass('ng-hide')).toBe(false);
  });
});
