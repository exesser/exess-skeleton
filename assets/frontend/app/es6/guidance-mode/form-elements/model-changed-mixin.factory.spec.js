'use strict';

describe('Mixin: modelChangedMixin', function () {

  // load the factory's module
  beforeEach(module('digitalWorkplaceApp'));

  let $rootScope;

  let modelChangedMixin;

  let guidanceFormObserver;

  beforeEach(inject(function (_$rootScope_, _modelChangedMixin_, GuidanceFormObserver) {
    $rootScope = _$rootScope_;

    modelChangedMixin = _modelChangedMixin_;

    guidanceFormObserver = new GuidanceFormObserver();
  }));

  it('should throw an error if the form key is missing.', function () {
    const controller = { guidanceObserversAccessor: 'fake', ngModel: 'fake' };

    expect(function () {
      modelChangedMixin.apply(controller);
    }).toThrow(new Error("Error: a form element controller must have a key, the current key is: undefined."));
  });

  it('should throw an error if guidanceObserversAccessor is missing.', function () {
    const controller = { key: "first_name", ngModel: 'fake' };

    expect(function () {
      modelChangedMixin.apply(controller);
    }).toThrow(new Error("Error: a form element controller must have a guidanceObserversAccessor, the current guidanceObserversAccessor is: undefined."));
  });

  it('should throw an error if ngModel is missing.', function () {
    const controller = { key: "first_name", guidanceObserversAccessor: 'fake' };

    expect(function () {
      modelChangedMixin.apply(controller);
    }).toThrow(new Error("Error: a form element must have an ngModel instance, the current value is: undefined."));
  });

  describe('when the controller is valid', function () {
    let controller;
    let fakeNgModel;
    let $scope;

    beforeEach(function () {
      fakeNgModel = { $viewValue: 42, $setViewValue: _.noop };

      controller = {
        key: "first_name",
        noBackendInteraction: false,
        ngModel: fakeNgModel,
        guidanceObserversAccessor: {
          getGuidanceFormObserver() {
            return guidanceFormObserver;
          }
        }
      };

      $scope = $rootScope.$new();
      $scope.controller = controller;
    });

    it('should NOT listen to external model changes', function () {
      modelChangedMixin.apply(controller, 'controller', $scope, false);
      expect(controller.internalModelValue).toBe(undefined);

      controller.ngModel.$viewValue = 'huray';
      $rootScope.$apply();

      expect(controller.internalModelValue).toBe(undefined);
    });

    describe('when it should listen to external model changes', function () {

      beforeEach(function () {
        modelChangedMixin.apply(controller, 'controller', $scope);
      });

      it('should listen to external model changes', function () {
        expect(controller.internalModelValue).toBe(undefined);

        controller.ngModel.$viewValue = 'huray';
        $rootScope.$apply();

        expect(controller.internalModelValue).toBe('huray');
      });

      it('should add an "internalModelValueChanged" method, which triggers the guidanceFormObserver, and sets the modelValue when run', function () {
        controller.internalModelValue = '   Spanjard  ';

        spyOn(fakeNgModel, '$setViewValue');
        spyOn(guidanceFormObserver, 'formValueChanged');

        controller.internalModelValueChanged();

        expect(fakeNgModel.$setViewValue).toHaveBeenCalledTimes(1);
        expect(fakeNgModel.$setViewValue).toHaveBeenCalledWith('Spanjard');

        expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(1);
        expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
          focus: 'first_name',
          value: "Spanjard"
        }, false);

        controller.noBackendInteraction = true;
        controller.internalModelValueChanged();

        expect(fakeNgModel.$setViewValue).toHaveBeenCalledTimes(2);
        expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(2);
        expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
          focus: 'first_name',
          value: "Spanjard"
        }, true);

        controller.errorMessages = ['An error!'];
        controller.internalModelValueChanged();

        expect(fakeNgModel.$setViewValue).toHaveBeenCalledTimes(3);
        expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(3);
      });
    });
  });
});
