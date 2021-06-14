"use strict";

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.component:tariffCalculationFormElement
 * @description
 * # tariffCalculationFormElement
 * Component of the digitalWorkplaceApp
 *
 * This component renders a tariff calculation form element.
 * It contains its own datasource to retrieve data to fill the table from.
 *
 * Example usage:
 *
 * <tariff-calculation-form-element
 *  ng-model
 *  id="tariffCalculation"
 *  key='tariffCalculation"
 *  model="model">
 * </tariff-calculation-form-element>
 *
 */
angular.module('digitalWorkplaceApp')
  .constant('DEBOUNCE_TIME_TARIFF_CALCULATION', 1700)
  .constant('PRICE_EVENT_MODEL_KEY', 'dwp|flag|priceEvent')
  .component('tariffCalculationFormElement', {
    templateUrl: 'es6/guidance-mode/form-elements/tariff-calculation/tariff-calculation-form-element.component.html',
    require: {
      ngModel: 'ngModel',
      guidanceObserversAccessor: '^guidanceObserversAccessor'
    },
    bindings: {
      id: "@",
      key: "@",
      model: "<",
      isDisabled: "<",
      hideButtonsConditions: "<"
    },
    controllerAs: 'tariffCalculationFormElementController',
    controller: function ($scope, $interpolate, promiseUtils, elementIdGenerator, DEBOUNCE_TIME_TARIFF_CALCULATION,
                          guidanceModeBackendState, modelChangedMixin, PRICE_EVENT_MODEL_KEY) {
      const tariffCalculationFormElementController = this;

      tariffCalculationFormElementController.loading = false;
      tariffCalculationFormElementController.noBackendInteraction = false;

      tariffCalculationFormElementController.$onInit = function () {
        const guidanceFormObserver = tariffCalculationFormElementController.guidanceObserversAccessor.getGuidanceFormObserver();
        tariffCalculationFormElementController.elementId = elementIdGenerator.generateId(tariffCalculationFormElementController.id, guidanceFormObserver);

        modelChangedMixin.apply(tariffCalculationFormElementController, 'tariffCalculationFormElementController', $scope, false);
      };

      /**
       * When the model value changes from the outside, we update the internal calculations array accordingly.
       */
      $scope.$watch("tariffCalculationFormElementController.ngModel.$viewValue", function (calculations) {
        tariffCalculationFormElementController.calculations = calculations;
      });

      /**
       * When the user presses the 'add year' button a request is sent to the backend with the event type 'ADD-YEAR'.
       * The result of this call will be the new model value for this field and the table will be updated accordingly.
       */
      tariffCalculationFormElementController.addEndDate = function () {
        tariffCalculationFormElementController.fetchData('ADD-YEAR');
      };

      /**
       * When the user presses the 'reset' button a request is sent to the backend with the event type 'RESET'.
       * The result of this call will be the new model value for this field and the table will be updated accordingly.
       */
      tariffCalculationFormElementController.resetEndDates = function () {
        tariffCalculationFormElementController.fetchData('RESET');
      };

      /**
       * Trigger a internalModelValueChanged - this will let the main guidance know that has to to a backend change call.
       *
       * Wrapped inside a DEBOUNCE_TIME, debounce so we limit the number of calls going to the backend.
       *
       * Add the price even on the model.
       *
       * @param event the event type
       */
      tariffCalculationFormElementController.realFetchData = _.debounce(function (event) {
        tariffCalculationFormElementController.loading = true;

        //add a random key on model to force call to backend
        tariffCalculationFormElementController.model['dwp|flag|randomValue'] = _.random(0, 9999);
        tariffCalculationFormElementController.model[PRICE_EVENT_MODEL_KEY] = event;

        tariffCalculationFormElementController.internalModelValueChanged();
      }, DEBOUNCE_TIME_TARIFF_CALCULATION);

      tariffCalculationFormElementController.fetchData = function (event) {
        guidanceModeBackendState.setBackendIsBusy(true);
        tariffCalculationFormElementController.realFetchData(event);
      };

      tariffCalculationFormElementController.isCalculating = function () {
        if (guidanceModeBackendState.getBackendIsBusy() === false) {
          tariffCalculationFormElementController.loading = false;
        }

        if (tariffCalculationFormElementController.loading === false) {
          _.unset(tariffCalculationFormElementController.model, PRICE_EVENT_MODEL_KEY);
        }

        return tariffCalculationFormElementController.loading;
      };

      /**
       * Returns a range of row indices to loop over in the template.
       * For example, if there are 3 'calculations' entries (which correspond with rows), the return value is:
       *
       * [0, 1, 2]
       *
       * If 'calculations' is not set to a correct array, the return value is simply an empty array.
       *
       * @returns {Array} range array
       */
      tariffCalculationFormElementController.getRowIndexRange = function () {
        // Assume that all rows have the same length as the first one.
        const firstElement = tariffCalculationFormElementController.calculations[0][0];
        if (_.isArray(firstElement)) {
          return _.range(firstElement.length);
        } else {
          return [];
        }
      };

      /**
       * Function to check is a tariff button (CALCULATE / ADD YEAR / RESET) should be hidden or not
       * @param {string} buttonKey CALCULATE | ADD-YEAR | RESET
       * @returns {boolean}
       */
      tariffCalculationFormElementController.hideButton = function (buttonKey) {
        const buttonCondition = tariffCalculationFormElementController.hideButtonsConditions[buttonKey];

        // if we don't have a condition for the requested buttonKey then show the button
        if (_.isUndefined(buttonCondition)) {
          return false;
        }

        return $interpolate(`{{ ${buttonCondition} }}`)({ model: tariffCalculationFormElementController.model }) === "true";
      };
    }
  });
