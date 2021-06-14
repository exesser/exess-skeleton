"use strict";

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.component:resizingInputComponent
 * @description
 * # resizingInputComponent
 *
 * Component which renders an input field that automatically grows and shrinks with its input.
 * Initially, the placeholder content is displayed. When the user starts typing the resizing begins.
 *
 * Example usage:
 *
 * <resizing-input
 *   ng-model="firstName"
 *   field-id="firstName-field"
 *   is-disabled="false"
 *   is-readonly="false"
 *   placeholder="First name">
 * </resizing-input>
 *
 * Component of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .component('resizingInput', {
    templateUrl: 'es6/guidance-mode/resizing-input/resizing-input.component.html',
    require: {
      ngModel: 'ngModel',
      guidanceObserversAccessor: '^guidanceObserversAccessor'
    },
    bindings: {
      fieldId: "@",
      isDisabled: "<",
      isReadonly: "<",
      placeholder: "@"
    },
    controllerAs: 'resizingInputController',
    controller: function ($scope, $element, $timeout, elementIdGenerator) {
      const resizingInputController = this;

      resizingInputController.$onInit = function () {
        $scope.$watch("resizingInputController.ngModel.$viewValue", function (value) {
          resizingInputController.inputValue = value;

          /*
           After a digest loop has finished the width of the span is measured
           and set to the spanWidth property. This is then used to make the
           input field larger or smaller.
           */
          $timeout(function () {
            resizingInputController.spanWidth = _.max([$element.find('span').width() + 20, 50]);
          }, 0);
        });

        const guidanceFormObserver = resizingInputController.guidanceObserversAccessor.getGuidanceFormObserver();
        resizingInputController.elementId = elementIdGenerator.generateId(resizingInputController.fieldId, guidanceFormObserver);

      };

      resizingInputController.inputValueChanged = function () {
        resizingInputController.ngModel.$setViewValue(resizingInputController.inputValue);
      };
    }
  });
