'use strict';

describe('Factory: plusMenuObserver', function () {

  beforeEach(module('digitalWorkplaceApp'));

  let plusMenuObserver;

  beforeEach(inject(function (_plusMenuObserver_) {
    plusMenuObserver = _plusMenuObserver_;
  }));

  it('should register setPlusMenuData callback.', function () {
    const observer = jasmine.createSpy('observer');
    const menuItems = [{ "test": "test1" }, { "test": "test2" }];

    plusMenuObserver.registerSetPlusMenuDataCallback(observer);
    plusMenuObserver.setPlusMenuData(menuItems); // call with fake parameter.

    expect(observer).toHaveBeenCalledTimes(1);
    expect(observer).toHaveBeenCalledWith(menuItems);
  });
});
