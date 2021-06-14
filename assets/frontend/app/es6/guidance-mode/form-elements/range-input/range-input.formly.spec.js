'use strict';

describe('Form type: range', function () {
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

  let inputElement;

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
      connection: {
        wattage: 0
      }
    };
    scope.fields = [{
      "id": "connection.wattage",
      "key": "connection.wattage",
      "type": "range",
      "templateOptions": {
        noBackendInteraction: false,
        "stepBy": 1,
        "min": 0,
        "max": 100,
        "disabled": false,
        "readonly": false
      }
    }];

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

    inputElement = $(element.find('input')[0]);
  }

  it('should render a range input slider', function () {
    compile();

    expect(elementIdGenerator.generateId).toHaveBeenCalledTimes(1);
    expect(elementIdGenerator.generateId).toHaveBeenCalledWith('connection.wattage', guidanceFormObserver);

    expect(inputElement.attr('id')).toBe("field-fake-id");
    expect(inputElement.attr('min')).toBe("0");
    expect(inputElement.attr('max')).toBe("100");
    expect(inputElement.attr('step')).toBe("1");
    expect(inputElement.val()).toBe("0");
  });

  it('should update its internal state when the ngModel changes', function () {
    compile();

    const tooltipElement = $(element.find(".tooltip-up")[0]);

    scope.model.connection.wattage = 75;
    $rootScope.$apply();

    expect(inputElement.val()).toBe("75");
    expect(tooltipElement.text()).toBe("75%");
  });

  it('should let the "guidanceFormObserver" know values have changed', function () {
    compile();

    expect(guidanceFormObserver.formValueChanged).not.toHaveBeenCalled();

    inputElement.val(1).change();
    $rootScope.$apply();
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(1);
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
      focus: 'connection.wattage',
      value: '1'
    }, false);

    inputElement.val(2).change();
    $rootScope.$apply();
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(2);
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
      focus: 'connection.wattage',
      value: '2'
    }, false);

    inputElement.val(3).change();
    $rootScope.$apply();
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(3);
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
      focus: 'connection.wattage',
      value: '3'
    }, false);
  });

  it('should let the "guidanceFormObserver" know that the values have changed when noBackendInteraction is true', function () {
    compile();

    expect(guidanceFormObserver.formValueChanged).not.toHaveBeenCalled();
    expect(inputElement.val()).toBe("0");

    scope.fields[0].templateOptions.noBackendInteraction = true;
    $rootScope.$apply();

    inputElement.val("1").change();
    $rootScope.$apply();

    expect(inputElement.val()).toBe("1");
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(1);
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
      focus: 'connection.wattage',
      value: '1'
    }, true);
  });

  it('should show the tooltip sliding and hide when finished', function () {
    compile();

    const tooltipUpElement = element.find('.tooltip-up')[0];

    inputElement.mousedown();
    $rootScope.$apply();

    expect(tooltipUpElement.style.opacity).toBe('1');
    expect(tooltipUpElement.style['z-index']).toBe('1');

    inputElement.mouseup();
    $rootScope.$apply();

    expect(tooltipUpElement.style.opacity).toBe('0');
    expect(tooltipUpElement.style['z-index']).toBe('0');
  });

  it('should update the width of the slider when the valid changes', function () {
    compile();

    inputElement.val(75).change();
    $rootScope.$apply();

    const backgroundElement = element.find('.range__background')[1];
    expect(backgroundElement.style.width).toBe('72%');
  });

  describe("the 'disabled' functionality", function () {
    it('should not change the value when you click it if the field is disabled', function () {
      compile();

      expect(inputElement.prop('readonly')).toBe(false);

      scope.fields[0].templateOptions.disabled = true;
      $rootScope.$apply();

      expect(inputElement.prop('readonly')).toBe(true);
    });

    it('should make the field readonly if the backend is busy', function () {
      spyOn(guidanceModeBackendState, 'getBackendIsBusy').and.returnValue(true);
      spyOn(guidanceModeBackendState, 'getPerformedAction').and.returnValue({
        event: ACTION_EVENT.CHANGED,
        focus: 'field'
      });
      compile();

      expect(inputElement.prop('readonly')).toBe(true);
    });
  });

  describe("the 'readonly' functionality", function () {
    it('should made the field readonly if the templateOptions.readonly property evaluates to true', function () {
      compile();

      expect(element.find('input').length).toBe(1);
      expect(element.find('strong').length).toBe(0);

      scope.fields[0].templateOptions.readonly = true;
      scope.model.connection.wattage = "This is read only";
      $rootScope.$apply();

      expect(element.find('input').length).toBe(0);

      const strong = $(element.find('strong')[0]);
      expect(strong.attr('id')).toBe('field-fake-id');
      expect(strong.text()).toBe('This is read only');
    });
  });

  describe('the validation functionality', function () {
    it('should set the field to erroneous if field errors occur', function () {
      compile();
      expect(scope.form.$valid).toBe(true);

      validationObserver.setErrors({
        'connection.wattage': ["An error occurred."]
      });
      $rootScope.$apply();
      expect(scope.form.$valid).toBe(false);
      expect(scope.form['connection.wattage'].$error.BACK_END_ERROR).toBe(true);
    });
  });
});
