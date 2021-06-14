'use strict';

/**
 * @ngdoc service
 * @name digitalWorkplaceApp.googleTagManagerFactory
 * @description Factory which handles data that needs to be send to google tag manager.
 *
 * Factory in the digitalWorkplaceApp.
 */
angular.module('digitalWorkplaceApp')
  .factory('googleTagManager', function ($window) {

    return { push };

    function push (data) {
      if (!_.isEmpty($window.dataLayer)) {
        $window.dataLayer.push(data);
      }
    }
  });
