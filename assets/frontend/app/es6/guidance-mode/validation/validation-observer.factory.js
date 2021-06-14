'use strict';

/**
 * @ngdoc factory
 * @name digitalWorkplaceApp.factory:validationObserverFactory
 * @description
 *
 * # Validation observer
 *
 * ## Responsibility
 *
 * The validationObserver is responsible for informing form elements
 * that their current value is invalid. There is only one event on the
 * validationObserver, the 'errorsChanged' event. This indicates that
 * validation has occurred, but does not return anything to the
 * specific listeners.
 *
 * After the guidance performs a validation/suggestions request and
 * the results are in, the errors are sent to the validationObserver
 * by calling 'setErrors' on it. The validationObserver then informs
 * the subscribers of the 'errorsChanged' event that the validation
 * has occurred, so they can request their specific errors
 * using the 'getErrorsForKey' function.
 *
 * ## Lifespan and cardinality
 *
 * The lifespan of the validationObserver is bound to that one specific
 * form. For a guidance this means that the validationObserver is
 * discarded after a step
 * change has occurred.
 *
 * In the filters a single validationObserver is created as we
 * don't have any step changes there.
 *
 * Factory in the digitalWorkplaceApp.
 */
angular.module('digitalWorkplaceApp')
  .factory('validationObserverFactory', function (ValidationObserver, flashMessageContainer) {

    return { createValidationObserver };

    /**
     * Creates a new validation observer.
     * @returns {ValidationObserver} instance of a ValidationObserver
     */
    function createValidationObserver() {
      return new ValidationObserver(flashMessageContainer);
    }
  });
