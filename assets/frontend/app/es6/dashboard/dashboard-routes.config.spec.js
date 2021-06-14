'use strict';

describe('Routes: dashboard routes', function() {

  // load the routes module
  beforeEach(module('digitalWorkplaceApp'));

  // Basic route testing requirements
  let $state;

  beforeEach(inject(function(_$state_) {
    $state = _$state_;

    mockHelpers.blockUIRouter($state);
  }));

  it('should configure the "dashboard" state correctly.', function() {
    const state = $state.get('dashboard');

    expect(state.parent).toBe('base');

    expect(state.views['main-content@'].template).toBe('<dashboard></dashboard>');

    expect(state.views['plus-menu@'].templateUrl).toBe('es6/sidebar/plus-menu/plus-menu.controller.html');
    expect(state.views['plus-menu@'].controller).toBe('PlusMenuController as plusMenuController');

    expect(state.views['filters@'].templateUrl).toBe('es6/sidebar/filters/filters.controller.html');
    expect(state.views['filters@'].controller).toBe('FiltersController as filtersController');
  });
});
