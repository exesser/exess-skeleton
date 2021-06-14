'use strict';

describe('Factory: listStatus', function () {
  const listKey = 'accounts';
  const sortBy = '123-456-789';
  const filters = {"company": {"default": {"value": "%WKY"}}};

  afterEach(function() {
    sessionStorage.clear();
  });

  describe('when sessionStorage is empty', function() {
    beforeEach(module('digitalWorkplaceApp'));

    let listStatus;

    beforeEach(inject(function (_listStatus_) {
      listStatus = _listStatus_;
    }));

    it('should return undefined if we don\'t set data', function() {
      expect(listStatus.getSort(listKey)).toBeUndefined();
      expect(listStatus.getFilters(listKey)).toBeUndefined();
      expect(listStatus.getPage(listKey)).toEqual(1);
      expect(listStatus.getQuickSearch(listKey)).toBeUndefined();

      listStatus.setSort(listKey, sortBy);
      listStatus.setFilters(listKey, filters);
      listStatus.setPage(listKey, 22);
      listStatus.setQuickSearch(listKey, 'Ken');

      expect(listStatus.getSort(listKey)).toBe(sortBy);
      expect(listStatus.getFilters(listKey)).toEqual(filters);
      expect(listStatus.getPage(listKey)).toEqual(22);
      expect(listStatus.getQuickSearch(listKey)).toEqual('Ken');
    });
  });

  describe('when sessionStorage has values', function() {
    beforeEach(module('digitalWorkplaceApp', function() {
      sessionStorage.setItem("LIST_SORT_KEY", angular.toJson({"accounts": sortBy}));
      sessionStorage.setItem("LIST_FILTER_KEY", angular.toJson({"accounts": filters}));
      sessionStorage.setItem("LIST_PAGE_KEY", angular.toJson({"accounts": 23}));
      sessionStorage.setItem("LIST_QUICK_SEARCH_KEY", angular.toJson({"accounts": 'Block'}));
    }));

    let listStatus;

    beforeEach(inject(function (_listStatus_) {
      listStatus = _listStatus_;
    }));

    it('should return the values from sessionStorage', function() {
      expect(listStatus.getSort(listKey)).toBe(sortBy);
      expect(listStatus.getFilters(listKey)).toEqual(filters);
      expect(listStatus.getPage(listKey)).toEqual(23);
      expect(listStatus.getQuickSearch(listKey)).toEqual('Block');
    });
  });
});
