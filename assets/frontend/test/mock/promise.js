'use strict';

const mockHelpers = mockHelpers || {};

(function(mockHelpers) {

  /*
    Copy the value so we don't mutate the original, otherwise you can get nasty
    bugs when projecting a value through a promise chain multiple times. Which
    can sometimes happen in test situations.
  */
  function copyValue(value) {
    /*
      If user doesn't care for a return value it will be undefined.
      We don't need to copy it then since the user won't do anything with it.
    */
    if (_.isUndefined(value)) {
      return undefined;
    } else {
      return _.clone(value, true);
    }
  }

  /**
   * Returns a function that creates a $q Promise
   * which resolves to the value that is provided.
   * @param  {[type]} $q    A reference to the $q object from Angular
   * @param  {[type]} value The value you want the promise to resolve with. Can also be empty when you don't care for a return value.
   * @return {[type]}       A resolved promise.
   */
  mockHelpers.resolvedPromise = function($q, value) {
    const copiedValue = copyValue(value);

    return function() {
      const deferred = $q.defer();
      deferred.resolve(copiedValue);
      return deferred.promise;
    };
  };

  /**
   * Returns a function that creates a $q Promise
   * which rejects with value that is provided.
   * @param  {[type]} $q    A reference to the $q object from Angular
   * @param  {[type]} value The value you want the promise to reject with. Can also be empty when you don't care for a return value.
   * @return {[type]}       A resolved rejected.
   */
  mockHelpers.rejectedPromise = function($q, value) {
    const copiedValue = copyValue(value);

    return function() {
      const deferred = $q.defer();
      deferred.reject(copiedValue);
      return deferred.promise;
    };
  };

  /**
   * A variant of 'resolvedPromise' which wraps the promise
   * in an object under the key '$promise'. Useful for testing
   * $resource objects.
   * @param  {[type]} $q    A reference to the $q object from Angular
   * @param  {[type]} value The value you want the promise to resolve with
   * @return {[type]}       An object with $promise as a key which points to a resolved promise
   */
  mockHelpers.resolvedResourcePromise = function($q, value) {
    return function() {
      const promise = mockHelpers.resolvedPromise($q, value)();
      return { $promise: promise };
    };
  };

  /**
   * A variant of 'rejectedPromise' which wraps the promise
   * in an object under the key '$promise'. Useful for testing
   * $resource objects.
   * @param  {[type]} $q    A reference to the $q object from Angular
   * @param  {[type]} value The value you want the promise to reject with
   * @return {[type]}       An object with $promise as a key which points to a rejected promise
   */
  mockHelpers.rejectedResourcePromise = function($q, value) {
    return function() {
      const promise = mockHelpers.rejectedPromise($q, value)();
      return { $promise: promise };
    };
  };
})(mockHelpers);
