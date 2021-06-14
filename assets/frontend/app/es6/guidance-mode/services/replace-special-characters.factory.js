'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp:replaceSpecialCharacters factory
 * @description
 * # replaceSpecialCharacters
 *
 * The replaceSpecialCharacters factory is used to replace custom CRM expressions
 * that are not supported in Angular
 *
 * Factory of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .factory('replaceSpecialCharacters', function () {

    return { replaceArraySign };

    /**
     * Replace the "[]" with "--theArray".
     * @param object
     * @param toDwpFormat
     */
    function replaceArraySign(object, toDwpFormat = true) {
      const json = angular.toJson(object);
      let formatted;

      if (toDwpFormat) {
        formatted = _.replace(json, /\[]\|/g, "--theArray\|");
      } else {
        formatted = _.replace(json, /--theArray\|/g, "[]|");
      }

      return angular.fromJson(formatted);
    }
  });
