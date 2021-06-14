'use strict';

describe('Form element: select (multiple)', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let scope;
  let element;
  let $rootScope;
  let $compile;

  let guidanceFormObserver;
  let validationObserver;
  let suggestionsObserver;

  let guidanceModeBackendState;
  let elementIdGenerator;
  let ACTION_EVENT;

  const template = '<formly-form form="form" model="model" fields="fields"/>';

  beforeEach(inject(function (_$rootScope_, _$compile_, $state, GuidanceFormObserver, ValidationObserver,
                              SuggestionsObserver, _guidanceModeBackendState_, _ACTION_EVENT_, _elementIdGenerator_) {
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

  function compile({ hasBorder = false, required = false } = {}) {
    scope = $rootScope.$new();

    scope.model = {
      question: {
        ultimateAnswer: []
      }
    };

    scope.fields = [
      {
        id: 'question.ultimateAnswer',
        key: 'question.ultimateAnswer',
        type: 'select',
        templateOptions: {
          label: 'Please select 42',
          noBackendInteraction: false,
          hasBorder,
          required,
          checkboxes: false,
          readonly: false,
          multipleSelect: true,
          options: [
            {
              name: "Forty-one",
              value: "41"
            },
            {
              name: "Forty-two",
              value: "42"
            },
            {
              name: "Forty-three",
              value: "43"
            }
          ]
        }
      }
    ];

    const guidanceFormObserverAccessorElement = mockHelpers.createGuidanceFormObserverAccessor({
      $compile,
      $rootScope,
      guidanceFormObserver,
      validationObserver,
      suggestionsObserver
    });
    element = angular.element(template);
    element = $compile(element)(scope);
    guidanceFormObserverAccessorElement.append(element);
    $rootScope.$apply();
  }

  it('should have the correct element id', function () {
    compile();

    expect(elementIdGenerator.generateId).toHaveBeenCalledTimes(1);
    expect(elementIdGenerator.generateId).toHaveBeenCalledWith('question.ultimateAnswer', guidanceFormObserver);
    expect($(element.find("select")[0]).attr('id')).toBe('field-fake-id');
  });

  it('should create a dropdown with three selectable options, and an arrow down.', function () {
    compile();

    const options = element.find("select option");
    expect(options.size()).toBe(3);

    expect($(options[0]).val()).toBe('string:41');
    expect($(options[0]).text()).toBe('Forty-one');

    expect($(options[1]).val()).toBe('string:42');
    expect($(options[1]).text()).toBe('Forty-two');

    expect($(options[2]).val()).toBe('string:43');
    expect($(options[2]).text()).toBe('Forty-three');

    expect(element.find("span.icon-arrow-down").length).toBe(1);
  });

  it('should create a dropdown that updates the ng-model value when clicking an option', function () {
    compile();

    expect(guidanceFormObserver.formValueChanged).not.toHaveBeenCalled();

    const options = element.find("select option");

    expect(scope.model.question.ultimateAnswer).toEqual([]);

    $(options[0]).prop('selected', true).change();
    $rootScope.$apply();

    expect(scope.model.question.ultimateAnswer).toEqual(["41"]);

    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(1);
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
      focus: 'question.ultimateAnswer',
      value: ["41"]
    }, false);

    $(options[1]).prop('selected', true).change();
    $rootScope.$apply();

    expect(scope.model.question.ultimateAnswer).toEqual(["41", "42"]);

    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(2);
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
      focus: 'question.ultimateAnswer',
      value: ["41", "42"]
    }, false);

    $(options[2]).prop('selected', true).change();
    $rootScope.$apply();

    expect(scope.model.question.ultimateAnswer).toEqual(["41", "42", "43"]);

    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(3);
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
      focus: 'question.ultimateAnswer',
      value: ["41", "42", "43"]
    }, false);
  });

  it('should let the "guidanceFormObserver" know that the values have changed when noBackendInteraction is true', function () {
    compile();

    expect(guidanceFormObserver.formValueChanged).not.toHaveBeenCalled();
    expect(scope.model.question.ultimateAnswer).toEqual([]);

    scope.fields[0].templateOptions.noBackendInteraction = true;
    $rootScope.$apply();

    $(element.find("select option")[2]).prop('selected', true).change();
    $rootScope.$apply();

    expect(scope.model.question.ultimateAnswer).toEqual(["43"]);
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(1);
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
      focus: 'question.ultimateAnswer',
      value: ["43"]
    }, true);
  });

  it('should update its internal state when the ngModel changes', function () {
    compile();

    let options = element.find("select option");
    expect($(options[0]).val()).toBe('string:41');
    expect($(options[1]).val()).toBe('string:42');
    expect($(options[2]).val()).toBe('string:43');

    //Initially we expect no elements to be chosen.
    expect($(options[0]).prop('selected')).toBe(false);
    expect($(options[1]).prop('selected')).toBe(false);
    expect($(options[2]).prop('selected')).toBe(false);

    scope.model.question.ultimateAnswer = ['42', '43'];
    $rootScope.$apply();

    expect($(options[0]).prop('selected')).toBe(false);
    expect($(options[1]).prop('selected')).toBe(true);
    expect($(options[2]).prop('selected')).toBe(true);
  });

  describe('the suggestions functionality', function () {
    it('should overwrite the options when receiving suggestions', function () {
      compile();

      suggestionsObserver.setSuggestions({
        'question.ultimateAnswer': [
          {
            name: "Aap",
            value: "1337"
          },
          {
            name: "Noot",
            value: "666"
          },
          {
            name: "Mies",
            value: "42"
          }
        ]
      });
      $rootScope.$apply();

      const options = element.find("select option");
      expect(options.size()).toBe(3);

      expect($(options[0]).val()).toBe('string:1337');
      expect($(options[0]).text()).toBe('Aap');

      expect($(options[1]).val()).toBe('string:666');
      expect($(options[1]).text()).toBe('Noot');

      expect($(options[2]).val()).toBe('string:42');
      expect($(options[2]).text()).toBe('Mies');
    });

    it('should not override the options when suggestions are empty', function () {
      compile();

      spyOn(suggestionsObserver, 'getSuggestionsForKey').and.returnValue(undefined);

      suggestionsObserver.setSuggestions({});
      $rootScope.$apply();

      expect(scope.model.question.ultimateAnswer).toEqual([]);

      const options = element.find("select option");
      expect(options.size()).toBe(3);

      expect($(options[0]).val()).toBe('string:41');
      expect($(options[0]).text()).toBe('Forty-one');

      expect($(options[1]).val()).toBe('string:42');
      expect($(options[1]).text()).toBe('Forty-two');

      expect($(options[2]).val()).toBe('string:43');
      expect($(options[2]).text()).toBe('Forty-three');
    });
  });

  describe('the validation functionality', function () {
    it('should set the field to erroneous if field errors occur', function () {
      compile();
      expect(scope.form.$valid).toBe(true);

      validationObserver.setErrors({
        "question.ultimateAnswer": ["An error occurred."]
      });
      $rootScope.$apply();
      expect(scope.form.$valid).toBe(false);
      expect(scope.form['question.ultimateAnswer'].$error.BACK_END_ERROR).toBe(true);
    });
  });

  describe('the border functionality', function () {
    it('should show a border when the hasBorder templateOption is true.', function () {
      compile({ hasBorder: true });

      expect(element.find('.editable-select').length).toBe(0);
    });

    it('should not show a border when the hasBorder templateOption is false.', function () {
      compile({ hasBorder: false });

      expect(element.find('.editable-select').length).toBe(1);
    });
  });

  describe("the 'required' functionality", function () {
    it('should not set any errors when the field is not required', function () {
      compile({ required: false });
      expect(scope.form.$valid).toBe(true);
    });

    it('should set errors when the field is required and the array is empty', function () {
      compile({ required: true });
      expect(scope.form.$valid).toBe(false);
      expect(scope.form['question.ultimateAnswer'].$error.required).toBe(true);
    });

    it('should remove errors when at least one value is chosen', function () {
      compile({ required: true });
      scope.model.question.ultimateAnswer = ["42"];
      $rootScope.$apply();

      expect(scope.form.$valid).toBe(true);
    });
  });

  describe("the 'disabled' functionality", function () {
    it('should made the field disabled if the templateOptions.disabled property evaluates to true', function () {
      compile();

      expect(element.find("select").prop('disabled')).toBe(false);

      scope.fields[0].templateOptions.disabled = true;
      $rootScope.$apply();

      expect(element.find("select").prop('disabled')).toBe(true);
    });

    it('should make the field disabled if the backend is busy', function () {
      spyOn(guidanceModeBackendState, 'getBackendIsBusy').and.returnValue(true);
      spyOn(guidanceModeBackendState, 'getPerformedAction').and.returnValue({
        event: ACTION_EVENT.CHANGED,
        focus: 'field'
      });
      compile();

      expect(element.find("select").prop('disabled')).toBe(true);
    });
  });

  describe("the 'readonly' functionality", function () {
    it('should made the field readonly if the templateOptions.readonly property evaluates to true', function () {
      compile();

      expect(element.find('select').length).toBe(1);
      expect(element.find('strong').length).toBe(0);

      scope.fields[0].templateOptions.readonly = true;
      scope.model.question.ultimateAnswer = ["41", "42"];
      $rootScope.$apply();

      expect(element.find('select').length).toBe(0);

      const strong = $(element.find('strong')[0]);
      expect(strong.attr('id')).toBe('field-fake-id');
      expect(strong.text()).toBe('Forty-one, Forty-two');

      scope.model.question.ultimateAnswer = [];
      $rootScope.$apply();

      expect(strong.attr('id')).toBe('field-fake-id');
      expect(strong.text()).toBe('');
    });
  });
});
