'use strict';

describe('Factory: navigateAwayWarning', function () {
  beforeEach(module('digitalWorkplaceApp'));

  let navigateAwayWarning;
  let $window;

  beforeEach(mockHelpers.mockTranslate); // Start mocking $translate

  beforeEach(inject(function ($state, _$window_, _navigateAwayWarning_) {
    mockHelpers.blockUIRouter($state);

    $window = _$window_;

    navigateAwayWarning = _navigateAwayWarning_;

    $window.onbeforeunload = undefined;
  }));

  afterEach(function() {
    /*
      Make sure the onbeforeload is deactivated otherwise other tests
      will fail because they will trigger window.onbeforeunload's.
    */
    $window.onbeforeunload = undefined;
    $window.event = undefined;
  });

  describe('enable behavior', function() {
    let onbeforeload;

    beforeEach(function() {
      navigateAwayWarning.enable();

      onbeforeload = $window.onbeforeunload;
      expect(onbeforeload).not.toBe(undefined);
    });

    it('should trigger navigate away messages when enabled', function () {
      const event = {};
      const message = onbeforeload(event);

      expect(message).toBe('NAVIGATE_AWAY_WARNING');
      expect(event.returnValue).toBe('NAVIGATE_AWAY_WARNING');
    });

    it('should use $window.event when event is not passed', function () {
      $window.event = {};
      const message = onbeforeload();

      expect(message).toBe('NAVIGATE_AWAY_WARNING');
      expect($window.event.returnValue).toBe('NAVIGATE_AWAY_WARNING');
    });
  });

  it('should disable the navigate away message when disable is called', function() {
    navigateAwayWarning.enable();
    expect($window.onbeforeunload).not.toBe(undefined);

    navigateAwayWarning.disable();
    expect($window.onbeforeunload).toBe(null);
  });
});
