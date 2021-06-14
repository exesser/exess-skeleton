'use strict';

describe('Routes: base', function() {

  // load the routes module
  beforeEach(module('digitalWorkplaceApp'));

  // Basic route testing requirements
  let $state;

  beforeEach(inject(function(_$state_) {
    $state = _$state_;
  }));

  it('should configure the "base" state correctly', function() {
    const state = $state.get('base'); // Abstract state so need to get instance with 'get';

    expect(state.abstract).toBe(true);

    expect(state.views['@'].template).toBe('<div ui-view></div>');

    expect(state.views.modal.template).toBe('<guidance-modal></guidance-modal>');
    expect(state.views['mini-guidance'].template).toBe('<mini-guidance-mode></mini-guidance-mode>');
    expect(state.views.logout.template).toBe('<logout location="menu"></logout>');
  });
});
