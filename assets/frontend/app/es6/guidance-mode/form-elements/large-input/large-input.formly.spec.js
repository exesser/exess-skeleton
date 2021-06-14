'use strict';

describe('Form type: large-input', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let scope;
  let element;

  let $rootScope;
  let $compile;

  let validationObserver;
  let suggestionsObserver;
  let guidanceFormObserver;

  let guidanceModeBackendState;
  let elementIdGenerator;
  let ACTION_EVENT;

  let inputElement;

  const template = '<formly-form form="form" model="model" fields="fields"/>';

  beforeEach(inject(function (_$rootScope_, _$compile_, $state, ValidationObserver, SuggestionsObserver,
                              GuidanceFormObserver, _guidanceModeBackendState_, _ACTION_EVENT_, _elementIdGenerator_) {
    $rootScope = _$rootScope_;
    $compile = _$compile_;

    guidanceModeBackendState = _guidanceModeBackendState_;
    elementIdGenerator = _elementIdGenerator_;
    ACTION_EVENT = _ACTION_EVENT_;

    validationObserver = new ValidationObserver();
    suggestionsObserver = new SuggestionsObserver();

    guidanceFormObserver = new GuidanceFormObserver();
    spyOn(guidanceFormObserver, 'formValueChanged');
    spyOn(elementIdGenerator, 'generateId').and.returnValue('field-fake-id');

    mockHelpers.blockUIRouter($state);
  }));

  function compile({ disabled = false, hasBorder = false, required = false, minlength, maxlength, pattern = '.*' } = {}) {
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
        type: 'large-input',
        templateOptions: {
          label: "First name",
          noBackendInteraction: false,
          disabled,
          readonly: false,
          hasBorder,
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
      suggestionsObserver,
      guidanceFormObserver
    });
    element = angular.element(template);
    element = $compile(element)(scope);
    guidanceFormObserverAccessorElement.append(element);
    $rootScope.$apply();

    inputElement = $(element.find("input")[0]);
  }

  it('should update its internal state when the ngModel changes', function () {
    compile();

    expect(inputElement.val()).toBe("");

    scope.model.person.firstName = "Jan";
    $rootScope.$apply();

    expect(inputElement.val()).toBe("Jan");
    expect(inputElement.attr('id')).toBe('field-fake-id');
  });

  it('should let the "guidanceFormObserver" know values have changed', function () {
    compile();

    expect(guidanceFormObserver.formValueChanged).not.toHaveBeenCalled();

    inputElement.val("42").change();
    $rootScope.$apply();

    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(1);
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
      focus: 'person.firstName',
      value: "42"
    }, false);
  });

  it('should let the "guidanceFormObserver" know that the values have changed when noBackendInteraction is true', function () {
    compile();

    expect(guidanceFormObserver.formValueChanged).not.toHaveBeenCalled();
    expect(inputElement.val()).toBe("");

    scope.fields[0].templateOptions.noBackendInteraction = true;
    $rootScope.$apply();

    inputElement.val("wky").change();
    $rootScope.$apply();

    expect(inputElement.val()).toBe("wky");
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(1);
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
      focus: 'person.firstName',
      value: "wky"
    }, true);
  });

  describe("the 'disabled' functionality", function () {
    it('should made the field readonly if the templateOptions.disabled property evaluates to true', function () {
      compile();

      expect(inputElement.prop('readonly')).toBe(false);

      scope.fields[0].templateOptions.disabled = true;
      $rootScope.$apply();

      expect(inputElement.prop('readonly')).toBe(true);
    });

    it('should make the field disabled if the backend is busy', function () {
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
      scope.model.person.firstName = "This is read only";
      $rootScope.$apply();

      expect(element.find('input').length).toBe(0);

      const strong = $(element.find('strong')[0]);
      expect(strong.attr('id')).toBe('field-fake-id');
      expect(strong.text()).toBe('This is read only');
    });
  });

  describe('the border functionality', function () {
    it('should show a border when the hasBorder templateOption is true.', function () {
      compile({ disabled: false, hasBorder: true });

      expect(element.find('.editable-input').length).toBe(0);
    });

    it('should not show a border when the hasBorder templateOption is false.', function () {
      compile({ disabled: false, hasBorder: false });

      expect(element.find('.editable-input').length).toBe(1);
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

  describe('the suggestion functionality', function () {
    it('should present options and choose a value when you select a suggestion', function () {
      compile();

      suggestionsObserver.setSuggestions({
        "person.firstName": [{
          label: "Henk",
          model: {
            /*
             * Notice that since we merge in a model here the structure is nested while
             * the key of the suggestion is separated with periods (which is the key coming back in
             * the initial definition of the field).
             */
            person: {
              "firstName": "Henk"
            }
          }
        }]
      });
      $rootScope.$apply();

      // check if for-elements are set correctly
      const autocomplete = $(element.find('autocomplete'));
      const autoCompleteController = autocomplete.controller('autocomplete');
      expect(autoCompleteController.forElements).toEqual(['field-fake-id']);

      // Check if the left and right text are properly set
      expect(autocomplete.attr('suggestion-left-text-property')).toBe('label');
      expect(autocomplete.attr('suggestion-right-text-property')).toBe('labelAddition');

      const suggestions = autocomplete.find("ul li");
      expect(suggestions.length).toBe(1);

      //Choose the suggestion and check that the model has changed
      $(suggestions[0]).click();
      $rootScope.$apply();

      expect(scope.model.person.firstName).toEqual("Henk");

      expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(1);
      expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
        focus: 'person.firstName',
        value: 'Henk'
      }, false);
    });
  });

});
