'use strict';

describe('Service: GuidanceFormObserver', function () {

  beforeEach(module('digitalWorkplaceApp'));

  let guidanceFormObserver;

  beforeEach(inject(function (GuidanceFormObserver) {
    guidanceFormObserver = new GuidanceFormObserver();
  }));

  it('should set a formControllerCreated callback.', function () {
    const observer = jasmine.createSpy('observer');

    guidanceFormObserver.setFormControllerCreatedCallback(observer);

    guidanceFormObserver.formControllerCreated('formController'); // call with fake parameter.
    expect(observer).toHaveBeenCalledTimes(1);
    expect(observer).toHaveBeenCalledWith('formController');
  });

  it('should set a formValueChanged callback.', function () {
    const observer = jasmine.createSpy('observer');

    guidanceFormObserver.setFormValueChangedCallback(observer);

    guidanceFormObserver.formValueChanged('guidanceAction'); // call with fake parameter.
    expect(observer).toHaveBeenCalledTimes(1);
    expect(observer).toHaveBeenCalledWith('guidanceAction', false);

    guidanceFormObserver.formValueChanged('guidanceAction', true); // call with fake parameter.
    expect(observer).toHaveBeenCalledTimes(2);
    expect(observer).toHaveBeenCalledWith('guidanceAction', true);
  });

  it('should set a formValidityUpdate callback', function () {
    const observer = jasmine.createSpy('observer');

    guidanceFormObserver.setFormValidityUpdateCallback(observer);

    guidanceFormObserver.formValidityUpdate(true); // call with fake parameter.
    expect(observer).toHaveBeenCalledTimes(1);
    expect(observer).toHaveBeenCalledWith(true);
  });

  it('should set a nextStep callback', function () {
    const observer = jasmine.createSpy('observer');

    guidanceFormObserver.setRequestNextStepCallback(observer);

    guidanceFormObserver.requestNextStep();
    expect(observer).toHaveBeenCalledTimes(1);
  });

  it('should add stepChange callbacks that can be deregistered by calling the result.', function () {
    const observer = jasmine.createSpy('observer');

    const deregisterFunction = guidanceFormObserver.addStepChangeOccurredCallback(observer);

    guidanceFormObserver.stepChangeOccurred('guidanceMode'); // call with fake parameter.

    expect(observer).toHaveBeenCalledTimes(1);
    expect(observer).toHaveBeenCalledWith('guidanceMode');

    //Deregister the callback
    deregisterFunction();

    guidanceFormObserver.stepChangeOccurred('guidanceMode'); // call with fake parameter.
    expect(observer).toHaveBeenCalledTimes(1); //Same as before
  });

  it('should set a confirmGuidance callback', function () {
    const observer = jasmine.createSpy('observer');

    guidanceFormObserver.setConfirmGuidanceCallback(observer);

    guidanceFormObserver.confirmGuidance('CONFIRM');
    expect(observer).toHaveBeenCalledTimes(1);
    expect(observer).toHaveBeenCalledWith('CONFIRM');
  });

  it('should set and get the full model', function () {
    expect(guidanceFormObserver.getFullModel()).toEqual({});

    guidanceFormObserver.setFullModel({ 'company_name': 'wky' });

    expect(guidanceFormObserver.getFullModel()).toEqual({ 'company_name': 'wky' });
  });

  it('should set and get the parent model', function () {
    expect(guidanceFormObserver.getParentModel()).toEqual({});

    guidanceFormObserver.setParentModel({ 'name': 'Ken Block' });

    expect(guidanceFormObserver.getParentModel()).toEqual({ 'name': 'Ken Block' });
  });

  it('should set and get the repeatable block key', function () {
    expect(guidanceFormObserver.getRepeatableBlockKey()).toEqual('');

    guidanceFormObserver.setRepeatableBlockKey('test-key');

    expect(guidanceFormObserver.getRepeatableBlockKey()).toEqual('test-key');
  });
});
