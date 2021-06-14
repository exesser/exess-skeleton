'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.filter:orDefault filter
 * @description
 *
 * Filter than returns the given value if it is not empty (when trimmed)
 * and otherwise returns the default value.
 */
angular.module('digitalWorkplaceApp')
  .filter('orDefault', function() {
    return function (value, defaultValue) {
      const trimmed = _.trim(value);
      return _.isEmpty(trimmed) ? defaultValue : value;
    };
  });
