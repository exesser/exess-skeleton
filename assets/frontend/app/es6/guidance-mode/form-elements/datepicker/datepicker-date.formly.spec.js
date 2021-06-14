'use strict';

/*
 Variant of the datepicker in date mode.

 For tests about the datetime see: that the 'datepicker-datetime.formly.spec.js'
 file. This file contains the test that both the 'date' and 'datetime'
 mode have in common.
 */
describe('Form type: datepicker in date mode', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let scope;
  let element;
  let inputElement;

  let $rootScope;
  let $compile;

  let guidanceModeBackendState;
  let ACTION_EVENT;

  let guidanceFormObserver;
  let validationObserver;
  let suggestionsObserver;

  let elementPosition;

  let template = '<formly-form form="form" model="model" fields="fields"/>';

  beforeEach(inject(function (_$rootScope_, _$compile_, $state, ValidationObserver,
                              SuggestionsObserver, GuidanceFormObserver, _elementPosition_,
                              _guidanceModeBackendState_, _ACTION_EVENT_) {
    $rootScope = _$rootScope_;
    $compile = _$compile_;

    guidanceModeBackendState = _guidanceModeBackendState_;
    ACTION_EVENT = _ACTION_EVENT_;

    validationObserver = new ValidationObserver();
    suggestionsObserver = new SuggestionsObserver();

    guidanceFormObserver = new GuidanceFormObserver();
    spyOn(guidanceFormObserver, 'formValueChanged');

    elementPosition = _elementPosition_;

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
          hasTime: false
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

    inputElement = $(element.find("input")[0]);
  }

  it('should update the input element when the model value changes', function () {
    compile();

    scope.model.future.deliverydate = "2017-02-01";
    $rootScope.$apply();
    expect(inputElement.val()).toBe("01/02/2017");
    expect(inputElement.attr('id')).toBe('future-deliverydate-field');
  });

  it('should update the model value when typing in a correct date', function () {
    compile();

    expect(scope.model.future.deliverydate).toBe("");

    inputElement.val('02/03/2016').change();
    inputElement.keyup();
    $rootScope.$apply();

    expect(scope.model.future.deliverydate).toBe("2016-03-02");
    expect(scope.form.$valid).toBe(true);

    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(1);
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
      focus: 'future.deliverydate',
      value: "2016-03-02"
    }, false);
  });

  it('should update the model when selecting a date', function () {
    compile(false, false, false, "2017-02-01");

    expect(guidanceFormObserver.formValueChanged).not.toHaveBeenCalled();

    const pikaDayElement = $('.pika-single');

    $(element.find(".button")).click();
    $rootScope.$apply();

    //The second button is for the 2nd of January
    const dayButtonElement = pikaDayElement.find('tbody > tr > td > button').get(1);

    dayButtonElement.dispatchEvent(new Event('mousedown'));
    $rootScope.$apply();

    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(1);
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
      focus: 'future.deliverydate',
      value: "2017-02-02"
    }, false);
  });

  it('should let the "guidanceFormObserver" know that the values have changed when noBackendInteraction is true', function () {
    compile(false, false, false, "2017-02-01");

    const pikaDayElement = $('.pika-single');

    expect(guidanceFormObserver.formValueChanged).not.toHaveBeenCalled();
    expect(pikaDayElement.val()).toBe("");

    scope.fields[0].templateOptions.noBackendInteraction = true;
    $rootScope.$apply();

    $(element.find(".button")).click();
    $rootScope.$apply();

    //The second button is for the 2nd of January
    const dayButtonElement = pikaDayElement.find('tbody > tr > td > button').get(1);

    dayButtonElement.dispatchEvent(new Event('mousedown'));
    $rootScope.$apply();

    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(1);
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
      focus: 'future.deliverydate',
      value: "2017-02-02"
    }, true);
  });

  it('should update the model value when typing in an empty string', function () {
    compile();

    //First we set a date, the test is about the possibility to 'unset' this.
    scope.model.future.deliverydate = "2016-03-02";
    $rootScope.$apply();

    inputElement.val('').change();
    inputElement.keyup();
    $rootScope.$apply();

    expect(scope.model.future.deliverydate).toBe("");
    expect(scope.form.$valid).toBe(true);
  });

  it('should set the model to an empty string and set the field to invalid when the user types in an incorrect format', function () {
    compile();

    scope.model.future.deliverydate = "2016-03-02";
    $rootScope.$apply();

    expect(scope.form.$valid).toBe(true);

    inputElement.val('2016-03-').change();
    inputElement.keyup();
    $rootScope.$apply();

    expect(scope.form.$valid).toBe(false);
    expect(scope.form['future.deliverydate'].$error.DATE_FORMAT_ERROR).toBe(true);
    expect(scope.model.future.deliverydate).toBe("");
  });

  it('should set the model when selected via the datepicker', function () {
    compile();

    scope.model.future.deliverydate = "2016-03-02";

    const pikaDayElement = $('.pika-single');

    $(element.find(".button")).click();
    $rootScope.$apply();

    const dayButtonElement = pikaDayElement.find('tbody > tr > td > button').get(0);

    dayButtonElement.dispatchEvent(new Event('mousedown'));
    $rootScope.$apply();

    expect(scope.model.future.deliverydate).toBe("2016-03-01");
    expect(scope.form.$valid).toBe(true);
  });

  it('should pick the best place for the datepicker', function () {
    compile();

    const isAboveFoldSpy = spyOn(elementPosition, 'isAboveFold');
    isAboveFoldSpy.and.returnValue(true);

    expect(inputElement.attr('position')).toBe('bottom left');

    isAboveFoldSpy.and.returnValue(false);
    $rootScope.$apply();

    expect(inputElement.attr('position')).toBe('top left');
  });

  it('should ignore the LEFT and RIGHT keys when parsing a date', function () {
    compile();

    /*
     PhantomJS will not allow us to test if the 'cursor' position
     changes when the LEFT or RIGHT keys are entered. That is
     why we test this method on the datepickerFormElementController
     directly.
     */
    const datepickerFormElementController = element.find("datepicker-form-element").controller("datepickerFormElement");

    // LEFT
    expect(datepickerFormElementController.parseDate({ keyCode: 37 })).toBe(false);

    // RIGHT
    expect(datepickerFormElementController.parseDate({ keyCode: 39 })).toBe(false);
  });

  describe("the border functionality", function () {
    it('should pass along the "has-border" property', function () {
      compile(true);

      expect(element.find('.editable-datepicker').length).toBe(0);

      compile(false);

      expect(element.find('.editable-datepicker').length).toBe(1);
    });
  });

  describe("the 'required' functionality", function () {
    it('should not set any errors when the field is not required', function () {
      compile();
      expect(scope.form.$valid).toBe(true);
    });

    it('should set errors if the field is required and there is no date set', function () {
      compile(false, false, true);
      expect(scope.form.$valid).toBe(false);
      expect(scope.form['future.deliverydate'].$error.required).toBe(true);
    });
  });

  describe('the validation functionality', function () {
    it('should set the field to erroneous if field errors occur', function () {
      compile();
      expect(scope.form.$valid).toBe(true);

      validationObserver.setErrors({
        "future.deliverydate": ["That is not a correct date."]
      });
      $rootScope.$apply();
      expect(scope.form.$valid).toBe(false);
      expect(scope.form['future.deliverydate'].$error.BACK_END_ERROR).toBe(true);
    });
  });

  describe('isDisabled behavior', function () {
    it('should when disabled render an input and a button which are disabled', function () {
      compile(false, true, false, "2016-03-02");

      expect(inputElement.length).toBe(1);
      expect(inputElement.val()).toBe('02/03/2016');
      expect(inputElement.attr('disabled')).toBe('disabled');

      const buttonElement = $(element.find("span.button")[0]);

      expect(buttonElement.length).toBe(1);
      expect(buttonElement.attr('disabled')).toBe('disabled');
    });

    it('should when not disabled render an input and a button which are clickable', function () {
      compile(false, false, false, "2016-03-02");

      expect(inputElement.length).toBe(1);
      expect(inputElement.val()).toBe('02/03/2016');
      expect(inputElement.attr('disabled')).toBe(undefined);

      const buttonElement = $(element.find("span.button")[0]);

      expect(buttonElement.length).toBe(1);
      expect(buttonElement.attr('disabled')).toBe(undefined);
    });

    it('should make the field disabled if the backend is busy', function () {
      spyOn(guidanceModeBackendState, 'getBackendIsBusy').and.returnValue(true);
      spyOn(guidanceModeBackendState, 'getPerformedAction').and.returnValue({
        event: ACTION_EVENT.CHANGED,
        focus: 'field'
      });
      compile();

      const buttonElement = $(element.find("span.button")[0]);
      expect(inputElement.prop('disabled')).toBe(true);
      expect(buttonElement.attr('disabled')).toBe('disabled');
    });
  });

  describe("the 'readonly' functionality", function () {
    it('should made the field readonly if the templateOptions.readonly property evaluates to true', function () {
      compile();

      expect(element.find('input').length).toBe(1);
      expect(element.find('strong').length).toBe(0);

      scope.fields[0].templateOptions.readonly = true;
      scope.model.future.deliverydate = "1989-03-21";
      $rootScope.$apply();

      expect(element.find('input').length).toBe(0);

      const strong = $(element.find('strong')[0]);
      expect(strong.attr('id')).toBe('future-deliverydate-field');
      expect(strong.text()).toBe('21/03/1989');
    });
  });

  describe('pikaday datepicker open behavior', function () {
    it('should open the pikaday when the input is focused', function () {
      compile();

      const pikaDayElement = $('.pika-single');

      expect(pikaDayElement.hasClass('is-hidden')).toBe(true);

      $(element.find("input")).click();
      $rootScope.$apply();

      expect(pikaDayElement.hasClass('is-hidden')).toBe(false);
    });

    it('should open the pikaday when the button is clicked', function () {
      compile();

      const pikaDayElement = $('.pika-single');

      expect(pikaDayElement.hasClass('is-hidden')).toBe(true);

      $(element.find(".button")).click();
      $rootScope.$apply();

      expect(pikaDayElement.hasClass('is-hidden')).toBe(false);
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
              "deliverydate": "1989-03-21"
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
              "deliverydate": "1990-09-26"
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

      expect(scope.model.future.deliverydate).toEqual("1989-03-21");

      expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(1);
      expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
        focus: 'future.deliverydate',
        value: '1989-03-21'
      }, false);

      //Choose the suggestion and check that the model has changed
      const meoSuggestion = $(suggestions[1]);
      expect(meoSuggestion.find('span').text()).toBe('MEO');

      meoSuggestion.click();
      $rootScope.$apply();

      expect(scope.model.future.deliverydate).toEqual("1990-09-26");

      expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(2);
      expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
        focus: 'future.deliverydate',
        value: '1990-09-26'
      }, false);
    });
  });
});
