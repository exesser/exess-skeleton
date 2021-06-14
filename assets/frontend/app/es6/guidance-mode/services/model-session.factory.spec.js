'use strict';

describe('Factory: modelSession', function () {
  const modelKey = 'ABCDE#12345';
  const model = { name: "Andreas Bakkerud", number: "13" };

  afterEach(function () {
    sessionStorage.clear();
  });

  describe('when sessionStorage is empty', function () {
    beforeEach(module('digitalWorkplaceApp'));

    let modelSession;

    beforeEach(inject(function (_modelSession_) {
      modelSession = _modelSession_;
    }));

    it('should return undefined if we don\'t set data', function () {
      expect(modelSession.getModel(modelKey)).toEqual({});

      modelSession.setModel(modelKey, model);

      expect(modelSession.getModel(modelKey)).toBe(model);
    });
  });

  describe('when sessionStorage has values', function () {
    beforeEach(module('digitalWorkplaceApp', function () {
      sessionStorage.setItem("MODEL_KEY", angular.toJson({ [modelKey]: model }));
    }));

    let modelSession;

    beforeEach(inject(function (_modelSession_) {
      modelSession = _modelSession_;
    }));

    it('should return the values from sessionStorage', function () {
      expect(modelSession.getModel(modelKey)).toEqual(model);
    });
  });
});
