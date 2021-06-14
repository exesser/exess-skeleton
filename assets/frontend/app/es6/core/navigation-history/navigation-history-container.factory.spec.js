'use strict';

describe('Factory: navigationHistoryContainer', function () {
  afterEach(function () {
    sessionStorage.clear();
  });

  describe('when sessionStorage has values', function () {
    beforeEach(module('digitalWorkplaceApp'));

    let navigationHistoryContainer;

    beforeEach(inject(function (_navigationHistoryContainer_) {
      navigationHistoryContainer = _navigationHistoryContainer_;
    }));

    it('should NOT have any actions when is instantiated', function () {
      expect(navigationHistoryContainer.getActions().length).toBe(0);
    });

    it('should add action, should remove the old ones with same label', function () {
      expect(navigationHistoryContainer.getActions().length).toBe(0);

      navigationHistoryContainer.addAction("label1", { current: { name: "dasboard" }, params: { dasId: 1234567 } });
      navigationHistoryContainer.addAction("label2", { current: { name: "dasboard" }, params: { dasId: 1234 } });
      navigationHistoryContainer.addAction("label3", { current: { name: "dasboard" }, params: { dasId: 12345 } });
      navigationHistoryContainer.addAction("label1", { current: { name: "dasboard" }, params: { dasId: 123 } });

      expect(navigationHistoryContainer.getActions().length).toBe(3);
      expect(navigationHistoryContainer.getActions()).toEqual([
        { label: "label2", stateName: "dasboard", stateParams: { dasId: 1234 } },
        { label: "label3", stateName: "dasboard", stateParams: { dasId: 12345 } },
        { label: "label1", stateName: "dasboard", stateParams: { dasId: 123 } }
      ]);

      expect(navigationHistoryContainer.getShowActions()).toEqual(true);
    });

  });

  describe('when sessionStorage has values', function () {
    beforeEach(module('digitalWorkplaceApp', function () {
      sessionStorage.setItem("HISTORY_ACTIONS_KEY", angular.toJson([
        { label: "label11", stateName: "dasboard", stateParams: { dasId: 1123 } },
        { label: "label12", stateName: "dasboard", stateParams: { dasId: 11234 } },
        { label: "label13", stateName: "dasboard", stateParams: { dasId: 112345 } }
      ]));
      sessionStorage.setItem("HISTORY_SHOW_ACTIONS_KEY", false);
    }));

    let navigationHistoryContainer;

    beforeEach(inject(function (_navigationHistoryContainer_) {
      navigationHistoryContainer = _navigationHistoryContainer_;
    }));

    it('should return the values from sessionStorage', function () {
      expect(navigationHistoryContainer.getActions().length).toBe(3);
      expect(navigationHistoryContainer.getActions()).toEqual([
        { label: "label11", stateName: "dasboard", stateParams: { dasId: 1123 } },
        { label: "label12", stateName: "dasboard", stateParams: { dasId: 11234 } },
        { label: "label13", stateName: "dasboard", stateParams: { dasId: 112345 } }
      ]);

      expect(navigationHistoryContainer.getShowActions()).toEqual(false);
      navigationHistoryContainer.setShowActions(true);
      expect(navigationHistoryContainer.getShowActions()).toEqual(true);
    });
  });

});
