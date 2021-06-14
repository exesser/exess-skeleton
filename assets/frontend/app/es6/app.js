'use strict';

// -------------------------------------------------------------------
// :: GENERAL
// -------------------------------------------------------------------
// We have javascript - remove the no-js class

document.documentElement.className = document.documentElement.className.replace('no-js', ''); //eslint-disable-line angular/document-service

// -------------------------------------------------------------------
// :: MAKE BUTTONS RESPOND FASTER
// -------------------------------------------------------------------
// Make buttons respond immediately (showing a different state)
// - http://www.mobify.com/blog/beginners-guide-to-perceived-performance

/* istanbul ignore next  */
if ('ontouchstart' in document) {
  document.addEventListener('touchend', _.noop, true); //eslint-disable-line angular/document-service
} else {
  document.body.className += 'force-nav no-touch'; //eslint-disable-line angular/document-service
}

// -------------------------------------------------------------------
// :: CUSTOM CONFIG FOR CONTANTS
// -------------------------------------------------------------------

/* eslint-disable */
let env_config = {};
/* istanbul ignore next  */
jQuery.ajax({
  url: "./custom.json",
  async: false,
  dataType: "json",
  success: function (data) {
    jQuery.extend(true, env_config, data);
  }
});
/* eslint-enable */

// -------------------------------------------------------------------
// :: ANGULAR APP
// -------------------------------------------------------------------

/**
 * @ngdoc overview
 * @name digitalWorkplaceApp
 * @description
 * # digitalWorkplaceApp
 *
 * Main module of the application.
 */
angular
  .module('digitalWorkplaceApp', [
    'ui.router',
    'ngAnimate',
    'ngCookies',
    'ngMessages',
    'ngSanitize',
    'ngTouch',
    'pascalprecht.translate',
    'digitalWorkplaceApp.Constants',
    'formly',
    'pikaday',
    'angular-loading-bar',
    'ngFileUpload',
    'monospaced.elastic',
    'uuid',
    'textAngular',
    'signature',
    'angulartics',
    'angulartics.google.tagmanager',
    'ng.jsoneditor',
    'cfp.hotkeys'
  ])
  .config(function (ENV, $compileProvider) {
    $compileProvider.debugInfoEnabled(ENV.name === 'development');
  })
  .config(function(cfpLoadingBarProvider) {
    cfpLoadingBarProvider.includeSpinner = false;
  })
  .config(function ($stateProvider, $urlRouterProvider) {
    // Route to the login controller when not recognized.
    $urlRouterProvider.otherwise('/');
  })
  .config(function ($httpProvider) {
    $httpProvider.interceptors.push('logHeadersInterceptor');
    $httpProvider.interceptors.push('authorizationInterceptor');
    $httpProvider.interceptors.push('flashMessageInterceptor');
    $httpProvider.interceptors.push('exceptionInterceptor');
    $httpProvider.defaults.withCredentials = true;
  })
  .constant(env_config) // set custom defaults for constants
  .config(function(pikadayConfigProvider) {
    let locales = {
      en_BE: {
        previousMonth: 'Previous Month',
        nextMonth: 'Next Month',
        months: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
        weekdays: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
        weekdaysShort: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"]
      },
      nl_BE: {
        previousMonth: 'Vorige maand',
        nextMonth: 'Volgende maand',
        months: ["Januari", "Februari", "Maart", "April", "Mei", "Juni", "Juli", "Augustus", "September", "Oktober", "November", "December"],
        weekdays: ["Zondag", "Maandag", "Dinsdag", "Woensdag", "Donderdag", "Vrijdag", "Zaterdag"],
        weekdaysShort: ["Zo", "Ma", "Di", "Wo", "Do", "Vr", "Za"]
      },
      fr_BE: {
        previousMonth: 'Mois précédent',
        nextMonth: 'Mois prochain',
        months: ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"],
        weekdays: ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"],
        weekdaysShort: ["Dim", "Lun", "Mar", "Mer", "Jeu", "Ven", "Sam"]
      },
      de_DE: {
        previousMonth: 'Vorheriger Monat',
        nextMonth: 'Nächster Monat',
        months: ["Januar", "Februar", "März", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember"],
        weekdays: ["Sonntag", "Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag"],
        weekdaysShort: ["So.", "Mo.", "Di.", "Mi.", "Do.", "Fr.", "Sa."]
      }
    };

    pikadayConfigProvider.setConfig({
      locales: locales // required if setting the language using the i18n attribute
    });
  })
  .run(function ($rootScope, ENV, $log, navigateAwayWarning) {
    const environment = ENV.name;

    /* istanbul ignore next  */
    if (environment === 'development') {
      $rootScope.$on('$stateChangeError', function (event, toState, toParams, fromState, fromParams, error) { //eslint-disable-line angular/on-watch
        $log.log('ui.router: $stateChangeError:');
        $log.log(error);
        $log.log('state info:');
        $log.log(event);
        $log.log(toState);
        $log.log(toParams);
        $log.log(fromState);
        $log.log(fromParams);
      });
    }

    /*
      Only do the 'navigate way' warning in production, since it is
      very annoying during development, because then every time the
      'livereload' kicks in the alert pops up.
    */
    if (environment === 'production') {
      navigateAwayWarning.enable();
    }
  });
