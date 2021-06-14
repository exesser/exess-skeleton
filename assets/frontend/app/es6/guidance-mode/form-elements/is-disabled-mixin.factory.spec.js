'use strict';

describe('Mixin: isDisabledMixin', function() {

  // load the factory's module
  beforeEach(module('digitalWorkplaceApp'));

  let isDisabledMixin;

  let guidanceModeBackendState;
  let ACTION_EVENT;
  let CONFIRM_ACTION;

  beforeEach(inject(function(_isDisabledMixin_, _guidanceModeBackendState_, _ACTION_EVENT_, _CONFIRM_ACTION_) {
    isDisabledMixin = _isDisabledMixin_;

    ACTION_EVENT = _ACTION_EVENT_;
    CONFIRM_ACTION = _CONFIRM_ACTION_;

    guidanceModeBackendState = _guidanceModeBackendState_;
  }));

  it('should throw an error if the form key is missing.', function() {
    const controller = { isDisabled: true };

    expect(function() {
      isDisabledMixin.apply(controller);
    }).toThrow(new Error("Error: a form element controller must have a key, the current key is: undefined."));
  });

  describe('when the controller is valid', function() {
    let controller;

    beforeEach(function() {
      controller = {
        key: "first_name"
      };

      expect(controller.fieldIsDisabled).toBeUndefined();
      isDisabledMixin.apply(controller);
    });

    it('should add an "fieldIsDisabled" method', function() {
      expect(controller.fieldIsDisabled).not.toBeUndefined();
    });

    describe('when the "fieldIsDisabled" is call', function() {
      it('should return true when the field is disabled by backend', function() {
        controller.isDisabled = true;
        expect(controller.fieldIsDisabled()).toBe(true);
      });

      it('should return false when the field is not disabled by backend and the backend is not busy', function() {
        spyOn(guidanceModeBackendState, 'getBackendIsBusy').and.returnValue(false);
        expect(controller.fieldIsDisabled()).toBe(false);
      });

      it('should return true when the backend is busy changing a different field', function() {
        spyOn(guidanceModeBackendState, 'getBackendIsBusy').and.returnValue(true);
        spyOn(guidanceModeBackendState, 'getPerformedAction').and.returnValue({
          event: ACTION_EVENT.CHANGED,
          focus: 'field'
        });

        expect(controller.fieldIsDisabled()).toBe(true);
      });

      it('should return true when the backend is busy and event is CONFIRM', function() {
        spyOn(guidanceModeBackendState, 'getBackendIsBusy').and.returnValue(true);
        spyOn(guidanceModeBackendState, 'getPerformedAction').and.returnValue({
          event: CONFIRM_ACTION.CONFIRM
        });

        expect(controller.fieldIsDisabled()).toBe(true);
      });

      it('should return false when the backend is busy changing the current field or making another action (not CHANGED)', function() {
        spyOn(guidanceModeBackendState, 'getBackendIsBusy').and.returnValue(true);

        let spyOnGetPerformedAction = spyOn(guidanceModeBackendState, 'getPerformedAction').and.returnValue({
          event: ACTION_EVENT.CHANGED,
          focus: 'first_name'
        });
        expect(controller.fieldIsDisabled()).toBe(false);

        spyOnGetPerformedAction.and.returnValue({
          event: ACTION_EVENT.CHANGED
        });
        expect(controller.fieldIsDisabled()).toBe(false);

        spyOnGetPerformedAction.and.returnValue({
          event: 'bla',
          focus: 'first_name'
        });
        expect(controller.fieldIsDisabled()).toBe(false);

        spyOnGetPerformedAction.and.returnValue({
          focus: 'first_name'
        });
        expect(controller.fieldIsDisabled()).toBe(false);
      });
    });
  });
});
