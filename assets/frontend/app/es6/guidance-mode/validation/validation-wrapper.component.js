'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp:validationWrapper component
 * @description
 * # validationWrapper
 *
 * This component wraps a validation-messages component to feed it with internal and external messages.
 * It also displays a label for a field and transcludes so the form element is put in a div inside this component.
 *
 * Internal messages are the ones that come from AngularJS' form validations,
 * while external messages come from the backend.
 *
 * When the registerErrorsChangedCallback is invoked, we retrieve the external errors for all the fields.
 *
 * Let's say we have a form that only contains a first name and that validationsObserver.getErrorsForKey("firstname") returns:
 *
 * ["Something is wrong"]
 *
 * And on the form there is an error for the firstname as well:
 *
 * { required: true }
 *
 * This component then translates the 'required' error into a human-friendly error message and feeds the
 * underlying validation-messages component with the combination of this internal and external message.
 *
 * The messages are re-evaluated when either the registerErrorsChangedCallback is invoked or there is a change in the internal form errors.
 *
 * Example usage:
 *
 * <validation-wrapper
 *   form="form"
 *   label="First name"
 *   fields="[{ key: "firstname", type: "input", ...}]"
 *   template="header-top">
 *  <formly-transclude></formly-transclude> <!-- To put the actual fields into this component -->
 * </validation-wrapper>
 *
 * The 'template' defines which template to use, here are the options:
 *
 *  1) 'header-top' which renders the label as a bold header, and the form element below.
 *  2) 'label-left' which renders the label to the left and the form element to the right.
 *  3) 'label-top' which renders the label above the form element.
 */
angular.module('digitalWorkplaceApp')
  .component('validationWrapper', {
    /*
      Unfortunately ng-annotate doesn't recognize using 'templateUrl'
      as a function, so it won't auto 'array' inject this code. That is
      why we must do it ourselves.

      See: https://github.com/olov/ng-annotate
    */
    templateUrl: ["$attrs", function($attrs) {
      return `es6/guidance-mode/validation/templates/${$attrs.template}.html`;
    }],
    transclude: true,
    require: {
      guidanceObserversAccessor: '^guidanceObserversAccessor'
    },
    bindings: {
      form: "<",
      label: "@",
      fields: "<"
    },
    controller: 'InputValidationController as inputValidationController'
  });

angular.module('digitalWorkplaceApp')
  .controller('InputValidationController', function($scope, $translate, validationObserverFactory, DATE_FORMAT_ERROR, TIME_FORMAT_ERROR) {
    const inputValidationController = this;

    inputValidationController.messages = [];

    inputValidationController.$onInit = function() {
      const validationObserver = inputValidationController.guidanceObserversAccessor.getValidationObserver();

      $scope.$watchCollection('validationMessagesController.internalMessages', setMessages);

      let internalMessages = [];
      $scope.$watch('inputValidationController.form.$error', function() {
        internalMessages = _(inputValidationController.fields)
          .map('id')
          .map((id) => ({ id, formController: inputValidationController.form[id] }))
          .filter((object) => _.isEmpty(object.formController) === false)
          .flatMap((object) => unpackAngularMessages(object))
          .value();
        setMessages();
      }, true);

      let externalMessages = [];
      validationObserver.registerErrorsChangedCallback(function() {
        externalMessages = _(inputValidationController.fields)
          .map('key')
          .flatMap((key) => validationObserver.getErrorsForKey(key))
          .uniq()
          .value();
        setMessages();
      });

      // Combine both external and internal messages into big array.
      function setMessages() {
        inputValidationController.messages = _.concat(externalMessages, internalMessages);
      }
    };

    /*
     Takes the angular errors from a formControl:

     { required: true, minlength: true, maxlength: true }

     and transforms them into a translated array or messages:

     [
     "You must fill in firstname.",
     "firstname must be longer than 1 characters.",
     "firstname must be smaller than 10 characters.",
     "firstname must match the following pattern: '[A-Z]*'"
     ]
     */
    function unpackAngularMessages({id, formController}) {
      const result = [];

      const fieldDefinition = _.find(inputValidationController.fields, (fieldDefinition) => fieldDefinition.id === id);

      const translationParams = {
        label: inputValidationController.label,
        minlength: fieldDefinition.templateOptions.minlength,
        maxlength: fieldDefinition.templateOptions.maxlength,
        pattern: fieldDefinition.templateOptions.pattern
      };

      if (formController.$error.required) {
        result.push(message('MESSAGE_REQUIRED', 'requiredValidationMessage', fieldDefinition, translationParams));
      }

      if (formController.$error.minlength) {
        result.push(message('MESSAGE_MINLENGTH', 'minlengthValidationMessage', fieldDefinition, translationParams));
      }

      if (formController.$error.maxlength) {
        result.push(message('MESSAGE_MAXLENGTH', 'maxlengthValidationMessage', fieldDefinition, translationParams));
      }

      if (formController.$error.pattern) {
        result.push(message('MESSAGE_PATTERN', 'patternValidationMessage', fieldDefinition, translationParams));
      }

      if (formController.$error[DATE_FORMAT_ERROR]) {
        result.push($translate.instant('MESSAGE_DATE_FORMAT_ERROR', translationParams));
      }

      if (formController.$error[TIME_FORMAT_ERROR]) {
        result.push($translate.instant('MESSAGE_TIME_FORMAT_ERROR', translationParams));
      }

      return result;
    }

    /*
      Returns the message to show to the user. Is either the default
      message as defined by the digital workplace, or a custom message
      provided by the back-end.
    */
    function message(defaultMessage, customMessage, fieldDefinition, translationParams) {
      // If there is no custom message use the default message.
      if (_.isEmpty(fieldDefinition.templateOptions[customMessage])) {
        return $translate.instant(defaultMessage, translationParams);
      } else {
        return fieldDefinition.templateOptions[customMessage];
      }
    }
  });
