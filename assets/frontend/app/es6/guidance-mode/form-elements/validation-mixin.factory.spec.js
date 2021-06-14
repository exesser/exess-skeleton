'use strict';

describe('Mixin: validationMixin', function () {

  // load the factory's module
  beforeEach(module('digitalWorkplaceApp'));

  let validationMixin;
  let validationObserver;

  let fakeNgModel;

  beforeEach(inject(function (_validationMixin_, ValidationObserver) {
    fakeNgModel = { $viewValue: 42, $setValidity: _.noop };

    validationMixin = _validationMixin_;
    validationObserver = new ValidationObserver();
  }));

  it('should throw an error if the form key is missing.', function () {
    const controller = { ngModel: fakeNgModel };

    expect(function () {
      validationMixin.apply(controller);
    }).toThrow(new Error("Error: a form element controller must have a key, the current key is: undefined."));
  });

  it('should throw an error if ngModel is missing.', function () {
    const controller = { key: "first_name" };

    expect(function () {
      validationMixin.apply(controller);
    }).toThrow(new Error("Error: a form element must have an ngModel instance, the current value is: undefined."));
  });

  it('should initialize the controller correctly.', function () {
    const controller = { key: 'first_name', ngModel: fakeNgModel };

    expect(controller.errorMessages).toBeUndefined();

    validationMixin.apply(controller, validationObserver);

    expect(controller.errorMessages).toEqual([]);
  });

  describe('registerFieldValidationCallback', function () {
    it('should set the error messages when the errorsChangedCallback is invoked', function () {
      spyOn(validationObserver, 'registerErrorsChangedCallback');
      spyOn(fakeNgModel, '$setValidity');

      const controller = { key: 'first_name', ngModel: fakeNgModel };
      validationMixin.apply(controller, validationObserver);

      const errorsChangedCallback = validationObserver.registerErrorsChangedCallback.calls.allArgs()[0][0]; //First call, first argument
      const getErrorsForKeySpy = spyOn(validationObserver, 'getErrorsForKey').and.returnValue(["This input is all wrong."]);

      // Manually trigger the field validation callback with an error.
      errorsChangedCallback();
      expect(fakeNgModel.$setValidity).toHaveBeenCalledTimes(1);
      expect(fakeNgModel.$setValidity).toHaveBeenCalledWith('BACK_END_ERROR', false);

      getErrorsForKeySpy.and.returnValue([]);

      //Manually trigger the field validation callback without an error that the validity is now valid.
      errorsChangedCallback();
      expect(fakeNgModel.$setValidity).toHaveBeenCalledTimes(2);
      expect(fakeNgModel.$setValidity).toHaveBeenCalledWith('BACK_END_ERROR', true);
    });
  });
});
