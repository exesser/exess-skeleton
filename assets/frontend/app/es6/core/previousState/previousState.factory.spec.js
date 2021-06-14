'use strict';

describe('Factory: previousState', function () {

  beforeEach(module('digitalWorkplaceApp'));

  let previousState;
  let $state;
  let $q;
  let $rootScope;
  let flashMessageContainer;

  beforeEach(inject(function (_previousState_, _$state_, _$q_, _$rootScope_, _flashMessageContainer_) {
    previousState = _previousState_;
    $state = _$state_;
    $q = _$q_;
    $rootScope = _$rootScope_;
    flashMessageContainer = _flashMessageContainer_;
  }));

  it('should call flashMessageContainer.clearMessages() every time before navigate', function () {
    spyOn(flashMessageContainer, 'clearMessages');

    expect(flashMessageContainer.clearMessages).not.toHaveBeenCalled();

    previousState.navigateTo();

    expect(flashMessageContainer.clearMessages).toHaveBeenCalledTimes(1);
  });

  it('should set the initial state to the home dashboard', function () {
    spyOn($state, 'go').and.callFake(mockHelpers.resolvedPromise($q));

    previousState.navigateTo();

    expect($state.go).toHaveBeenCalledTimes(1);
    expect($state.go).toHaveBeenCalledWith('dashboard', { mainMenuKey: 'start', dashboardId: 'home' });
  });

  it('should know how to navigate from a dashboard to another dashboard', function () {
    spyOn($state, 'go').and.callFake(mockHelpers.resolvedPromise($q));

    previousState.registerStateChange({ name: 'dashboard', params: { mainMenuKey: 'end', dashboardId: 'garden' } }, 'dashboard');

    previousState.navigateTo();

    expect($state.go).toHaveBeenCalledTimes(1);
    expect($state.go).toHaveBeenCalledWith('dashboard', { mainMenuKey: 'end', dashboardId: 'garden' });
  });

  it('should go to the dashboard when navigating from a focus-mode', function () {
    spyOn($state, 'go').and.callFake(mockHelpers.resolvedPromise($q));

    previousState.registerStateChange({ name: 'focus-mode', params: { mainMenuKey: 'start', focusModeId: '1' } }, 'focus-mode');
    previousState.registerStateChange({ name: 'focus-mode', params: { mainMenuKey: 'middle', focusModeId: '2' } }, 'focus-mode');
    previousState.registerStateChange({ name: 'focus-mode', params: { mainMenuKey: 'end', focusModeId: '3' } }, 'focus-mode');

    previousState.navigateTo();

    expect($state.go).toHaveBeenCalledTimes(1);
    expect($state.go).toHaveBeenCalledWith('dashboard', { mainMenuKey: 'start', dashboardId: 'home' });
  });

  it('should go to the closest focus-mode when navigating from a guidance-mode', function () {
    spyOn($state, 'go').and.callFake(mockHelpers.resolvedPromise($q));

    previousState.registerStateChange({ name: 'focus-mode', params: { mainMenuKey: 'start', focusModeId: '1' } }, 'focus-mode');
    previousState.registerStateChange({ name: 'focus-mode', params: { mainMenuKey: 'middle', focusModeId: '2' } }, 'focus-mode');
    previousState.registerStateChange({ name: 'guidance-mode', params: { mainMenuKey: 'end', flowId: '3' } }, 'guidance-mode');

    previousState.navigateTo();

    expect($state.go).toHaveBeenCalledTimes(1);
    expect($state.go).toHaveBeenCalledWith('focus-mode', { mainMenuKey: 'middle', focusModeId: '2' });
  });

  it('should go to the dashboard from a guidance-mode if no focus-mode is found', function () {
    spyOn($state, 'go').and.callFake(mockHelpers.resolvedPromise($q));

    previousState.registerStateChange({ name: 'guidance-mode', params: { mainMenuKey: 'end', focusModeId: '3' } }, 'guidance-mode');

    previousState.navigateTo();

    expect($state.go).toHaveBeenCalledTimes(1);
    expect($state.go).toHaveBeenCalledWith('dashboard', { mainMenuKey: 'start', dashboardId: 'home' });
  });

  it('should clear the last state if the state transition is successful', function () {
    spyOn($state, 'go').and.callFake(mockHelpers.resolvedPromise($q));

    previousState.registerStateChange({ name: 'focus-mode', params: { mainMenuKey: 'focus', focusModeId: '2' } }, 'focus-mode');
    previousState.registerStateChange({ name: 'guidance-mode', params: { mainMenuKey: 'end', flowId: '3' } }, 'guidance-mode');

    // In the first previousState.navigateTo() call we attempt to go back to the focus-mode.
    previousState.navigateTo();
    $rootScope.$apply();
    expect($state.go).toHaveBeenCalledTimes(1);
    expect($state.go).toHaveBeenCalledWith('focus-mode', { mainMenuKey: 'focus', focusModeId: '2' });

    // Since the previousState.navigateTo() call was successful, the next time it is invoked we should go tho the main dashboard.
    previousState.navigateTo();
    $rootScope.$apply();
    expect($state.go).toHaveBeenCalledTimes(2);
    expect($state.go).toHaveBeenCalledWith('dashboard', { mainMenuKey: 'start', dashboardId: 'home' });
  });

  it('should not clear the last state if the state transition is unsuccessful', function () {
    spyOn($state, 'go').and.callFake(mockHelpers.rejectedPromise($q));

    previousState.registerStateChange({ name: 'focus-mode', params: { mainMenuKey: 'focus', focusModeId: '2' } }, 'focus-mode');
    previousState.registerStateChange({ name: 'guidance-mode', params: { mainMenuKey: 'end', flowId: '3' } }, 'guidance-mode');

    // In the first previousState.navigateTo() call we attempt to go back to the focus-mode.
    previousState.navigateTo();
    $rootScope.$apply();
    expect($state.go).toHaveBeenCalledTimes(1);
    expect($state.go).toHaveBeenCalledWith('focus-mode', { mainMenuKey: 'focus', focusModeId: '2' });

    // Since the previousState.navigateTo() call was unsuccessful, the next time it is invoked we should attempt to go back to the focus-mode again.
    previousState.navigateTo();
    $rootScope.$apply();
    expect($state.go).toHaveBeenCalledTimes(2);
    expect($state.go).toHaveBeenCalledWith('focus-mode', { mainMenuKey: 'focus', focusModeId: '2' });
  });
});

