'use strict';

// Register helpers to 'mockHelpers' because it is a global in the .jshintrc
const mockHelpers = mockHelpers || {};

/*
  Function that handles expecting the loading of the en.json file.
 */
mockHelpers.expectTranslationGET = function($httpBackend) {
  $httpBackend.expectGET('/languages/en.json').respond({});
};

/*
  Mocks the entire $translate service.

  The $translate service is not something we want to 'actually' test.
  We are more interested if the $translate was called with the proper
  arguments than if the translation actually works. This mocked version
  of the $translate service returns an Object which holds the 'key' of
  the translation and any placeholders.

  Example code that you might want to test:

  const translationMockTester = {
    genericErrorHandler: function() {
      $translate('ERROR.GENERIC').then(function (translation) {
        flashMessageFactory.addError(translation);
      });
    }
  }

  You write this in your test code:

  beforeEach(mockHelpers.mockTranslate); // Start mocking $translate

  it('should know how to add a generic Error Handler', inject(function($rootScope) {
    spyOn(flashMessageFactory, 'addError');

    translationMockTester.genericErrorHandler();

    $rootScope.$apply(); // Call to resolve $translate promise

    expect(flashMessageFactory.addError.calls.count()).toBe(1);
    expect(flashMessageFactory.addError).toHaveBeenCalledWithTranslation('ERROR.GENERIC');
  }));
 */
mockHelpers.mockTranslate = function() {
  jasmine.addMatchers(mockHelpers.translationMatchers);

  module('digitalWorkplaceApp', function config($provide) {
    $provide.provider('$translate', function() {
      const store                 = {};
      this.get                  = function() { return false; };
      this.preferredLanguage    = function() { return false; };
      this.storage              = function() { return false; };
      this.translations         = function() { return {}; };

      this.$get = function($q) {
        const $translate = function(key, placeholders) {
          const translation = createFakeTranslation(key, placeholders);

          const deferred = $q.defer();
          deferred.resolve(translation); // Immediately translation object.

          return deferred.promise;
        };

        $translate.addPair    = function(key, val) { store[key] = val; };
        $translate.isPostCompilingEnabled = function() { return false; };
        $translate.preferredLanguage = function() { return false; };
        $translate.storage    = function() { return false; };
        $translate.storageKey = function() { return true; };
        $translate.use        = function() { return false; };
        $translate.instant    = function(key) { return key; };

        return $translate;

        /**
         * Wraps a fake translation and any 'placeholders' arguments
         * into an object. The object will contain a 'key' which represents
         * the translations key, for instance USER.NAME. It can optionally contain
         * 'placeholders' which are the 'dynamic' arguments for a translation, such
         * as 'Hi my name is {{name}}'.
         *
         * @param {String} key The key you want to translate
         * @param {Object} placeholders The placeholders that were provided for the key.
         * @return {Object{key, (optional)placeholders}} The faked object containing the key and optionally the placeholders
         */
        function createFakeTranslation(key, placeholders) {
          let fake = fakeTranslation(key);

          if (_.isUndefined(placeholders) === false) {
            fake = _.mapValues(fake, function(n) {
              n.placeholders = placeholders;
              return n;
            });
          }

          return fake;
        }

        /**
         * Returns an object where all key's are transformed to
         * an object where the key and the value match.
         * @param  {String|Array} key The key or keys you want to translate
         * @return {[type]}     Object
         */
        function fakeTranslation(key) {
          if (key.constructor === Array) {
            return transformArrayToObject(key);
          } else {
            return transformToObject(key);
          }
        }

        // transforms ['a', 'b', 'c'] -> {a: { key: 'a'}, b: { key: 'b'}, c: { key: 'c'}}
        function transformArrayToObject(array) {
          const tranformToObject = _.map(array, transformToObject);

          // Merge all objects into one.
          return _.reduce(tranformToObject, _.merge);
        }

        // transforms 'GREET' -> {'GREET': { key: 'GREET' } }
        function transformToObject(key) {
          const object = {};
          object[key] = { key: key };
          return object;
        }
      };
    });
  });
};

/**
 * The translationMatchers so we can have a very readable way to check
 * if a function was ever called with a specific translation.
 */
