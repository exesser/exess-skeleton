'use strict';
/**
 * @ngdoc function
 * @name digitalWorkplaceApp.drawPadFormElement component
 * @description
 * # drawPadFormElement
 *
 * The drawPadFormElement component can be used to create base64 signature.
 *
 * Example usage:
 *
 * <draw-pad-form-element
 *   ng-model
 *   id="signature"
 *   key="signature"
 *   width="400"
 *   height="280"
 *   is-readonly="false"
 *   no-backend-interaction="false"
 *   is-disabled="false"
 *   is-required="false">
 * </draw-pad-form-element>
 *
 * Component of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .component('drawPadFormElement', {
    templateUrl: 'es6/guidance-mode/form-elements/draw-pad/draw-pad-form-element.component.html',
    require: {
      ngModel: 'ngModel',
      guidanceObserversAccessor: '^guidanceObserversAccessor'
    },
    controllerAs: 'drawPadFormElementController',
    bindings: {
      id: "@",
      key: "@",
      width: "@",
      height: "@",
      isDisabled: "<",
      isReadonly: "<",
      noBackendInteraction: "<",
      isRequired: "<"
    },
    controller: function ($scope, $compile, $document, modelChangedMixin, isDisabledMixin, elementIdGenerator, $timeout) {
      const drawPadFormElementController = this;

      drawPadFormElementController.draw = {};
      drawPadFormElementController.internalModelValue = '';
      drawPadFormElementController.originalData = '';

      drawPadFormElementController.$onInit = function () {
        const guidanceFormObserver = drawPadFormElementController.guidanceObserversAccessor.getGuidanceFormObserver();
        drawPadFormElementController.elementId = elementIdGenerator.generateId(drawPadFormElementController.id, guidanceFormObserver);

        modelChangedMixin.apply(drawPadFormElementController, 'drawPadFormElementController', $scope);

        isDisabledMixin.apply(drawPadFormElementController);

        $timeout(function () {
          if (!_.isEmpty(drawPadFormElementController.internalModelValue)) {
            drawPadFormElementController.originalData = angular.copy(drawPadFormElementController.internalModelValue);
          }
        }, 100);

        setRequired();
      };

      drawPadFormElementController.onDrawing = function () {
        $timeout(function () {
          drawPadFormElementController.internalModelValueChanged();
        }, 10);
      };

      drawPadFormElementController.clearDrawing = function () {
        drawPadFormElementController.clear();
        drawPadFormElementController.internalModelValue = '';
        drawPadFormElementController.internalModelValueChanged();
      };

      drawPadFormElementController.resetDrawing = function () {
        drawPadFormElementController.internalModelValue = angular.copy(drawPadFormElementController.originalData);
        drawPadFormElementController.internalModelValueChanged();
      };

      drawPadFormElementController.fieldIsDisabledOrReadOnly = function () {
        return drawPadFormElementController.fieldIsDisabled() || drawPadFormElementController.isReadonly;
      };

      function setRequired() {
        if (drawPadFormElementController.isRequired === true) {
          const length = _.result(drawPadFormElementController.internalModelValue, 'length', 0);
          const isValid = length > 0;

          drawPadFormElementController.ngModel.$setValidity('required', isValid);
        }
      }
    }
  });
