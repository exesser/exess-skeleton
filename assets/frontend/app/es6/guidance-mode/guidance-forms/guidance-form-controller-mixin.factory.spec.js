'use strict';

describe('Mixin: guidanceFormControllerMixin', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let $rootScope;
  let $compile;

  let formlyFieldsTranslator;

  let guidanceFormControllerMixin;
  let guidanceFormObserver;

  let fakeElement;

  const template = '<awesome-thing title="Something" description="Awesome"></awesome-thing>';

  beforeEach(inject(function (_$rootScope_, _$compile_, $state, _guidanceFormControllerMixin_,
                              GuidanceFormObserver, _formlyFieldsTranslator_) {
    mockHelpers.blockUIRouter($state);

    $rootScope = _$rootScope_;
    $compile = _$compile_;
    formlyFieldsTranslator = _formlyFieldsTranslator_;

    guidanceFormControllerMixin = _guidanceFormControllerMixin_;
    guidanceFormObserver = new GuidanceFormObserver();

    //We create a directive of some sort to pass as the element this mixin is called for, with awesome-thing as an example.
    const fakeElementScope = $rootScope.$new();

    fakeElement = angular.element(template);
    fakeElement = $compile(fakeElement)(fakeElementScope);

    $rootScope.$apply();
  }));

  it('should initialize the controller correctly.', function () {
    const controller = { formKey: 'default' };

    guidanceFormControllerMixin.apply({
      controller, controllerAs: 'guidanceFormController',
      scope: $rootScope.$new(),
      guidanceFormObserver
    });

    expect(controller.model).toEqual({});
    expect(controller.fields).toEqual([]);
  });

  describe('addStepChangeOccurredCallback', function () {
    const controller = { formKey: 'default' };

    let deregisterSpy;

    beforeEach(function () {
      //Deregister spy is the mock result value of addStepChangeOccurredCallback that is invoked when onDestroy is triggered.
      deregisterSpy = jasmine.createSpy("deregisterSpy");

      spyOn(guidanceFormObserver, 'addStepChangeOccurredCallback').and.returnValue(deregisterSpy);
      guidanceFormControllerMixin.apply({
        controller,
        controllerAs: 'guidanceFormController',
        scope: $rootScope.$new(),
        guidanceFormObserver
      });
    });

    it('should register the step change callback on the guidanceFormObserver', function () {
      expect(guidanceFormObserver.addStepChangeOccurredCallback).toHaveBeenCalledTimes(1);
    });

    it('should call the stepChangeCallbackDeregister function when controller.$onDestroy is called.', function () {
      expect(deregisterSpy).not.toHaveBeenCalled();
      controller.$onDestroy();
      expect(deregisterSpy).toHaveBeenCalledTimes(1);
    });
  });

  it('should validate the controller correctly.', function () {
    const controller = {};

    expect(function () {
      guidanceFormControllerMixin.apply({
        controller,
        controllerAs: 'guidanceFormController',
        scope: $rootScope.$new(),
        guidanceFormObserver
      });
    }).toThrow(new Error("Error a GuidanceFormController must have a formKey, the current formKey is: undefined."));
  });

  describe('', function () {
    let guidanceModeStep;
    let fakeFields;
    let controller;
    let $scope;

    beforeEach(function () {
      controller = { formKey: 'default' };
      $scope = $rootScope.$new();
      $scope.guidanceFormController = controller;

      guidanceFormControllerMixin.apply({
        controller,
        controllerAs: 'guidanceFormController',
        scope: $scope,
        guidanceFormObserver
      });

      // Shortened fake GuidanceMode
      guidanceModeStep = {
        "model": {
          "company": {
            "name": "42",
            "number": "424242"
          }
        },
        "grid": {},
        "form": {
          'default': {
            fields: [
              {
                id: "company.name",
                label: "Company name",
                type: "varchar"
              }
            ]
          }
        },
        "guidance": {},
        "step": {
          "willSave": false,
          "done": false
        },
        "errors": {},
        "suggestions": {},
        "progress": {}
      };
    });

    describe('', function () {
      beforeEach(function () {
        fakeFields = [
          {
            type: "large-input",
            key: "company.name",
            defaultValue: undefined,
            templateOptions: {
              label: "Company name",
              required: true
            }
          }
        ];

        spyOn(formlyFieldsTranslator, 'translate').and.returnValue(fakeFields);

        guidanceFormObserver.stepChangeOccurred(guidanceModeStep);
        $rootScope.$apply();
      });

      it('should when the "stepChanged" callback is fired assign the variables.', function () {
        expect(formlyFieldsTranslator.translate).toHaveBeenCalledTimes(1);
        expect(formlyFieldsTranslator.translate).toHaveBeenCalledWith([{
          id: "company.name",
          label: "Company name",
          type: "varchar"
        }]);

        expect(controller.model).toEqual({ "company": { "name": "42", "number": "424242" } });
        expect(controller.fields).toEqual(fakeFields);
      });

      it('should when the forms changes trigger a "formControllerCreated".', function () {
        spyOn(guidanceFormObserver, 'formControllerCreated');

        $scope.guidanceFormController.form = [];
        $rootScope.$apply();

        expect(guidanceFormObserver.formControllerCreated).not.toHaveBeenCalled();

        $scope.guidanceFormController.form = { $valid: true };
        $rootScope.$apply();

        expect(guidanceFormObserver.formControllerCreated).toHaveBeenCalledTimes(1);
        expect(guidanceFormObserver.formControllerCreated).toHaveBeenCalledWith(controller.form);
      });
    });
  });
});
