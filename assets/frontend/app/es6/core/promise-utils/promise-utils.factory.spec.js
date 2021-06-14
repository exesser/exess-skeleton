'use strict';

describe('Factory: promiseUtils', function () {
  // load the directive's module
  beforeEach(module('digitalWorkplaceApp'));

  let $rootScope;
  let $timeout;
  let $q;

  let promiseUtils;

  beforeEach(inject(function ($state, _$rootScope_, _$q_, _$timeout_, _promiseUtils_) {
    mockHelpers.blockUIRouter($state);

    $rootScope = _$rootScope_;
    $q = _$q_;
    $timeout = _$timeout_;

    promiseUtils = _promiseUtils_;
  }));

  describe('resolve behavior', function() {
    // Creates a promise which resolves with the value after x seconds
    function promiseCreator(after, value) {
      return $timeout(function() {
        return value;
      }, after);
    }

    it('should only use the latest promise even when the promise is resolved afterwards.', function() {
      let value = null;

      const latestPromiseCreator = promiseUtils.useLatest(promiseCreator);

      // Irrelevant promise
      latestPromiseCreator(1000, 1337).then((v) => {
        value = v;
      });

      // Latest promise
      latestPromiseCreator(600, 666).then((v) => {
        value = v;
      });

      $timeout.flush();

      expect(value).toBe(666);
    });

    it('should when two promises are fired after each use both values ', function() {
      let value = null;

      const latestPromiseCreator = promiseUtils.useLatest(promiseCreator);

      latestPromiseCreator(1000, 1337).then((v) => {
        value = v;
      });

      $timeout.flush();

      expect(value).toBe(1337);

      latestPromiseCreator(600, 666).then((v) => {
        value = v;
      });

      $timeout.flush();

      expect(value).toBe(666);
    });

    it('should when there is only one promise consider that promise the latest promise.', function() {
      let value = null;

      const latestPromiseCreator = promiseUtils.useLatest(promiseCreator);

      latestPromiseCreator(1000, 1337).then((v) => {
        value = v;
      });

      $timeout.flush();

      expect(value).toBe(1337);
    });
  });

  describe('reject behavior', function() {
    // Creates a promise which rejects with the value after x seconds
    function promiseCreator(after, value) {
      var deferred = $q.defer();

      setTimeout(() => {
        deferred.reject(value);
      }, after);

      return deferred.promise;
    }

    beforeEach(function() {
      jasmine.clock().install();
    });

    afterEach(function() {
      jasmine.clock().uninstall();
    });

    it('should only use the latest promise even when the promise is resolved afterwards.', function() {
      let value = null;

      const latestPromiseCreator = promiseUtils.useLatest(promiseCreator);

      // Irrelevant promise
      latestPromiseCreator(1000, 1337).catch((v) => {
        value = v;
      });

      // Latest promise
      latestPromiseCreator(600, 666).catch((v) => {
        value = v;
      });

      jasmine.clock().tick(1000);
      $rootScope.$apply();

      expect(value).toBe(666);
    });

    it('should when two promises are fired after each use both values ', function() {
      let value = null;

      const latestPromiseCreator = promiseUtils.useLatest(promiseCreator);

      latestPromiseCreator(1000, 1337).catch((v) => {
        value = v;
      });

      jasmine.clock().tick(1000);
      $rootScope.$apply();

      expect(value).toBe(1337);

      latestPromiseCreator(600, 666).catch((v) => {
        value = v;
      });

      jasmine.clock().tick(600);
      $rootScope.$apply();

      expect(value).toBe(666);
    });

    it('should when there is only one promise consider that promise the latest promise.', function() {
      let value = null;

      const latestPromiseCreator = promiseUtils.useLatest(promiseCreator);

      latestPromiseCreator(1000, 1337).catch((v) => {
        value = v;
      });

      jasmine.clock().tick(1000);
      $rootScope.$apply();

      expect(value).toBe(1337);
    });
  });

  describe('when error are thrown', function() {
    it('should throw a NotAFunction error when "promiseReturningFunction" is not a function', function() {
      expect(function() {
        promiseUtils.useLatest(42);
      }).toThrow(new Error('promiseUtils.useLatest error NotAFunction: the "promiseReturningFunction" is not a function.'));
    });

    it('should throw a NotAPromise error when there is no "then" function', function() {
      function notAPromise() {
        return {
          then: 42,
          error: _.noop
        };
      }

      expect(function() {
        promiseUtils.useLatest(notAPromise)();
      }).toThrow(new Error('promiseUtils.useLatest error NotAPromise: the "promiseReturningFunction" does not return a promise.'));
    });

    it('should throw a NotAPromise error when there is no "catch" function', function() {
      function notAPromise() {
        return {
          then: _.noop,
          error: 42
        };
      }

      expect(function() {
        promiseUtils.useLatest(notAPromise)();
      }).toThrow(new Error('promiseUtils.useLatest error NotAPromise: the "promiseReturningFunction" does not return a promise.'));
    });
  });
});
