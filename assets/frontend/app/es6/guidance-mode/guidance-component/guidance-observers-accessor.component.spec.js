'use strict';

describe('Component: guidanceObserversAccessor', function() {

  // load the directive's module
  beforeEach(module('digitalWorkplaceApp'));

  const template = '<guidance-observers-accessor guidance-form-observer="guidanceFormObserver"></guidance-observers-accessor>';

  let guidanceFormObserver;
  let guidanceObserversAccessorController;

  beforeEach(inject(function($rootScope, $compile, $state, GuidanceFormObserver) {
    mockHelpers.blockUIRouter($state);

    guidanceFormObserver = new GuidanceFormObserver();

    const scope = $rootScope.$new();
    scope.guidanceFormObserver = guidanceFormObserver;

    let element = angular.element(template);
    element = $compile(element)(scope);
    $rootScope.$apply();

    guidanceObserversAccessorController = element.controller("guidanceObserversAccessor");
  }));

  describe('getGuidanceFormObserver', function() {
    it('should return the guidanceFormObserver.', function() {
      expect(guidanceObserversAccessorController.getGuidanceFormObserver()).toBe(guidanceFormObserver);
    });
  });
});
