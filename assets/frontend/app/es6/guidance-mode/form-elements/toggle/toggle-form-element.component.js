"use strict";

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.component:toggleFormElement
 * @description
 * # toggleFormElement
 * Component of the digitalWorkplaceApp
 *
 * This component creates a toggle form element.
 *
 * Example usage:
 *
 * <toggle-form-element
 *   ng-model
 *   id="lead.hasGas" <!-- The id for the form element, used for e2e testing -->
 *   key="lead.hasGas" <!-- The key to bind to in the model -->
 *   label="Yes" <!-- The label to initially show in the radio option -->
 *   is-readonly="false" <!-- Expression that makes the field readonly when it evaluates to true -->
 *   no-backend-interaction="false" <!-- Indicates whether or not to make a call to backend when the field is changed -->
 *   is-disabled="false"> <!-- Expression that disables this field when it evaluates to true -->
 * </toggle-form-element>
 *
 * The reason the 'toggleFormElement' is necessary, and why the
 * 'toggle' formly types uses this Component, is because we need to
 * require the 'guidanceObserversAccessor' in order to obtain the
 * validationObserver.
 *
 */
angular.module('digitalWorkplaceApp')
  .component('toggleFormElement', {
    templateUrl: 'es6/guidance-mode/form-elements/toggle/toggle-form-element.component.html',
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
    controllerAs: 'toggleFormElementController',
    controller: function ($scope, validationMixin, modelChangedMixin, isDisabledMixin, elementIdGenerator) {
      const toggleFormElementController = this;

      toggleFormElementController.$onInit = function () {
        const validationObserver = toggleFormElementController.guidanceObserversAccessor.getValidationObserver();
        validationMixin.apply(toggleFormElementController, validationObserver);

        modelChangedMixin.apply(toggleFormElementController, 'toggleFormElementController', $scope);

        isDisabledMixin.apply(toggleFormElementController);

        const guidanceFormObserver = toggleFormElementController.guidanceObserversAccessor.getGuidanceFormObserver();
        toggleFormElementController.elementId = elementIdGenerator.generateId(toggleFormElementController.id, guidanceFormObserver);
      };
    }
  });

