'use strict';

describe('Filter: formatNumber', function () {
  // load the directive's module
  beforeEach(module('digitalWorkplaceApp'));

  let formatNumberFilter;

  beforeEach(inject(function(_formatNumberFilter_) {
    formatNumberFilter = _formatNumberFilter_;
  }));

  it('should be able to format a number', function () {
    expect(formatNumberFilter("22")).toBe("22.00");
    expect(formatNumberFilter("0")).toBe("0.00");
    expect(formatNumberFilter(0)).toBe("0.00");
    expect(formatNumberFilter("text")).toBe("text");
    expect(formatNumberFilter(22.567)).toBe("22.57");
    expect(formatNumberFilter(22.567890, 4)).toBe("22.5679");
    expect(formatNumberFilter(null)).toBe(null);
  });
});
