'use strict';

describe('Factory: filtersObserver', function () {

  beforeEach(module('digitalWorkplaceApp'));

  let filtersObserver;

  beforeEach(inject(function (_filtersObserver_) {
    filtersObserver = _filtersObserver_;
  }));

  it('should register filtersHaveChanged callbacks.', function () {
    const observer = jasmine.createSpy('observer');

    filtersObserver.registerFiltersHaveChangedCallback('listKey', observer);
    filtersObserver.filtersHaveChanged('listKey', { first_name: "Ken" }); // call with fake parameter.

    expect(observer).toHaveBeenCalledTimes(1);
    expect(observer).toHaveBeenCalledWith({ first_name: "Ken" });
  });

  it('should register setFilterData callbacks.', function () {
    const observer = jasmine.createSpy('observer');

    filtersObserver.registerSetFilterDataCallback(observer);
    filtersObserver.setFilterData('filterKey', 'listKey'); // call with fake parameter.

    expect(observer).toHaveBeenCalledTimes(1);
    expect(observer).toHaveBeenCalledWith('filterKey', 'listKey');
  });
});
