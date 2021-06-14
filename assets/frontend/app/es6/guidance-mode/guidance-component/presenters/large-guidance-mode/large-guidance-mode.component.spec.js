'use strict';

describe('Component: largeGuidanceMode', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let $rootScope;
  let $timeout;
  let $q;
  let $compile;
  let element;

  let guidanceFormObserverFactory;
  let guidanceFormObserver;
  let commandHandler;
  let previousState;
  let CONFIRM_ACTION;
  let primaryButtonObserver;
  let translateFilter;
  let guidanceModeDatasource;

  let guidanceMode;
  let guidancePromise;

  const template = `<large-guidance-mode></large-guidance-mode>`;

  /*
   We mock the translate filter here to simply return the key.
   This is to prevent having to take the translations into account
   in the test.
   */
  beforeEach(module(function ($provide) {
    $provide.provider('translateFilter', function () {
      this.$get = function () {
        const spy = jasmine.createSpy('translateFilter');
        spy.and.callFake(function (translationKey) {
          return translationKey;
        });
        return spy;
      };
    });
  }));

  // Mock the '$stateParams'
  beforeEach(module(function ($provide) {
    $provide.value('$stateParams', {
      flowId: 666,
      recordId: 1337,
      recordType: 'type1',
      flowAction: 'duplicate'
    });
  }));

  beforeEach(inject(function (_$rootScope_, $state, _$timeout_, _$q_,
                              _$compile_, _guidanceFormObserverFactory_,
                              _translateFilter_, GuidanceFormObserver,
                              _commandHandler_, _previousState_, _CONFIRM_ACTION_,
                              _primaryButtonObserver_, _guidanceModeDatasource_) {
    mockHelpers.blockUIRouter($state);

    $rootScope = _$rootScope_;
    $timeout = _$timeout_;
    $q = _$q_;
    $compile = _$compile_;

    previousState = _previousState_;
    primaryButtonObserver = _primaryButtonObserver_;
    translateFilter = _translateFilter_;
    CONFIRM_ACTION = _CONFIRM_ACTION_;
    commandHandler = _commandHandler_;
    guidanceModeDatasource = _guidanceModeDatasource_;

    guidanceFormObserverFactory = _guidanceFormObserverFactory_;
    guidanceFormObserver = new GuidanceFormObserver();
    spyOn(guidanceFormObserverFactory, 'createGuidanceFormObserver').and.returnValue(guidanceFormObserver);

    guidanceMode = {
      model: {
        company: {
          name: "",
          number: ""
        }
      },
      grid: {
        columns: [{
          size: "1-1",
          rows: [{
            size: "1-1",
            type: "basicFormlyForm",
            options: {
              formKey: "default"
            }
          }]
        }]
      },
      form: {
        default: {
          type_c: "DEFAULT",
          id: "8b42beb4-5ab2-018f-bb1f-56a8afa918ba",
          key_c: "CLS1",
          name: "Create Lead Step 1",
          fields: [{
            id: "company.name",
            label: "Bedrijfsnaam",
            type: "LargeTextField"
          }, {
            id: "company.number",
            label: "Bedrijfsnummer",
            type: "LargeTextField"
          }]
        }
      },
      guidance: {
        title: "Create Opportunity",
        loadingMessage: "Saving Opportunity"
      },
      step: {
        willSave: false,
        done: false
      },
      errors: {},
      suggestions: {},
      progress: {
        steps: [{
          id: "cca1046a-b1c2-14a8-7ef8-56e1999f7c93",
          key_c: "someKey",
          name: "Complete Customer Data",
          active: true,
          canBeActivated: true,
          disabled: false,
          progressPercentage: 0,
          substeps: [{
            id: "3718c184-ea94-19b9-9c38-56e198f9d2eb",
            name: "Company",
            active: true,
            canBeActivated: true,
            disabled: false
          }, {
            id: "e21b60de-79c2-7b0e-9e29-56e1982f39a8",
            name: "Contact Information",
            active: false,
            canBeActivated: false,
            disabled: false
          }]
        }, {
          id: "df603495-d93d-7b05-2c52-56ba0a190bf7",
          name: "Manage Opportunity",
          active: false,
          canBeActivated: false,
          disabled: true,
          progressPercentage: 0,
          substeps: []
        }, {
          id: "step4Id",
          name: "Price table",
          active: false,
          canBeActivated: false,
          disabled: true,
          progressPercentage: 0,
          substeps: []
        }]
      }
    };
  }));

  function compile() {
    guidancePromise = $q.defer();

    spyOn(guidanceModeDatasource, 'get').and.returnValue(guidancePromise.promise);

    const scope = $rootScope.$new();

    element = angular.element(template);
    element = $compile(element)(scope);

    $rootScope.$apply();
  }

  describe('after initialisation', function () {
    let stepChangeCallback;
    let formValidityUpdateCallback;

    let confirmGuidanceSpy;

    beforeEach(function () {
      //We need to return a fake deregister function to invoke when the scope is destroyed.
      spyOn(guidanceFormObserver, 'addStepChangeOccurredCallback').and.returnValue(_.noop);
      spyOn(guidanceFormObserver, 'setFormValidityUpdateCallback');

      spyOn(guidanceFormObserver, 'requestNextStep');
      spyOn(primaryButtonObserver, 'resetPrimaryButtonData');
      spyOn(primaryButtonObserver, 'setPrimaryButtonData').and.callThrough();
      confirmGuidanceSpy = spyOn(guidanceFormObserver, 'confirmGuidance');

      compile();
      guidancePromise.resolve(guidanceMode);
      $timeout.flush();

      /*
       The addStepChangeOccurredCallback is called twice, first by the largeGuidanceMode component
       (this is the call we are interested in) and once by the underlying basic-formly-form.
       We take out the callback from the invocation of the largeGuidanceMode component here.
       */
      expect(guidanceFormObserver.addStepChangeOccurredCallback).toHaveBeenCalledTimes(2);
      stepChangeCallback = guidanceFormObserver.addStepChangeOccurredCallback.calls.argsFor(0)[0];

      //Retrieve the callback to set largeGuidanceModeController.valid to true or false
      expect(guidanceFormObserver.setFormValidityUpdateCallback).toHaveBeenCalledTimes(1);
      formValidityUpdateCallback = guidanceFormObserver.setFormValidityUpdateCallback.calls.argsFor(0)[0];

      expect(guidanceModeDatasource.get).toHaveBeenCalledTimes(1);
      expect(guidanceModeDatasource.get).toHaveBeenCalledWith({
        flowId: 666,
        recordId: 1337,
        recordType: 'type1',
        flowAction: 'duplicate'
      });
    });

    it('should initialize the "guidance" component correctly', function () {
      const guidanceElement = $(element.find('guidance'));

      expect(guidanceElement.attr('inform-progress-bar-observer')).toBe('true');
      expect(guidanceElement.attr('enable-navigate-away-guard')).toBe('true');

      expect(primaryButtonObserver.setPrimaryButtonData).toHaveBeenCalledTimes(1);
      expect(primaryButtonObserver.setPrimaryButtonData).toHaveBeenCalledWith({ title: "NEXT", disabled: false });
    });

    describe('backArrowClicked', function () {
      it("should go to the previous page.", function () {
        spyOn(previousState, 'navigateTo');

        expect(primaryButtonObserver.resetPrimaryButtonData).not.toHaveBeenCalled();

        element.find(".top a:has(span.icon-previous)").click();
        $rootScope.$apply();

        expect(primaryButtonObserver.resetPrimaryButtonData).toHaveBeenCalledTimes(1);
        $timeout.flush();

        expect(previousState.navigateTo).toHaveBeenCalledTimes(1);
      });
    });

    describe('the primary button', function () {
      it('should initially show NEXT if there is a next step', function () {
        expect(element.find("#primaryButton span:nth-child(2)").text()).toBe("NEXT");
        expect(translateFilter).toHaveBeenCalledWith('NEXT');
      });

      it("should show 'NEXT' if there is a next step after a step change", function () {
        guidanceMode.step.willSave = false;
        stepChangeCallback();
        $rootScope.$apply();

        expect(element.find("#primaryButton span:nth-child(2)").text()).toBe("NEXT");
        expect(translateFilter).toHaveBeenCalledWith('NEXT');
      });

      it("should show 'CONFIRM' if there is not a next step after a step change", function () {
        guidanceMode.step.willSave = true;
        stepChangeCallback();
        $rootScope.$apply();

        expect(element.find("#primaryButton span:nth-child(2)").text()).toBe("CONFIRM");
        expect(translateFilter).toHaveBeenCalledWith('CONFIRM');
      });

      describe('if the forms are valid', function () {
        beforeEach(function () {
          formValidityUpdateCallback(true);
          $rootScope.$apply();
        });

        it('should enable the button', function () {
          expect(element.find("#primaryButton").attr('disabled')).toBe(undefined);
        });

        it('should call guidanceFormObserver.requestNextStep if there is a next step when clicked', function () {
          guidanceMode.step.willSave = false;
          $rootScope.$apply();
          expect(guidanceFormObserver.requestNextStep).not.toHaveBeenCalled();

          element.find("#primaryButton").click();

          expect(guidanceFormObserver.requestNextStep).toHaveBeenCalled();
        });

        it('should call guidanceFormObserver.confirmGuidance if there is no next step', function () {
          //We first want to resolve the slide animation's promise and then the confirm guidance's.
          const confirmGuidanceDeferred = $q.defer();

          guidanceMode.step.willSave = true;
          $rootScope.$apply();

          confirmGuidanceSpy.and.returnValue(confirmGuidanceDeferred.promise);
          spyOn(commandHandler, 'handle');

          expect(guidanceFormObserver.confirmGuidance).not.toHaveBeenCalled();

          // Click the primary button
          element.find("#primaryButton").click();

          $rootScope.$apply();

          // We expect to see a loading indicator now
          expect(element.find("div.loading").hasClass('ng-hide')).toBe(false);

          expect(guidanceFormObserver.confirmGuidance).toHaveBeenCalledTimes(1);

          //Resolve the confirm guidance's promise
          confirmGuidanceDeferred.resolve({
            command: "reloadPage"
          });

          $rootScope.$apply();
          expect(guidanceFormObserver.confirmGuidance).toHaveBeenCalledWith(CONFIRM_ACTION.CONFIRM);
          expect(commandHandler.handle).toHaveBeenCalledWith({ command: "reloadPage" });

          // We expect the loading indicator to have disappeared
          expect(element.find("div.loading").hasClass('ng-hide')).toBe(true);
        });

        it('should when guidanceFormObserver.confirmGuidance fails hide the loading indicator', function () {
          //We first want to resolve the slide animation's promise and then the confirm guidance's.
          const confirmGuidanceDeferred = $q.defer();

          guidanceMode.step.willSave = true;
          $rootScope.$apply();

          confirmGuidanceSpy.and.returnValue(confirmGuidanceDeferred.promise);

          // Click the primary button
          element.find("#primaryButton").click();
          $rootScope.$apply();

          // We expect to see a loading indicator now
          expect(element.find("div.loading").hasClass('ng-hide')).toBe(false);

          //Resolve the confirm guidance's promise
          confirmGuidanceDeferred.reject();
          $rootScope.$apply();

          // We expect the loading indicator to have disappeared
          expect(element.find("div.loading").hasClass('ng-hide')).toBe(true);
        });
      });

      describe('if the forms are not valid', function () {
        beforeEach(function () {
          formValidityUpdateCallback(false);
          $rootScope.$apply();
        });

        it('should disable the button', function () {
          expect(element.find("#primaryButton").attr('disabled')).toBe('disabled');
        });

        it('should do nothing if it is clicked', function () {
          // Click the primary button
          primaryButtonObserver.primaryButtonClicked();

          expect(guidanceFormObserver.requestNextStep).not.toHaveBeenCalled();
          expect(guidanceFormObserver.confirmGuidance).not.toHaveBeenCalled();
        });
      });
    });
  });

  describe('loading behavior', function () {
    it("should show loading message and hide the <guidance>", function () {
      compile();

      const loadingDiv = $(element.find('div.loading'));

      // Expect loading to be true
      expect(element.find('guidance').length).toBe(0);
      expect(loadingDiv.hasClass('ng-hide')).toBe(false);

      // Expect loading to vanish after request came in.
      guidancePromise.resolve(guidanceMode);
      $timeout.flush();

      expect(element.find('guidance').length).toBe(1);
      expect(loadingDiv.hasClass('ng-hide')).toBe(true);
    });
  });

  describe('after scope destruction', function () {
    it("should call the addStepChangeOccurredCallback's deregister function", function () {
      const deregisterSpy = jasmine.createSpy();
      spyOn(guidanceFormObserver, 'addStepChangeOccurredCallback').and.returnValue(deregisterSpy);

      compile();
      guidancePromise.resolve(guidanceMode);
      $timeout.flush();

      expect(deregisterSpy).not.toHaveBeenCalled();

      element.remove();
      $rootScope.$apply();

      expect(deregisterSpy).toHaveBeenCalledTimes(1);
    });
  });
});
