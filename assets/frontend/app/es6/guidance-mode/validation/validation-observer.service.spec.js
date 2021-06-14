'use strict';

describe('Service: validationObserver', function () {

  beforeEach(module('digitalWorkplaceApp'));

  let validationObserver;
  let flashMessageContainer;

  beforeEach(inject(function (ValidationObserver, _flashMessageContainer_) {
    flashMessageContainer = _flashMessageContainer_;
    validationObserver = new ValidationObserver(flashMessageContainer);
    spyOn(flashMessageContainer, 'addMessageOfType');
  }));

  describe('setErrors', function () {
    it('should signal observers that errors have changed and be able to return it via getErrorsForKey.', function () {
      expect(validationObserver.getErrorsForKey("first-name")).toEqual([]);

      //Callbacks are invoked serially when calling setErrors
      validationObserver.registerErrorsChangedCallback(function () {
        expect(validationObserver.getErrorsForKey("first-name")).toEqual(["This is incorrect."]);
      });
      validationObserver.registerErrorsChangedCallback(function () {
        expect(validationObserver.getErrorsForKey("first-name")).toEqual(["This is incorrect."]);
      });

      validationObserver.setErrors({ "first-name": ["This is incorrect."] });
    });

    it('should completely override previous errors', function () {
      //First name error is set
      expect(validationObserver.getErrorsForKey("first-name")).toEqual([]);
      validationObserver.setErrors({ "first-name": ["This is incorrect."] });
      expect(validationObserver.getErrorsForKey("first-name")).toEqual(["This is incorrect."]);
      expect(validationObserver.getErrorsForKey("last-name")).toEqual([]);

      //New invocation sets last name error, first name error is now gone
      validationObserver.setErrors({ "last-name": ["This is also incorrect."] });
      expect(validationObserver.getErrorsForKey("first-name")).toEqual([]);
      expect(validationObserver.getErrorsForKey("last-name")).toEqual(["This is also incorrect."]);
    });
  });

  describe('setError', function () {
    it('should signal observers that errors have changed and be able to return it via getErrorsForKey.', function () {
      expect(validationObserver.getErrorsForKey("first-name")).toEqual([]);

      //Callbacks are invoked serially when calling setErrors
      validationObserver.registerErrorsChangedCallback(function () {
        expect(validationObserver.getErrorsForKey("first-name")).toEqual(["This is incorrect."]);
      });
      validationObserver.registerErrorsChangedCallback(function () {
        expect(validationObserver.getErrorsForKey("first-name")).toEqual(["This is incorrect."]);
      });

      validationObserver.setError("first-name", ["This is incorrect."]);
    });

    it('should not completely override previous errors', function () {
      //First name error is set
      expect(validationObserver.getErrorsForKey("first-name")).toEqual([]);
      validationObserver.setError("first-name", ["This is incorrect."]);
      expect(validationObserver.getErrorsForKey("first-name")).toEqual(["This is incorrect."]);
      expect(validationObserver.getErrorsForKey("last-name")).toEqual([]);

      //New invocation sets last name error, first name error is still there
      validationObserver.setError("last-name", ["This is also incorrect."]);
      expect(validationObserver.getErrorsForKey("first-name")).toEqual(["This is incorrect."]);
      expect(validationObserver.getErrorsForKey("last-name")).toEqual(["This is also incorrect."]);
    });
  });

  describe('clearError', function () {
    it('should signal observers that errors have changed and should no longer be able to return them via getErrorsForKey.', function () {
      validationObserver.setError("first-name", ["This is incorrect."]);
      expect(validationObserver.getErrorsForKey("first-name")).toEqual(["This is incorrect."]);

      //Callbacks are invoked serially when calling setErrors
      validationObserver.registerErrorsChangedCallback(function () {
        expect(validationObserver.getErrorsForKey("first-name")).toEqual([]);
      });
      validationObserver.registerErrorsChangedCallback(function () {
        expect(validationObserver.getErrorsForKey("first-name")).toEqual([]);
      });

      validationObserver.clearError("first-name");
    });

    it('should clear the errors of one field and leave the rest in place', function () {
      validationObserver.setErrors({
        "first-name": ["This is incorrect."],
        "last-name": ["This is also incorrect."]
      });
      expect(validationObserver.getErrorsForKey("first-name")).toEqual(["This is incorrect."]);
      expect(validationObserver.getErrorsForKey("last-name")).toEqual(["This is also incorrect."]);

      validationObserver.clearError("first-name");
      expect(validationObserver.getErrorsForKey("first-name")).toEqual([]);
      expect(validationObserver.getErrorsForKey("last-name")).toEqual(["This is also incorrect."]);
    });
  });

  describe('AddFlashMessages', function () {
    it('should not add FlashMessage if all errors are used', function () {
      //Callbacks to use errors
      validationObserver.registerErrorsChangedCallback(function () {
        expect(validationObserver.getErrorsForKey("first-name")).toEqual(["This is incorrect."]);
      });
      validationObserver.registerErrorsChangedCallback(function () {
        expect(validationObserver.getErrorsForKey("last-name")).toEqual(["This is incorrect."]);
      });

      validationObserver.setErrors({ "first-name": ["This is incorrect."], "last-name": ["This is incorrect."] });
      expect(flashMessageContainer.addMessageOfType).not.toHaveBeenCalled();
    });

    it('should add FlashMessage if not all errors are used', function () {
      //Callbacks to one error
      validationObserver.registerErrorsChangedCallback(function () {
        expect(validationObserver.getErrorsForKey("last-name")).toEqual(["This is incorrect."]);
      });

      validationObserver.setErrors({
        "bla": [],
        "last-name": ["This is incorrect."],
        "quote|account|account_first_name_c": ["This is incorrect.", "This should not be empty."],
        "quote|account|account_number_c": ["This is incorrect.", "This should be grater then 0."]
      });
      expect(flashMessageContainer.addMessageOfType).toHaveBeenCalledTimes(2);

      expect(flashMessageContainer.addMessageOfType).toHaveBeenCalledWith(
        'ERROR',
        'Account first name: This is incorrect. This should not be empty.',
        'quote|account|account_first_name_c'
      );

      expect(flashMessageContainer.addMessageOfType).toHaveBeenCalledWith(
        'ERROR',
        'Account number: This is incorrect. This should be grater then 0.',
        'quote|account|account_number_c'
      );
    });
  });
});
