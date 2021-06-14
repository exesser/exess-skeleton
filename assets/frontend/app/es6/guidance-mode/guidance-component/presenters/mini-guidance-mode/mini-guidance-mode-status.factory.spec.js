'use strict';

describe('Factory: miniGuidanceModeStatus', function () {
  const guidanceData = {
    title: "UNQUALIFY LEAD",
    confirmLabel: "UNQUALIFY LEAD",
    model: {
      firstName: "Wky"
    },
    recordId: 1337
  };

  afterEach(function () {
    sessionStorage.clear();
  });

  describe('when sessionStorage is empty', function () {
    beforeEach(module('digitalWorkplaceApp'));

    let miniGuidanceModeStatus;

    beforeEach(inject(function (_miniGuidanceModeStatus_) {
      miniGuidanceModeStatus = _miniGuidanceModeStatus_;
    }));

    it(`should return an empty object if we don't have data in session`, function () {
      expect(miniGuidanceModeStatus.getGuidanceData()).toEqual({});
    });

    it(`should set data to session and return it correctly`, function () {
      miniGuidanceModeStatus.setGuidanceData(guidanceData);
      expect(miniGuidanceModeStatus.getGuidanceData()).toEqual(guidanceData);
    });

  });

  describe('when sessionStorage has values', function () {
    beforeEach(module('digitalWorkplaceApp', function (MINI_GUIDANCE_SESSION_KEY) {
      sessionStorage.setItem(MINI_GUIDANCE_SESSION_KEY, angular.toJson(guidanceData));
    }));

    let miniGuidanceModeStatus;

    beforeEach(inject(function (_miniGuidanceModeStatus_) {
      miniGuidanceModeStatus = _miniGuidanceModeStatus_;
    }));

    it('should return the values from sessionStorage', function () {
      expect(miniGuidanceModeStatus.getGuidanceData()).toEqual(guidanceData);
    });

    it(`should be able to change only the model`, function () {
      const model = { lastName: 'Wky' };
      const guidanceData2 = angular.copy(guidanceData);
      guidanceData2.model = model;

      expect(miniGuidanceModeStatus.getGuidanceData()).toEqual(guidanceData);
      expect(miniGuidanceModeStatus.getGuidanceData()).not.toEqual(guidanceData2);

      miniGuidanceModeStatus.updateModel(model);

      expect(miniGuidanceModeStatus.getGuidanceData()).toEqual(guidanceData2);
    });

    it(`should NOT change the model if the guidance is empty`, function () {
      expect(miniGuidanceModeStatus.getGuidanceData()).toEqual(guidanceData);

      miniGuidanceModeStatus.setGuidanceData({});
      expect(miniGuidanceModeStatus.getGuidanceData()).toEqual({});

      miniGuidanceModeStatus.updateModel({ lastName: 'Wky' });

      expect(miniGuidanceModeStatus.getGuidanceData()).toEqual({});
    });
  });
});
