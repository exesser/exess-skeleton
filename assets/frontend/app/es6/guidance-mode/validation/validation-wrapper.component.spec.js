'use strict';

describe('Component: validation-wrapper', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let $rootScope;
  let $compile;

  let validationObserver;
  let errorsChangedCallback = _.noop;

  let scope;
  let element;

  let DATE_FORMAT_ERROR;
  let TIME_FORMAT_ERROR;

  beforeEach(inject(function (_$rootScope_, _$compile_, $state, ValidationObserver, _DATE_FORMAT_ERROR_, _TIME_FORMAT_ERROR_) {
    $rootScope = _$rootScope_;
    $compile = _$compile_;
    mockHelpers.blockUIRouter($state);

    DATE_FORMAT_ERROR = _DATE_FORMAT_ERROR_;
    TIME_FORMAT_ERROR = _TIME_FORMAT_ERROR_;

    validationObserver = new ValidationObserver();
    spyOn(validationObserver, 'registerErrorsChangedCallback');

    scope = $rootScope.$new();
    scope.form = {};
  }));

  function makeTemplate(labelTemplate) {
    return `
      <validation-wrapper form="form"
                          fields="fields"
                          label="firstname"
                          template="${labelTemplate}">
        <p>Transcluded content</p>
      </validation-wrapper>
    `;
  }

  // Contains the shared test between templates: 'label-left', 'header-top', 'label-top'
  function testTemplate(labelTemplate) {
    beforeEach(function () {
      scope.fields = [
        {
          id: 'first-name',
          key: 'first-name',
          type: 'input',
          templateOptions: {
            minlength: 1,
            maxlength: 12,
            pattern: '[A-Z][a-z]+'
          }
        }
      ];

      const template = makeTemplate(labelTemplate);

      const guidanceFormObserverAccessorElement = mockHelpers.createGuidanceFormObserverAccessor({
        $compile,
        $rootScope,
        validationObserver
      });
      element = angular.element(template);
      element = $compile(element)(scope);
      guidanceFormObserverAccessorElement.append(element);
      $rootScope.$apply();

      expect(validationObserver.registerErrorsChangedCallback).toHaveBeenCalledTimes(1);
      errorsChangedCallback = validationObserver.registerErrorsChangedCallback.calls.argsFor(0)[0];
    });

    it('should transclude content', function () {
      expect($(element.find("p")[0]).text()).toBe("Transcluded content");
    });

    it('should render nothing if no messages are set.', function () {
      expect(element.find("span.error-message").length).toBe(0);
      expect(element.find("li.error-message").length).toBe(0);
    });

    it('should render a span if it receives one external message', function () {
      spyOn(validationObserver, 'getErrorsForKey').and.returnValue(["Something is wrong."]);
      errorsChangedCallback();
      $rootScope.$apply();

      expect(validationObserver.getErrorsForKey).toHaveBeenCalledTimes(1);
      expect(validationObserver.getErrorsForKey).toHaveBeenCalledWith("first-name");

      const errorMessage = element.find("span.error-message");
      expect(errorMessage.length).toBe(1);
      expect(errorMessage.text()).toBe("Something is wrong.");
      expect(element.find("li.error-message").length).toBe(0);
    });

    it('should render a span if it receives one internal message', function () {
      scope.form = {
        "first-name": {
          $error: {
            required: true
          }
        },
        $error: {
          required: true
        }
      };
      $rootScope.$apply();

      const errorMessage = element.find("span.error-message");
      expect(errorMessage.length).toBe(1);
      expect(errorMessage.text()).toBe("You must fill in firstname.");
      expect(element.find("li.error-message").length).toBe(0);
    });

    it('should render a list if it receives multiple external messages', function () {
      spyOn(validationObserver, 'getErrorsForKey').and.returnValue(["Something is wrong.", "Something is REALLY wrong."]);
      errorsChangedCallback();
      $rootScope.$apply();

      const errorMessages = element.find("li.error-message");
      expect(errorMessages.length).toBe(2);
      expect($(errorMessages[0]).text()).toBe("Something is wrong.");
      expect($(errorMessages[1]).text()).toBe("Something is REALLY wrong.");
      expect(element.find("span.error-message").length).toBe(0);
    });

    it('should render a list if it receives multiple internal messages', function () {
      scope.form = {
        "first-name": {
          $error: {
            minlength: true,
            maxlength: true,
            pattern: true
          }
        },
        $error: {
          minlength: true,
          maxlength: true,
          pattern: true
        }
      };
      $rootScope.$apply();

      const errorMessages = element.find("li.error-message");
      expect(errorMessages.length).toBe(3);
      expect($(errorMessages[0]).text()).toBe("firstname must be longer than 1 characters.");
      expect($(errorMessages[1]).text()).toBe("firstname must be smaller than 12 characters.");
      expect($(errorMessages[2]).text()).toBe("firstname must match the following pattern: '[A-Z][a-z]+'.");
      expect(element.find("span.error-message").length).toBe(0);
    });

    it('should render a list if it receives both internal and external messages', function () {
      //Internal messages
      scope.form = {
        "first-name": {
          $error: {
            minlength: true,
            maxlength: true,
            pattern: true
          }
        },
        $error: {
          minlength: true,
          maxlength: true,
          pattern: true
        }
      };

      scope.form['first-name'].$error[DATE_FORMAT_ERROR] = true;
      scope.form.$error[DATE_FORMAT_ERROR] = true;

      scope.form['first-name'].$error[TIME_FORMAT_ERROR] = true;
      scope.form.$error[TIME_FORMAT_ERROR] = true;

      //External messages
      spyOn(validationObserver, 'getErrorsForKey').and.returnValue(["Something is wrong.", "Something is REALLY wrong."]);
      errorsChangedCallback();

      $rootScope.$apply();

      const errorMessages = element.find("li.error-message");
      expect(errorMessages.length).toBe(7);
      expect($(errorMessages[0]).text()).toBe("Something is wrong.");
      expect($(errorMessages[1]).text()).toBe("Something is REALLY wrong.");
      expect($(errorMessages[2]).text()).toBe("firstname must be longer than 1 characters.");
      expect($(errorMessages[3]).text()).toBe("firstname must be smaller than 12 characters.");
      expect($(errorMessages[4]).text()).toBe("firstname must match the following pattern: '[A-Z][a-z]+'.");
      expect($(errorMessages[5]).text()).toBe("firstname has an incorrect date format. Correct format is: 'dd/mm/yyyy'.");
      expect($(errorMessages[6]).text()).toBe("firstname has an incorrect time format. Correct format is: 'hh:mm'.");
      expect(element.find("span.error-message").length).toBe(0);
    });

    it('should render a list with custom messages', function () {
      scope.fields[0].templateOptions.requiredValidationMessage = 'fill in';
      scope.fields[0].templateOptions.minlengthValidationMessage = 'to small';
      scope.fields[0].templateOptions.maxlengthValidationMessage = 'to big';
      scope.fields[0].templateOptions.patternValidationMessage = 'pattern is no good.';

      //Internal messages
      scope.form = {
        "first-name": {
          $error: {
            required: true,
            minlength: true,
            maxlength: true,
            pattern: true
          }
        },
        $error: {
          required: true,
          minlength: true,
          maxlength: true,
          pattern: true
        }
      };

      scope.form['first-name'].$error[DATE_FORMAT_ERROR] = true;
      scope.form.$error[DATE_FORMAT_ERROR] = true;

      scope.form['first-name'].$error[TIME_FORMAT_ERROR] = true;
      scope.form.$error[TIME_FORMAT_ERROR] = true;

      //External messages
      spyOn(validationObserver, 'getErrorsForKey').and.returnValue([]);
      errorsChangedCallback();

      $rootScope.$apply();

      const errorMessages = element.find("li.error-message");
      expect(errorMessages.length).toBe(6);
      expect($(errorMessages[0]).text()).toBe("fill in");
      expect($(errorMessages[1]).text()).toBe("to small");
      expect($(errorMessages[2]).text()).toBe("to big");
      expect($(errorMessages[3]).text()).toBe("pattern is no good.");
      expect($(errorMessages[4]).text()).toBe("firstname has an incorrect date format. Correct format is: 'dd/mm/yyyy'.");
      expect($(errorMessages[5]).text()).toBe("firstname has an incorrect time format. Correct format is: 'hh:mm'.");
      expect(element.find("span.error-message").length).toBe(0);
    });
  }

  describe('label-left', function () {
    testTemplate('label-left');

    it('should render with a "label-inline" CSS class', function () {
      expect(element.find('div.input.label-inline').length).toBe(1);
    });

    it('should render the label inside a <label> element', function () {
      const label = element.find("label");
      expect(label.length).toBe(1);
      expect(label.text()).toBe("firstname");
    });
  });

  describe('label-top', function () {
    testTemplate('label-top');

    it('should render the label inside a <label> element', function () {
      const label = element.find("label");
      expect(label.length).toBe(1);
      expect(label.text()).toBe("firstname");
    });
  });

  describe('header-top', function () {
    testTemplate('header-top');

    it('should render the label inside a <h3> element', function () {
      const h3 = element.find("h3");
      expect(h3.length).toBe(1);
      expect(h3.text()).toBe("firstname");
    });
  });
});
