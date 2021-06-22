'use strict';

describe('Form type: checkbox', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let scope;
  let element;

  let $rootScope;
  let $compile;
  let validationObserver;
  let guidanceFormObserver;

  let guidanceModeBackendState;
  let elementIdGenerator;
  let ACTION_EVENT;

  const template = '<formly-form form="form" model="model" fields="fields"/>';

  let checkBoxElement;

  beforeEach(inject(function (_$rootScope_, _$compile_, ValidationObserver, $state, GuidanceFormObserver,
                              _guidanceModeBackendState_, _ACTION_EVENT_, _elementIdGenerator_) {
    $rootScope = _$rootScope_;
    $compile = _$compile_;

    guidanceModeBackendState = _guidanceModeBackendState_;
    elementIdGenerator = _elementIdGenerator_;
    ACTION_EVENT = _ACTION_EVENT_;

    validationObserver = new ValidationObserver();

    guidanceFormObserver = new GuidanceFormObserver();
    spyOn(guidanceFormObserver, 'formValueChanged');
    spyOn(elementIdGenerator, 'generateId').and.returnValue('field-fake-id');

    mockHelpers.blockUIRouter($state);
  }));

  function compile(disableField = false) {
    scope = $rootScope.$new();
    scope.model = {
      house: {
        hasFrontDoor: false
      }
    };
    scope.fields = [
      {
        id: 'house.hasFrontDoor',
        key: 'house.hasFrontDoor',
        type: 'checkbox',
        templateOptions: {
          label: 'Do you have a front door?',
          noBackendInteraction: false,
          disabled: disableField,
          readonly: false
        }
      }
    ];

    const guidanceFormObserverAccessorElement = mockHelpers.createGuidanceFormObserverAccessor({
      $compile,
      $rootScope,
      validationObserver,
      guidanceFormObserver
    });
    element = angular.element(template);
    element = $compile(element)(scope);
    guidanceFormObserverAccessorElement.append(element);

    $rootScope.$apply();

    checkBoxElement = $(element.find("input")[0]);
  }

  it('should create a checkbox with a label', function () {
    compile();

    expect(elementIdGenerator.generateId).toHaveBeenCalledTimes(1);
    expect(elementIdGenerator.generateId).toHaveBeenCalledWith('house.hasFrontDoor', guidanceFormObserver);

    expect(checkBoxElement.attr('id')).toBe('field-fake-id');
    expect(checkBoxElement.prop('disabled')).toBe(false);
    expect(checkBoxElement.attr('type')).toBe('checkbox');
    expect($(element.find('label')[0]).text()).toContain('Do you have a front door?');
  });

  it('should update its internal state when the ngModel changes', function () {
    compile();

    expect(checkBoxElement.is(':checked')).toBe(false);

    scope.model.house.hasFrontDoor = true;
    $rootScope.$apply();

    expect(checkBoxElement.is(':checked')).toBe(true);
  });

  it('should let the "guidanceFormObserver" know values have changed', function () {
    compile();

    expect(guidanceFormObserver.formValueChanged).not.toHaveBeenCalled();

    expect(scope.model.house.hasFrontDoor).toBe(false);

    checkBoxElement.click();
    $rootScope.$apply();

    expect(scope.model.house.hasFrontDoor).toBe(true);

    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(1);
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
      focus: 'house.hasFrontDoor',
      value: true
    }, false);

    checkBoxElement.click();
    $rootScope.$apply();

    expect(scope.model.house.hasFrontDoor).toBe(false);

    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(2);
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
      focus: 'house.hasFrontDoor',
      value: false
    }, false);
  });

  it('should let the "guidanceFormObserver" know that the values have changed when noBackendInteraction is true', function () {
    compile();

    expect(guidanceFormObserver.formValueChanged).not.toHaveBeenCalled();
    expect(scope.model.house.hasFrontDoor).toBe(false);

    scope.fields[0].templateOptions.noBackendInteraction = true;
    $rootScope.$apply();

    checkBoxElement.click();
    $rootScope.$apply();

    expect(scope.model.house.hasFrontDoor).toBe(true);
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(1);
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
      focus: 'house.hasFrontDoor',
      value: true
    }, true);
  });

  describe('the validation functionality', function () {
    it('should set the field to erroneous if field errors occur', function () {
      compile();
      expect(scope.form.$valid).toBe(true);

      validationObserver.setErrors({
        'house.hasFrontDoor': ["How can you not have a front door?!"]
      });
      $rootScope.$apply();
      expect(scope.form.$valid).toBe(false);
      expect(scope.form['house.hasFrontDoor'].$error.BACK_END_ERROR).toBe(true);
    });
  });

  describe("the 'disabled' functionality", function () {
    it('should made the checkbox disabled if the templateOptions.disabled property evaluates to true', function () {
      compile();
      expect(checkBoxElement.prop('disabled')).toBe(false);

      compile(true);
      expect(checkBoxElement.prop('disabled')).toBe(true);
    });

    it('should make the field disabled if the backend is busy', function () {
      spyOn(guidanceModeBackendState, 'getBackendIsBusy').and.returnValue(true);
      spyOn(guidanceModeBackendState, 'getPerformedAction').and.returnValue({
        event: ACTION_EVENT.CHANGED,
        focus: 'field'
      });
      compile();

      expect(checkBoxElement.prop('disabled')).toBe(true);
    });
  });

  describe("the 'readonly' functionality", function () {
    it('should made the checkbox disabled if the templateOptions.disabled property evaluates to true', function () {
      compile();
      expect(checkBoxElement.prop('disabled')).toBe(false);

      compile(true);
      expect(checkBoxElement.prop('disabled')).toBe(true);
    });
  });
});
