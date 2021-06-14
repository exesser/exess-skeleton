"use strict";

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.component:radioFormElement
 * @description
 * # radioFormElement
 * Component of the digitalWorkplaceApp
 *
 * This component creates a radio form element.
 *
 * Example usage:
 *
 * <radio-form-element
 *   ng-model
 *   id="lead.hasElec" <!-- The id for the form element, used for e2e testing -->
 *   key="lead.hasElec" <!-- The key to bind to in the model -->
 *   label="Yes" <!-- The label to initially show in the radio option -->
 *   value="true" <!-- The value of this specific radio option -->
 *   is-disabled="false" <!-- Expression that disables this field when it evaluates to true -->
 *   is-readonly="false" <!-- Expression that makes the field readonly when it evaluates to true -->
 *   no-backend-interaction="false" <!-- Indicates whether or not to make a call to backend when the field is changed -->
 *   has-border="false"> <!-- Indicates whether or not to draw a border around the fields -->
 * </radio-form-element>
 *
 * The reason the 'radioFormElement' is necessary, and why the
 * 'radio' formly types uses this Component, is because we need to
 * require the 'guidanceObserversAccessor' in order to obtain the
 * validationObserver.
 *
 */
angular.module('digitalWorkplaceApp')
  .component('radioFormElement', {
    templateUrl: 'es6/guidance-mode/form-elements/radio/radio-form-element.component.html',
    require: {
      ngModel: 'ngModel',
      guidanceObserversAccessor: '^guidanceObserversAccessor'
    },
    bindings: {
      id: "@",
      key: "@",
      label: "@",
      value: "@",
      isDisabled: "<",
      isReadonly: "<",
      noBackendInteraction: "<",
      hasBorder: "<"
    },
    controllerAs: 'radioFormElementController',
    controller: function ($scope, validationMixin, modelChangedMixin, isDisabledMixin, elementIdGenerator) {
      const radioFormElementController = this;

      radioFormElementController.$onInit = function () {
        const validationObserver = radioFormElementController.guidanceObserversAccessor.getValidationObserver();
        validationMixin.apply(radioFormElementController, validationObserver);

        modelChangedMixin.apply(radioFormElementController, 'radioFormElementController', $scope);

        isDisabledMixin.apply(radioFormElementController);

        const guidanceFormObserver = radioFormElementController.guidanceObserversAccessor.getGuidanceFormObserver();
        radioFormElementController.elementId = elementIdGenerator.generateId(radioFormElementController.id, guidanceFormObserver);
      };
    }
  });
