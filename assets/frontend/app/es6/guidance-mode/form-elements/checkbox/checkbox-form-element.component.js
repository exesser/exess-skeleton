"use strict";

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.component:checkboxFormElement
 * @description
 * # checkboxFormElement
 * Component of the digitalWorkplaceApp
 *
 * This component creates a checkbox form element.
 *
 * Example usage:
 *
 * <checkbox-form-element
 *   ng-model
 *   id="lead.hasGas" <!-- The id for the form element, used for e2e testing -->
 *   key="lead.hasGas" <!-- The key to bind to in the model -->
 *   label="Gas" <!-- The label to display next to the checkbox -->
 *   is-readonly="false" <!-- Expression that makes the field readonly when it evaluates to true -->
 *   no-backend-interaction="false" <!-- Indicates whether or not to make a call to backend when the field is changed -->
 *   is-disabled="false"> <!-- Expression that disables this field when it evaluates to true -->
 * </checkbox-form-element>
 *
 * The reason the 'checkboxFormElement' is necessary, and why the
 * 'checkbox' formly types uses this Component, is because we need to
 * require the 'guidanceObserversAccessor' in order to obtain the
 * validationObserver.
 *
 */
angular.module('digitalWorkplaceApp')
  .component('checkboxFormElement', {
    templateUrl: 'es6/guidance-mode/form-elements/checkbox/checkbox-form-element.component.html',
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
      noBackendInteraction: "<"
    },
    controllerAs: 'checkboxFormElementController',
    controller: function ($scope, validationMixin, modelChangedMixin, isDisabledMixin, elementIdGenerator) {
      const checkboxFormElementController = this;

      checkboxFormElementController.$onInit = function () {
        const validationObserver = checkboxFormElementController.guidanceObserversAccessor.getValidationObserver();
        validationMixin.apply(checkboxFormElementController, validationObserver);

        modelChangedMixin.apply(checkboxFormElementController, 'checkboxFormElementController', $scope);

        isDisabledMixin.apply(checkboxFormElementController);

        const guidanceFormObserver = checkboxFormElementController.guidanceObserversAccessor.getGuidanceFormObserver();
        checkboxFormElementController.elementId = elementIdGenerator.generateId(checkboxFormElementController.id, guidanceFormObserver);
      };
    }
  });
