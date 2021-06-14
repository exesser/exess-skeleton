'use strict';

/*
 Variant of the datepicker in datetime mode. Contains all tests
 that specifically test the 'time'. It does not test the things
 that the 'datepicker-date.formly.spec.js' file already tests.
 */
describe('Form type: datepicker in datetime mode', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let scope;
  let element;
  let inputDateElement;
  let inputTimeElement;

  let $rootScope;
  let $compile;
  let elementIdGenerator;

  let guidanceFormObserver;
  let validationObserver;
  let suggestionsObserver;

  let template = '<formly-form form="form" model="model" fields="fields"/>';

  beforeEach(inject(function (_$rootScope_, _$compile_, $state, ValidationObserver, SuggestionsObserver,
                              GuidanceFormObserver, _elementIdGenerator_) {
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

  function compile(hasBorder = false, disabled = false, required = false, deliverydate = "") {
    scope = $rootScope.$new();

    scope.model = {
      future: {
        deliverydate
      }
    };

    scope.fields = [
      {
        id: 'future.deliverydate',
        key: 'future.deliverydate',
        type: 'datepicker',
        templateOptions: {
          disabled,
          noBackendInteraction: false,
          readonly: false,
          hasBorder,
          required,
          hasTime: true
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

    const inputElements = element.find("input");

    const firstInput = $(inputElements[0]);

    if (firstInput.attr('id') === 'future-deliverydate-time-field') {
      inputTimeElement = firstInput;
      inputDateElement = $(inputElements[1]);
    } else {
      inputDateElement = firstInput;
      inputTimeElement = $(inputElements[1]);
    }
  }

  it('should have the correct element id', function () {
    compile();

    expect(elementIdGenerator.generateId).toHaveBeenCalledTimes(1);
    expect(elementIdGenerator.generateId).toHaveBeenCalledWith('future.deliverydate', guidanceFormObserver);
    expect(inputDateElement.attr('id')).toBe('field-fake-id');
  });

  it('should update the input elements when the model value changes', function () {
    compile();

    scope.model.future.deliverydate = "2017-02-01 13:37:00";
    $rootScope.$apply();

    expect(inputDateElement.val()).toBe("01/02/2017");

    expect(inputTimeElement.val()).toBe("13:37");
    expect(inputTimeElement.attr('id')).toBe('future-deliverydate-time-field');
  });

  it('should update the model value when typing in a correct date and time', function () {
    compile();

    expect(scope.model.future.deliverydate).toBe("");

    inputDateElement.val('02/03/2016').change();
    inputDateElement.keyup();

    inputTimeElement.val('13:37').change();
    inputTimeElement.keyup();

    $rootScope.$apply();

    expect(scope.model.future.deliverydate).toBe("2016-03-02 13:37:00");
    expect(scope.form.$valid).toBe(true);

    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(1);
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
      focus: 'future.deliverydate',
      value: "2016-03-02 13:37:00"
    }, false);

    inputTimeElement.val('13:38').change();
    inputTimeElement.keyup();

    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(2);
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
      focus: 'future.deliverydate',
      value: "2016-03-02 13:38:00"
    }, false);

    inputDateElement.val('21/03/2016').change();
    inputDateElement.keyup();

    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(3);
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
      focus: 'future.deliverydate',
      value: "2016-03-21 13:38:00"
    }, false);
  });

  it('should when "24:00" is typed in keep the date as it is, but consider the model invalid.', function () {
    compile();

    expect(scope.model.future.deliverydate).toBe("");

    inputDateElement.val('02/03/2016').change();
    inputDateElement.keyup();

    inputTimeElement.val('24:00').change();
    inputTimeElement.keyup();

    $rootScope.$apply();

    expect(inputDateElement.val()).toBe('02/03/2016');
    expect(inputTimeElement.val()).toBe('24:00');

    expect(scope.model.future.deliverydate).toBe("");
    expect(scope.form.$valid).toBe(false);
  });

  it('should update the model value when typing in an empty strings', function () {
    compile();

    //First we set a date, the test is about the possibility to 'unset' this.
    scope.model.future.deliverydate = "2016-03-02";
    $rootScope.$apply();

    inputDateElement.val('').change();
    inputDateElement.keyup();

    inputTimeElement.val('').change();
    inputTimeElement.keyup();

    $rootScope.$apply();

    expect(scope.model.future.deliverydate).toBe("");
    expect(scope.form.$valid).toBe(true);
  });

  it('should update the model value when typing in an empty date but filled time', function () {
    compile();

    //First we set a date, the test is about the possibility to 'unset' this.
    scope.model.future.deliverydate = "2016-03-02";
    $rootScope.$apply();

    inputDateElement.val('').change();
    inputDateElement.keyup();

    inputTimeElement.val('13:37').change();
    inputTimeElement.keyup();

    $rootScope.$apply();

    expect(scope.model.future.deliverydate).toBe("");
    expect(scope.form.$valid).toBe(true);
  });

  it('should set the model to an empty string and set the field to invalid when the user types in an incorrect format', function () {
    compile();

    scope.model.future.deliverydate = "2016-03-02";
    $rootScope.$apply();

    expect(scope.form.$valid).toBe(true);

    inputDateElement.val('2016-03-').change();
    inputDateElement.keyup();
    $rootScope.$apply();

    inputTimeElement.val('13;').change();
    inputTimeElement.keyup();
    $rootScope.$apply();

    expect(scope.form.$valid).toBe(false);
    expect(scope.form['future.deliverydate'].$error.DATE_FORMAT_ERROR).toBe(true);
    expect(scope.form['future.deliverydate'].$error.TIME_FORMAT_ERROR).toBe(true);
    expect(scope.model.future.deliverydate).toBe("");
  });

  it('should set the model when selected via the datepicker', function () {
    compile();

    scope.model.future.deliverydate = "2016-03-02 13:37:00";

    const pikaDayElement = $('.pika-single');

    $(element.find(".button")).click();
    $rootScope.$apply();

    const dayButtonElement = pikaDayElement.find('tbody > tr > td > button').get(0);

    dayButtonElement.dispatchEvent(new Event('mousedown'));
    $rootScope.$apply();

    expect(scope.model.future.deliverydate).toBe("2016-03-01 13:37:00");
    expect(scope.form.$valid).toBe(true);
  });

  it('should ignore the LEFT and RIGHT keys when parsing a time', function () {
    compile();
    /*
     PhantomJS will not allow us to test if the 'cursor' position
     changes when the LEFT or RIGHT keys are entered. That is
     why we test this method on the datepickerFormElementController
     directly.
     */
    const datepickerFormElementController = element.find("datepicker-form-element").controller("datepickerFormElement");

    // LEFT
    expect(datepickerFormElementController.parseTime({ keyCode: 37 })).toBe(false);

    // RIGHT
    expect(datepickerFormElementController.parseTime({ keyCode: 39 })).toBe(false);
  });

  describe('isDisabled behavior', function () {
    it('should when disabled render an input and a button which are disabled', function () {
      compile(false, true, false, "2016-03-02 13:37:00");

      // The time is rendered next to the date
      expect(inputDateElement.length).toBe(1);
      expect(inputDateElement.val()).toBe('02/03/2016 13:37');
      expect(inputDateElement.attr('disabled')).toBe('disabled');

      const buttonElement = $(element.find("span.button")[0]);

      expect(buttonElement.length).toBe(1);
      expect(buttonElement.attr('disabled')).toBe('disabled');
    });

    it('should when not disabled render an input and a button which are clickable', function () {
      compile(false, false, false, "2016-03-02 13:37:00");

      expect(inputDateElement.length).toBe(1);
      expect(inputDateElement.val()).toBe('02/03/2016');
      expect(inputDateElement.attr('disabled')).toBe(undefined);

      expect(inputTimeElement.length).toBe(1);
      expect(inputTimeElement.val()).toBe('13:37');
      expect(inputTimeElement.attr('disabled')).toBe(undefined);

      const buttonElement = $(element.find("span.button")[0]);

      expect(buttonElement.length).toBe(1);
      expect(buttonElement.attr('disabled')).toBe(undefined);
    });
  });

  describe("the 'readonly' functionality", function () {
    it('should made the field readonly if the templateOptions.readonly property evaluates to true', function () {
      compile();

      expect(element.find('input').length).toBe(2);
      expect(element.find('strong').length).toBe(0);

      scope.fields[0].templateOptions.readonly = true;
      scope.model.future.deliverydate = "1989-03-21 21:00:00";
      $rootScope.$apply();

      expect(element.find('input').length).toBe(0);

      const strong = $(element.find('strong')[0]);
      expect(strong.attr('id')).toBe('field-fake-id');
      expect(strong.text()).toBe('21/03/1989 21:00');
    });
  });

  describe('the suggestion functionality', function () {
    it('should present options and choose a value when you select a suggestion', function () {
      compile();

      suggestionsObserver.setSuggestions({
        "future.deliverydate": [{
          label: "EOM",
          model: {
            /*
             * Notice that since we merge in a model here the structure is nested while
             * the key of the suggestion is separated with periods (which is the key coming back in
             * the initial definition of the field).
             */
            future: {
              "deliverydate": "1989-03-21 12:00:00"
            }
          }
        }, {
          label: "MEO",
          model: {
            /*
             * Notice that since we merge in a model here the structure is nested while
             * the key of the suggestion is separated with periods (which is the key coming back in
             * the initial definition of the field).
             */
            future: {
              "deliverydate": "1990-09-26 12:00:00"
            }
          }
        }]
      });
      $rootScope.$apply();

      $(element.find("input")).click();
      $rootScope.$apply();

      const pikaDayElement = $('.pika-single');

      const suggestions = pikaDayElement.find("button.button-secondary");
      expect(suggestions.length).toBe(2);

      //Choose the suggestion and check that the model has changed
      const eomSuggestion = $(suggestions[0]);
      expect(eomSuggestion.find('span').text()).toBe('EOM');

      eomSuggestion.click();
      $rootScope.$apply();

      expect(scope.model.future.deliverydate).toEqual("1989-03-21 12:00:00");

      expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(1);
      expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
        focus: 'future.deliverydate',
        value: '1989-03-21 12:00:00'
      }, false);

      //Choose the suggestion and check that the model has changed
      const meoSuggestion = $(suggestions[1]);
      expect(meoSuggestion.find('span').text()).toBe('MEO');

      meoSuggestion.click();
      $rootScope.$apply();

      expect(scope.model.future.deliverydate).toEqual("1990-09-26 12:00:00");

      expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(2);
      expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
        focus: 'future.deliverydate',
        value: '1990-09-26 12:00:00'
      }, false);
    });
  });
});
