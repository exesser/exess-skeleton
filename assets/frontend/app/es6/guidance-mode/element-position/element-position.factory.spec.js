'use strict';

describe('Factory: elementPosition', function () {
  beforeEach(module('digitalWorkplaceApp'));

  let elementPosition;

  let pElement;

  let $rootScope;

  const template = `
    <p class='element-position-test'>Look at me I'm a bird man fish.</p>
  `;

  beforeEach(inject(function (_elementPosition_, _$rootScope_) {
    $rootScope = _$rootScope_;
    elementPosition = _elementPosition_;

    $('body').append($(template));
    pElement = $('p.element-position-test');

    $rootScope.$apply();
  }));

  afterEach(function() {
    pElement.remove();
  });

  it('should know when something is above or below the fold', function() {
    expect(elementPosition.isAboveFold(pElement)).toBe(true);

    const spy = spyOn($.prototype, 'height');

    spy.and.returnValue(32);
    $rootScope.$apply();

    expect(elementPosition.isAboveFold(pElement)).toBe(true);

    spy.and.returnValue(31);
    $rootScope.$apply();

    expect(elementPosition.isAboveFold(pElement)).toBe(false);
  });
});
