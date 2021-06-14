'use strict';

describe('Factory: guidanceModeBackendState', function () {
  beforeEach(module('digitalWorkplaceApp'));

  // instantiate service
  let guidanceModeBackendState;
  beforeEach(inject(function (_guidanceModeBackendState_) {
    guidanceModeBackendState = _guidanceModeBackendState_;
  }));

  it('should know if backendIsBusy is true or false and the performed action', function () {
    expect(guidanceModeBackendState.getBackendIsBusy()).toBe(false);
    expect(guidanceModeBackendState.getPerformedAction()).toEqual({});

    guidanceModeBackendState.setBackendIsBusy(true, {event: 'CHANGED'});
    expect(guidanceModeBackendState.getBackendIsBusy()).toBe(true);
    expect(guidanceModeBackendState.getPerformedAction()).toEqual({event: 'CHANGED'});

    guidanceModeBackendState.setBackendIsBusy(false);
    expect(guidanceModeBackendState.getBackendIsBusy()).toBe(false);
    expect(guidanceModeBackendState.getPerformedAction()).toEqual({});

    guidanceModeBackendState.addBackendIsBusyFor(1234);
    expect(guidanceModeBackendState.getBackendIsBusy()).toBe(true);

    guidanceModeBackendState.addBackendIsBusyFor(1235);
    expect(guidanceModeBackendState.getBackendIsBusy()).toBe(true);

    guidanceModeBackendState.removeBackendIsBusyFor(1235);
    guidanceModeBackendState.removeBackendIsBusyFor(121212);
    expect(guidanceModeBackendState.getBackendIsBusy()).toBe(true);

    guidanceModeBackendState.removeBackendIsBusyFor(1234);
    expect(guidanceModeBackendState.getBackendIsBusy()).toBe(false);
  });
});
