'use strict';

describe('Form type: toggle', function () {
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

  let checkboxElement;

  const template = '<formly-form form="form" model="model" fields="fields"/>';

  beforeEach(inject(function (_$rootScope_, _$compile_, $state, ValidationObserver, GuidanceFormObserver,
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

  function compile() {
    scope = $rootScope.$new();

    scope.model = {
      house: {
        hasFrontWindow: false
      }
    };

    scope.fields = [
      {
        id: 'house.hasFrontWindow',
        key: 'house.hasFrontWindow',
        type: 'toggle',
        templateOptions: {
          noBackendInteraction: false,
          label: 'Do you have a front window?',
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

    checkboxElement = $(element.find("input")[0]);
  }

  it('should have the correct element id', function () {
    compile();

    expect(elementIdGenerator.generateId).toHaveBeenCalledTimes(1);
    expect(elementIdGenerator.generateId).toHaveBeenCalledWith('house.hasFrontWindow', guidanceFormObserver);
    expect(checkboxElement.attr('id')).toBe('field-fake-id');
  });

  it('should create a toggle with a label', function () {
    compile();

    expect(checkboxElement.attr('type')).toBe('checkbox');
    expect($(element.find('label')[0]).text()).toContain('Do you have a front window?');
  });

  it('should update its internal state when the ngModel changes', function () {
    compile();

    expect(checkboxElement.is(':checked')).toBe(false);

    scope.model.house.hasFrontWindow = true;
    $rootScope.$apply();

    expect(checkboxElement.is(':checked')).toBe(true);
  });

  it('should let the "guidanceFormObserver" know values have changed', function () {
    compile();

    expect(guidanceFormObserver.formValueChanged).not.toHaveBeenCalled();

    expect(scope.model.house.hasFrontWindow).toBe(false);

    checkboxElement.click();
    $rootScope.$apply();

    expect(scope.model.house.hasFrontWindow).toBe(true);

    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(1);
    expect(guidanceFormObserver.formValueChanged.calls.mostRecent().args[0]).toEqual({
      focus: 'house.hasFrontWindow',
      value: true
    });

    checkboxElement.click();
    $rootScope.$apply();

    expect(scope.model.house.hasFrontWindow).toBe(false);

    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(2);
    expect(guidanceFormObserver.formValueChanged.calls.mostRecent().args[0]).toEqual({
      focus: 'house.hasFrontWindow',
      value: false
    });

    checkboxElement.click();
    $rootScope.$apply();

    expect(scope.model.house.hasFrontWindow).toBe(true);

    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(3);
    expect(guidanceFormObserver.formValueChanged.calls.mostRecent().args[0]).toEqual({
      focus: 'house.hasFrontWindow',
      value: true
    }, false);
  });

  it('should let the "guidanceFormObserver" know that the values have changed when noBackendInteraction is true', function () {
    compile();

    expect(guidanceFormObserver.formValueChanged).not.toHaveBeenCalled();
    expect(scope.model.house.hasFrontWindow).toBe(false);

    scope.fields[0].templateOptions.noBackendInteraction = true;
    $rootScope.$apply();

    checkboxElement.click();
    $rootScope.$apply();

    expect(scope.model.house.hasFrontWindow).toBe(true);
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(1);
    expect(guidanceFormObserver.formValueChanged.calls.mostRecent().args[0]).toEqual({
      focus: 'house.hasFrontWindow',
      value: true
    }, true);
  });

  describe("the 'disabled' functionality", function () {
    it('should made the field disabled if the templateOptions.disabled property evaluates to true', function () {
      compile();

      expect(checkboxElement.prop('disabled')).toBe(false);

      scope.fields[0].templateOptions.disabled = true;
      $rootScope.$apply();

      expect(checkboxElement.prop('disabled')).toBe(true);
    });

    it('should make the field disabled if the backend is busy', function () {
      spyOn(guidanceModeBackendState, 'getBackendIsBusy').and.returnValue(true);
      spyOn(guidanceModeBackendState, 'getPerformedAction').and.returnValue({
        event: ACTION_EVENT.CHANGED,
        focus: 'field'
      });
      compile();

      expect(checkboxElement.prop('disabled')).toBe(true);
    });
  });

  describe("the 'readonly' functionality", function () {
    it('should made the field disabled if the templateOptions.readonly property evaluates to true', function () {
      compile();

      expect(checkboxElement.prop('disabled')).toBe(false);

      scope.fields[0].templateOptions.readonly = true;
      $rootScope.$apply();

      expect(checkboxElement.prop('disabled')).toBe(true);
    });
  });

  describe('the validation functionality', function () {
    it('should set the field to erroneous if field errors occur', function () {
      compile();
      expect(scope.form.$valid).toBe(true);

      validationObserver.setErrors({
        'house.hasFrontWindow': ["An error occurred."]
      });
      $rootScope.$apply();
      expect(scope.form.$valid).toBe(false);
    });
  });
});
