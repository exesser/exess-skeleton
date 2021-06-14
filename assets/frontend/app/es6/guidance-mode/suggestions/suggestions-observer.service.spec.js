'use strict';

describe('Service: suggestionsObserver', function () {

  beforeEach(module('digitalWorkplaceApp'));

  let suggestionsObserver;
  let firstNameSuggestion;

  beforeEach(inject(function(SuggestionsObserver) {
    suggestionsObserver = new SuggestionsObserver();
    firstNameSuggestion = { "label": "John", "model": { "first_name": "John"}};
  }));

  describe('setSuggestions', function() {
    it('should signal observers that suggestions have changed and be able to return it via getSuggestionsForKey.', function() {
      expect(suggestionsObserver.getSuggestionsForKey("first-name")).toEqual(undefined);

      //Callbacks are invoked serially when calling setSuggestions
      suggestionsObserver.registerSuggestionsChangedCallback(function() {
        expect(suggestionsObserver.getSuggestionsForKey("first-name")).toEqual(firstNameSuggestion);
      });
      suggestionsObserver.registerSuggestionsChangedCallback(function() {
        expect(suggestionsObserver.getSuggestionsForKey("first-name")).toEqual(firstNameSuggestion);
      });

      suggestionsObserver.setSuggestions({ "first-name": firstNameSuggestion });
    });

    it('should completely override previous suggestions', function() {
      //First name error is set
      expect(suggestionsObserver.getSuggestionsForKey("first-name")).toEqual(undefined);
      suggestionsObserver.setSuggestions({ "first-name": firstNameSuggestion });
      expect(suggestionsObserver.getSuggestionsForKey("first-name")).toEqual(firstNameSuggestion);
      expect(suggestionsObserver.getSuggestionsForKey("last-name")).toEqual(undefined);

      //New invocation sets last name error, first name error is now gone
      suggestionsObserver.setSuggestions({ "last-name": firstNameSuggestion });
      expect(suggestionsObserver.getSuggestionsForKey("first-name")).toEqual(undefined);
      expect(suggestionsObserver.getSuggestionsForKey("last-name")).toEqual(firstNameSuggestion);
    });
  });
});
