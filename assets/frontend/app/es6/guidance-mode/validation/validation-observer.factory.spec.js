'use strict';

describe('Factory: validationObserverFactory', function () {

  beforeEach(module('digitalWorkplaceApp'));

  let validationObserverFactory;
  let ValidationObserver;

  beforeEach(inject(function(_validationObserverFactory_, _ValidationObserver_) {
    validationObserverFactory = _validationObserverFactory_;
    ValidationObserver = _ValidationObserver_;
  }));

  describe('createValidationObserver', function() {
    it('should create a new ValidationObserver and return it', function() {
      let validationObserver = validationObserverFactory.createValidationObserver();
      expect(_.isEmpty(validationObserver)).toBe(false);
      expect(validationObserver instanceof ValidationObserver).toBe(true);
    });
  });
});
