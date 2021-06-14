"use strict";

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.component:rangeInputFormElement
 * @description
 * # rangeInputFormElement
 * Component of the digitalWorkplaceApp
 *
 * This component creates a range input form element.
 *
 * Example usage:
 *
 * <range-input-form-element
 *   ng-model
 *   id="lead.elecUsage" <!-- The id for the form element, used for e2e testing -->
 *   key="lead.elecUsage" <!-- The key to bind to in the model -->
 *   min="0" <!-- The minimal value for the range slider -->
 *   max="100" <!-- The maximal value for the range slider -->
 *   step-by="1" <!-- The increase of the value for each 'tick' -->
 *   is-readonly="false" <!-- Expression that makes the field readonly when it evaluates to true -->
 *   no-backend-interaction="false" <!-- Indicates whether or not to make a call to backend when the field is changed -->
 *   is-disabled="false"> <!-- Expression that disables this field when it evaluates to true -->
 * </range-input-form-element>
 *
 * The reason the 'rangeInputFormElement' is necessary, and why the
 * 'range-input' formly types uses this Component, is because we need to
 * require the 'guidanceObserversAccessor' in order to obtain the
 * validationObserver.
 *
 */
angular.module('digitalWorkplaceApp')
  .component('rangeInputFormElement', {
    templateUrl: 'es6/guidance-mode/form-elements/range-input/range-input-form-element.component.html',
    require: {
      ngModel: 'ngModel',
      guidanceObserversAccessor: '^guidanceObserversAccessor'
    },
    bindings: {
      id: "@",
      key: "@",
      min: "@",
      max: "@",
      stepBy: "@",
      isDisabled: "<",
      isReadonly: "<",
      noBackendInteraction: "<"
    },
    controllerAs: 'rangeInputFormElementController',
    controller: function ($scope, $element, validationMixin, modelChangedMixin, isDisabledMixin, elementIdGenerator) {
      const rangeInputFormElementController = this;

      rangeInputFormElementController.$onInit = function () {
        rangeInputFormElementController.hideTooltip();

        const validationObserver = rangeInputFormElementController.guidanceObserversAccessor.getValidationObserver();
        validationMixin.apply(rangeInputFormElementController, validationObserver);

        modelChangedMixin.apply(rangeInputFormElementController, 'rangeInputFormElementController', $scope);

        isDisabledMixin.apply(rangeInputFormElementController);

        $scope.$watch("rangeInputFormElementController.ngModel.$viewValue", function () {
          /*
           Calculation was taken directly from styleguide. I think the
           number 96 has to do with some padding the range has.
           */
          const backgroundElement = $element.find('.range__background')[1];
          if (backgroundElement) {
            backgroundElement.style.width = (rangeInputFormElementController.internalModelValue / 100 * 96) + '%';
          }
        });

        const guidanceFormObserver = rangeInputFormElementController.guidanceObserversAccessor.getGuidanceFormObserver();
        rangeInputFormElementController.elementId = elementIdGenerator.generateId(rangeInputFormElementController.id, guidanceFormObserver);
      };

      rangeInputFormElementController.showTooltip = function () {
        const tooltipUpElement = $element.find('.tooltip-up')[0];
        tooltipUpElement.style.opacity = 1;
        tooltipUpElement.style['z-index'] = 1;
      };

      rangeInputFormElementController.hideTooltip = function () {
        const tooltipUpElement = $element.find('.tooltip-up')[0];
        if (tooltipUpElement) {
          tooltipUpElement.style.opacity = 0;
          tooltipUpElement.style['z-index'] = 0;
        }
      };
    }
  });
