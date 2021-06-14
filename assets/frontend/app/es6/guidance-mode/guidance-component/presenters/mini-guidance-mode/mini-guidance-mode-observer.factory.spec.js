'use strict';

describe('Factory: miniGuidanceModeObserver', function () {

  beforeEach(module('digitalWorkplaceApp'));

  let miniGuidanceModeObserver;
  let $q;

  beforeEach(inject(function (_miniGuidanceModeObserver_, _$q_) {
    miniGuidanceModeObserver = _miniGuidanceModeObserver_;
    $q = _$q_;
  }));

  it('should register openGridModalCallback.', function () {
    const deferred = $q.defer();
    const observer = jasmine.createSpy("observer").and.returnValue(deferred.promise);

    miniGuidanceModeObserver.registerOpenMiniGuidanceCallback(observer);

    //Call with fake parameter.
    const promise = miniGuidanceModeObserver.openMiniGuidance({ columns: {} });

    expect(observer).toHaveBeenCalledTimes(1);
    expect(observer).toHaveBeenCalledWith({ columns: {} });
    expect(promise).toBe(deferred.promise);
  });

});