describe('previousState: run block', function () {

  beforeEach(module('digitalWorkplaceApp'));

  var runBlock;

  var $rootScope;
  var previousState;
  var $stateChangeSuccess;

  beforeEach(inject(function (_previousState_) {
    previousState = _previousState_;

    var myModule = angular.module('digitalWorkplaceApp');

    /*
     This is kind of magical, what this does is get the login .angular.run block,
     which resides at a certain index in the array. If you encounter the following
     error:

     TypeError: 'undefined' is not a function (evaluating '$stateChangeStart(event, toState)')

     This mean that the runBlock has changed position because another run block was added, the
     fix then is to find the correct index.
     */

    runBlock = myModule._runBlocks[1];

    // Mock rootScope's $on method.
    $rootScope = { $on: _.noop };

    spyOn($rootScope, '$on').and.callFake((listener, f) => {
      if (listener === '$stateChangeSuccess') {
        $stateChangeSuccess = f;
      }
    });
  }));

  it('should store the previous state', function () {
    runBlock($rootScope, previousState);

    spyOn(previousState, 'registerStateChange');

    $stateChangeSuccess(undefined, { name: 'focus-mode' }, undefined, { name: 'dashboard' }, { query: 'Hi guys' });

    expect(previousState.registerStateChange).toHaveBeenCalledTimes(1);
    expect(previousState.registerStateChange).toHaveBeenCalledWith({ name: 'dashboard', params: { query: 'Hi guys' } }, 'focus-mode');
  });
});
