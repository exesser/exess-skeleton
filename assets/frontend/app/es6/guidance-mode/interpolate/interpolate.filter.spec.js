'use strict';

describe('Filter: interpolate', function () {
  // load the directive's module
  beforeEach(module('digitalWorkplaceApp'));

  let interpolateFilter;

  beforeEach(inject(function (_interpolateFilter_) {
    interpolateFilter = _interpolateFilter_;
  }));

  it('should be able to render a title based on an expression and model', function () {
    const model = { firstName: "Jan" };

    expect(interpolateFilter("{{firstName}}", model, "default")).toBe("Jan");
  });

  it('should load the default title if the expression results in an empty string', function () {
    const model = { firstName: "" };

    expect(interpolateFilter("{{firstName}}", model, "Piet")).toBe("Piet");
  });

  it('should be able to handle an expression with a | (pipe) in it.', function () {
    const model = { "parent|model": { "first|name": "WKY" } };

    expect(interpolateFilter("{{parent|model.first|name}}", model, "default")).toBe("WKY");
  });
});
