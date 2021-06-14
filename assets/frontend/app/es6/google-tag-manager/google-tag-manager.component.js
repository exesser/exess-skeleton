'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.google-tag-manager component
 * @description
 * # googleTagManager
 *
 * <google-tag-manager></google-tag-manager>
 *
 * Component of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .component('googleTagManager', {
    controllerAs: 'googleTagManagerController',
    controller: function ($window, GTM_CONTAINER) {
      const googleTagManagerController = this;

      googleTagManagerController.$onInit = function () {
        loadGTM();
      };

      function loadGTM() {
        // Ignore this function for code coverage because we don't want to change the GTM script
        /* istanbul ignore next */
        (function (w, d, s, l, i) {
          w[l] = w[l] || [];
          w[l].push({'gtm.start': new Date().getTime(), event: 'gtm.js'});

          let f = d.getElementsByTagName(s)[0],
            j = d.createElement(s),
            dl = l !== 'dataLayer' ? '&l=' + l : '';
          j.async = true;
          j.src = 'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
          f.parentNode.insertBefore(j, f);
        })($window, document, 'script', 'dataLayer', GTM_CONTAINER);
      }
    }
  });
