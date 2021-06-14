'use strict';

describe('Routes: guidance mode routes', function () {

  // load the routes module
  beforeEach(module('digitalWorkplaceApp'));

  // Basic route testing requirements
  let $state;
  let $injector;

  let primaryButtonObserver;

  beforeEach(inject(function (_$state_, _$injector_, _primaryButtonObserver_) {
    $state = _$state_;
    $injector = _$injector_;

    primaryButtonObserver = _primaryButtonObserver_;

    mockHelpers.blockUIRouter($state);
  }));

  it('should configure the "kitchen-sink" state correctly.', function () {
    const state = $state.get('kitchen-sink');
    expect(state.parent).toBe('base');
    expect(state.views['focus-mode@'].template).toBe('<kitchen-sink></kitchen-sink>');
  });

  it('should configure the "guidance-mode" state correctly.', function () {
    const state = $state.get('guidance-mode');
    expect(state.parent).toBe('base');

    expect(state.views['focus-mode@'].template).toBe('<large-guidance-mode></large-guidance-mode>');
  });

  describe('the onExit functions', function () {
    it('should reset the primary button data when exiting the guidance-mode state', function () {
      primaryButtonResetTest('guidance-mode');
    });

    it('should reset the primary button data when exiting the guidance-mode state', function () {
      primaryButtonResetTest('kitchen-sink');
    });

    function primaryButtonResetTest(stateName) {
      spyOn(primaryButtonObserver, 'resetPrimaryButtonData');

      const state = $state.get(stateName);

      expect(primaryButtonObserver.resetPrimaryButtonData).not.toHaveBeenCalled();

      const locals = { primaryButtonObserver };
      $injector.invoke(state.onExit, undefined, locals);

      expect(primaryButtonObserver.resetPrimaryButtonData).toHaveBeenCalledTimes(1);
    }
  });
});
