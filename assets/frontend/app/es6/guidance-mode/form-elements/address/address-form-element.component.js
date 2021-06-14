"use strict";

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.component:addressFormElement
 * @description
 * # addressFormElement
 * Component of the digitalWorkplaceApp
 *
 * This component creates an address block that binds to an object on
 * the model. The address fields are bound to properties on that object.
 *
 * Example usage:
 *
 * <address-form-element
 *  ng-model
 *  id="delivery.address" <!-- The id for the form element, used for e2e testing -->
 *  key="delivery.address" <!-- The key to bind to in the model -->
 *  fields="fields" <!--  The information for the subfields of the address -->
 *  model="model <!-- The entire model object -->
 *  has-border="false" <!-- Indicates whether or not to draw a border around the field -->
 *  is-readonly="false" <!-- Expression that makes the field readonly when it evaluates to true -->
 *  no-backend-interaction="false" <!-- Indicates whether or not to make a call to backend when the field is changed -->
 *  is-disabled="otherField > 2"> <!-- Expression that disables this field when it evaluates to true -->
 * </address-form-element>
 *
 * The reason the 'addressFormElement' is necessary, and why the
 * 'address' formly types uses this Component, is because it makes
 * it easier to manipulate the ngModel of the 'address' formly type.
 *
 * Formly can automatically generate an ngModel for you, which binds
 * correctly to nested or non-nested properties. By creating an extra
 * level of dept all the addressFormElement needs to do is require
 * 'ngModel'. AddressFormElement can then manipulate the ngModel
 * just as in a regular Angular Component without having to worry about
 * where it came from.
 *
 * Also, we need a component to require the 'guidanceObserversAccessor'
 * in order to obtain the validationObserver and suggestionsObserver.
 */
