'use strict';

describe('Form element: hashtag-text', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let scope;
  let element;
  let $rootScope;
  let $compile;
  let $q;
  let $timeout;

  let guidanceFormObserver;
  let validationObserver;

  let hashtagTextFactory;
  let hashtagDatasource;
  let promiseUtils;

  let guidanceModeBackendState;
  let elementIdGenerator;
  let ACTION_EVENT;

  let textareaElement;
  let inputElement;

  const template = '<formly-form form="form" model="model" fields="fields"/>';

  // Keeps track of lodashes original debounce function
  let lodashDebounce;

  // Keeps track of all the functions that went through the debounce.
  let debouncedFunctions;

  beforeEach(inject(function (_$rootScope_, _$compile_, _$q_, _$timeout_, $state, GuidanceFormObserver, ValidationObserver,
                              _hashtagTextFactory_, _hashtagDatasource_, _promiseUtils_, DEBOUNCE_TIME,
                              _guidanceModeBackendState_, _ACTION_EVENT_, _elementIdGenerator_) {
    $rootScope = _$rootScope_;
    $compile = _$compile_;
    $q = _$q_;
    $timeout = _$timeout_;

    guidanceModeBackendState = _guidanceModeBackendState_;
    elementIdGenerator = _elementIdGenerator_;
    ACTION_EVENT = _ACTION_EVENT_;

    validationObserver = new ValidationObserver();
    guidanceFormObserver = new GuidanceFormObserver();
    spyOn(guidanceFormObserver, 'formValueChanged');
    spyOn(elementIdGenerator, 'generateId').and.returnValue('field-fake-id');

    hashtagTextFactory = _hashtagTextFactory_;
    hashtagDatasource = _hashtagDatasource_;
    promiseUtils = _promiseUtils_;

    // Mock lodash debounce so we can test the key up events more easily.
    lodashDebounce = _.debounce;

    // Clear the debouncedFunctions
    debouncedFunctions = [];

    /*
     Mock the debounce so it immediately executes the function.
     Remember we are not testing 'lodash' it has plenty of tests itself.
     */
    _.debounce = function (fn, time) {
      debouncedFunctions.push(fn.name);

      expect(time).toBe(DEBOUNCE_TIME);
      return fn;
    };

    mockHelpers.blockUIRouter($state);
  }));

  // Reset the debounce to its original lodash function.
  afterEach(function () {
    _.debounce = lodashDebounce;
  });

  function compile(required = false, displayWysiwyg = false) {
    scope = $rootScope.$new();

    scope.model = {
      info: {
        "case": {
          "text": "",
          "tags": []
        }
      }
    };

    scope.fields = [
      {
        id: 'info.case',
        key: 'info.case',
        type: 'hashtagText',
        expressionProperties: {
          "templateOptions.disabled": 'false'
        },
        templateOptions: {
          datasourceName: 'Incoming',
          displayWysiwyg,
          noBackendInteraction: false,
          required,
          readonly: false
        }
      }
    ];

    const guidanceFormObserverAccessorElement = mockHelpers.createGuidanceFormObserverAccessor({
      $compile,
      $rootScope,
      guidanceFormObserver,
      validationObserver,
      suggestionsObserver: null
    });

    element = angular.element(template);
    element = $compile(element)(scope);

    guidanceFormObserverAccessorElement.append(element);

    $rootScope.$apply();

    textareaElement = $(element.find('textarea')[0]);
    inputElement = $(element.find('input')[0]);
  }

  it('should have the correct element id', function () {
    compile();

    expect(elementIdGenerator.generateId).toHaveBeenCalledTimes(1);
    expect(elementIdGenerator.generateId).toHaveBeenCalledWith('info.case', guidanceFormObserver);
    expect(textareaElement.attr('id')).toBe('field-fake-id');
    expect(inputElement.attr('id')).toBe('field-fake-id-tag');
  });

  it('should have the correct element id when we use WYSIWYG', function () {
    compile(false, true);

    expect(elementIdGenerator.generateId).toHaveBeenCalledTimes(2);
    expect(elementIdGenerator.generateId).toHaveBeenCalledWith('info.case', guidanceFormObserver);
    expect(textareaElement.attr('id')).toContain('taHtmlElement');
  });

  it('should wrap "guidanceModeDatasource.step" in a "promiseUtils.useLatest"', function () {
    spyOn(promiseUtils, 'useLatest').and.callThrough();

    compile(false);

    expect(promiseUtils.useLatest).toHaveBeenCalledTimes(2);

    const firstCallParams = promiseUtils.useLatest.calls.argsFor(0)[0];
    expect(firstCallParams).toBe(hashtagDatasource.search);

    const secondCallParams = promiseUtils.useLatest.calls.argsFor(1)[0];
    expect(secondCallParams).toBe(hashtagDatasource.search);
  });

  it('should debounce "textareaKeyPress" and "inputKeyPress"', function () {
    compile();

    expect(debouncedFunctions).toEqual(['textareaKeyPress', 'inputKeyPress']);
  });

  it('should populate its child elements with the values from the model', function () {
    compile();

    scope.model.info.case = {
      text: 'The quick brown fox jumped over the lazy dog.',
      tags: [{ hashtag: 'NL', id: '41' }, { hashtag: 'BE', id: '42' }]
    };

    $rootScope.$apply();

    expect(textareaElement.val()).toBe('The quick brown fox jumped over the lazy dog.');

    const tags = element.find('.tag');
    expect(tags.length).toBe(2);

    const nlTagElement = $(tags[0]);
    expect(nlTagElement.text()).toContain('#NL');

    const beTagElement = $(tags[1]);
    expect(beTagElement.text()).toContain('#BE');
  });

  it('should let the "guidanceFormObserver" know values have changed', function () {
    compile();

    expect(guidanceFormObserver.formValueChanged).not.toHaveBeenCalled();

    textareaElement.val("42").change();
    $rootScope.$apply();

    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(1);
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
      focus: 'info.case',
      value: {
        text: "42",
        tags: []
      }
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
      focus: 'info.case',
      value: {
        text: "wky",
        tags: []
      }
    }, true);
  });

  describe('textarea autocompletion', function () {
    it('should request the autocompletion from the back-end, and when clicked apply them.', function () {
      spyOn(hashtagDatasource, 'search').and.callFake(mockHelpers.resolvedPromise($q, [
        {
          "id": "41",
          "label": "Netherlands",
          "hashtag": "NL",
          "replacement": "the Netherlands"
        }, {
          "id": "42",
          "label": "Belgium",
          "hashtag": "BE",
          "replacement": "the Belgiums"
        }
      ]));

      compile();

      spyOn(hashtagTextFactory, 'caretPositionForElement').and.returnValue(3);
      spyOn(hashtagTextFactory, 'isCaretAtEndOfWord').and.returnValue(true);
      spyOn(hashtagTextFactory, 'threeWordsBackFromCaret').and.returnValue('the lazy dog');
      spyOn(hashtagTextFactory, 'applyAutocompletion').and.returnValue('The quick brown fox jumped over the Netherlands.');

      scope.model.info.case = {
        text: 'The quick brown fox jumped over the lazy dog.',
        tags: []
      };

      $rootScope.$apply();

      textareaElement.triggerHandler({ type: 'keypress', keyCode: 71 }); // 'G' key

      $rootScope.$apply();
      $timeout.flush();

      expect(hashtagDatasource.search).toHaveBeenCalledTimes(1);
      expect(hashtagDatasource.search).toHaveBeenCalledWith('Incoming', 'the lazy dog');

      expect(hashtagTextFactory.caretPositionForElement).toHaveBeenCalledTimes(1);
      expect(hashtagTextFactory.caretPositionForElement).toHaveBeenCalledWith(textareaElement[0]);

      expect(hashtagTextFactory.isCaretAtEndOfWord).toHaveBeenCalledTimes(1);
      expect(hashtagTextFactory.isCaretAtEndOfWord).toHaveBeenCalledWith('The quick brown fox jumped over the lazy dog.', 3);

      expect(hashtagTextFactory.threeWordsBackFromCaret).toHaveBeenCalledTimes(1);
      expect(hashtagTextFactory.threeWordsBackFromCaret).toHaveBeenCalledWith('The quick brown fox jumped over the lazy dog.', 3);

      const textareaSuggestionsElement = $(element.find('.suggestions')[0]);

      const boldElements = textareaSuggestionsElement.find('b');
      expect(boldElements.length).toBe(2);

      expect($(boldElements[0]).text()).toBe('Netherlands');
      expect($(boldElements[1]).text()).toBe('Belgium');

      const smallElements = textareaSuggestionsElement.find('small');
      expect(smallElements.length).toBe(2);

      expect($(smallElements[0]).text()).toBe('NL');
      expect($(smallElements[1]).text()).toBe('BE');

      $(textareaSuggestionsElement.find('li')[0]).click();
      $rootScope.$apply();

      expect(hashtagTextFactory.applyAutocompletion).toHaveBeenCalledTimes(1);
      expect(hashtagTextFactory.applyAutocompletion).toHaveBeenCalledWith('The quick brown fox jumped over the lazy dog.', 'the Netherlands', 'the lazy dog');

      expect(scope.model.info.case.text).toBe('The quick brown fox jumped over the Netherlands.');

      expect(scope.model.info.case.tags.length).toBe(1);
      expect(scope.model.info.case.tags[0].hashtag).toBe('NL');
      expect(scope.model.info.case.tags[0].id).toBe('41');

      // The autosuggestions should be empty now
      expect(textareaSuggestionsElement.find('b').length).toBe(0);
    });

    it('should when no caret can be found stop', function () {
      compile();

      spyOn(hashtagDatasource, 'search');

      spyOn(hashtagTextFactory, 'caretPositionForElement').and.returnValue(false);

      scope.model.info.case = {
        text: 'The quick brown fox jumped over the lazy dog.',
        tags: [{ hashtag: 'NL', id: '41' }, { hashtag: 'BE', id: '42' }]
      };

      $rootScope.$apply();

      textareaElement.triggerHandler({ type: 'keypress', keyCode: 13 });

      $rootScope.$apply();
      $timeout.flush();

      expect(hashtagDatasource.search).not.toHaveBeenCalled();

      expect(hashtagTextFactory.caretPositionForElement).toHaveBeenCalledTimes(1);
      expect(hashtagTextFactory.caretPositionForElement).toHaveBeenCalledWith(textareaElement[0]);
    });

    it('should when the caret is in the middle of a word stop', function () {
      compile();

      spyOn(hashtagDatasource, 'search');

      spyOn(hashtagTextFactory, 'caretPositionForElement').and.returnValue(1);
      spyOn(hashtagTextFactory, 'isCaretAtEndOfWord').and.returnValue(false);

      scope.model.info.case = {
        text: 'The quick brown fox jumped over the lazy dog.',
        tags: [{ hashtag: 'NL', id: '41' }, { hashtag: 'BE', id: '42' }]
      };

      $rootScope.$apply();

      textareaElement.triggerHandler({ type: 'keypress', keyCode: 13 });

      $rootScope.$apply();
      $timeout.flush();

      expect(hashtagDatasource.search).not.toHaveBeenCalled();

      expect(hashtagTextFactory.caretPositionForElement).toHaveBeenCalledTimes(1);
      expect(hashtagTextFactory.caretPositionForElement).toHaveBeenCalledWith(textareaElement[0]);

      expect(hashtagTextFactory.isCaretAtEndOfWord).toHaveBeenCalledTimes(1);
      expect(hashtagTextFactory.isCaretAtEndOfWord).toHaveBeenCalledWith('The quick brown fox jumped over the lazy dog.', 1);
    });

    it('should when the query is empty stop', function () {
      compile();

      spyOn(hashtagDatasource, 'search');

      spyOn(hashtagTextFactory, 'caretPositionForElement').and.returnValue(3);
      spyOn(hashtagTextFactory, 'isCaretAtEndOfWord').and.returnValue(true);
      spyOn(hashtagTextFactory, 'threeWordsBackFromCaret').and.returnValue('');

      scope.model.info.case = {
        text: 'The quick brown fox jumped over the lazy dog.',
        tags: [{ hashtag: 'NL', id: '41' }, { hashtag: 'BE', id: '42' }]
      };

      $rootScope.$apply();

      textareaElement.triggerHandler({ type: 'keypress', keyCode: 13 });

      $rootScope.$apply();
      $timeout.flush();

      expect(hashtagDatasource.search).not.toHaveBeenCalled();

      expect(hashtagTextFactory.caretPositionForElement).toHaveBeenCalledTimes(1);
      expect(hashtagTextFactory.caretPositionForElement).toHaveBeenCalledWith(textareaElement[0]);

      expect(hashtagTextFactory.isCaretAtEndOfWord).toHaveBeenCalledTimes(1);
      expect(hashtagTextFactory.isCaretAtEndOfWord).toHaveBeenCalledWith('The quick brown fox jumped over the lazy dog.', 3);

      expect(hashtagTextFactory.threeWordsBackFromCaret).toHaveBeenCalledTimes(1);
      expect(hashtagTextFactory.threeWordsBackFromCaret).toHaveBeenCalledWith('The quick brown fox jumped over the lazy dog.', 3);
    });

    describe('when to suppress key events', function () {
      const UP_KEY = 38;
      const DOWN_KEY = 40;
      const ENTER_KEY = 13;

      function checkSupression(keyCode) {
        const controller = element.find('hashtag-text-form-element').controller('hashtagTextFormElement');

        // Mock the autocompletions
        controller.textAutocompletions = [1, 2, 3];

        const preventDefaultSpy = jasmine.createSpy('preventDefault');
        textareaElement.triggerHandler({ type: 'keydown', keyCode: keyCode, preventDefault: preventDefaultSpy });

        $rootScope.$apply();

        expect(preventDefaultSpy).toHaveBeenCalledTimes(1);
      }

      function checkAllowance(keyCode) {
        const controller = element.find('hashtag-text-form-element').controller('hashtagTextFormElement');

        // Mock the autocompletions
        controller.textAutocompletions = [];

        const preventDefaultSpy = jasmine.createSpy('preventDefault');
        textareaElement.triggerHandler({ type: 'keydown', keyCode: keyCode, preventDefault: preventDefaultSpy });

        $rootScope.$apply();

        expect(preventDefaultSpy).not.toHaveBeenCalled();
      }

      it('should prevent ENTER events when pressed when there are autocompletions', function () {
        checkSupression(ENTER_KEY);
      });

      it('should prevent UP events when pressed when there are autocompletions', function () {
        checkSupression(UP_KEY);
      });

      it('should prevent DOWN events when pressed when there are autocompletions', function () {
        checkSupression(DOWN_KEY);
      });

      it('should allow ENTER events when pressed when there are no autocompletions', function () {
        checkAllowance(ENTER_KEY);
      });

      it('should allow UP events when pressed when there are no autocompletions', function () {
        checkAllowance(UP_KEY);
      });

      it('should allow DOWN events when pressed when there are no autocompletions', function () {
        checkAllowance(DOWN_KEY);
      });
    });
  });

  it('should know how to delete tags when the X is clicked', function () {
    compile();

    scope.model.info.case = {
      text: 'The quick brown fox jumped over the lazy dog.',
      tags: [{ hashtag: 'NL', id: '41' }, { hashtag: 'BE', id: '42' }]
    };

    $rootScope.$apply();

    const tags = element.find('a.icon-close');
    expect(tags.length).toBe(2);

    const nlAHrefElement = $(tags[0]);
    const beAHrefElement = $(tags[1]);

    nlAHrefElement.click();
    $rootScope.$apply();

    expect(element.find('a.icon-close').length).toBe(1);
    expect(scope.model.info.case.tags).toEqual([{ hashtag: 'BE', id: '42' }]);

    beAHrefElement.click();
    $rootScope.$apply();

    expect(element.find('a.icon-close').length).toBe(0);

    expect(scope.model.info.case.tags).toEqual([]);
  });

  describe('hashtag input autocompletion', function () {
    it('should request the autocompletion from the back-end, and when clicked apply them.', function () {
      spyOn(hashtagDatasource, 'search').and.callFake(mockHelpers.resolvedPromise($q, [
        {
          "id": "41",
          "label": "Netherlands",
          "hashtag": "NL"
        }, {
          "id": "42",
          "label": "Belgium",
          "hashtag": "BE"
        }
      ]));

      compile();

      scope.model.info.case = {
        text: 'The quick brown fox jumped over the lazy dog.',
        tags: []
      };

      $rootScope.$apply();

      inputElement.val('Netherlands');
      inputElement.triggerHandler({ type: 'keypress', keyCode: 83 }); // 'S' key

      $rootScope.$apply();
      $timeout.flush();

      expect(hashtagDatasource.search).toHaveBeenCalledTimes(1);
      expect(hashtagDatasource.search).toHaveBeenCalledWith('Incoming', 'Netherlands');

      const hashtaginputSuggestionsElement = $(element.find('.suggestions')[1]);

      const boldElements = hashtaginputSuggestionsElement.find('b');
      expect(boldElements.length).toBe(2);

      expect($(boldElements[0]).text()).toBe('NL');
      expect($(boldElements[1]).text()).toBe('BE');

      $(hashtaginputSuggestionsElement.find('li')[0]).click();
      $rootScope.$apply();

      expect(scope.model.info.case.tags.length).toBe(1);
      expect(scope.model.info.case.tags[0].hashtag).toBe('NL');
      expect(scope.model.info.case.tags[0].id).toBe('41');

      // The autosuggestions should be empty now
      expect(hashtaginputSuggestionsElement.find('b').length).toBe(0);

      expect(inputElement.val()).toBe('');
    });

    it('should not add the same tag twice', function () {
      spyOn(hashtagDatasource, 'search').and.callFake(mockHelpers.resolvedPromise($q, [
        {
          "id": "41",
          "label": "Netherlands",
          "hashtag": "NL"
        }, {
          "id": "42",
          "label": "Belgium",
          "hashtag": "BE"
        }
      ]));

      compile();

      scope.model.info.case = {
        text: 'The quick brown fox jumped over the lazy dog.',
        tags: [{ hashtag: 'NL', id: '41' }]
      };

      $rootScope.$apply();

      inputElement.val('Netherlands');
      inputElement.triggerHandler({ type: 'keypress', keyCode: 83 }); // S key;

      $rootScope.$apply();

      expect(hashtagDatasource.search).toHaveBeenCalledTimes(1);
      expect(hashtagDatasource.search).toHaveBeenCalledWith('Incoming', 'Netherlands');

      const hashtaginputSuggestionsElement = $(element.find('.suggestions')[1]);

      $(hashtaginputSuggestionsElement.find('li')[0]).click();
      $rootScope.$apply();

      expect(scope.model.info.case.tags.length).toBe(1);
      expect(scope.model.info.case.tags[0].hashtag).toBe('NL');
      expect(scope.model.info.case.tags[0].id).toBe('41');
    });

    describe('when to suppress key events', function () {
      const UP_KEY = 38;
      const DOWN_KEY = 40;

      function checkSupression(keyCode) {
        const controller = element.find('hashtag-text-form-element').controller('hashtagTextFormElement');

        // Mock the autocompletions
        controller.hashtagAutocompletions = [1, 2, 3];

        const preventDefaultSpy = jasmine.createSpy('preventDefault');
        inputElement.triggerHandler({ type: 'keydown', keyCode: keyCode, preventDefault: preventDefaultSpy });

        $rootScope.$apply();

        expect(preventDefaultSpy).toHaveBeenCalledTimes(1);
      }

      function checkAllowance(keyCode) {
        const controller = element.find('hashtag-text-form-element').controller('hashtagTextFormElement');

        // Mock the autocompletions
        controller.hashtagAutocompletions = [];

        const preventDefaultSpy = jasmine.createSpy('preventDefault');
        inputElement.triggerHandler({ type: 'keydown', keyCode: keyCode, preventDefault: preventDefaultSpy });

        $rootScope.$apply();

        expect(preventDefaultSpy).not.toHaveBeenCalled();
      }

      it('should prevent UP events when pressed when there are autocompletions', function () {
        checkSupression(UP_KEY);
      });

      it('should prevent DOWN events when pressed when there are autocompletions', function () {
        checkSupression(DOWN_KEY);
      });

      it('should allow UP events when pressed when there are no autocompletions', function () {
        checkAllowance(UP_KEY);
      });

      it('should allow DOWN events when pressed when there are no autocompletions', function () {
        checkAllowance(DOWN_KEY);
      });
    });
  });

  it('should set the ngModel value when the text changes', function () {
    compile();

    textareaElement.val('The quick brown fox jumped over the lazy dog.').change();

    $rootScope.$apply();

    expect(scope.model.info.case).toEqual({
      text: 'The quick brown fox jumped over the lazy dog.',
      tags: []
    });
  });

  describe('the validation functionality', function () {
    it('should set the field to erroneous if field errors occur', function () {
      compile();
      expect(scope.form.$valid).toBe(true);

      validationObserver.setErrors({
        "info.case": ["Objection!."]
      });
      $rootScope.$apply();
      expect(scope.form.$valid).toBe(false);
      expect(scope.form['info.case'].$error.BACK_END_ERROR).toBe(true);
    });
  });

  describe("the 'disabled' functionality", function () {
    it('should made the field readonly if the templateOptions.disabled property evaluates to true', function () {
      compile();

      scope.model.info.case = {
        text: 'The quick brown fox jumped over the lazy dog.',
        tags: [{ hashtag: 'NL', id: '41' }]
      };

      $rootScope.$apply();

      const nlTagCloseElement = $(element.find('.tag > a')[0]);
      expect(nlTagCloseElement.hasClass('ng-hide')).toBe(false);

      expect(textareaElement.prop('readonly')).toBe(false);
      expect(inputElement.prop('readonly')).toBe(false);

      scope.fields[0].templateOptions.disabled = true;
      $rootScope.$apply();

      expect(nlTagCloseElement.hasClass('ng-hide')).toBe(true);

      expect(textareaElement.prop('readonly')).toBe(true);
      expect(inputElement.prop('readonly')).toBe(true);
    });

    it('should make the field readonly if the backend is busy', function () {
      spyOn(guidanceModeBackendState, 'getBackendIsBusy').and.returnValue(true);
      spyOn(guidanceModeBackendState, 'getPerformedAction').and.returnValue({
        event: ACTION_EVENT.CHANGED,
        focus: 'field'
      });
      compile();

      scope.model.info.case = {
        text: 'The quick brown fox jumped over the lazy dog.',
        tags: [{ hashtag: 'NL', id: '41' }]
      };

      $rootScope.$apply();

      const nlTagCloseElement = $(element.find('.tag > a')[0]);

      expect(inputElement.prop('readonly')).toBe(true);
      expect(nlTagCloseElement.hasClass('ng-hide')).toBe(true);
      expect(textareaElement.prop('readonly')).toBe(true);
    });
  });

  describe("the 'readonly' functionality", function () {
    it('should made the field readonly if the templateOptions.readonly property evaluates to true', function () {
      compile();

      expect(element.find('strong').length).toBe(0);
      expect(element.find('textarea').length).toBe(1);

      scope.fields[0].templateOptions.readonly = true;
      scope.model.info.case = {
        text: 'This is read only',
        tags: [{ hashtag: 'NL', id: '41' }, { hashtag: 'BE', id: '42' }]
      };
      $rootScope.$apply();

      const strong = $(element.find('strong')[0]);
      expect(strong.attr('id')).toBe('field-fake-id');
      expect(strong.text()).toBe('This is read only');

      expect(element.find('textarea').length).toBe(0);

      const tags = element.find('.tag');
      expect(tags.length).toBe(2);

      const nlTagElement = $(tags[0]);
      expect(nlTagElement.text()).toContain('#NL');

      const beTagElement = $(tags[1]);
      expect(beTagElement.text()).toContain('#BE');
    });
  });

  describe("the 'required' functionality", function () {
    it('should not set any errors when the field is not required', function () {
      compile();
      expect(scope.form.$valid).toBe(true);
    });

    it('should set errors when the field is required and the object is empty', function () {
      compile(true);
      expectCaseToBeInvalid();
    });

    it('should remove errors when the street, houseNumber, postalCode and city are set', function () {
      compile(true);

      scope.model.info.case.text = '';
      $rootScope.$apply();
      expectCaseToBeInvalid();

      scope.model.info.case.text = 'Veldkant';
      $rootScope.$apply();
      expect(scope.form.$valid).toBe(true);
    });

    function expectCaseToBeInvalid() {
      expect(scope.form.$valid).toBe(false);
      expect(scope.form['info.case'].$error.required).toBe(true);
    }
  });
});
