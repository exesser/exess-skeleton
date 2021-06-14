'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp:selectFormElement component
 * @description
 * # selectFormElement
 *
 * SelectFormElement creates a form element that, depending on the configuration,
 * consists of a single-select dropdown or a multi-select box.
 *
 * Example usage:
 *
 * <select-form-element
 *   ng-model
 *   id="lead.homeCountry" <!-- The id for the form element, used for e2e testing -->
 *   key="lead.homeCountry" <!-- The key to bind to in the model -->
 *   multiple-select="false" <!-- Indicates whether or not multiple values may be selected -->
 *   checkboxes="false" <!-- Indicates whether or not to display a list of checkboxes instead select -->
 *   hasBorder="true" <!-- Indicates whether or not to draw a border around the fields -->
 *   is-disabled="false" <!-- Expression that disables this field when it evaluates to true -->
 *   is-readonly="false" <!-- Expression that makes the field readonly when it evaluates to true -->
 *   sort-enums="true" <!-- Indicates whether or not to sort the dropdown options -->
 *   no-backend-interaction="false" <!-- Indicates whether or not to make a call to backend when the field is changed -->
 *   dropdownOptions="countries"> <!-- The options the user can choose from initially, can be replaced using suggestions -->
 * </select-form-element>
 *
 * The reason the 'selectFormElement' is necessary, and why the
 * 'select' formly types uses this Component, is because it makes
 * it easier to manipulate the ngModel of the 'select' formly type.
 *
 * Formly can automatically generate an ngModel for you, which binds
 * correctly to nested or non-nested properties. By creating an extra
 * level of dept all the selectFormElement needs to do is require
 * 'ngModel'. SelectFormElement can then manipulate the ngModel
 * just as in a regular Angular Component without having to worry about
 * where it came from.
 *
 * Also, we need a component to require the 'guidanceObserversAccessor' in order to obtain the
 * validationObserver and suggestionsObserver.
 *
 */
