"use strict";

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.component:largeInputFormElement
 * @description
 * # largeInputFormElement
 * Component of the digitalWorkplaceApp
 *
 * This component creates a large input form element.
 *
 * Example usage:
 *
 * <large-input-form-element
 *   ng-model
 *   id="lead.elecUsage" <!-- The id for the form element, used for e2e testing -->
 *   key="lead.elecUsage" <!-- The key to bind to in the model -->
 *   label="Electricity usage" <!-- The label to initially show in the large input field -->
 *   is-disabled="false" <!-- Expression that disables this field when it evaluates to true -->
 *   is-readonly="false" <!-- Expression that makes the field readonly when it evaluates to true -->
 *   no-backend-interaction="false" <!-- Indicates whether or not to make a call to backend when the field is changed -->
 *   has-border="false" <!-- Indicates whether or not to draw a border around the fields -->
 *   model="model"> <!-- The full form model -->
 * </large-input-form-element>
 *
 * The reason the 'largeInputFormElement' is necessary, and why the
 * 'large-input' formly types uses this Component, is because we need to
 * require the 'guidanceObserversAccessor' in order to obtain the
 * validationObserver and suggestionsObserver.
 *
 */
angular.module('digitalWorkplaceApp')
  .component('largeInputFormElement', {
    templateUrl: 'es6/guidance-mode/form-elements/large-input/large-input-form-element.component.html',
    require: {
      ngModel: 'ngModel',
      guidanceObserversAccessor: '^guidanceObserversAccessor'
    },
    bindings: {
      id: "@",
      key: "@",
      label: "@",
      isDisabled: "<",
      isReadonly: "<",
      noBackendInteraction: "<",
      hasBorder: "<",
      model: "<"
    },
    controllerAs: 'largeInputFormElementController',
    controller: function ($scope, validationMixin, suggestionsMixin, modelChangedMixin, isDisabledMixin, elementIdGenerator) {
      const largeInputFormElementController = this;

      // the parent model is used by suggestions
      largeInputFormElementController.parentModel = {};

      largeInputFormElementController.$onInit = function () {
        const validationObserver = largeInputFormElementController.guidanceObserversAccessor.getValidationObserver();
        validationMixin.apply(largeInputFormElementController, validationObserver);

        const suggestionsObserver = largeInputFormElementController.guidanceObserversAccessor.getSuggestionsObserver();
        suggestionsMixin.apply(largeInputFormElementController, suggestionsObserver);

        const guidanceFormObserver = largeInputFormElementController.guidanceObserversAccessor.getGuidanceFormObserver();
        largeInputFormElementController.elementId = elementIdGenerator.generateId(largeInputFormElementController.id, guidanceFormObserver);
        largeInputFormElementController.autoCompleteElementIds = [largeInputFormElementController.elementId];
        largeInputFormElementController.parentModel = guidanceFormObserver.getParentModel();

        modelChangedMixin.apply(largeInputFormElementController, 'largeInputFormElementController', $scope);

        isDisabledMixin.apply(largeInputFormElementController);
      };
    }
  });
