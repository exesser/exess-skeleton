'use strict';

describe('Factory: suggestionsObserverFactory', function () {

  beforeEach(module('digitalWorkplaceApp'));

  let suggestionsObserverFactory;
  let SuggestionsObserver;

  beforeEach(inject(function(_suggestionsObserverFactory_, _SuggestionsObserver_) {
    suggestionsObserverFactory = _suggestionsObserverFactory_;
    SuggestionsObserver = _SuggestionsObserver_;
  }));

  describe('createSuggestionsObserver', function() {
    it('should create a new SuggestionsObserver and return it', function() {
      let suggestionsObserver = suggestionsObserverFactory.createSuggestionsObserver();
      expect(_.isEmpty(suggestionsObserver)).toBe(false);
      expect(suggestionsObserver instanceof SuggestionsObserver).toBe(true);
    });
  });
});
