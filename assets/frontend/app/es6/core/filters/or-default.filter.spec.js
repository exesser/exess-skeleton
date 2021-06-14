'use strict';

describe('Filter: orDefault', function () {
  // load the directive's module
  beforeEach(module('digitalWorkplaceApp'));

  let orDefaultFilter;

  beforeEach(inject(function(_orDefaultFilter_) {
    orDefaultFilter = _orDefaultFilter_;
  }));

  it('should return the value when not empty', function () {
    expect(orDefaultFilter("hi mom", "hi dad")).toBe("hi mom");
  });

  it('should return the default value when empty', function () {
    expect(orDefaultFilter("", "hi dad")).toBe("hi dad");
    expect(orDefaultFilter("   ", "hi dad")).toBe("hi dad");
  });
});
