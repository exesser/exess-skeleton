'use strict';

describe('Factory: novaExpressionTransformer', function () {
  // load the directive's module
  beforeEach(module('digitalWorkplaceApp'));

  let novaExpressionTransformer;

  beforeEach(inject(function(_novaExpressionTransformer_) {
    novaExpressionTransformer = _novaExpressionTransformer_;
  }));

  it('should transform a nova expression to an Angular expression', function () {
    expect(novaExpressionTransformer("{%firstName%}")).toBe("{{firstName}}");
    expect(novaExpressionTransformer("{%firstName%} {%lastName%}")).toBe("{{firstName}} {{lastName}}");
    expect(novaExpressionTransformer("Mister {%firstName%} {%lastName%}")).toBe("Mister {{firstName}} {{lastName}}");
  });
});
