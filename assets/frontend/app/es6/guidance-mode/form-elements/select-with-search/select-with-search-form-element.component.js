'use strict';
/**
 * @ngdoc function
 * @name digitalWorkplaceApp.selectWithSearchFormElement component
 * @description
 * # selectWithSearchFormElement
 *
 * The selectWithSearchFormElement component can be used to create a form element where a modal opens when you click the plus button.
 * It then allows you to search for data there and select some records. When clicking confirm the ngModel is updated with these selected elements.
 *
 * Example usage:
 *
 * <select-with-search-form-element
 *   ng-model
 *   id="nace"
 *   key="nace"
 *   plus-button-title="Add one or more NACE code(s)"
 *   modal-title="Select one or more NACE code(s)"
 *   selected-results-title="Selected NACE code(s)"
 *   multiple-select="true"
 *   datasource-name="Nace"
 *   is-readonly="false" <!-- Expression that makes the field readonly when it evaluates to true -->
 *   no-backend-interaction="false" <!-- Indicates whether or not to make a call to backend when the field is changed -->
 *   is-disabled="false" <!-- Expression that disables this field when it evaluates to true -->
 *   is-required="false"> <!-- Expression that makes this field required when it evaluates to true -->
 * </select-with-search-form-element>
 *
 * Component of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .component('selectWithSearchFormElement', {
    templateUrl: 'es6/guidance-mode/form-elements/select-with-search/select-with-search-form-element.component.html',
    require: {
      ngModel: 'ngModel',
      guidanceObserversAccessor: '^guidanceObserversAccessor'
    },
    controllerAs: 'selectWithSearchFormElementController',
    bindings: {
      //The id of the field
      id: "@",

      key: "@",

      //The title to display on first item in the list
      //The item with plus sign and text: "Add one or more ..."
      plusButtonTitle: "@",

      //The title to display in the top bar of the modal
      modalTitle: "@",

      //The title to display above the selected results
      selectedResultsTitle: "@",

      params: "<",
      multipleSelect: "<",

      //The datasource name to send to the backend when searching for data
      datasourceName: "@",

      isDisabled: "<",
      isReadonly: "<",
      readonlyJoin: "<",
      noBackendInteraction: "<",
      isRequired: "<"
    },
    controller: function ($scope, $compile, $document, modelChangedMixin, isDisabledMixin, elementIdGenerator) {
      const selectWithSearchFormElementController = this;

      selectWithSearchFormElementController.fullModel = {};
      selectWithSearchFormElementController.selectedResults = [];

      selectWithSearchFormElementController.readonlyShowAll = false;
      selectWithSearchFormElementController.LIMIT = 2;
      selectWithSearchFormElementController.errorMessages = [];

      selectWithSearchFormElementController.$onInit = function () {
        const validationObserver = selectWithSearchFormElementController.guidanceObserversAccessor.getValidationObserver();
        validationObserver.registerErrorsChangedCallback(function() {
          selectWithSearchFormElementController.errorMessages = validationObserver.getErrorsForKey(selectWithSearchFormElementController.key);
          selectWithSearchFormElementController.ngModel.$setValidity('BACK_END_ERROR', selectWithSearchFormElementController.errorMessages.length === 0);
          setRequired();
        });


        const guidanceFormObserver = selectWithSearchFormElementController.guidanceObserversAccessor.getGuidanceFormObserver();
        selectWithSearchFormElementController.fullModel = guidanceFormObserver.getFullModel();
        selectWithSearchFormElementController.elementId = elementIdGenerator.generateId(selectWithSearchFormElementController.id, guidanceFormObserver);

        modelChangedMixin.apply(selectWithSearchFormElementController, 'selectWithSearchFormElementController', $scope);

        isDisabledMixin.apply(selectWithSearchFormElementController);

        /**
         * This is called when we need to determine if the value of an input is empty.
         *
         * For instance, the required directive does this to work out if the input has data or not.
         *
         * The default `$isEmpty` function checks whether the value is `undefined`, `''`, `null` or `NaN`.
         *
         * In the case of a select-with-search, the default $isEmpty function would consider an empty array non-empty,
         * but in the context of a select-with-search select an empty array would mean that no options have been chosen.
         * We consider a select-with-search empty if the value is `undefined`, `''`, `null`, `NaN` or '[]'.
         *
         * See Angular's $isEmpty function for more information.
         *
         * @param {*} value The value of the input to check for emptiness.
         * @returns {boolean} True if `value` is "empty".
         */
        selectWithSearchFormElementController.ngModel.$isEmpty = (value) => _.isEmpty(value);

        setRequired();
      };

      selectWithSearchFormElementController.setRequired = setRequired;

      selectWithSearchFormElementController.selectWithSearchModal = undefined;

      /**
       * Open the modal to select/deselect results.
       */
      selectWithSearchFormElementController.open = () => {
        if (selectWithSearchFormElementController.fieldIsDisabled() === true) {
          return;
        }
        selectWithSearchFormElementController.selectedResults = angular.copy(selectWithSearchFormElementController.internalModelValue);
        const body = angular.element($document[0].body);
        const scope = $scope.$new();
        const template = `
          <select-with-search-modal
            selected-results="selectWithSearchFormElementController.selectedResults"
            confirm-callback="selectWithSearchFormElementController.modalConfirm()"
            modal-title="{{selectWithSearchFormElementController.modalTitle}}"
            selected-results-title="{{selectWithSearchFormElementController.selectedResultsTitle}}"
            multiple-select="selectWithSearchFormElementController.multipleSelect"
            params="selectWithSearchFormElementController.params"
            datasource-name="{{selectWithSearchFormElementController.datasourceName}}"
            full-model="selectWithSearchFormElementController.fullModel">
          </select-with-search-modal>
        `;

        selectWithSearchFormElementController.selectWithSearchModal = angular.element(template);
        $compile(selectWithSearchFormElementController.selectWithSearchModal)(scope);

        //When the element is destroyed, destroy the scope as well.
        angular.element(selectWithSearchFormElementController.selectWithSearchModal).on("$destroy", function () {
          scope.$destroy();
        });

        body.append(selectWithSearchFormElementController.selectWithSearchModal);
      };

      /**
       * Removes a given item from the selected results.
       * @param item a selected result
       */
      selectWithSearchFormElementController.deselectItem = (item) => {
        if (selectWithSearchFormElementController.fieldIsDisabled() === true) {
          return;
        }

        selectWithSearchFormElementController.internalModelValue = _.without(selectWithSearchFormElementController.internalModelValue, item);
        updateModelData();
      };

      selectWithSearchFormElementController.modalConfirm = () => {
        selectWithSearchFormElementController.internalModelValue = angular.copy(selectWithSearchFormElementController.selectedResults);
        updateModelData();
      };

      selectWithSearchFormElementController.readonlyValue = function () {
        const { readonlyShowAll, internalModelValue, LIMIT, readonlyJoin } = selectWithSearchFormElementController;

        const takeAmount = readonlyShowAll ? internalModelValue.length : LIMIT;

        return _(selectWithSearchFormElementController.internalModelValue)
          .values()
          .take(takeAmount)
          .map('label')
          .join(readonlyJoin);
      };

      selectWithSearchFormElementController.toggleReadonlyShowAll = function () {
        selectWithSearchFormElementController.readonlyShowAll = !selectWithSearchFormElementController.readonlyShowAll;
      };

      /**
       * Update the model with the selected values.
       * Delete the modal element
       */
      function updateModelData() {
        selectWithSearchFormElementController.internalModelValueChanged();

        if (_.isUndefined(selectWithSearchFormElementController.selectWithSearchModal) === false) {
          selectWithSearchFormElementController.selectWithSearchModal.remove();
        }

        setRequired();
      }

      function setRequired() {
        if (selectWithSearchFormElementController.isRequired === true) {
          const length = _.result(selectWithSearchFormElementController.internalModelValue, 'length', 0);
          const isValid = length > 0;

          selectWithSearchFormElementController.ngModel.$setValidity('required', isValid);
        }
      }
    }
  });
