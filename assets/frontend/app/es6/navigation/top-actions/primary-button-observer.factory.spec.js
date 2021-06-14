'use strict';

describe('Factory: primaryButtonObserver', function () {

  beforeEach(module('digitalWorkplaceApp'));

  let primaryButtonObserver;
  let topActionState;

  beforeEach(inject(function (_primaryButtonObserver_, _topActionState_) {
    primaryButtonObserver = _primaryButtonObserver_;
    topActionState = _topActionState_;
  }));

  it('should set the primaryButtonData on the topActionState when calling setPrimaryButtonData.', function () {
    spyOn(topActionState, 'setPrimaryButtonData');
    const primaryButtonData = [{ "test": "test1" }, { "test": "test2" }];

    primaryButtonObserver.setPrimaryButtonData(primaryButtonData); // call with fake parameter.

    expect(topActionState.setPrimaryButtonData).toHaveBeenCalledTimes(1);
    expect(topActionState.setPrimaryButtonData).toHaveBeenCalledWith(primaryButtonData);
  });

  it('should register primaryButtonClicked callback.', function () {
    const observer = jasmine.createSpy('observer');

    primaryButtonObserver.setPrimaryButtonClickedCallback(observer);
    primaryButtonObserver.primaryButtonClicked();

    expect(observer).toHaveBeenCalledTimes(1);
  });

  it('should know how to reset the primary button.', function () {
    spyOn(topActionState, 'resetPrimaryButtonData');

    primaryButtonObserver.resetPrimaryButtonData();

    expect(topActionState.resetPrimaryButtonData).toHaveBeenCalledTimes(1);
  });
});
