'use strict';

describe('Routes: login', function () {

  beforeEach(module('digitalWorkplaceApp'));

  let runBlock;

  let rootScope;
  let $stateChangeStart;
  let loginFactory;
  let state;
  let event;
  let $state;

  beforeEach(inject(function (_$state_) {
    $state = _$state_;

    const myModule = angular.module('digitalWorkplaceApp');

    /*
     This is kind of magical, what this does is get the login .angular.run block,
     which resides at a certain index in the array. If you encounter the following
     error:

     TypeError: 'undefined' is not a function (evaluating '$stateChangeStart(event, toState)')

     This mean that the runBlock has changed position because another run block was added, the
     fix then is to find the correct index.
     */
    runBlock = myModule._runBlocks[3];

    // Mock rootScope's $on method.
    rootScope = { $on: _.noop };
    state = { transitionTo: _.noop };

    spyOn(rootScope, '$on').and.callFake((listener, f) => {
      expect(listener).toBe('$stateChangeStart');
      $stateChangeStart = f;
    });

    loginFactory = { afterLoginState: null };
    event = { preventDefault: _.noop };
  }));

  it('should transition user to home page if he tries to go to the login page if he is already logged in', function () {
    const tokenFactory = createTokenFactory(true);

    runBlock(rootScope, tokenFactory, undefined, state);

    spyOn(tokenFactory, 'hasToken').and.callThrough();
    spyOn(state, 'transitionTo');

    const toState = { name: 'login' };

    spyOn(event, 'preventDefault');

    $stateChangeStart(event, toState);

    expect(tokenFactory.hasToken).toHaveBeenCalledTimes(1);

    expect(event.preventDefault).toHaveBeenCalledTimes(1);

    expect(state.transitionTo).toHaveBeenCalledTimes(1);
    expect(state.transitionTo).toHaveBeenCalledWith('dashboard', { mainMenuKey: 'start', dashboardId: 'home' });
  });

  it('should transition user to login page if he tries to go a page when he is not logged in', function () {
    const tokenFactory = createTokenFactory(false);

    runBlock(rootScope, tokenFactory, loginFactory, state);

    spyOn(tokenFactory, 'hasToken').and.callThrough();
    spyOn(state, 'transitionTo');

    const toState = { name: 'home-page' };
    const toParams = 'params';

    spyOn(event, 'preventDefault');

    $stateChangeStart(event, toState, toParams);

    expect(tokenFactory.hasToken).toHaveBeenCalledTimes(1);

    expect(event.preventDefault).toHaveBeenCalledTimes(1);

    expect(loginFactory.afterLoginState.name).toBe('home-page');
    expect(loginFactory.afterLoginState.params).toBe('params');

    expect(state.transitionTo).toHaveBeenCalledTimes(1);
    expect(state.transitionTo).toHaveBeenCalledWith('login');
  });

  it('should allow the user to go to the page if he is logged in', function () {
    const tokenFactory = createTokenFactory(true);

    runBlock(rootScope, tokenFactory, loginFactory, state);

    spyOn(tokenFactory, 'hasToken').and.callThrough();
    spyOn(state, 'transitionTo');

    const toState = { name: 'home-page' };
    const toParams = 'params';

    spyOn(event, 'preventDefault');

    $stateChangeStart(event, toState, toParams);

    expect(tokenFactory.hasToken).toHaveBeenCalledTimes(1);

    expect(event.preventDefault).not.toHaveBeenCalled();
    expect(state.transitionTo).not.toHaveBeenCalled();
  });

  it('should configure the "login" state correctly', function () {
    // Retrieve the route
    const state = $state.get('login');

    // Assert the basics of the state.
    expect(state.name).toBe('login');
    expect(state.parent).toBe('base');
    expect(state.url).toBe('/');

    expect(state.views['modal@'].template).toBe('<login></login>');
  });

  function createTokenFactory(isLoggedIn) {
    return {
      hasToken() {
        return isLoggedIn;
      }
    };
  }
});
