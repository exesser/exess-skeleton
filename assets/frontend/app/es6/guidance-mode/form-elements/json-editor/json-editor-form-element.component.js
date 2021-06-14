'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp:json-editor component
 * @description
 * # json-editor
 *
 * The json-editor element allows the user to type in a large textual
 * description.
 *
 * Example usage:
 *
 * <json-editor-form-element
 *   ng-model
 *   id="{{ options.id }}"  <!-- The id for the form element, used for e2e testing -->
 *   key="{{ options.key }}" <!-- The key to bind to in the model -->
 *   is-disabled="options.templateOptions.disabled" <!-- Expression that disables this field when it evaluates to true -->
 *   is-readonly="false" <!-- Expression that makes the field readonly when it evaluates to true -->
 *   no-backend-interaction="false" <!-- Indicates whether or not to make a call to backend when the field is changed -->
 *   has-border="false" <!-- Indicates whether or not to draw a border around the fields -->
 * </json-editor-form-element>
 *
 * The reason the 'json-editor' is necessary, and why the
 * 'hashtag-text' formly types uses this Component, is because we need to
 * require the 'guidanceObserversAccessor' in order to obtain the
 * validationObserver.
 */
angular.module('digitalWorkplaceApp')
  .component('jsonEditorFormElement', {
    templateUrl: 'es6/guidance-mode/form-elements/json-editor/json-editor-form-element.component.html',
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
    controllerAs: 'jsonEditorFormElementController',
    controller: function ($scope, validationMixin, modelChangedMixin, isDisabledMixin, elementIdGenerator) {
      const jsonEditorFormElementController = this;
      jsonEditorFormElementController.newInternalModelValue = {};

      jsonEditorFormElementController.$onInit = function () {
        const validationObserver = jsonEditorFormElementController.guidanceObserversAccessor.getValidationObserver();
        validationMixin.apply(jsonEditorFormElementController, validationObserver);

        modelChangedMixin.apply(jsonEditorFormElementController, 'jsonEditorFormElementController', $scope, false);

        isDisabledMixin.apply(jsonEditorFormElementController);

        const guidanceFormObserver = jsonEditorFormElementController.guidanceObserversAccessor.getGuidanceFormObserver();
        jsonEditorFormElementController.elementId = elementIdGenerator.generateId(jsonEditorFormElementController.id, guidanceFormObserver);

        $scope.$watch(`jsonEditorFormElementController.ngModel.$viewValue`, function (newValue, oldValue) {
          if (_.isUndefined(newValue) || _.isNaN(newValue) || oldValue === newValue) {
            return;
          }

          if (_.isEmpty(newValue)) {
            newValue = '[]';
          }

          jsonEditorFormElementController.newInternalModelValue = angular.fromJson(newValue);
        });

        $scope.$watch(`jsonEditorFormElementController.newInternalModelValue`, function (newValue, oldValue) {
          if (_.isUndefined(newValue) || _.isNaN(newValue) || oldValue === newValue) {
            return;
          }

          if (_.isEmpty(newValue)) {
            newValue = {};
          }

          jsonEditorFormElementController.internalModelValue = angular.toJson(newValue);
          jsonEditorFormElementController.internalModelValueChanged();
        });

        jsonEditorFormElementController.componentOptions = {
          mode: 'tree',
          search: true,
          history: false,
          expanded: true,
          value: jsonEditorFormElementController.newInternalModelValue
        };
      };

      jsonEditorFormElementController.getInternalModelValue = function () {
        if (_.isUndefined(jsonEditorFormElementController.internalModelValue)) {
          return {};
        }

        return JSON.parse(jsonEditorFormElementController.internalModelValue);
      };

      // Needed for spec.
      jsonEditorFormElementController.expandAll = function () {
        jsonEditorFormElementController.jsonEditor.expandAll();
      };

      // Needed for spec.
      jsonEditorFormElementController.editorLoaded = function (jsonEditor) {
        jsonEditorFormElementController.jsonEditor = jsonEditor;
      };
    }
  });
