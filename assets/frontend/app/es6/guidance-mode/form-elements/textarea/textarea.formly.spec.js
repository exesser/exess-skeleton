'use strict';

describe('Form type: textarea', function () {
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

  let textareaElement;

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

  function compile({ disabled = false, required = false, minlength, maxlength, pattern = '.*' } = {}) {
    scope = $rootScope.$new();

    scope.model = {
      person: {
        'firstName': ""
      }
    };
    scope.fields = [
      {
        id: 'person.firstName',
        key: 'person.firstName',
        type: 'textarea',
        templateOptions: {
          noBackendInteraction: false,
          disabled,
          readonly: false,
          required,
          minlength,
          maxlength,
          pattern
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

    textareaElement = $(element.find('textarea')[0]);
  }

  it('should have the correct element id', function () {
    compile();

    expect(elementIdGenerator.generateId).toHaveBeenCalledTimes(1);
    expect(elementIdGenerator.generateId).toHaveBeenCalledWith('person.firstName', guidanceFormObserver);
    expect(textareaElement.attr('id')).toBe('field-fake-id');
  });

  it('should update its internal state when the ngModel changes', function () {
    compile();

    expect(textareaElement.val()).toBe("");

    scope.model.person.firstName = "Jan";
    $rootScope.$apply();

    expect(textareaElement.val()).toBe("Jan");
  });

  it('should let the "guidanceFormObserver" know values have changed', function () {
    compile();

    expect(guidanceFormObserver.formValueChanged).not.toHaveBeenCalled();

    textareaElement.val("42").change();
    $rootScope.$apply();

    expect(scope.model.person.firstName).toBe('42');

    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(1);
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
      focus: 'person.firstName',
      value: "42"
    }, false);
  });

  it('should let the "guidanceFormObserver" know that the values have changed when noBackendInteraction is true', function () {
    compile();

    expect(guidanceFormObserver.formValueChanged).not.toHaveBeenCalled();
    expect(textareaElement.val()).toBe("");

    scope.fields[0].templateOptions.noBackendInteraction = true;
    $rootScope.$apply();

    textareaElement.val("wky").change();
    $rootScope.$apply();

    expect(textareaElement.val()).toBe("wky");
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(1);
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
      focus: 'person.firstName',
      value: "wky"
    }, true);
  });

  describe("the 'disabled' functionality", function () {
    it('should made the field readonly if the templateOptions.disabled property evaluates to true', function () {
      compile();

      expect(textareaElement.prop('readonly')).toBe(false);

      scope.fields[0].templateOptions.disabled = true;
      $rootScope.$apply();

      expect(textareaElement.prop('readonly')).toBe(true);
    });

    it('should make the field readonly if the backend is busy', function () {
      spyOn(guidanceModeBackendState, 'getBackendIsBusy').and.returnValue(true);
      spyOn(guidanceModeBackendState, 'getPerformedAction').and.returnValue({
        event: ACTION_EVENT.CHANGED,
        focus: 'field'
      });
      compile();

      expect(textareaElement.prop('readonly')).toBe(true);
    });
  });

  describe("the 'readonly' functionality", function () {
    it('should made the field readonly if the templateOptions.readonly property evaluates to true', function () {
      compile();

      expect(element.find('textarea').length).toBe(1);
      expect(element.find('strong').length).toBe(0);

      scope.fields[0].templateOptions.readonly = true;
      scope.model.person.firstName = "This is read only";
      $rootScope.$apply();

      expect(element.find('textarea').length).toBe(0);

      const strong = $(element.find('strong')[0]);
      expect(strong.attr('id')).toBe('field-fake-id');
      expect(strong.text()).toBe('This is read only');
    });
  });

  describe('the minlength functionality', function () {
    it('should initially not give an error', function () {
      compile({ minlength: 3 });
      expect(scope.form.$valid).toBe(true);
    });

    it('should give an error when a value smaller than the minlength is entered', function () {
      compile({ minlength: 3 });
      scope.model.person.firstName = "Ja";
      $rootScope.$apply();

      expect(scope.form.$valid).toBe(false);
      expect(scope.form['person.firstName'].$error.minlength).toBe(true);
    });
  });

  describe('the maxlength functionality', function () {
    it('should give an error when a value larger than the maxlength is entered', function () {
      compile({ maxlength: 3 });
      scope.model.person.firstName = "Jann";
      $rootScope.$apply();

      expect(scope.form.$valid).toBe(false);
      expect(scope.form['person.firstName'].$error.maxlength).toBe(true);
    });
  });

  describe("the 'required' functionality", function () {
    it('should not set any errors when the field is not required', function () {
      compile();
      expect(scope.form.$valid).toBe(true);
    });

    it('should set errors when the field is required and the object is empty', function () {
      compile({ required: true });
      expect(scope.form.$valid).toBe(false);
      expect(scope.form['person.firstName'].$error.required).toBe(true);
    });

    it('should remove errors when a value is set', function () {
      compile({ required: true });
      scope.model.person.firstName = "Jan";
      $rootScope.$apply();

      expect(scope.form.$valid).toBe(true);
    });
  });

  describe('the pattern functionality', function () {
    it('should not give an error when the value matches the pattern', function () {
      compile({ pattern: '.*szoon' });

      scope.model.person.firstName = "Janszoon";
      $rootScope.$apply();
      expect(scope.form.$valid).toBe(true);

      scope.model.person.firstName = "Pieterszoon";
      $rootScope.$apply();
      expect(scope.form.$valid).toBe(true);
    });

    it('should give an error when the value does not match the pattern', function () {
      compile({ pattern: '.*szoon' });
      scope.model.person.firstName = "Visser";
      $rootScope.$apply();

      expect(scope.form.$valid).toBe(false);
      expect(scope.form['person.firstName'].$error.pattern).toBe(true);
    });
  });

  describe('the validation functionality', function () {
    it('should set the field to erroneous if field errors occur', function () {
      compile();
      expect(scope.form.$valid).toBe(true);

      validationObserver.setErrors({
        "person.firstName": ["That is not a correct name."]
      });
      $rootScope.$apply();
      expect(scope.form.$valid).toBe(false);
      expect(scope.form['person.firstName'].$error.BACK_END_ERROR).toBe(true);
    });
  });

});
