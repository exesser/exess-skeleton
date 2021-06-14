'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.filter:kebabCase filter
 * @description Translates an string to the kebabCase.
 */
angular.module('digitalWorkplaceApp')
  .filter('kebabCase', function() {
    return function (string) {
      return _.kebabCase(string);
    };
  });
