'use strict';

/**
 * @ngdoc service
 * @name digitalWorkplaceApp.promiseUtils
 * @description
 *
 * This file contains Promise utilities which were needed in the
 * digital workplace.
 *
 * Factory in the digitalWorkplaceApp.
 */
angular.module('digitalWorkplaceApp')
  .factory('promiseUtils', function ($q) {

    const NOT_A_FUNCTION = 'promiseUtils.useLatest error NotAFunction: the "promiseReturningFunction" is not a function.';
    const NOT_A_PROMISE = 'promiseUtils.useLatest error NotAPromise: the "promiseReturningFunction" does not return a promise.';

    return { useLatest };

    /**
     * 'useLatest' takes a function which returns a Promise, and returns
     * a new function, that wraps the old function. The new function
     * will make sure only the latests promise is used when two or
     * more promises are pending at the same time. So the last promise
     * that was created will be used and all preceding ones will be
     * ignored.
     *
     * Here is why this is useful: imagine that we have an autocomplete
     * field and we type in 'Rotterdam' and then realize we made a mistake
     * and that we meant to enter 'Amsterdam'. Now two HTTP request
     * are in the air: Rotterdam and Amsterdam. Now imagine that the
     * server responds with Amsterdam first and Rotterdam second. We
     * now see suggestions for Rotterdam, even though Amsterdam is in
     * the searchbox, which is a UX nightmare.
     *
     * What we want to express is the idea that two request 'belong'
     * to each other and that we should only use the last response of
     * that request. 'useLatest' expresses this idea.
     *
     * @param {Function} A function which returns a Promise.
     * @throws {NotAFunction} If the provided function is not a function.
     * @throws {NotAPromise} If the provided function is not a promise.
     * @return {Promise} A Promise object representing the latest Promise.
     */
    function useLatest(promiseReturningFunction) {
      // Check if the promiseReturningFunction is a function before continuing
      if (_.isFunction(promiseReturningFunction) === false) {
        throw new Error(NOT_A_FUNCTION);
      }

      // latestPromiseId will keep increasing to identify promises.
      let latestPromiseId = 0;

      return function(...args) {
        // Increase the id so the next time
        latestPromiseId += 1;

        // Call the function to get the promise, and pass along the arguments.
        const promise = promiseReturningFunction(...args);

        // Check if the promise is an actual promise before continuing.
        if (_.isFunction(promise.then) === false || _.isFunction(promise.catch) === false) {
          throw new Error(NOT_A_PROMISE);
        }

        // Create a proxy promise for the real 'promise';
        const proxy = $q.defer();

        // Resolve the proxy but only when it is the latest.
        promise.then(doWhenLatest(latestPromiseId, proxy.resolve));

        // Reject the proxy but only when it is the latest.
        promise.catch(doWhenLatest(latestPromiseId, proxy.reject));

        // Finally return the proxy promise object.
        return proxy.promise;
      };

      // Perform the resolve / reject action only when it is the latest promise.
      function doWhenLatest(promiseId, action) {
        return function(arg) {
          if (latestPromiseId === promiseId) {
            action(arg);
          }
        };
      }
    }
  });

