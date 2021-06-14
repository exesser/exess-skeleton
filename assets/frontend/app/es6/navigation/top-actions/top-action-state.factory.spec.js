'use strict';

describe('Factory: topActionState', function () {
  beforeEach(module('digitalWorkplaceApp'));

  // instantiate service
  let topActionState;
  beforeEach(inject(function (_topActionState_) {
    topActionState = _topActionState_;
  }));

  it('should know if filtersCanBeOpened is true or false', function () {
    expect(topActionState.filtersCanBeOpened()).toBe(false);

    topActionState.setFiltersCanBeOpened(true);
    expect(topActionState.filtersCanBeOpened()).toBe(true);

    topActionState.setFiltersCanBeOpened(false);
    expect(topActionState.filtersCanBeOpened()).toBe(false);
  });

  it('should know if plusMenuCanBeOpened is true or false', function () {
    expect(topActionState.plusMenuCanBeOpened()).toBe(false);

    topActionState.setPlusMenuCanBeOpened(true);
    expect(topActionState.plusMenuCanBeOpened()).toBe(true);

    topActionState.setPlusMenuCanBeOpened(false);
    expect(topActionState.plusMenuCanBeOpened()).toBe(false);
  });

  it('should know if miniGuidanceCanBeOpened is true or false', function () {
    expect(topActionState.miniGuidanceCanBeOpened()).toBe(false);

    topActionState.setMiniGuidanceCanBeOpened(true);
    expect(topActionState.miniGuidanceCanBeOpened()).toBe(true);

    topActionState.setMiniGuidanceCanBeOpened(false);
    expect(topActionState.miniGuidanceCanBeOpened()).toBe(false);
  });

  it('should be able to get, set and reset the primary button data', function () {
    expect(topActionState.getPrimaryButtonData()).toBe(null);

    topActionState.setPrimaryButtonData({ "fakeObject": 42 });
    expect(topActionState.getPrimaryButtonData()).toEqual({ "fakeObject": 42 });

    topActionState.resetPrimaryButtonData();
    expect(topActionState.getPrimaryButtonData()).toBe(null);
  });
});
