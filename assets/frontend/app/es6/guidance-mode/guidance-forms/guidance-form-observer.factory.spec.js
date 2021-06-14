'use strict';

describe('Factory: guidanceFormObserverFactory', function () {

  beforeEach(module('digitalWorkplaceApp'));

  let guidanceFormObserverFactory;
  let GuidanceFormObserver;

  beforeEach(inject(function(_guidanceFormObserverFactory_, _GuidanceFormObserver_) {
    guidanceFormObserverFactory = _guidanceFormObserverFactory_;
    GuidanceFormObserver = _GuidanceFormObserver_;
  }));

  describe('createGuidanceFormObserver', function() {
    it('should create a new GuidanceFormObserver and return it', function() {
      let guidanceFormObserver = guidanceFormObserverFactory.createGuidanceFormObserver();
      expect(_.isEmpty(guidanceFormObserver)).toBe(false);
      expect(GuidanceFormObserver.prototype.isPrototypeOf(guidanceFormObserver)).toBe(true);
    });
  });
});
