'use strict';

describe('Filter: kebabCase', function () {
  // load the directive's module
  beforeEach(module('digitalWorkplaceApp'));

  let kebabCaseFilter;

  beforeEach(inject(function(_kebabCaseFilter_) {
    kebabCaseFilter = _kebabCaseFilter_;
  }));

  it('should be able to transform a string to kebabCase', function () {
    expect(kebabCaseFilter("module|fields.first")).toBe("module-fields-first");
  });
});
