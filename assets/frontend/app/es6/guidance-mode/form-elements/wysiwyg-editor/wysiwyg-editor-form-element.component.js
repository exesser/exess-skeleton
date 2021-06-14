'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.component:editorFormElement
 * @description
 * # wysiwygEditorFormElement
 * Component of the digitalWorkplaceApp
 *
 * This component creates an editor form element which represents
 * the textAngular WYSIWYG text editor.
 *
 * Example usage:
 *
 * <wysiwyg-editor-form-element
 *   ng-model
 *   id="message" <!-- The id for the form element, used for e2e testing -->
 *   key="message" <!-- The key to bind to in the model -->
 *   is-disabled="false" <!-- Expression that disables this field when it evaluates to true -->
 *   is-readonly="false" <!-- Expression that makes the field readonly when it evaluates to true -->
 *   no-backend-interaction="false" <!-- Indicates whether or not to make a call to backend when the field is changed -->
 * </wysiwyg-editor-form-element>
 */
angular.module('digitalWorkplaceApp')
  .config(function ($provide) {
    $provide.decorator('taOptions', function ($delegate, wysiwygEditorToolbarFactory) {
      wysiwygEditorToolbarFactory.setToolbar($delegate);
      return $delegate;
    });
  })
  .component('wysiwygEditorFormElement', {
    templateUrl: 'es6/guidance-mode/form-elements/wysiwyg-editor/wysiwyg-editor-form-element.component.html',
    require: {
      ngModel: 'ngModel',
      guidanceObserversAccessor: '^guidanceObserversAccessor'
    },
    bindings: {
      id: "@",
      key: "@",
      isDisabled: "<",
      isReadonly: "<",
      noBackendInteraction: "<"
    },
    controllerAs: 'wysiwygEditorFormElementController',
    controller: function ($scope, validationMixin, elementIdGenerator, modelChangedMixin, isDisabledMixin) {
      const wysiwygEditorFormElementController = this;
      wysiwygEditorFormElementController.previousValue = null;

      wysiwygEditorFormElementController.$onInit = function () {
        const validationObserver = wysiwygEditorFormElementController.guidanceObserversAccessor.getValidationObserver();
        validationMixin.apply(wysiwygEditorFormElementController, validationObserver);

        const guidanceFormObserver = wysiwygEditorFormElementController.guidanceObserversAccessor.getGuidanceFormObserver();
        wysiwygEditorFormElementController.elementId = elementIdGenerator.generateId(wysiwygEditorFormElementController.id, guidanceFormObserver);

        modelChangedMixin.apply(wysiwygEditorFormElementController, 'wysiwygEditorFormElementController', $scope);

        isDisabledMixin.apply(wysiwygEditorFormElementController);
      };

      wysiwygEditorFormElementController.internalModelValueChangedLocal = function () {
        if (
          _.includes(wysiwygEditorFormElementController.internalModelValue, 'selectionBoundary')
          || _.isEqual(wysiwygEditorFormElementController.internalModelValue, wysiwygEditorFormElementController.previousValue)
        ) {
          return;
        }

        wysiwygEditorFormElementController.previousValue = wysiwygEditorFormElementController.internalModelValue;
        wysiwygEditorFormElementController.internalModelValueChanged();
      };
    }
  });
