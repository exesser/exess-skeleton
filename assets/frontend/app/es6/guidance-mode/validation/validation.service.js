'use strict';

(function () {
  class ValidationObserver {

    constructor(flashMessageContainer) {
      this.flashMessageContainer = flashMessageContainer;
      this.errorsChangedCallbacks = [];
      this.errors = {};
      this.unusedErrors = {};
    }

    /**
     * Overwrite the currently set errors with the given object.
     * @param errors Object with field keys as keys and arrays of errors as values
     */
    setErrors(errors) {
      this.errors = errors;
      this.unusedErrors = angular.copy(errors);
      _.forEach(this.errorsChangedCallbacks, function (callback) {
        callback();
      });

      const validationObserver = this;
      _.forEach(this.unusedErrors, function(errors, key) {
        if (_.isEmpty(errors)) {
          return;
        }

        validationObserver.flashMessageContainer.addMessageOfType(
          'ERROR',
          validationObserver.getLabelFromFieldKey(key) + ': ' + _.join(errors, ' '),
          key
        );
      });
    }

    /**
     * Sets the errors for one specific field key.
     * @param fieldKey key to set errors for
     * @param errors array of error messages
     */
    setError(fieldKey, errors) {
      _.set(this.errors, fieldKey, errors);
      _.forEach(this.errorsChangedCallbacks, function (callback) {
        callback();
      });
    }

    /**
     * Clear the errors for one specific field key.
     * @param fieldKey key to set errors for
     */
    clearError(fieldKey) {
      _.unset(this.errors, fieldKey);
      _.forEach(this.errorsChangedCallbacks, function (callback) {
        callback();
      });
    }

    /**
     * Return all the set errors for one specific field key
     * @param fieldKey key to set errors for
     * @returns {Array} possibly empty array of field messages
     */
    getErrorsForKey(fieldKey) {
      _.unset(this.unusedErrors, fieldKey);
      return _.get(this.errors, fieldKey, []);
    }

    /**
     * Register a callback that is invoked when the errors have changed.
     * @param callback function
     */
    registerErrorsChangedCallback(callback) {
      this.errorsChangedCallbacks.push(callback);
    }

    /**
     * Transform this: “quote|account|company_number_c” to “Company number“
     *
     * @param key string
     * @return string
     */
    getLabelFromFieldKey(key) {
      return _.upperFirst(_.replace(_.trimEnd(_.split(key, '|').pop(), '_c'), /_/g, ' '));
    }
  }

  angular.module('digitalWorkplaceApp').service('ValidationObserver', function () {
    return ValidationObserver;
  });
}());
