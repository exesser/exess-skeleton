"use strict";

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.component:checkableIconFormElement
 * @description
 * # checkableIconFormElement
 * Component of the digitalWorkplaceApp
 *
 * This component creates a checkable icon form element.
 *
 * Example usage:
 *
 * <checkable-icon-form-element
 *   ng-model
 *   id="lead.hasGas" <!-- The id for the form element, used for e2e testing -->
 *   key="lead.hasGas" <!-- The key to bind to in the model -->
 *   is-disabled="false"> <!-- Expression that disables this field when it evaluates to true -->
 *   is-readonly="false" <!-- Expression that makes the field readonly when it evaluates to true -->
 *   no-backend-interaction="false" <!-- Indicates whether or not to make a call to backend when the field is changed -->
 *   icon-class="gas"> <!-- Determines the icon used for the checkable icon -->
 * </checkable-icon-form-element>
 *
 * The reason the 'checkableIconFormElement' is necessary, and why the
 * 'checkbox' formly types uses this Component, is because we need to
 * require the 'guidanceObserversAccessor' in order to obtain the
 * validationObserver.
 */
angular.module('digitalWorkplaceApp')
  .component('checkableIconFormElement', {
    templateUrl: 'es6/guidance-mode/form-elements/checkable-icon/checkable-icon-form-element.component.html',
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
      iconClass: "@"
    },
    controllerAs: 'checkableIconFormElementController',
    controller: function ($scope, modelChangedMixin, isDisabledMixin, elementIdGenerator) {
      const checkableIconFormElementController = this;

      checkableIconFormElementController.$onInit = function () {
        modelChangedMixin.apply(checkableIconFormElementController, 'checkableIconFormElementController', $scope);

        const guidanceFormObserver = checkableIconFormElementController.guidanceObserversAccessor.getGuidanceFormObserver();
        checkableIconFormElementController.elementId = elementIdGenerator.generateId(checkableIconFormElementController.id, guidanceFormObserver);

        isDisabledMixin.apply(checkableIconFormElementController);
      };
    }
  });

