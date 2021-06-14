'use strict';

describe('app: run block', function () {

  beforeEach(module('digitalWorkplaceApp'));

  let runBlock;
  let navigateAwayWarning;

  beforeEach(inject(function (_navigateAwayWarning_) {
    navigateAwayWarning = _navigateAwayWarning_;

    const myModule = angular.module('digitalWorkplaceApp');

    /*
     This is kind of magical, what this does is get the login .angular.run block,
     which resides at a certain index in the array. If you encounter the following
     error:

     TypeError: 'undefined' is not a function (evaluating '$stateChangeStart(event, toState)')

     This mean that the runBlock has changed position because another run block was added, the
     fix then is to find the correct index.
     */
    runBlock = myModule._runBlocks[0];
  }));

  it('should enable the navigate away warning', function () {
    spyOn(navigateAwayWarning, 'enable');

    runBlock(undefined, { name: 'production' }, undefined, navigateAwayWarning);

    expect(navigateAwayWarning.enable).toHaveBeenCalledTimes(1);
  });
});
