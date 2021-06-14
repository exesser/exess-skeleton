'use strict';

describe('Component: labeledAccordionWrapper', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let $rootScope;
  let $compile;

  let element;

  let aHref;
  let spans;
  let ulElement;

  beforeEach(inject(function (_$rootScope_, _$compile_, $state) {
    $rootScope = _$rootScope_;
    $compile = _$compile_;

    mockHelpers.blockUIRouter($state);
  }));

  function compile(isOpen = false) {
    const template = `
      <labeled-accordion-wrapper label="Hello World" is-open="${isOpen}" icon="icon-profiel">
        <h3>Moose</h3>
        <h4>Sheep</h4>
      </labeled-accordion-wrapper>
    `;

    const scope = $rootScope.$new();

    element = angular.element(template);
    element = $compile(element)(scope);

    $rootScope.$apply();

    aHref = $(element.find('a')[0]);
    spans = element.find('span');
    ulElement = $(element.find('div.content')[0]);
  }

  it('should compile down to a ahref with a label text and icon.', function () {
    compile();

    expect(aHref.hasClass('icon-arrow-down')).toBe(true);
    expect($(spans[1]).text()).toBe('Hello World');

    expect($(spans[0]).hasClass('icon-profiel')).toBe(true);

    // Test the transclusion
    expect($(element.find('h3')[0]).text()).toBe('Moose');
    expect($(element.find('h4')[0]).text()).toBe('Sheep');
  });

  it('should open the group when clicking the link and the group is closed and vice versa.', function() {
    compile();

    expect(aHref.hasClass('is-active')).toBe(false);
    expect(ulElement.hasClass('is-open')).toBe(false);

    // The first click should open the accordion
    aHref.click();
    expect(aHref.hasClass('is-active')).toBe(true);
    expect(ulElement.hasClass('is-open')).toBe(true);

    // The second click should close it again
    aHref.click();
    expect(aHref.hasClass('is-active')).toBe(false);
    expect(ulElement.hasClass('is-open')).toBe(false);
  });

  it('should be able to start the labeled-accordion opened by default', function() {
    compile(true);

    expect(aHref.hasClass('is-active')).toBe(true);
    expect(ulElement.hasClass('is-open')).toBe(true);

    // The first click should open the accordion
    aHref.click();
    expect(aHref.hasClass('is-active')).toBe(false);
    expect(ulElement.hasClass('is-open')).toBe(false);

    // The second click should close it again
    aHref.click();
    expect(aHref.hasClass('is-active')).toBe(true);
    expect(ulElement.hasClass('is-open')).toBe(true);
  });
});
