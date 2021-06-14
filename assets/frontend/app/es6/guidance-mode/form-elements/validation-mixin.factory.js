'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp:validationMixin factory
 * @description
 * # validationMixin
 *
 * The validationMixin factory abstracts the common functionality
 * related to subscribing the form elements to their appropriate
 * incoming errors.
 *
 * Factory of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .factory('validationMixin', function() {

    return { apply };

    /**
     * When 'apply' is called with a controller as input it sets an
     * empty errorMessages array to the controller and registers for
     * field validation callbacks at the validationObserver.
     * This errorMessages array is then replaced by new incoming arrays
     * that can either be empty or filled.
     *
     * @throws Error if a 'key' property is not set on the form element controller.
     * @throws Error if an 'ngModel' property is not set on the form element controller.
     * @param controller Controller to set errorMessages on.
     * @param validationObserver the ValidationObserver instance for this form element.
     */
    function apply(controller, validationObserver) {
      validate(controller);

      initialize(controller);

      registerFieldValidationCallback(controller, validationObserver);
    }

    /**
     * Asserts that a key and ngModel property have been set on the controller.
     * @throws Error if a key or ngModel has not been set on the controller.
     * @param controller the controller
     */
    function validate(controller) {
      if (_.isEmpty(controller.key)) {
        throw new Error(`Error: a form element controller must have a key, the current key is: ${controller.key}.`);
      }
      if (_.isEmpty(controller.ngModel)) {
        throw new Error(`Error: a form element must have an ngModel instance, the current value is: ${controller.ngModel}.`);
      }
    }

    /**
     * Sets an empty errorMessages array on the controller as the initial value.
     * @param controller the controller
     */
    function initialize(controller) {
      controller.errorMessages = [];
    }

    /**
     * Registers for errors coming back for the key specified in the controller.
     * When the errors change the errors array on the controller is overwritten with a new array
     * containing the new errors of the field.
     * @param controller the controller
     * @param validationObserver the validationObserver to get the errors from
     */
    function registerFieldValidationCallback(controller, validationObserver) {
      validationObserver.registerErrorsChangedCallback(function() {
        controller.errorMessages = validationObserver.getErrorsForKey(controller.key);
        controller.ngModel.$setValidity('BACK_END_ERROR', controller.errorMessages.length === 0);
      });
    }
  });
