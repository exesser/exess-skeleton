'use strict';

/**
 * Configures ng-translate to use the DEFAULT_TRANSLATION,
 * and stores the uses preference in LocalStorage.
 */
angular.module('digitalWorkplaceApp')
  .config(function ($translateProvider, LANGUAGE, DEFAULT_TRANSLATION) {
    // The default language is english, it is bundled with the application via DEFAULT_TRANSLATION.
    $translateProvider.translations(LANGUAGE.ENGLISH_BELGIUM, DEFAULT_TRANSLATION);
    $translateProvider.preferredLanguage(LANGUAGE.ENGLISH_BELGIUM);

    // Configure where the language exists so we can dynamically swap the language at runtime.
    $translateProvider.useStaticFilesLoader({
      prefix: 'languages/',
      suffix: '.json'
    });

    // Store users selected language in local storage.
    $translateProvider.useLocalStorage();

    // Secure translations see: http://angular-translate.github.io/docs/#/guide/19_security
    // Unfortunately we cannot currently use sanitize, as this breaks special characters. See: https://github.com/angular-translate/angular-translate/issues/1101
    $translateProvider.useSanitizeValueStrategy('escape');
  });
