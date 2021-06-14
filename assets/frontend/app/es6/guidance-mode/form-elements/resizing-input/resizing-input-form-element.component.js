"use strict";

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.component:resizingInputFormElement
 * @description
 * # resizingInputFormElement
 * Component of the digitalWorkplaceApp
 *
 * This component creates a large input form element.
 *
 * Example usage:
 *
 * <resizing-input-form-element
 *   ng-model
 *   id="lead.elecUsage" <!-- The id for the form element, used for e2e testing -->
 *   key="lead.elecUsage" <!-- The key to bind to in the model -->
 *   label="Electricity usage" <!-- The label to initially show in the resizing input field -->
 *   is-disabled="false" <!-- Expression that disables this field when it evaluates to true -->
 *   is-readonly="false" <!-- Expression that makes the field readonly when it evaluates to true -->
 *   no-backend-interaction="false" <!-- Indicates whether or not to make a call to backend when the field is changed -->
 *   has-border="false" <!-- Indicates whether or not to draw a border around the fields -->
 *   model="model"> <!-- The full form model -->
 * </resizing-input-form-element>
 *
 * The reason the 'resizingInputFormElement' is necessary, and why the
 * 'resizing-input' formly types uses this Component, is because we need to
 * require the 'guidanceObserversAccessor' in order to obtain the
 * validationObserver and suggestionsObserver.
 *
 */
angular.module('digitalWorkplaceApp')
  .component('resizingInputFormElement', {
    templateUrl: 'es6/guidance-mode/form-elements/resizing-input/resizing-input-form-element.component.html',
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
    controllerAs: 'resizingInputFormElementController',
    controller: function ($scope, validationMixin, suggestionsMixin, modelChangedMixin, isDisabledMixin, elementIdGenerator) {
      const resizingInputFormElementController = this;

      resizingInputFormElementController.$onInit = function () {
        const validationObserver = resizingInputFormElementController.guidanceObserversAccessor.getValidationObserver();
        validationMixin.apply(resizingInputFormElementController, validationObserver);

        const suggestionsObserver = resizingInputFormElementController.guidanceObserversAccessor.getSuggestionsObserver();
        suggestionsMixin.apply(resizingInputFormElementController, suggestionsObserver);

        const guidanceFormObserver = resizingInputFormElementController.guidanceObserversAccessor.getGuidanceFormObserver();
        resizingInputFormElementController.autoCompleteElementIds = [elementIdGenerator.generateId(resizingInputFormElementController.id, guidanceFormObserver)];

        modelChangedMixin.apply(resizingInputFormElementController, 'resizingInputFormElementController', $scope);

        isDisabledMixin.apply(resizingInputFormElementController);
      };
    }
  });
