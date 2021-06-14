'use strict';

(function() {
  class SuggestionsObserver {

    constructor() {
      this.suggestionsChangedCallbacks = [];
      this.suggestions = {};
    }

    /**
     * Overwrite the currently set suggestions with the given object.
     * @param suggestions Object with field keys as keys and arrays of suggestions as values
     */
    setSuggestions(suggestions) {
      this.suggestions = suggestions;
      _.forEach(this.suggestionsChangedCallbacks, function(callback) {
        callback();
      });
    }

    /**
     * Return all the set suggestions for one specific field key
     * @param fieldKey key to set suggestions for
     * @returns {Array} possibly empty array of field messages
     */
    getSuggestionsForKey(fieldKey) {
      return _.get(this.suggestions, fieldKey);
    }

    /**
     * Register a callback that is invoked when the suggestions have changed.
     * @param callback function
     */
    registerSuggestionsChangedCallback(callback) {
      this.suggestionsChangedCallbacks.push(callback);
    }
  }

  angular.module('digitalWorkplaceApp').service('SuggestionsObserver', function () {
    return SuggestionsObserver;
  });
}());
