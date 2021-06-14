"use strict";

/**
 * @ngdoc function
 * @name digitalWorkplaceApp:suggestionsObserverFactory factory
 * @description
 * # suggestionsObserverFactory
 *
 * ## Responsibility
 *
 * The suggestionsObserver is responsible for informing form elements
 * that there are new suggestions available. There is only one event on
 * the suggestionsObserver, the 'suggestionsChanged' event.
 * This indicates there are new suggestions available,
 * but does not return anything to the specific listeners.
 *
 * After the guidance performs a validation/suggestions request and
 * the results are in, the suggestions are sent to the suggestionsObserver
 * by calling 'setSuggestions' on it.
 *
 * The suggestionsObserver then informs the subscribers of the 'suggestionsChanged'
 * event that new suggestions are in, so they can request their specific
 * suggestions using the 'getSuggestionsForKey' function.
 *
 * ## Lifespan and cardinality
 *
 * The lifespan of the suggestionsObserver is bound to that one specific
 * form. For a guidance this means that the suggestionsObserver is discarded
 * after a step change has occurred. In the filters a single suggestionsObserver
 * is created as we don't have any step changes there.
 */
angular.module('digitalWorkplaceApp')
  .factory('suggestionsObserverFactory', function(SuggestionsObserver) {

    return { createSuggestionsObserver };

    /**
     * Creates a new suggestions observer.
     * @returns {SuggestionsObserver} instance of a SuggestionsObserver
     */
    function createSuggestionsObserver() {
      return new SuggestionsObserver();
    }
  });