angular.module('digitalWorkplaceApp')
  .component('addressFormElement', {
    templateUrl: 'es6/guidance-mode/form-elements/address/address-form-element.component.html',
    require: {
      ngModel: 'ngModel',
      guidanceObserversAccessor: '^guidanceObserversAccessor'
    },
    bindings: {
      id: "@",
      key: "@",
      fields: "<",
      model: "<",
      hasBorder: "<",
      isDisabled: "<",
      isReadonly: "<",
      noBackendInteraction: "<"
    },
    controllerAs: 'addressFormElementController',
    controller: function ($scope, validationMixin, suggestionsMixin, isDisabledMixin, elementIdGenerator, modelChangedMixin) {
      const addressFormElementController = this;

      addressFormElementController.internalModelValue = [];

      addressFormElementController.$onInit = function () {
        const validationObserver = addressFormElementController.guidanceObserversAccessor.getValidationObserver();
        validationMixin.apply(addressFormElementController, validationObserver);

        const suggestionsObserver = addressFormElementController.guidanceObserversAccessor.getSuggestionsObserver();
        suggestionsMixin.apply(addressFormElementController, suggestionsObserver, true);

        isDisabledMixin.apply(addressFormElementController);

        modelChangedMixin.apply(addressFormElementController, 'addressFormElementController', $scope, false);

        const guidanceFormObserver = addressFormElementController.guidanceObserversAccessor.getGuidanceFormObserver();
        addressFormElementController.elementId = elementIdGenerator.generateId(addressFormElementController.id, guidanceFormObserver);

        // The id's the autocomplete needs to listen to.
        addressFormElementController.autoCompleteElementIds = _.map([
          addressFormElementController.fields.street.key,
          addressFormElementController.fields.houseNumber.key,
          addressFormElementController.fields.box.key,
          addressFormElementController.fields.addition.key,
          addressFormElementController.fields.postalCode.key,
          addressFormElementController.fields.city.key,
          addressFormElementController.fields.country.key
        ], (id) => elementIdGenerator.generateId(id, guidanceFormObserver));

        /**
         * This is called when we need to determine if the value of an input is empty.
         *
         * For instance, the required directive does this to work out if the input has data or not.
         *
         * The default `$isEmpty` function checks whether the value is `undefined`, `''`, `null` or `NaN`.
         *
         * In the case of an address, the default $isEmpty function would
         * consider an empty object non-empty, but in the context of an
         * address we consider an address empty if it has no street, houseNumber,
         * postalCode, city or country.
         *
         * If one of those fields is not visible, it is not considered
         * part of the 'emptyness'.
         *
         * See Angular's $isEmpty function for more information.
         *
         * @param {*} value The value of the input to check for emptiness.
         * @returns {boolean} True if `value` is "empty".
         */
        addressFormElementController.ngModel.$isEmpty = function (value) {
          /*
           For the fields inside the address are not hard-coded because
           whether or not they are visable is configurable. The following
           fields when visible are required.
           */
          const requiredFields = [
            addressFormElementController.fields.street,
            addressFormElementController.fields.houseNumber,
            addressFormElementController.fields.postalCode,
            addressFormElementController.fields.city,
            addressFormElementController.fields.country
          ];

          // Get all id's of the requiredFields which are visible.
          const requiredFieldIds = _(requiredFields)
            .filter((fields) => fields.display === true)
            .map('key');

          return _(requiredFieldIds)
            .map((fieldId) => _.get(value, fieldId))
            .some(_.isEmpty);
        };
      };

      /*
       * The address form element retrieves the initial non-undefined ngModel
       * $viewValue and sets this to˜ addressFormElementController.internalModelValue.
       * That is the field all the sub fields (for street, house number, etc.)
       * are bound to in the view. Because we bind to properties on the
       * same object we do not need to propagate them out again.
       *
       * Every time the ngModel.$viewValue changes after the first
       * initial setting we send out formValueChanged events.
       *
       * Unfortunately we cannot add a callback to the $viewChangeListeners
       * here because we need to do an object equality check.
       *
       * Afterwards we trigger the re-evaluation of the validation.
       * If we don't do this the empty check is not performed again.
       */
      $scope.$watch('addressFormElementController.ngModel.$viewValue', function (newValue, oldValue) {
        if (_.isObject(newValue)) {
          addressFormElementController.internalModelValue = newValue;

          // When the previous value was already an object and it is unequal to the new value, a change has occurred.
          // We don't use ngChange for this since it could also have been triggered by a suggestion.
          if (_.isObject(oldValue) && _.isEqual(newValue, oldValue) === false) {
            addressFormElementController.internalModelValueChanged();
          }
        }
        addressFormElementController.ngModel.$validate();
      }, true);

      // When the suggestion is clicked change the model.
      addressFormElementController.suggestionClicked = function (suggestion) {
        addressFormElementController.model = _.merge(addressFormElementController.model, suggestion.model);
      };

      addressFormElementController.clearAddressFields = function () {
        addressFormElementController.suggestions = {};

        addressFormElementController.internalModelValue[addressFormElementController.fields.street.key] = '';
        addressFormElementController.internalModelValue[addressFormElementController.fields.houseNumber.key] = '';
        addressFormElementController.internalModelValue[addressFormElementController.fields.addition.key] = '';
        addressFormElementController.internalModelValue[addressFormElementController.fields.box.key] = '';
        addressFormElementController.internalModelValue[addressFormElementController.fields.postalCode.key] = '';
        addressFormElementController.internalModelValue[addressFormElementController.fields.city.key] = '';
        addressFormElementController.internalModelValue[addressFormElementController.fields.country.key] = '';
      };

      addressFormElementController.showResetButton = function () {
        const fields = [
          addressFormElementController.internalModelValue[addressFormElementController.fields.street.key],
          addressFormElementController.internalModelValue[addressFormElementController.fields.houseNumber.key],
          addressFormElementController.internalModelValue[addressFormElementController.fields.addition.key],
          addressFormElementController.internalModelValue[addressFormElementController.fields.box.key],
          addressFormElementController.internalModelValue[addressFormElementController.fields.postalCode.key],
          addressFormElementController.internalModelValue[addressFormElementController.fields.city.key],
          addressFormElementController.internalModelValue[addressFormElementController.fields.country.key]
        ];

        return _.isEmpty(_.filter(fields, (value) => {return _.isEmpty(value) === false;})) === false;
      };

      addressFormElementController.readonlyValueLine1 = function () {
        const street = addressFormElementController.internalModelValue[addressFormElementController.fields.street.key];
        const houseNumber = addressFormElementController.internalModelValue[addressFormElementController.fields.houseNumber.key];
        const addition = addressFormElementController.internalModelValue[addressFormElementController.fields.addition.key];
        const box = addressFormElementController.internalModelValue[addressFormElementController.fields.box.key];

        return _([street, houseNumber, addition, box])
          .filter((value) => _.isEmpty(value) === false || _.isNumber(value))
          .join(' ');
      };

      addressFormElementController.readonlyValueLine2 = function () {
        const postalCode = addressFormElementController.internalModelValue[addressFormElementController.fields.postalCode.key];
        const city = addressFormElementController.internalModelValue[addressFormElementController.fields.city.key];
        const country = addressFormElementController.internalModelValue[addressFormElementController.fields.country.key];

        return _([postalCode, city, country])
          .filter((value) => _.isEmpty(value) === false || _.isNumber(value))
          .join(' ');
      };
    }
  });
