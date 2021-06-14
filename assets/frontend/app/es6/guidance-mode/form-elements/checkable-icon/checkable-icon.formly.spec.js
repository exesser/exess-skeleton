'use strict';

describe('Form element: checkable icon', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let scope;
  let element;
  let $rootScope;
  let $compile;

  let guidanceModeBackendState;
  let elementIdGenerator;
  let ACTION_EVENT;

  let guidanceFormObserver;

  const template = '<formly-form model="model" fields="fields"/>';

  let electricityIcon;

  beforeEach(inject(function (_$rootScope_, _$compile_, $state, GuidanceFormObserver,
                              _guidanceModeBackendState_, _ACTION_EVENT_, _elementIdGenerator_) {
    $rootScope = _$rootScope_;
    $compile = _$compile_;
    mockHelpers.blockUIRouter($state);

    guidanceModeBackendState = _guidanceModeBackendState_;
    elementIdGenerator = _elementIdGenerator_;
    ACTION_EVENT = _ACTION_EVENT_;

    scope = $rootScope.$new();

    scope.model = {
      house: {
        electricity: false
      }
    };

    scope.fields = [
      {
        id: "house.electricity",
        key: "house.electricity",
        type: "checkable-icon",
        templateOptions: {
          iconClass: "icon-elektriciteit",
          noBackendInteraction: false,
          disabled: false,
          readonly: false
        }
      }
    ];

    guidanceFormObserver = new GuidanceFormObserver();
    spyOn(guidanceFormObserver, 'formValueChanged');
    spyOn(elementIdGenerator, 'generateId').and.returnValue('field-fake-id');

    const guidanceFormObserverAccessorElement = mockHelpers.createGuidanceFormObserverAccessor({
      $compile,
      $rootScope,
      guidanceFormObserver
    });
    element = angular.element(template);
    element = $compile(element)(scope);
    guidanceFormObserverAccessorElement.append(element);

    $rootScope.$apply();

    electricityIcon = $(element.find("input")[0]);
  }));

  it('should have the correct element id', function () {
    expect(elementIdGenerator.generateId).toHaveBeenCalledTimes(1);
    expect(elementIdGenerator.generateId).toHaveBeenCalledWith('house.electricity', guidanceFormObserver);
    expect(electricityIcon.attr('id')).toBe('field-fake-id');
  });

  it('should populate with the values from the model', function () {
    expect(electricityIcon.prop('checked')).toBe(false);

    scope.model = {
      house: {
        electricity: false
      }
    };
    $rootScope.$apply();

    expect(electricityIcon.prop('checked')).toBe(false);
  });

  it('should let the "guidanceFormObserver" know values have changed', function () {
    expect(guidanceFormObserver.formValueChanged).not.toHaveBeenCalled();

    electricityIcon.click();
    $rootScope.$apply();

    expect(scope.model.house.electricity).toBe(true);

    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(1);
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
      focus: 'house.electricity',
      value: true
    }, false);

    electricityIcon.click();
    $rootScope.$apply();

    expect(scope.model.house.electricity).toBe(false);

    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(2);
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
      focus: 'house.electricity',
      value: false
    }, false);
  });

  it('should let the "guidanceFormObserver" know that the values have changed when noBackendInteraction is true', function () {
    expect(guidanceFormObserver.formValueChanged).not.toHaveBeenCalled();
    expect(scope.model.house.electricity).toBe(false);

    scope.fields[0].templateOptions.noBackendInteraction = true;
    $rootScope.$apply();

    electricityIcon.click();
    $rootScope.$apply();

    expect(scope.model.house.electricity).toBe(true);
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(1);
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
      focus: 'house.electricity',
      value: true
    }, true);
  });

  describe("the 'disabled' functionality", function () {
    it('should made the icon disabled if the templateOptions.disabled property evaluates to true', function () {
      const electricityIcon = $(element.find("input")[0]);
      expect(electricityIcon.prop('disabled')).toBe(false);

      scope.fields[0].templateOptions.disabled = true;
      $rootScope.$apply();

      expect(electricityIcon.prop('disabled')).toBe(true);
    });

    it('should make the field disabled if the backend is busy', function () {
      spyOn(guidanceModeBackendState, 'getBackendIsBusy').and.returnValue(true);
      spyOn(guidanceModeBackendState, 'getPerformedAction').and.returnValue({
        event: ACTION_EVENT.CHANGED,
        focus: 'field'
      });

      $rootScope.$apply();

      expect(electricityIcon.prop('disabled')).toBe(true);
    });
  });

  describe("the 'readonly' functionality", function () {
    it('should made the icon disabled if the templateOptions.disabled property evaluates to true', function () {
      const electricityIcon = $(element.find("input")[0]);
      expect(electricityIcon.prop('disabled')).toBe(false);

      scope.fields[0].templateOptions.readonly = true;
      $rootScope.$apply();

      expect(electricityIcon.prop('disabled')).toBe(true);
    });
  });

});