mockHelpers.translationMatchers = {
  toHaveBeenCalledWithTranslation: function() {
    return { compare };

    /**
     * Checks if the combination of key and placeholders was
     * ever called on the 'actual' function.
     * @param  {function}             actual       The function you want to check for if it was ever called with a translation.
     * @param  {string|[string]}      key          The key as a string or keys as an array of strings.
     * @param  {object{name: value}}  placeholders The placeholders of the translation keys. A placeholder is a dynamic part of a translation.
     * @return {object{pass}}         An object with a key pass which is a boolean whether or not the translation was called.
     */
    function compare (actual, key, placeholders) {
      const translationCalls = collectCalls(actual);

      const pass = _(translationCalls).map((fakeTranslations) => {
        return existsInFakeTranslations(key, placeholders, fakeTranslations);
      }).some((r) => r === true);

      return { pass };
    }

    /**
     * Takes all calls to the 'actual' function an returns the first arguments
     * which should be the fakeTranslation object.
     */
    function collectCalls(actual) {
      const calls = [];

      for (let i = 0; i < actual.calls.count(); i += 1) {
        calls.push(actual.calls.argsFor(i)[0]);
      }

      return calls;
    }

    function existsInFakeTranslations(key, placeholders, fakeTranslations) {
      const keys = transformKeyToArray(key);

      return _(keys).map((key) => {
        const fakeTranslation = getFakeTranslation(key, fakeTranslations);
        return existsInFakeTranslation(key, placeholders, fakeTranslation);
      }).every((r) => r === true);
    }

    function existsInFakeTranslation(key, placeholders, fakeTranslation) {
      if (_.isUndefined(fakeTranslation) === false) {
        if (_.isUndefined(fakeTranslation.placeholders)) {
          return fakeTranslation.key === key;
        } else {
          return fakeTranslation.key === key && _.isEqual(fakeTranslation.placeholders, placeholders);
        }
      }
      return false;
    }

    function transformKeyToArray(key) {
      if (_.isString(key)) {
        return [key];
      }

      return key;
    }

    function getFakeTranslation(key, fakeTranslations) {
      if (fakeTranslations.key === key) {
        return fakeTranslations;
      } else {
        return fakeTranslations[key];
      }
    }
  }
};

/**
 * @ngdoc service
 * @name translateTest.translationMockTester
 * @description Factory for testing the toHaveBeenCalledWithTranslation custom Jasmine matcher.
 */
angular.module('translateTest', [])
  .factory('flashMessageFactory', function() {
    return {
      addError: _.noop
    };
  })
  .factory('translationMockTester', function (flashMessageFactory, $translate) {

    const translationMockTester = {};
    translationMockTester.singleString = singleString;
    translationMockTester.doubleString = doubleString;
    translationMockTester.singleStringWithPlaceholder = singleStringWithPlaceholder;
    translationMockTester.doubleStringWithPlaceholder = doubleStringWithPlaceholder;
    translationMockTester.dynamicArrayWithPlaceholder = dynamicArrayWithPlaceholder;
    translationMockTester.doubleStringOneIsPickedWithPlaceholder = doubleStringOneIsPickedWithPlaceholder;
    return translationMockTester;

    /**
     * @description Adds a genericError to the flashMessageFactory
     */
    function singleString() {
      $translate('ERROR.GENERIC').then((errorMessage) => {
        flashMessageFactory.addError(errorMessage);
      });
    }

    /**
     * @description Adds a genericError to the flashMessageFactory
     */
    function doubleString() {
      $translate(['ERROR.GENERIC', 'ERROR.ALL']).then((errorMessage) => {
        flashMessageFactory.addError(errorMessage);
      });
    }

    /**
     * @description Adds a genericError to the flashMessageFactory
     */
    function singleStringWithPlaceholder() {
      $translate('ERROR.GENERIC', { username: 'Harry' }).then((errorMessage) => {
        flashMessageFactory.addError(errorMessage);
      });
    }

    /**
     * @description Adds a genericError to the flashMessageFactory
     */
    function doubleStringWithPlaceholder() {
      $translate(['ERROR.GENERIC', 'ERROR.ALL'], { username: 'Harry' }).then((errorMessage) => {
        flashMessageFactory.addError(errorMessage);
      });
    }

    function dynamicArrayWithPlaceholder(array) {
      $translate(array, { username: 'Harry' }).then((errorMessage) => {
        flashMessageFactory.addError(errorMessage);
      });
    }

    function doubleStringOneIsPickedWithPlaceholder() {
      $translate(['ERROR.GENERIC', 'ERROR.ALL'], { username: 'Harry' }).then((errorMessage) => {
        flashMessageFactory.addError(errorMessage['ERROR.GENERIC']);
      });
    }
  });

