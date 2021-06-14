'use strict';

describe('Routes: focus-mode routes', function() {

  // load the routes module
  beforeEach(module('digitalWorkplaceApp'));

  // Basic route testing requirements
  let $state;
  let state;

  beforeEach(inject(function(_$state_) {
    $state = _$state_;

    state = $state.get('focus-mode');

    mockHelpers.blockUIRouter($state);
  }));

  it('should configure the "focus-mode" state correctly.', function() {
    expect(state.parent).toBe('base');

    expect(state.views['focus-mode@'].template).toBe('<focus-mode></focus-mode>');

    expect(state.views['plus-menu@'].templateUrl).toBe('es6/sidebar/plus-menu/plus-menu.controller.html');
    expect(state.views['plus-menu@'].controller).toBe('PlusMenuController as plusMenuController');

    expect(state.views['filters@'].templateUrl).toBe('es6/sidebar/filters/filters.controller.html');
    expect(state.views['filters@'].controller).toBe('FiltersController as filtersController');
  });
});
