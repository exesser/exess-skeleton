'use strict';

describe('Factory: guidanceModalObserver', function () {

  beforeEach(module('digitalWorkplaceApp'));

  let guidanceModalObserver;
  let $q;
  let CONFIRM_ACTION;

  beforeEach(inject(function (_guidanceModalObserver_, _$q_, $httpBackend, _CONFIRM_ACTION_) {
    guidanceModalObserver = _guidanceModalObserver_;
    $q = _$q_;
    CONFIRM_ACTION = _CONFIRM_ACTION_;
  }));

  it('should register openGridModalCallback.', function () {
    const deferred = $q.defer();
    const observer = jasmine.createSpy().and.returnValue(deferred.promise);

    guidanceModalObserver.registerOpenModalCallback(observer);

    //Call with fake parameter.
    const promise = guidanceModalObserver.openModal({ columns: {} }, CONFIRM_ACTION.CONFIRM_MODAL);

    expect(observer).toHaveBeenCalledTimes(1);
    expect(observer).toHaveBeenCalledWith({ columns: {} }, CONFIRM_ACTION.CONFIRM_MODAL);
    expect(promise).toBe(deferred.promise);
  });

  it('should register resetModalCallback.', function () {
    const observer = jasmine.createSpy('observer');
    guidanceModalObserver.registerResetModalCallback(observer);
    guidanceModalObserver.resetModal();

    expect(observer).toHaveBeenCalledTimes(1);
  });
});
