'use strict';

describe('Factory: expressionTransformer', function () {
  // load the directive's module
  beforeEach(module('digitalWorkplaceApp'));

  let expressionTransformer;

  beforeEach(inject(function(_expressionTransformer_) {
    expressionTransformer = _expressionTransformer_;
  }));

  it('should transform an exess expression to an Angular expression', function () {
    expect(expressionTransformer("{%firstName%}")).toBe("{{firstName}}");
    expect(expressionTransformer("{%firstName%} {%lastName%}")).toBe("{{firstName}} {{lastName}}");
    expect(expressionTransformer("Mister {%firstName%} {%lastName%}")).toBe("Mister {{firstName}} {{lastName}}");
  });
});