angular.module('digitalWorkplaceApp')
  .component('selectFormElement', {
    templateUrl: 'es6/guidance-mode/form-elements/select/select-form-element.component.html',
    require: {
      ngModel: "ngModel",
      guidanceObserversAccessor: '^guidanceObserversAccessor'
    },
    bindings: {
      id: "@",
      key: "@",
      model: "<",
      multipleSelect: "<",
      checkboxes: "<",
      hasBorder: "<",
      dropdownOptions: "<",
      isDisabled: "<",
      isReadonly: "<",
      sortEnums: "<",
      noBackendInteraction: "<"
    },
    controllerAs: 'selectFormElementController',
    controller: function ($scope, validationMixin, isDisabledMixin, modelChangedMixin, elementIdGenerator) {
      const selectFormElementController = this;

      // The 'select' type handles the suggestions himself.
      selectFormElementController.suggestions = [];

      selectFormElementController.$onInit = function () {
        const validationObserver = selectFormElementController.guidanceObserversAccessor.getValidationObserver();
        validationMixin.apply(selectFormElementController, validationObserver);

        const guidanceFormObserver = selectFormElementController.guidanceObserversAccessor.getGuidanceFormObserver();
        selectFormElementController.elementId = elementIdGenerator.generateId(selectFormElementController.id, guidanceFormObserver);

        isDisabledMixin.apply(selectFormElementController);

        modelChangedMixin.apply(selectFormElementController, 'selectFormElementController', $scope, false);

        const suggestionsObserver = selectFormElementController.guidanceObserversAccessor.getSuggestionsObserver();
        suggestionsObserver.registerSuggestionsChangedCallback(function () {
          const suggestions = suggestionsObserver.getSuggestionsForKey(selectFormElementController.key);

          if (_.isUndefined(suggestions) === false && _.isEqual(selectFormElementController.suggestions, suggestions) === false) {
            selectFormElementController.suggestions = suggestions;
            selectFormElementController.dropdownOptions = getParsedSuggestions();
          }
        });

        /*
         * For the multiple select the default ngModel.$isEmpty check does not suffice since it doesn't consider an empty array as empty.
         * Therefore we override this method when selectFormElementController.multipleSelect is true.
         */
        if (selectFormElementController.multipleSelect) {
          /**
           * This is called when we need to determine if the value of an input is empty.
           *
           * For instance, the required directive does this to work out if the input has data or not.
           *
           * The default `$isEmpty` function checks whether the value is `undefined`, `''`, `null` or `NaN`.
           *
           * In the case of a multiple select, the default $isEmpty function would consider an empty array non-empty,
           * but in the context of a multiple select an empty array would mean that no options have been chosen.
           * We consider a multiple select empty if the value is `undefined`, `''`, `null`, `NaN` or '[]'.
           *
           * See Angular's $isEmpty function for more information.
           *
           * @param {*} value The value of the input to check for emptiness.
           * @returns {boolean} True if `value` is "empty".
           */
          selectFormElementController.ngModel.$isEmpty = (value) => _.isEmpty(value);
        }
      };

      function getParsedSuggestions() {

        if (!suggestionsContainsModel()) {
          return selectFormElementController.suggestions;
        }

        return _.map(selectFormElementController.suggestions, (suggestion) => {
          return {
            name: suggestion.label,
            value: _.get(suggestion.model, selectFormElementController.key, suggestion.label),
            model: suggestion.model
          };
        });

      }

      function suggestionsContainsModel() {
        if (_.isEmpty(selectFormElementController.suggestions)) {
          return false;
        }

        return _.has(_.last(selectFormElementController.suggestions), 'model');
      }

      function isCorrectValue(value) {
        return _.isUndefined(value) === false && _.isNaN(value) === false;
      }

      $scope.$watch("selectFormElementController.ngModel.$viewValue", function (newValue, oldValue) {
        if (isCorrectValue(newValue)) {
          selectFormElementController.selectValue = newValue;

          // We're checking the value here and sending out change events rather than doing it in the selectFormElementController.valueSelected function
          // because the change can also be triggered from elsewhere and we still want to send out change events.
          if (isCorrectValue(oldValue) && _.isEqual(newValue, oldValue) === false) {
            selectFormElementController.internalModelValue = newValue;
            selectFormElementController.internalModelValueChanged();
            if (suggestionsContainsModel()) {
              const selectedSuggestion = _.find(getParsedSuggestions(), { 'value': newValue });
              selectFormElementController.model = _.merge(selectFormElementController.model, selectedSuggestion.model);
            }
          }
        }
      });

      selectFormElementController.selectValueChanged = function (value) {
        selectFormElementController.ngModel.$setViewValue(value);
      };

      selectFormElementController.getDropdownOptions = function () {
        if (selectFormElementController.sortEnums) {
          return _.sortBy(selectFormElementController.dropdownOptions, [(option) => _.get(option, 'name', '').toLowerCase()]);
        }

        return selectFormElementController.dropdownOptions;
      };

      selectFormElementController.readonlyValue = function () {
        if (selectFormElementController.multipleSelect) {
          return _(selectFormElementController.dropdownOptions)
            .filter((option) => _.includes(selectFormElementController.selectValue, option.value))
            .map('name')
            .join(', ');
        } else {
          const selected = selectFormElementController.selectValue;
          const option = _.find(selectFormElementController.dropdownOptions, (option) => option.value === selected);

          return _.get(option, 'name', '');
        }
      };

      selectFormElementController.optionIsSelected = function (optionValue) {
        if (selectFormElementController.multipleSelect) {
          return _.includes(selectFormElementController.selectValue, optionValue);
        }

        return selectFormElementController.selectValue === optionValue;
      };

      selectFormElementController.selectOption = function (optionValue) {
        const option = _.find(selectFormElementController.dropdownOptions, (op) => {
          return op.value === optionValue;
        });

        if (_.get(option, 'disabled', false) === true || selectFormElementController.fieldIsDisabled()) {
          return;
        }

        selectFormElementController.ngModel.$setViewValue('');

        if (selectFormElementController.multipleSelect === false) {
          if (selectFormElementController.selectValue === optionValue) {
            selectFormElementController.selectValue = '';
          } else {
            selectFormElementController.selectValue = optionValue;
          }

          selectFormElementController.internalModelValue = selectFormElementController.selectValue;
          selectFormElementController.ngModel.$setViewValue(selectFormElementController.selectValue);
        }

        if (selectFormElementController.multipleSelect) {
          if (_.isArray(selectFormElementController.selectValue) === false) {
            selectFormElementController.selectValue = [];
          }

          if (_.includes(selectFormElementController.selectValue, optionValue)) {
            _.remove(selectFormElementController.selectValue, (value) => {
              return value === optionValue;
            });
          } else {
            selectFormElementController.selectValue.push(optionValue);
          }

          selectFormElementController.internalModelValue =
            _(selectFormElementController.dropdownOptions)
              .filter((option) => _.includes(selectFormElementController.selectValue, option.value))
              .map('value')
              .value();
          selectFormElementController.internalModelValueChanged();
        }

      };
    }
  });
