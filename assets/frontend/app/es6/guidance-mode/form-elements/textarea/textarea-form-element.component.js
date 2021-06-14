'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp:textarea component
 * @description
 * # textarea
 *
 * The textarea element allows the user to type in a large textual
 * description.
 *
 * Example usage:
 *
 * <textarea-form-element
 *   ng-model
 *   id="{{ options.id }}"  <!-- The id for the form element, used for e2e testing -->
 *   key="{{ options.key }}" <!-- The key to bind to in the model -->
 *   is-disabled="options.templateOptions.disabled" <!-- Expression that disables this field when it evaluates to true -->
 *   is-readonly="false" <!-- Expression that makes the field readonly when it evaluates to true -->
 *   no-backend-interaction="false" <!-- Indicates whether or not to make a call to backend when the field is changed -->
 *   has-border="false" <!-- Indicates whether or not to draw a border around the fields -->
 * </textarea-form-element>
 *
 * The reason the 'textarea' is necessary, and why the
 * 'hashtag-text' formly types uses this Component, is because we need to
 * require the 'guidanceObserversAccessor' in order to obtain the
 * validationObserver.
 */
angular.module('digitalWorkplaceApp')
  .component('textareaFormElement', {
    templateUrl: 'es6/guidance-mode/form-elements/textarea/textarea-form-element.component.html',
    require: {
      ngModel: "ngModel",
      guidanceObserversAccessor: '^guidanceObserversAccessor'
    },
    bindings: {
      id: "@",
      key: "@",
      isDisabled: "<",
      isReadonly: "<",
      noBackendInteraction: "<",
      hasBorder: "<"
    },
    controllerAs: 'textareaFormElementController',
    controller: function ($scope, validationMixin, modelChangedMixin, isDisabledMixin, elementIdGenerator) {
      const textareaFormElementController = this;

      textareaFormElementController.$onInit = function () {
        const validationObserver = textareaFormElementController.guidanceObserversAccessor.getValidationObserver();
        validationMixin.apply(textareaFormElementController, validationObserver);

        modelChangedMixin.apply(textareaFormElementController, 'textareaFormElementController', $scope);

        isDisabledMixin.apply(textareaFormElementController);

        const guidanceFormObserver = textareaFormElementController.guidanceObserversAccessor.getGuidanceFormObserver();
        textareaFormElementController.elementId = elementIdGenerator.generateId(textareaFormElementController.id, guidanceFormObserver);
      };
    }
  });
