'use strict';

describe('Form element: select (checkboxes)', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let scope;
  let element;
  let $rootScope;
  let $compile;

  let guidanceFormObserver;
  let validationObserver;
  let suggestionsObserver;

  let elementIdGenerator;

  const template = '<formly-form form="form" model="model" fields="fields"/>';

  beforeEach(inject(function (_$rootScope_, _$compile_, $state, GuidanceFormObserver, ValidationObserver,
                              SuggestionsObserver, _elementIdGenerator_) {
    $rootScope = _$rootScope_;
    $compile = _$compile_;

    elementIdGenerator = _elementIdGenerator_;

    validationObserver = new ValidationObserver();
    suggestionsObserver = new SuggestionsObserver();

    guidanceFormObserver = new GuidanceFormObserver();
    spyOn(guidanceFormObserver, 'formValueChanged');
    spyOn(elementIdGenerator, 'generateId').and.returnValue('field-fake-id');

    mockHelpers.blockUIRouter($state);
  }));

  function compile({ multipleSelect = true } = {}) {
    scope = $rootScope.$new();

    scope.model = {
      question: {
        ultimateAnswer: ''
      }
    };

    scope.fields = [
      {
        id: 'question.ultimateAnswer',
        key: 'question.ultimateAnswer',
        type: 'select',
        templateOptions: {
          noBackendInteraction: false,
          label: 'Please select 42',
          hasBorder: false,
          required: false,
          checkboxes: true,
          readonly: false,
          multipleSelect,
          options: [
            {
              name: "Forty-one",
              value: "41"
            },
            {
              name: "Forty-two",
              value: "42",
              disabled: false
            },
            {
              name: "Forty-three",
              value: "43",
              disabled: true
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
    expect($(element.find("div")[1]).attr('id')).toBe('field-fake-id');
  });

  it('should create a list of three checkboxes in labels', function () {
    compile();

    const checkboxes = element.find("input[type=\"checkbox\"]");
    expect(checkboxes.size()).toBe(3);

    const labels = element.find("label");
    expect(labels.size()).toBe(3);

    expect($(labels[0]).text()).toContain('Forty-one');
    expect($(labels[1]).text()).toContain('Forty-two');
    expect($(labels[2]).text()).toContain('Forty-three');
  });

  it('should notify that tha value has changed when we click on a checkbox with multiselect', function () {
    compile();
    const checkboxes = element.find("input[type=\"checkbox\"]");

    expect(scope.model.question.ultimateAnswer).toEqual('');
    expect(guidanceFormObserver.formValueChanged).not.toHaveBeenCalled();

    $(checkboxes[0]).click();

    expect(scope.model.question.ultimateAnswer).toEqual(["41"]);
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(2);
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
      focus: 'question.ultimateAnswer',
      value: ["41"]
    }, false);

    $(checkboxes[1]).click();

    expect(scope.model.question.ultimateAnswer).toEqual(["41", "42"]);
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(3);
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
      focus: 'question.ultimateAnswer',
      value: ["41", "42"]
    }, false);

    $(checkboxes[0]).click();

    expect(scope.model.question.ultimateAnswer).toEqual(["42"]);
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(4);
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
      focus: 'question.ultimateAnswer',
      value: ["42"]
    }, false);

    // 3rd options is disabled so it should not change the model
    $(checkboxes[2]).attr('disabled', false);
    $(checkboxes[2]).click();

    expect(scope.model.question.ultimateAnswer).toEqual(["42"]);
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(4);
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
      focus: 'question.ultimateAnswer',
      value: ["42"]
    }, false);

    $(checkboxes[0]).click();

    expect(scope.model.question.ultimateAnswer).toEqual(["41", "42"]);
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
      focus: 'question.ultimateAnswer',
      value: ["41", "42"]
    }, false);
  });

  it('should notify that tha value has changed when we click on a checkbox without multiselect', function () {
    compile({ multipleSelect: false });
    const checkboxes = element.find("input[type=\"checkbox\"]");

    expect(scope.model.question.ultimateAnswer).toEqual("");
    expect(guidanceFormObserver.formValueChanged).not.toHaveBeenCalled();

    $(checkboxes[0]).click();

    expect(scope.model.question.ultimateAnswer).toEqual("41");
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(1);
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
      focus: 'question.ultimateAnswer',
      value: "41"
    }, false);

    $(checkboxes[1]).click();

    expect(scope.model.question.ultimateAnswer).toEqual("42");
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(2);
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
      focus: 'question.ultimateAnswer',
      value: "42"
    }, false);

    $(checkboxes[0]).click();

    expect(scope.model.question.ultimateAnswer).toEqual("41");
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(3);
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
      focus: 'question.ultimateAnswer',
      value: "41"
    }, false);

    $(checkboxes[0]).click();

    expect(scope.model.question.ultimateAnswer).toEqual("");
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(4);
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
      focus: 'question.ultimateAnswer',
      value: ""
    }, false);
  });
});
