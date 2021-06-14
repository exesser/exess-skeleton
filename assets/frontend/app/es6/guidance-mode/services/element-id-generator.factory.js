'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp:elementIdGenerator factory
 * @description
 * # elementIdGenerator
 *
 * The elementIdGenerator factory generate the and id base
 * on the id that is coming from backend and the repeatable block key
 *
 * Factory of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .factory('elementIdGenerator', function(kebabCaseFilter) {

    return { generateId };

    /**
     * Generate the elementId.
     * @param id
     * @param guidanceFormObserver the GuidanceFormObserver from where we take the repeatableBockKey
     */
    function generateId(id, guidanceFormObserver) {
      var repeatableBockKey = guidanceFormObserver.getRepeatableBlockKey();
      return kebabCaseFilter(`${id}-${repeatableBockKey}-field`);
    }
  });
