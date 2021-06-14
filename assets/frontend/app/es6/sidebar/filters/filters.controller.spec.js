'use strict';

describe('Controller: FiltersController', function () {

  // load the controller's module
  beforeEach(module('digitalWorkplaceApp'));

  let $controller;
  let $state;
  let $rootScope;
  let $scope;
  let $q;

  let filtersObserver;
  let sidebarObserver;
  let topActionState;
  let filterDatasource;
  let guidanceFormObserverFactory;
  let validationObserverFactory;
  let suggestionsObserverFactory;
  let listStatus;
  let DEBOUNCE_TIME;
  let formlyFieldsTranslator;

  let filtersObserverRegisterSetFilterDataCallback;

  let filtersController;
  let spyOnListStatusGetFilters;

  // Keeps track of lodashes original debounce function
  let lodashDebounce;

  // Keeps track of all the functions that went through the debounce.
  let debouncedFunctions;

  beforeEach(inject(function (_$controller_, _$state_, _$rootScope_, _$q_,
                              _filtersObserver_, _sidebarObserver_, _listStatus_,
                              _topActionState_, _filterDatasource_, _guidanceFormObserverFactory_,
                              _validationObserverFactory_, _suggestionsObserverFactory_, _DEBOUNCE_TIME_,
                              _formlyFieldsTranslator_) {
    $controller = _$controller_;
    $state = _$state_;
    $q = _$q_;
    $rootScope = _$rootScope_;
    filtersObserver = _filtersObserver_;
    sidebarObserver = _sidebarObserver_;
    listStatus = _listStatus_;
    topActionState = _topActionState_;
    filterDatasource = _filterDatasource_;
    guidanceFormObserverFactory = _guidanceFormObserverFactory_;
    validationObserverFactory = _validationObserverFactory_;
    suggestionsObserverFactory = _suggestionsObserverFactory_;
    formlyFieldsTranslator = _formlyFieldsTranslator_;
    DEBOUNCE_TIME = _DEBOUNCE_TIME_;

    mockHelpers.blockUIRouter($state);
    spyOn(filtersObserver, 'registerSetFilterDataCallback');

    spyOn(guidanceFormObserverFactory, 'createGuidanceFormObserver');
    spyOn(validationObserverFactory, 'createValidationObserver');
    spyOn(suggestionsObserverFactory, 'createSuggestionsObserver');
    spyOn(topActionState, 'setFiltersCanBeOpened');
    spyOn(listStatus, 'setFilters');
    spyOn(formlyFieldsTranslator, 'translate');

    const fakeFilters = {
      "model": { "company": 'WKY' },
      "fieldGroups": [{
        "fields": [{
          "id": "company_name_c.default.value",
          "label": "Company name",
          "type": "varchar",
          "operator": "="
        }],
        "name": "Lead - main data",
        "sort": "10"
      }]
    };

    spyOn(filterDatasource, 'get').and.callFake(mockHelpers.resolvedPromise($q, fakeFilters));

    spyOnListStatusGetFilters = spyOn(listStatus, 'getFilters').and.returnValue(undefined);

    $scope = $rootScope.$new();
    filtersController = $controller('FiltersController', { $scope });
    $scope.filtersController = filtersController; // Set controller on scope manually to mimic controllerAs behavior.

    $rootScope.$apply();
    filtersObserverRegisterSetFilterDataCallback = filtersObserver.registerSetFilterDataCallback.calls.argsFor(0)[0];

    // Mock lodash debounce so we can test the key up events more easily.
    lodashDebounce = _.debounce;

    // Clear the debouncedFunctions
    debouncedFunctions = [];

    /*
     Mock the debounce so it immediately executes the function.
     Remember we are not testing 'lodash' it has plenty of tests itself.
     */
    _.debounce = function (fn, time) {
      debouncedFunctions.push(fn.name);

      expect(time).toBe(DEBOUNCE_TIME);
      return fn;
    };
  }));

  // Reset the debounce to its original lodash function.
  afterEach(function () {
    _.debounce = lodashDebounce;
  });

  it('should correctly configure the controller', function () {
    expect(filtersController.model).toEqual({});
    expect(filtersController.originalModel).toEqual({});
    expect(filtersController.fieldGroups).toEqual([]);
    expect(topActionState.setFiltersCanBeOpened).toHaveBeenCalledTimes(1);
    expect(topActionState.setFiltersCanBeOpened).toHaveBeenCalledWith(false);
  });

  describe('filtersObserver.registerSetFilterDataCallback', function () {
    it('should request filter if topActionState.filtersCanBeOpened return false', function () {
      spyOn(topActionState, 'filtersCanBeOpened').and.returnValue(false);
      filtersObserverRegisterSetFilterDataCallback('filterKey', 'listKey');

      $rootScope.$apply();

      expect(guidanceFormObserverFactory.createGuidanceFormObserver).toHaveBeenCalledTimes(1);
      expect(validationObserverFactory.createValidationObserver).toHaveBeenCalledTimes(1);
      expect(suggestionsObserverFactory.createSuggestionsObserver).toHaveBeenCalledTimes(1);

      expect(filterDatasource.get).toHaveBeenCalledTimes(1);
      expect(filterDatasource.get).toHaveBeenCalledWith({ filterKey: 'filterKey', listKey: 'listKey' });

      expect(formlyFieldsTranslator.translate).toHaveBeenCalledTimes(1);
      expect(formlyFieldsTranslator.translate).toHaveBeenCalledWith([{
        "id": "company_name_c.default.value",
        "label": "Company name",
        "type": "varchar",
        "operator": "="
      }]);

      expect(topActionState.setFiltersCanBeOpened).toHaveBeenCalledTimes(2);
      expect(topActionState.setFiltersCanBeOpened).toHaveBeenCalledWith(true);

      expect(filtersController.model).toEqual({ 'company': 'WKY' });
      expect(filtersController.originalModel).toEqual({ 'company': 'WKY' });

      spyOn(filtersObserver, 'filtersHaveChanged');

      filtersController.model.company = '42';
      $rootScope.$apply();

      filtersController.model.company = 'Exesser';
      $rootScope.$apply();

      // Repeat Exesser twice it should be ignored.
      filtersController.model.company = 'Exesser';
      $rootScope.$apply();

      expect(filtersObserver.filtersHaveChanged).toHaveBeenCalledTimes(2);
      expect(filtersObserver.filtersHaveChanged).toHaveBeenCalledWith('listKey', { 'company': 'Exesser' });
      expect(listStatus.setFilters).toHaveBeenCalledWith('listKey', { 'company': 'Exesser' });
    });

    it('should replace only the model value if we have data in sessionStorage', function () {
      spyOnListStatusGetFilters.and.returnValue({ 'company': 'TERA' });
      spyOn(topActionState, 'filtersCanBeOpened').and.returnValue(false);
      filtersObserverRegisterSetFilterDataCallback('filterKey', 'listKey');

      $rootScope.$apply();

      expect(filtersController.model).toEqual({ 'company': 'TERA' });
      expect(filtersController.originalModel).toEqual({ 'company': 'WKY' });
    });

    it('should NOT request filter if topActionState.filtersCanBeOpened return true', function () {
      spyOn(topActionState, 'filtersCanBeOpened').and.returnValue(true);
      filtersObserverRegisterSetFilterDataCallback('filterKey', 'listKey');

      $rootScope.$apply();

      expect(guidanceFormObserverFactory.createGuidanceFormObserver).not.toHaveBeenCalled();
      expect(validationObserverFactory.createValidationObserver).not.toHaveBeenCalled();
      expect(suggestionsObserverFactory.createSuggestionsObserver).not.toHaveBeenCalled();
      expect(filterDatasource.get).not.toHaveBeenCalled();
      expect(topActionState.setFiltersCanBeOpened).toHaveBeenCalledTimes(1);
    });
  });

  it('should reset filter model when the `reset` button is clicked ', function () {
    filtersObserverRegisterSetFilterDataCallback('filterKey', 'listKey');
    $rootScope.$apply();

    expect(filtersController.model).toEqual({ 'company': 'WKY' });

    $scope.filtersController.model.company = '42';
    $rootScope.$apply();

    expect(filtersController.model).toEqual({ 'company': '42' });

    filtersController.resetModel();

    expect(filtersController.model).toEqual({ 'company': 'WKY' });
  });

  describe('when the scope is destroyed', function () {
    beforeEach(function () {
      spyOn(sidebarObserver, 'closeAllSidebarElements');
    });

    describe('when the filters have been set', function () {
      beforeEach(function () {
        filtersObserverRegisterSetFilterDataCallback('filterKey', 'listKey');
        $rootScope.$apply();

        //Next we destroy the scope and trigger the deletion callback.
        $scope.$destroy();
      });

      it('should signal the sidebar observer to close all sidebar elements', function () {
        expect(sidebarObserver.closeAllSidebarElements).toHaveBeenCalledTimes(1);
      });

      it('should call setFiltersCanBeOpened on the topActionState with false', function () {
        // The first invocation happens when first opening the filters.
        // The second invocation happens when the setFilterDataCallback is invoked.
        expect(topActionState.setFiltersCanBeOpened).toHaveBeenCalledTimes(3);
        expect(topActionState.setFiltersCanBeOpened.calls.mostRecent().args[0]).toBe(false);
      });
    });

    describe('when the filters have not been set', function () {
      beforeEach(function () {
        //We destroy the scope but the filters haven not opened yet.
        $scope.$destroy();
      });

      it('should signal the sidebar observer to close all sidebar elements', function () {
        expect(sidebarObserver.closeAllSidebarElements).not.toHaveBeenCalled();
      });

      it('should call setFiltersCanBeOpened on the topActionState with false', function () {
        //The first invocation happens when first opening the filters.
        expect(topActionState.setFiltersCanBeOpened).toHaveBeenCalledTimes(1);
        expect(topActionState.setFiltersCanBeOpened.calls.mostRecent().args[0]).toBe(false);
      });
    });
  });
});
