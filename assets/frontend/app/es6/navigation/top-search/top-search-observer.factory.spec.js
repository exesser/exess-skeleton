'use strict';

describe('Factory: topSearchObserver', function () {

  beforeEach(module('digitalWorkplaceApp'));

  let topSearchObserver;

  beforeEach(inject(function (_topSearchObserver_) {
    topSearchObserver = _topSearchObserver_;
  }));

  it('should register setTopSearchData callback.', function () {
    const observer = jasmine.createSpy('observer');
    const searchData = [{ "test": "test1" }, { "test": "test2" }];

    topSearchObserver.registerSetTopSearchDataCallback(observer);
    topSearchObserver.setTopSearchData(searchData); // call with fake parameter.

    expect(observer).toHaveBeenCalledTimes(1);
    expect(observer).toHaveBeenCalledWith(searchData);
  });
});
