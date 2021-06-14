'use strict';

describe('Animation: Slide animation', function () {
  beforeEach(module('digitalWorkplaceApp'));

  let body;
  let slideAnimation;

  beforeEach(inject(function ($state, _slideAnimation_) {
    mockHelpers.blockUIRouter($state);

    slideAnimation = _slideAnimation_;

    body = $('body');
  }));

  afterEach(function() {
    body.removeClass('focus-is-open');
  });

  describe('slideAnimation.open', function() {
    it('should know how to perform the open animation', function() {
      expect(body.hasClass('focus-is-open')).toBe(false);

      slideAnimation.open();

      expect(body.hasClass('focus-is-open')).toBe(true);
    });

    it('should not open when the menu is already opened.', function() {
      body.addClass('focus-is-open');
      expect(body.hasClass('focus-is-open')).toBe(true);

      slideAnimation.open();

      expect(body.hasClass('focus-is-open')).toBe(true);
    });
  });

  describe('slideAnimation.close', function() {
    it('should know how to perform the close animation', function() {
      body.addClass('focus-is-open');
      expect(body.hasClass('focus-is-open')).toBe(true);

      slideAnimation.close();

      expect(body.hasClass('focus-is-open')).toBe(false);
    });

    it('should not close when the menu is already closed.', function() {
      expect(body.removeClass('focus-is-open'));
      expect(body.hasClass('focus-is-open')).toBe(false);

      slideAnimation.close();

      expect(body.hasClass('focus-is-open')).toBe(false);
    });
  });
});
