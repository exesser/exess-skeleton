'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.filter:kebabCase filter
 * @description Translates an string to the kebabCase.
 */
angular.module('digitalWorkplaceApp')
  .filter('formatNumber', function () {
    return function (number, digits = 2) {

      let realNumber = _.toNumber(number);

      if (_.isNaN(realNumber)) {
        return number;
      }

      // Number should be explicitly checked because it could actually be a string
      if (realNumber === 0 && realNumber != number) {
        return number;
      }

      return realNumber.toFixed(digits);
    };
  });
