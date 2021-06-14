"use strict";

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.component:inputFormElement
 * @description
 * # inputFormElement
 * Component of the digitalWorkplaceApp
 *
 * This component creates a input form element.
 *
 * Example usage:
 *
 * <input-form-element
 *   ng-model
 *   id="lead.elecUsage" <!-- The id for the form element, used for e2e testing -->
 *   key="lead.elecUsage" <!-- The key to bind to in the model -->
 *   is-disabled="false" <!-- Expression that disables this field when it evaluates to true -->
 *   is-readonly="false" <!-- Expression that makes the field readonly when it evaluates to true -->
 *   no-backend-interaction="false" <!-- Indicates whether or not to make a call to backend when the field is changed -->
 *   has-border="false" <!-- Indicates whether or not to draw a border around the fields -->
 *   unit-of-measure="kWh" <!-- An optional unit of measure to display next to the input -->
 *   model="model"> <!-- The full form model -->
 * </input-form-element>
 *
 * The reason the 'inputFormElement' is necessary, and why the
 * 'input' formly types uses this Component, is because we need to
 * require the 'guidanceObserversAccessor' in order to obtain the
 * validationObserver and suggestionsObserver.
 *
 */
angular.module('digitalWorkplaceApp')
  .component('inputFormElement', {
    templateUrl: 'es6/guidance-mode/form-elements/input/input-form-element.component.html',
    require: {
      ngModel: 'ngModel',
      guidanceObserversAccessor: '^guidanceObserversAccessor'
    },
    bindings: {
      id: "@",
      key: "@",
      isDisabled: "<",
      isReadonly: "<",
      noBackendInteraction: "<",
      hasBorder: "<",
      unitOfMeasure: "@",
      model: "<"
    },
    controllerAs: 'inputFormElementController',
    controller: function ($scope, validationMixin, suggestionsMixin, modelChangedMixin, isDisabledMixin, elementIdGenerator) {
      const inputFormElementController = this;
      // the parent model is used by suggestions
      inputFormElementController.parentModel = {};
      inputFormElementController.newInternalModelValue = '';

      inputFormElementController.$onInit = function () {
        const validationObserver = inputFormElementController.guidanceObserversAccessor.getValidationObserver();
        validationMixin.apply(inputFormElementController, validationObserver);

        const suggestionsObserver = inputFormElementController.guidanceObserversAccessor.getSuggestionsObserver();
        suggestionsMixin.apply(inputFormElementController, suggestionsObserver);

        const guidanceFormObserver = inputFormElementController.guidanceObserversAccessor.getGuidanceFormObserver();
        inputFormElementController.elementId = elementIdGenerator.generateId(inputFormElementController.id, guidanceFormObserver);
        inputFormElementController.autoCompleteElementIds = [inputFormElementController.elementId];
        inputFormElementController.parentModel = guidanceFormObserver.getParentModel();

        modelChangedMixin.apply(inputFormElementController, 'inputFormElementController', $scope);

        isDisabledMixin.apply(inputFormElementController);
      };

      $scope.$watch(`inputFormElementController.ngModel.$viewValue`, function (newValue, oldValue) {
        if (_.isUndefined(newValue) || _.isNaN(newValue) || oldValue === newValue) {
          return;
        }

        if (!_.isEmpty(_.toString(newValue).match(/^[\d]+[.]{1}[\d]+$/))) {
          newValue = _.replace(newValue, '.', ',');
        }

        inputFormElementController.newInternalModelValue = newValue;
      });

      inputFormElementController.newInternalModelValueChanged = function () {
        let newValue = inputFormElementController.newInternalModelValue;

        if (!_.isEmpty(_.toString(newValue).match(/^[\d]+[,]{1}[\d]+$/))) {
          newValue = _.replace(newValue, ',', '.');
        }

        inputFormElementController.internalModelValue = newValue;
        inputFormElementController.internalModelValueChanged();
      };
    }
  });
