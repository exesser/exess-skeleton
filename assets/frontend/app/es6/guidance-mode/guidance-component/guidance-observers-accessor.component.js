"use strict";

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.flow:guidanceObserversAccessor component
 * @description
 * # guidanceObserversAccessor
 *
 * GuidanceObserversAccessor is a component from which the observer instances belonging to that guidance can be retrieved.
 * This is set by the guidance mode and can retrieved by the form elements, grid wrappers, or other elements rendered inside that guidance.
 * This component can be obtained by requiring it in the other components.
 *
 * Component of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .component('guidanceObserversAccessor', {
    bindings: {
      guidanceFormObserver: '<',
      validationObserver: '<',
      suggestionsObserver: '<'
    },
    controllerAs: 'guidanceFormObserverAccessorController',
    controller: function() {
      const guidanceFormObserverAccessorController = this;

      /**
       * Retrieve the guidanceFormObserver that is set in this component.
       * @returns {Object} guidanceFormObserver
       */
      guidanceFormObserverAccessorController.getGuidanceFormObserver = function() {
        return guidanceFormObserverAccessorController.guidanceFormObserver;
      };

      /**
       * Retrieve the validationObserver that is set in this component.
       * @returns {Object} validationObserver
       */
      guidanceFormObserverAccessorController.getValidationObserver = function() {
        return guidanceFormObserverAccessorController.validationObserver;
      };

      /**
       * Retrieve the suggestionsObserver that is set in this component.
       * @returns {Object} suggestionsObserver
       */
      guidanceFormObserverAccessorController.getSuggestionsObserver = function() {
        return guidanceFormObserverAccessorController.suggestionsObserver;
      };
    }
  });
