'use strict';

describe('Factory: guidanceGuardian', function () {

  beforeEach(module('digitalWorkplaceApp'));

  let guidanceGuardian;

  beforeEach(inject(function (_guidanceGuardian_) {
    guidanceGuardian = _guidanceGuardian_;
  }));

  it('should not guard by default', function () {
    expect(guidanceGuardian.isGuarding()).toBe(false);
  });

  it('should know how to start and stop guarding', function () {
    const guidance1 = { id: 1 };
    const guidance2 = { id: 2 };

    // Lets add two guidances to guard
    guidanceGuardian.startGuard(guidance1);
    guidanceGuardian.startGuard(guidance2);

    // It should now be guarding two guidances.
    expect(guidanceGuardian.isGuarding()).toBe(true);

    // Lets stop guarding guidance1
    guidanceGuardian.endGuard(guidance1);

    // It should still guard guidance2
    expect(guidanceGuardian.isGuarding()).toBe(true);

    // Now lets stop guarding guidance2
    guidanceGuardian.endGuard(guidance2);

    // Now it should not be guarding anything.
    expect(guidanceGuardian.isGuarding()).toBe(false);
  });

  it('should not add the same guidance twice', function () {
    const guidance = { id: 1 };

    // Lets add two guidances to guard
    guidanceGuardian.startGuard(guidance);
    guidanceGuardian.startGuard(guidance);

    // It should now be guarding just one guidance.
    expect(guidanceGuardian.isGuarding()).toBe(true);

    // Lets stop guarding the guidance
    guidanceGuardian.endGuard(guidance);

    // Now it should not be guarding anything.
    expect(guidanceGuardian.isGuarding()).toBe(false);
  });

  it('should know how to reset the guardian', function () {
    const guidance1 = { id: 1 };
    const guidance2 = { id: 2 };

    // Lets add two guidances to guard
    guidanceGuardian.startGuard(guidance1);
    guidanceGuardian.startGuard(guidance2);

    // It should now be guarding two guidances.
    expect(guidanceGuardian.isGuarding()).toBe(true);

    // Now reset the guardian so it doesn't guard anything anymore.
    guidanceGuardian.resetGuardian();

    // It should not guard anything after a reset
    expect(guidanceGuardian.isGuarding()).toBe(false);
  });
});

describe('guidanceGuardian: run block', function () {

  beforeEach(module('digitalWorkplaceApp'));

  let runBlock;

  let $rootScope;
  let guidanceGuardian;
  let $stateChangeStart;
  let $window;
  let $translate;
  let actionDatasource;

  let fakeEvent;

  beforeEach(inject(function (_guidanceGuardian_, _$window_, _$translate_, _actionDatasource_) {
    guidanceGuardian = _guidanceGuardian_;
    $window = _$window_;
    $translate = _$translate_;
    actionDatasource = _actionDatasource_;

    const myModule = angular.module('digitalWorkplaceApp');

    /*
     This is kind of magical, what this does is get the login .angular.run block,
     which resides at a certain index in the array. If you encounter the following
     error:

     TypeError: 'undefined' is not a function (evaluating '$stateChangeStart(event, toState)')

     This mean that the runBlock has changed position because another run block was added, the
     fix then is to find the correct index.
     */

    runBlock = myModule._runBlocks[2];

    // Mock rootScope's $on method.
    $rootScope = { $on: _.noop };

    spyOn($rootScope, '$on').and.callFake((listener, f) => {
      if (listener === '$stateChangeStart') {
        $stateChangeStart = f;
      }
    });

    fakeEvent = { preventDefault: _.noop };
    spyOn(fakeEvent, 'preventDefault');
  }));

  it('should allow state change when not guarding', function () {
    spyOn(guidanceGuardian, 'isGuarding').and.returnValue(false);

    runBlock($rootScope, guidanceGuardian);

    $stateChangeStart(fakeEvent);

    expect(guidanceGuardian.isGuarding).toHaveBeenCalledTimes(1);
    expect(fakeEvent.preventDefault).not.toHaveBeenCalled();
  });

  describe('when the guard is active', function () {
    const fakeLeavingMessage = 'Are you sure...';

    beforeEach(function () {
      spyOn($translate, 'instant').and.returnValue(fakeLeavingMessage);
      spyOn(guidanceGuardian, 'isGuarding').and.returnValue(true);

      spyOn(guidanceGuardian, 'resetGuardian');
      spyOn(actionDatasource, 'performAndHandle');
    });

    afterEach(function () {
      expect(guidanceGuardian.isGuarding).toHaveBeenCalledTimes(1);

      expect($translate.instant).toHaveBeenCalledTimes(1);
      expect($translate.instant).toHaveBeenCalledWith('NAVIGATE_AWAY_WARNING_DATA_LOSS');

      expect($window.confirm).toHaveBeenCalledTimes(1);
      expect($window.confirm).toHaveBeenCalledWith(fakeLeavingMessage);
    });

    it('should block the state change when guard is active and user wants to stay', function () {
      spyOn($window, 'confirm').and.returnValue(false);

      runBlock($rootScope, guidanceGuardian, $translate, $window);

      $stateChangeStart(fakeEvent);

      expect(fakeEvent.preventDefault).toHaveBeenCalledTimes(1);
      expect(guidanceGuardian.resetGuardian).not.toHaveBeenCalled();
    });

    it('should not block the state change when guard is active and user wants to leave', function () {
      spyOn($window, 'confirm').and.returnValue(true);

      runBlock($rootScope, guidanceGuardian, $translate, $window, actionDatasource);

      $stateChangeStart(fakeEvent);

      expect(fakeEvent.preventDefault).not.toHaveBeenCalled();
      expect(guidanceGuardian.resetGuardian).toHaveBeenCalledTimes(1);

      expect(actionDatasource.performAndHandle).toHaveBeenCalledTimes(1);
      expect(actionDatasource.performAndHandle).toHaveBeenCalledWith({id: 'remove_recovery_guidance_data'});
    });
  });
});