describe('Service: translationMockTester', function () {

  // load the service's module
  beforeEach(module('translateTest'));

  // instantiate service
  let translationMockTester;

  let flashMessageFactory;

  beforeEach(mockHelpers.mockTranslate); // Start mocking $translate

  beforeEach(inject(function (_translationMockTester_, _flashMessageFactory_, $state) {
    translationMockTester = _translationMockTester_;
    flashMessageFactory = _flashMessageFactory_;

    mockHelpers.blockUIRouter($state);
  }));

  it('should know how to translate a singleString', inject(function($rootScope) {
    spyOn(flashMessageFactory, 'addError');
    translationMockTester.singleString();
    $rootScope.$apply(); // Call to resolve $translate promise

    expect(flashMessageFactory.addError.calls.count()).toBe(1);
    expect(flashMessageFactory.addError).toHaveBeenCalledWithTranslation('ERROR.GENERIC');
    expect(flashMessageFactory.addError).not.toHaveBeenCalledWithTranslation('ERROR.GENERICs');
  }));

  it('should know how to translate a doubleString', inject(function($rootScope) {
    spyOn(flashMessageFactory, 'addError');
    translationMockTester.doubleString();
    $rootScope.$apply(); // Call to resolve $translate promise

    expect(flashMessageFactory.addError.calls.count()).toBe(1);
    expect(flashMessageFactory.addError).toHaveBeenCalledWithTranslation(['ERROR.GENERIC', 'ERROR.ALL']);
    expect(flashMessageFactory.addError).not.toHaveBeenCalledWithTranslation(['ERROR', 'BERROR']);
  }));

  it('should know how to translate a singleStringWithPlaceholder', inject(function($rootScope) {
    spyOn(flashMessageFactory, 'addError');
    translationMockTester.singleStringWithPlaceholder();
    $rootScope.$apply(); // Call to resolve $translate promise

    expect(flashMessageFactory.addError.calls.count()).toBe(1);
    expect(flashMessageFactory.addError).toHaveBeenCalledWithTranslation('ERROR.GENERIC', { username: 'Harry' });
    expect(flashMessageFactory.addError).not.toHaveBeenCalledWithTranslation('ERROR.GENERIC', { username: 'Guy' });
    expect(flashMessageFactory.addError).not.toHaveBeenCalledWithTranslation('ERROR.GENERICS', { username: 'Harry' });
  }));

  it('should know how to translate a doubleStringWithPlaceholder', inject(function($rootScope) {
    spyOn(flashMessageFactory, 'addError');
    translationMockTester.doubleStringWithPlaceholder();
    $rootScope.$apply(); // Call to resolve $translate promise

    expect(flashMessageFactory.addError.calls.count()).toBe(1);
    expect(flashMessageFactory.addError).toHaveBeenCalledWithTranslation(['ERROR.GENERIC', 'ERROR.ALL'], { username: 'Harry' });
    expect(flashMessageFactory.addError).not.toHaveBeenCalledWithTranslation(['ERROR.GENERIC', 'ERROR.ALL'], { username: 'Harrys' });
    expect(flashMessageFactory.addError).not.toHaveBeenCalledWithTranslation(['ERROR.GENERIC', 'ERROR.ALL', 'KEVIN'], { username: 'Harry' });
  }));

  it('should know how to translate a dynamicArrayWithPlaceholder', inject(function($rootScope) {
    spyOn(flashMessageFactory, 'addError');
    translationMockTester.dynamicArrayWithPlaceholder(['ERROR.GENERIC', 'ERROR.ALL']);
    translationMockTester.dynamicArrayWithPlaceholder(['ERROR.GENERIC']);
    $rootScope.$apply(); // Call to resolve $translate promise

    expect(flashMessageFactory.addError.calls.count()).toBe(2);
    expect(flashMessageFactory.addError).toHaveBeenCalledWithTranslation(['ERROR.GENERIC', 'ERROR.ALL'], { username: 'Harry' });
    expect(flashMessageFactory.addError).toHaveBeenCalledWithTranslation(['ERROR.GENERIC'], { username: 'Harry' });
  }));

  it('should know how to translate a doubleStringOneIsPickedWithPlaceholder', inject(function($rootScope) {
    spyOn(flashMessageFactory, 'addError');
    translationMockTester.doubleStringOneIsPickedWithPlaceholder();
    $rootScope.$apply(); // Call to resolve $translate promise

    expect(flashMessageFactory.addError.calls.count()).toBe(1);
    expect(flashMessageFactory.addError).toHaveBeenCalledWithTranslation('ERROR.GENERIC', { username: 'Harry' });
  }));
});
