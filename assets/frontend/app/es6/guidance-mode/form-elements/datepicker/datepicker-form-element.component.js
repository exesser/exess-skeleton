"use strict";

(function (jquery) {
  /**
   * @ngdoc function
   * @name digitalWorkplaceApp:datepickerFormElement component
   * @description
   * # datepickerFormElement
   *
   * Creates an <input> element with a pikaday datepicker popup, so the
   * user can pick dates by selecting them. There is also a button with
   * a calendar icon which also triggers opening the datepicker.
   *
   * If the datepicker is in 'datetime' mode the user can also select
   * the time.
   *
   * Example usage:
   *
   * <datepicker-form-element
   *   ng-model
   *   id="parcel.deliveryDate" <!-- The id for the form element, used for e2e testing -->
   *   key="parcel.deliveryDate" <!-- The key to bind to in the model -->
   *   has-border="true" <!-- Indicates whether or not to draw a border around the field -->
   *   is-disabled="false" <!-- Expression that disables this field when it evaluates to true -->
   *   is-readonly="false" <!-- Expression that makes the field readonly when it evaluates to true -->
   *   no-backend-interaction="false" <!-- Indicates whether or not to make a call to backend when the field is changed -->
   *   has-time="false"> <!-- When true the user can also select the time -->
   * </datepicker-form-element>
   *
   * The reason the 'datepickerFormElement' is necessary, and why the
   * 'datepicker' formly types uses this Component, is because it makes
   * it easier to manipulate the ngModel of the 'datepicker' formly type.
   *
   * Formly can automatically generate an ngModel for you, which binds
   * correctly to nested or non-nested properties. By creating an extra
   * level of dept all the datepickerFormElement needs to do is require
   * 'ngModel'. DatepickerFormElement can then manipulate the ngModel
   * just as in a regular Angular Component without having to worry about
   * where it came from.
   *
   * Also, we need a component to require the 'guidanceObserversAccessor' in order to obtain the
   * validationObserver.
   *
   */
  angular.module('digitalWorkplaceApp')
    .constant('DATE_FORMAT_ERROR', 'DATE_FORMAT_ERROR')
    .constant('TIME_FORMAT_ERROR', 'TIME_FORMAT_ERROR')
    .component('datepickerFormElement', {
      templateUrl: 'es6/guidance-mode/form-elements/datepicker/datepicker-form-element.component.html',
      require: {
        ngModel: 'ngModel',
        guidanceObserversAccessor: "^guidanceObserversAccessor"
      },
      bindings: {
        id: "@",
        key: "@",
        hasBorder: "<",
        isDisabled: "<",
        isReadonly: "<",
        noBackendInteraction: "<",
        hasTime: "<"
      },
      controllerAs: 'datepickerFormElementController',
      controller: function ($scope, validationMixin, suggestionsMixin, isDisabledMixin, guidanceFormObserverFactory, modelChangedMixin,
                            datepickerFactory, $translate, $element, DATE_FORMAT_ERROR, TIME_FORMAT_ERROR, elementPosition, elementIdGenerator) {
        const datepickerFormElementController = this;

        datepickerFormElementController.userInputDate = '';
        datepickerFormElementController.userInputTime = '';
        datepickerFormElementController.language = '';

        /*
         * When a change occurs here this can have one of three origins:
         *  - The model is updated from elsewhere
         *  - The user clicks a date in the Pikaday selector
         *  - The user fills in a date manually
         *
         *  This three change origin enum options represent the source of such changes.
         */
        const ORIGIN = {
          MODEL: "MODEL",
          PIKADAY: "PIKADAY",
          USERDATE_INPUT: "USERDATE_INPUT"
        };

        datepickerFormElementController.$onInit = function () {
          const validationObserver = datepickerFormElementController.guidanceObserversAccessor.getValidationObserver();
          validationMixin.apply(datepickerFormElementController, validationObserver);

          modelChangedMixin.apply(datepickerFormElementController, 'datepickerFormElementController', $scope, false);

          const suggestionsObserver = datepickerFormElementController.guidanceObserversAccessor.getSuggestionsObserver();
          suggestionsMixin.apply(datepickerFormElementController, suggestionsObserver, true);

          isDisabledMixin.apply(datepickerFormElementController);

          const guidanceFormObserver = datepickerFormElementController.guidanceObserversAccessor.getGuidanceFormObserver();
          datepickerFormElementController.elementId = elementIdGenerator.generateId(datepickerFormElementController.id, guidanceFormObserver);

          datepickerFormElementController.language = $translate.use();
        };

        // The datepicker has a custom suggestion clicked
        datepickerFormElementController.suggestionClicked = function (suggestion) {
          const value = _.get(suggestion.model, datepickerFormElementController.key);
          datepickerFormElementController.ngModel.$setViewValue(value);
        };

        // When pikaday opens add the suggestions if we have any.
        datepickerFormElementController.pikaDayOpened = function () {
          if (datepickerFormElementController.suggestions.length > 0) {
            const pikaday = jquery('.pika-single:not(.is-hidden) > .pika-lendar');

            const pElement = jquery('<p style="text-align: center;"></p>');

            _.each(datepickerFormElementController.suggestions, (suggestion) => {
              const buttonElement = jquery(`
                <button class="button-secondary" style="margin: 0 2px;">
                  <span>${suggestion.label}</span>
                </button>
              `);

              buttonElement.on('click touchstart', () => { datepickerFormElementController.suggestionClicked(suggestion); });

              pElement.append(buttonElement);
            });

            pikaday.append(pElement);
          }
        };

        // Help pikaday to determine where on the screen to show the datepicker.
        datepickerFormElementController.pikaDayPosition = function () {
          return elementPosition.isAboveFold($element) ? 'bottom left' : 'top left';
        };

        // When the Pikaday date changes we also update the model in our required format.
        datepickerFormElementController.pikaDateChanged = function (date) {
          const dateString = datepickerFactory.convertDateToIsoDateFormat(date);
          setDate(dateString, ORIGIN.PIKADAY);
        };

        //The first time in this function the pikaday date object is still undefined. So we wait until it isn't.
        const deregisterPikadayDateWatch = $scope.$watch("datepickerFormElementController.pikadayDate", function (date) {
          if (_.isEmpty(date) === false) {
            //Set the initial model value
            setModelFromViewValue(datepickerFormElementController.ngModel.$viewValue);

            // When the model changes we also set the Pikaday date object to the changed value
            $scope.$watch('datepickerFormElementController.ngModel.$viewValue', function (newValue, oldValue) {
              // Don't do anything when the values are the same.
              if (newValue !== oldValue) {
                const hasDateFormatError = _.get(datepickerFormElementController.ngModel, `$error.${DATE_FORMAT_ERROR}`, false);
                const hasTimeFormatError = _.get(datepickerFormElementController.ngModel, `$error.${TIME_FORMAT_ERROR}`, false);

                /*
                 Only set the $viewValue when there are no format errors.
                 Otherwise the user will lose the data he inputted.
                 */
                if (hasDateFormatError === false && hasTimeFormatError === false) {
                  formValueChanged(newValue);

                  setModelFromViewValue(datepickerFormElementController.ngModel.$viewValue);
                }
              }
            });

            //We only need to get here once so we unregister the watch now.
            deregisterPikadayDateWatch();
          }
        }, true);

        /**
         * Parse a user input date and set this on the model when we
         * either have an empty string or a valid date.
         *
         * @param $event event
         */
        datepickerFormElementController.parseDate = function ($event) {
          /*
           LEFT and RIGHT should be ignored when parsing dates, otherwise
           the user cannot correct dates that are valid via the keyboard,
           because the cursor will be moved all the way to the right
           each time LEFT or RIGHT is pressed.
           */
          if ($event.keyCode === 37 || $event.keyCode === 39) {
            return false; // Return false so we can unit test this behavior.
          }

          const text = $event.target.value;

          if (datepickerFactory.isUserInputDateValid(datepickerFormElementController.userInputDate)) {
            const date = datepickerFactory.convertUserInputDateToIsoDateString(text);
            setDate(date, ORIGIN.USERDATE_INPUT);
          } else if (_.isEmpty(text)) {
            formValueChanged('');
            setDate('', ORIGIN.USERDATE_INPUT);
          } else {
            // We don't use setDate here so we don't change the value in the userInput field.
            datepickerFormElementController.ngModel.$setViewValue('');
            datepickerFormElementController.ngModel.$setValidity(DATE_FORMAT_ERROR, false);

            /*
             The date formatting issue takes precedence over all the
             other errors (as we manually set the model value to empty).

             When the format becomes correct the normal validation is performed again.
             */
            datepickerFormElementController.errorMessages = [$translate.instant(DATE_FORMAT_ERROR)];
          }
        };

        /**
         * Parse a user input time and set this on the model when we
         * either have an empty string or a valid time.
         *
         * @param $event event
         */
        datepickerFormElementController.parseTime = function ($event) {
          /*
           LEFT and RIGHT should be ignored when parsing dates, otherwise
           the user cannot correct dates that are valid via the keyboard,
           because the cursor will be moved all the way to the right
           each time LEFT or RIGHT is pressed.
           */
          if ($event.keyCode === 37 || $event.keyCode === 39) {
            return false; // Return false so we can unit test this behavior.
          }

          const text = $event.target.value;

          const { userInputTime, userInputDate } = datepickerFormElementController;

          const inputDateValid = datepickerFactory.isUserInputDateValid(userInputDate);
          const inputTimeValid = datepickerFactory.isUserInputTimeValid(userInputTime);

          if (inputDateValid && inputTimeValid) {
            const viewValue = datepickerFactory.convertUserDateAndTimeInputToIsoDateTimeString(
              datepickerFormElementController.userInputDate,
              datepickerFormElementController.userInputTime
            );

            datepickerFormElementController.ngModel.$setViewValue(viewValue);

            datepickerFormElementController.ngModel.$setValidity(DATE_FORMAT_ERROR, true);
            datepickerFormElementController.ngModel.$setValidity(TIME_FORMAT_ERROR, true);
          } else if (inputTimeValid) {
            datepickerFormElementController.ngModel.$setViewValue('');
            datepickerFormElementController.ngModel.$setValidity(TIME_FORMAT_ERROR, true);
          } else if (_.isEmpty(text)) {
            formValueChanged('');
          } else {
            datepickerFormElementController.ngModel.$setViewValue('');
            datepickerFormElementController.ngModel.$setValidity(TIME_FORMAT_ERROR, false);

            /*
             The date formatting issue takes precedence over all the
             other errors (as we manually set the model value to empty).

             When the format becomes correct the normal validation is performed again.
             */
            datepickerFormElementController.errorMessages = [$translate.instant(TIME_FORMAT_ERROR)];
          }
        };

        datepickerFormElementController.openPikaday = function () {
          /*
           Because we use ng-if to toggle between the disabled or enabled
           state, the 'input' element will be removed from the DOM.

           So that is why each time openPikaday is fired we need to
           re-find the inputElement.
           */
          const inputElement = $element.find('input');

          inputElement.click();
        };

        // The value to show when disabled or readonly
        datepickerFormElementController.userStringValue = function () {
          if (_.isEmpty(datepickerFormElementController.ngModel.$modelValue) === false) {
            const modelValue = datepickerFormElementController.ngModel.$modelValue;

            if (datepickerFormElementController.hasTime) {
              return datepickerFactory.convertIsoDateTimeToUserInputDateTime(modelValue);
            } else {
              return datepickerFactory.convertIsoDateToUserInputDate(modelValue);
            }
          }
        };

        /**
         * setDate is triggered from one of three origins: the model
         * itself, the pikaday datepicker or the user input field.
         *
         * If it is indeed a correct date, the values are then propagated
         * to the other two fields in the format they require.
         *
         * The 'origin' of the change is ignored so we don't set the values
         * needlessly.
         *
         * @param dateString Date string, always in the YYYY-MM-DD format or an empty string.
         * @param origin The origin of the change.
         */
        function setDate(dateString, origin) {
          if (origin !== ORIGIN.MODEL) {
            // Only attempt to parse if the dateString is not empty.
            if (datepickerFormElementController.hasTime && _.isEmpty(dateString) === false) {
              // Check if the time is valid
              if (datepickerFactory.isUserInputTimeValid(datepickerFormElementController.userInputTime)) {
                // If it is valid combine the user's date and time
                const viewValue = datepickerFactory.convertIsoDateAndUserTimeToIsoDateTimeString(
                  dateString,
                  datepickerFormElementController.userInputTime
                );

                // Consider the date valid now that the model will be set.
                datepickerFormElementController.ngModel.$setValidity(DATE_FORMAT_ERROR, true);

                // Consider the time valid now it has been set.
                datepickerFormElementController.ngModel.$setValidity(TIME_FORMAT_ERROR, true);

                // Set the view value to the combined date and time.
                datepickerFormElementController.ngModel.$setViewValue(viewValue);
              }
            } else {
              // Consider the date valid now that the model will be set.
              datepickerFormElementController.ngModel.$setValidity(DATE_FORMAT_ERROR, true);

              // Write either the 'YYYY-MM-DD' string or the empty string.
              datepickerFormElementController.ngModel.$setViewValue(dateString);
            }
          }

          if (origin !== ORIGIN.PIKADAY) {
            datepickerFormElementController.pikadayDate.setDate(dateString);
          }

          if (origin !== ORIGIN.USERDATE_INPUT) {
            if (_.isEmpty(dateString)) {
              datepickerFormElementController.userInputDate = '';
            } else {
              datepickerFormElementController.userInputDate = datepickerFactory.convertIsoDateToUserInputDate(dateString);
            }
          }
        }

        function setModelFromViewValue(viewValue) {
          let modelDate = viewValue;

          if (datepickerFormElementController.hasTime && _.isEmpty(viewValue) === false) {
            // Strip the hours from the 'isoDateTimeFormat'.
            modelDate = datepickerFactory.convertIsoDateTimeStringToIsoDateFormat(viewValue);

            // Set the user input time to the new model.
            datepickerFormElementController.userInputTime = datepickerFactory.convertIsoDateTimeToUserInputTime(viewValue);
          }

          setDate(modelDate, ORIGIN.MODEL);
        }

        function formValueChanged(value) {
          datepickerFormElementController.internalModelValue = value;
          datepickerFormElementController.internalModelValueChanged();
        }
      }
    });
})(window.$); //eslint-disable-line angular/window-service
