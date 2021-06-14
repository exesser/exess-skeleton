'use strict';

describe('Component: guidance', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let $rootScope;
  let $compile;
  let $timeout;
  let $q;

  let guidanceModeDatasource;
  let guidanceData;

  let guidanceFormObserver;
  let progressBarObserver;

  let validationObserverFactory;
  let validationObserver;

  let suggestionsObserverFactory;
  let suggestionsObserver;

  let promiseUtils;

  let commandHandler;
  let guidanceGuardian;

  let DEBOUNCE_TIME;
  let CONFIRM_ACTION;

  let element;
  let scope;

  //Function to create a template for the guidance component. This was added because there was a lot of duplication in this file.
  const createTemplate = (informProgressBarObserver, enableNavigateAwayGuard, enableGuidanceRecovery) =>
    `<guidance guidance-form-observer="guidanceFormObserver"
               guidance-data="guidanceData"
               flow-id="{{ flowId }}"
               record-id="{{ recordId }}"
               inform-progress-bar-observer="${informProgressBarObserver}"
               enable-guidance-recovery=${enableGuidanceRecovery}
               enable-navigate-away-guard=${enableNavigateAwayGuard}>
     </guidance>`;

  // Initialize the controller and a mock scope
  beforeEach(inject(function ($controller, $state, _$rootScope_, _$compile_, _guidanceModeDatasource_, GuidanceFormObserver,
                              _progressBarObserver_, _$timeout_, _$q_, _validationObserverFactory_, ValidationObserver,
                              _suggestionsObserverFactory_, SuggestionsObserver, _DEBOUNCE_TIME_, _CONFIRM_ACTION_,
                              _promiseUtils_, _commandHandler_, _guidanceGuardian_) {
    mockHelpers.blockUIRouter($state);
    $state.current = { name: "my-route" };
    $state.params = { parma1: "my-route-param-1" };

    $rootScope = _$rootScope_;
    $compile = _$compile_;
    $timeout = _$timeout_;
    $q = _$q_;
    DEBOUNCE_TIME = _DEBOUNCE_TIME_;
    CONFIRM_ACTION = _CONFIRM_ACTION_;

    scope = $rootScope.$new();

    guidanceModeDatasource = _guidanceModeDatasource_;
    guidanceFormObserver = new GuidanceFormObserver();

    progressBarObserver = _progressBarObserver_;

    validationObserverFactory = _validationObserverFactory_;
    validationObserver = new ValidationObserver();
    spyOn(validationObserverFactory, 'createValidationObserver').and.returnValue(validationObserver);

    suggestionsObserverFactory = _suggestionsObserverFactory_;
    suggestionsObserver = new SuggestionsObserver();
    spyOn(suggestionsObserverFactory, 'createSuggestionsObserver').and.returnValue(suggestionsObserver);

    promiseUtils = _promiseUtils_;
    commandHandler = _commandHandler_;
    guidanceGuardian = _guidanceGuardian_;

    guidanceData = {
      "model": {
        "company": {
          "name": "",
          "number": ""
        }
      },
      "grid": {
        "columns": [{
          "size": "1-4",
          "hasMargin": false,
          "cssClasses": ["progressbar"],
          "rows": [{
            "size": "1-1",
            "type": "progressBar",
            "options": {
              "title": "Kitchen Sink"
            }
          }]
        }, {
          "size": "3-4",
          "cssClasses": ["guidance"],
          "hasMargin": false,
          "rows": [{
            "size": "1-1",
            "type": "guidanceForm",
            "options": {
              'cardSize': 'col-1-2'
            }
          }]
        }]
      },
      "form": {
        "type_c": "DEFAULT",
        "id": "8b42beb4-5ab2-018f-bb1f-56a8afa918ba",
        "key_c": "CLS1",
        "name": "Create Lead Step 1",
        "fields": [{
          "id": "company.name",
          "label": "Bedrijfsnaam",
          "default": "",
          "type": "large-input",
          "module": "leads",
          "moduleField": "company_name_c",
          "validation": {
            required: true
          }
        }, {
          "id": "company.number",
          "label": "Bedrijfsnummer",
          "default": "",
          "type": "large-input",
          "module": "leads",
          "moduleField": "company_name_c",
          "validation": {
            required: true,
            minlength: 5,
            maxlength: 8
          }
        }]
      },
      "guidance": {
        title: "Create Opportunity",
        loadingMessage: "Saving Opportunity"
      },
      "step": {
        "willSave": false,
        "done": false
      },
      "errors": {},
      "suggestions": {},
      "progress": {
        "steps": [{
          "id": "cca1046a-b1c2-14a8-7ef8-56e1999f7c93",
          "key_c": "someKey",
          "name": "Complete Customer Data",
          "active": true,
          "canBeActivated": true,
          "disabled": false,
          "progressPercentage": 0,
          "substeps": [{
            "id": "3718c184-ea94-19b9-9c38-56e198f9d2eb",
            "name": "Company",
            "active": true,
            "canBeActivated": true,
            "disabled": false
          }, {
            "id": "e21b60de-79c2-7b0e-9e29-56e1982f39a8",
            "name": "Contact Information",
            "active": false,
            "canBeActivated": false,
            "disabled": false
          }]
        }, {
          "id": "df603495-d93d-7b05-2c52-56ba0a190bf7",
          "name": "Manage Opportunity",
          "active": false,
          "canBeActivated": false,
          "disabled": true,
          "progressPercentage": 0,
          "substeps": []
        }, {
          "id": "step4Id",
          "name": "Price table",
          "active": false,
          "canBeActivated": false,
          "disabled": true,
          "progressPercentage": 0,
          "substeps": []
        }]
      }
    };

    spyOn(commandHandler, 'handle');
  }));

  describe('controller initialisation', function () {
    it('should set the required data', function () {
      const guidanceController = compile();
      expect(guidanceController.flowId).toBe('42');
      expect(guidanceController.recordId).toBe('1337');
      expect(guidanceController.forms).toEqual([]);
      expect(guidanceController.guidanceData).toEqual(guidanceData);
      expect(guidanceController.informProgressBarObserver).toBe(true);
    });

    it('should call the stepChangeOccurred function on the guidanceFormObserver', function () {
      spyOn(guidanceFormObserver, 'stepChangeOccurred');
      compile();
      $timeout.flush();

      expect(guidanceFormObserver.stepChangeOccurred).toHaveBeenCalledTimes(1);
      expect(guidanceFormObserver.stepChangeOccurred).toHaveBeenCalledWith(guidanceData);
    });

    it('should call the setFullModel function on the guidanceFormObserver', function () {
      spyOn(guidanceFormObserver, 'setFullModel');
      compile();
      $timeout.flush();

      expect(guidanceFormObserver.setFullModel).toHaveBeenCalledTimes(1);
      expect(guidanceFormObserver.setFullModel).toHaveBeenCalledWith(guidanceData.model);
    });

    it('should inform the progress bar observer if guidanceController.informProgressBarObserver is true', function () {
      spyOn(progressBarObserver, 'setProgressMetadata');
      compile();
      $timeout.flush();

      expect(progressBarObserver.setProgressMetadata).toHaveBeenCalledTimes(1);
      expect(progressBarObserver.setProgressMetadata).toHaveBeenCalledWith(guidanceData.progress);
    });

    it('should not inform the progress bar observer if guidanceController.informProgressBarObserver is false', function () {
      spyOn(progressBarObserver, 'setProgressMetadata');
      compile(false);
      $timeout.flush();

      expect(progressBarObserver.setProgressMetadata).not.toHaveBeenCalled();
    });

    it('should register a click call back on the progressBarObserver if guidanceController.informProgressBarObserver is true', function () {
      spyOn(progressBarObserver, 'registerClickCallback');
      compile();
      expect(progressBarObserver.registerClickCallback).toHaveBeenCalledTimes(1);
    });

    it('should register a click call back on the progressBarObserver if guidanceController.informProgressBarObserver is false', function () {
      spyOn(progressBarObserver, 'registerClickCallback');
      compile(false);
      expect(progressBarObserver.registerClickCallback).not.toHaveBeenCalled();
    });

    it('should wrap "guidanceModeDatasource.step" in a "promiseUtils.useLatest"', function () {
      spyOn(promiseUtils, 'useLatest').and.callThrough();

      compile(false);

      expect(promiseUtils.useLatest).toHaveBeenCalledTimes(1);
      expect(promiseUtils.useLatest).toHaveBeenCalledWith(guidanceModeDatasource.step);
    });
  });

  describe('setFormControllerCreatedCallback', function () {
    it('should push the formController on the list', function () {
      //We don't want to render any forms in this test
      guidanceData.grid = {};

      const guidanceController = compile();
      expect(guidanceController.forms.length).toBe(0);

      const fakeFormController = {};
      guidanceFormObserver.formControllerCreated(fakeFormController);
      expect(guidanceController.forms.length).toBe(1);
      expect(guidanceController.forms[0]).toBe(fakeFormController);
    });
  });

  describe('setFormValueChangedCallback', function () {
    describe('within the debounce happy flow', function () {
      // Spy on the debounce and just execute the func when called to mock the _.debounce.
      beforeEach(function () {
        spyOn(_, 'debounce').and.callFake(function (func) {
          return function () {
            func.apply(this, arguments);
          };
        });
      });

      it('setFormValueChangedCallback: should call formValidityUpdate when noBackendInteraction is true', function () {
        spyOn(guidanceFormObserver, 'formValidityUpdate');
        spyOn(guidanceModeDatasource, 'step');

        compile();

        guidanceFormObserver.formValueChanged({}, true);
        $rootScope.$apply();
        $timeout.flush();
        expect(guidanceFormObserver.formValidityUpdate).toHaveBeenCalledTimes(2);
        expect(guidanceFormObserver.formValidityUpdate).toHaveBeenCalledWith(true);
        expect(guidanceModeDatasource.step).not.toHaveBeenCalled();
      });

      it('setFormValueChangedCallback: should extend the current model with data from the backend.', function () {
        //Set the initial model with a city that is not present in the backend response later
        guidanceData.model = {
          company: {
            name: "",
            number: "",
            city: ""
          },
          first_name: "Ken"
        };

        //Send back data with a company name and number but without a city.
        spyOn(guidanceModeDatasource, 'step').and.callFake(mockHelpers.resolvedPromise($q, {
          model: {
            company: {
              name: "Exesser",
              number: "0123456789"
            }
          }
        }));

        spyOn($q, 'reject').and.callFake(mockHelpers.rejectedPromise($q));

        const guidanceController = compile();

        const fakeGuidanceAction = { focus: "city", value: "Antwerpen" };
        guidanceData.model.company.city = 'Antwerpen';
        guidanceData.model.last_name = 'Block';
        _.unset(guidanceData.model, 'first_name');

        // Call observer directly as 'mocking' this is to much extra work for such a simple observer.
        guidanceFormObserver.formValueChanged(fakeGuidanceAction);
        guidanceFormObserver.formValueChanged(fakeGuidanceAction);
        $rootScope.$apply();

        const request = {
          model: {
            company: {
              name: "",
              number: "",
              city: "Antwerpen"
            },
            last_name: "Block"
          },
          progress: guidanceController.guidanceData.progress,
          action: {
            event: "CHANGED",
            focus: "city",
            currentStep: "someKey",
            previousValue: null,
            changedFields: {
              company: {
                name: '',
                number: '',
                city: ''
              },
              first_name: 'Ken',
              last_name: null
            }
          }
        };

        expect($q.reject).toHaveBeenCalledTimes(1);
        expect($q.reject).toHaveBeenCalledWith('There is already a call with this model in progress.');

        expect(guidanceModeDatasource.step).toHaveBeenCalledTimes(1);
        expect(guidanceModeDatasource.step).toHaveBeenCalledWith({ flowId: '42', recordId: '1337' }, request);
        //Check that the model has been extended with the company name and number and that the city is still set.
        expect(guidanceController.guidanceData.model).toEqual({
          company: {
            name: "Exesser",
            number: "0123456789",
            city: "Antwerpen"
          },
          last_name: "Block"
        });

        guidanceFormObserver.formValueChanged(fakeGuidanceAction);
        expect($q.reject).toHaveBeenCalledTimes(2);
        expect($q.reject).toHaveBeenCalledWith('The model was not change from last response.');
      });

      it('should set the errors object with data from the backend and inform the validationObserver', function () {
        //original model
        guidanceData.model = {
          "name": "",
          "number": "",
          "city": "Verweggistan Old"
        };

        //expected response after we change the city
        spyOn(guidanceModeDatasource, 'step').and.callFake(mockHelpers.resolvedPromise($q, {
          model: guidanceData.model, //this model will contain city = "Verweggistan"
          errors: {
            "city": [
              "That is not a real place."
            ]
          }
        }));

        //initialize guidance
        const guidanceController = compile();

        //change city value in model (mock - changed by field)
        guidanceData.model.city = 'Verweggistan';
        const fakeGuidanceAction = { focus: "city", value: "Verweggistan" };

        //Spy on the validationObserver
        spyOn(validationObserver, 'setErrors');

        // Call observer directly as 'mocking' this is to much extra work for such a simple observer.
        guidanceFormObserver.formValueChanged(fakeGuidanceAction);
        $rootScope.$apply();

        const request = {
          model: {
            "name": "",
            "number": "",
            "city": "Verweggistan"
          },
          progress: guidanceController.guidanceData.progress,
          action: {
            event: "CHANGED",
            focus: "city",
            currentStep: "someKey",
            previousValue: "Verweggistan Old",
            changedFields: {
              city: 'Verweggistan Old'
            }
          }
        };

        expect(guidanceModeDatasource.step).toHaveBeenCalledTimes(1);
        expect(guidanceModeDatasource.step).toHaveBeenCalledWith({ flowId: '42', recordId: '1337' }, request);

        $timeout.flush();

        //Check that the error  object was set
        expect(guidanceController.guidanceData.errors).toEqual({ "city": ["That is not a real place."] });

        //Check that the validationObserver was called and contains elements for all properties in the model
        //Count is 3 since this is also called in the init fase and on watch
        expect(validationObserver.setErrors).toHaveBeenCalledTimes(3);
        expect(validationObserver.setErrors).toHaveBeenCalledWith({
          city: ["That is not a real place."]
        });
      });

      it('should set the suggestions object with data from the backend and inform the suggestionsObserver', function () {
        //Setup the initial model with a partially typed in city name
        guidanceData.model = {
          "name": "",
          "number": "",
          "city": "Timboek Old"
        };

        const timbuktuSuggestion = { "label": "Timbuktu", "model": { "city": "Timbuktu" } };

        //Send back data with a company name and number but without a city.
        spyOn(guidanceModeDatasource, 'step').and.callFake(mockHelpers.resolvedPromise($q, {
          model: guidanceData.model,
          suggestions: {
            "city": [timbuktuSuggestion]
          }
        }));

        const guidanceController = compile();

        //Spy on the suggestionsObserver
        spyOn(suggestionsObserver, 'setSuggestions');

        const fakeGuidanceAction = { focus: "city", value: "Timboek" };
        guidanceData.model.city = "Timboek";
        // Call observer directly as 'mocking' this is to much extra work for such a simple observer.
        guidanceFormObserver.formValueChanged(fakeGuidanceAction);
        $rootScope.$apply();
        $timeout.flush();

        const request = {
          model: guidanceController.guidanceData.model,
          progress: guidanceController.guidanceData.progress,
          action: {
            event: "CHANGED",
            focus: "city",
            currentStep: "someKey",
            previousValue: "Timboek Old",
            changedFields: { city: "Timboek Old" }
          }
        };

        expect(guidanceModeDatasource.step).toHaveBeenCalledTimes(1);
        expect(guidanceModeDatasource.step).toHaveBeenCalledWith({ flowId: '42', recordId: '1337' }, request);

        //Check that the error object was set
        expect(guidanceController.guidanceData.suggestions).toEqual({ "city": [timbuktuSuggestion] });

        //Check that the suggestionsObserver was called and contains elements for all properties in the model
        //Count is 2 since this is also called in the init fase and on watch
        expect(suggestionsObserver.setSuggestions).toHaveBeenCalledTimes(3);
        expect(suggestionsObserver.setSuggestions).toHaveBeenCalledWith({
          city: [timbuktuSuggestion]
        });
      });

      it('should call the handleRequestStepResponse when a form object is returned.', function () {
        const verweggistanOostSuggestion = { "label": "Verweggistan-Oost", "model": { "city": "Verweggistan-Oost" } };
        const fakeGuidanceMode = {
          model: guidanceData.model,
          form: { "a": "b" },
          grid: {},
          suggestions: {
            city: [verweggistanOostSuggestion]
          },
          errors: {
            city: [
              "That is not a real place."
            ]
          }
        };

        //Send back data with a company name and number but without a city.
        spyOn(guidanceModeDatasource, 'step').and.callFake(mockHelpers.resolvedPromise($q, fakeGuidanceMode));

        //Set the initial model with a city that is not present in the backend response later
        guidanceData.model = {
          "name": "",
          "number": "",
          "city": "Verweggistan"
        };

        compile();

        spyOn(guidanceFormObserver, 'stepChangeOccurred');
        spyOn(guidanceFormObserver, 'setFullModel');
        spyOn(validationObserver, 'setErrors');
        spyOn(suggestionsObserver, 'setSuggestions');

        guidanceFormObserver.requestNextStep();
        $rootScope.$apply();

        $timeout.flush();

        //Check that the guidanceFormObserver was called
        expect(guidanceFormObserver.stepChangeOccurred).toHaveBeenCalledTimes(2);
        const changeStepArg = guidanceFormObserver.stepChangeOccurred.calls.argsFor(1)[0];
        expect(changeStepArg).toEqual(fakeGuidanceMode);

        expect(guidanceFormObserver.setFullModel).toHaveBeenCalledTimes(1);
        expect(guidanceFormObserver.setFullModel).toHaveBeenCalledWith(fakeGuidanceMode.model);

        //Check that the validationObserver and suggestionsObserver are called
        //Count is 3 since this is also called in the init fase and on watch
        expect(validationObserver.setErrors).toHaveBeenCalledTimes(3);
        expect(validationObserver.setErrors).toHaveBeenCalledWith({
          city: ["That is not a real place."]
        });

        //Count is 2 since this is also called in the init fase and on watch
        expect(suggestionsObserver.setSuggestions).toHaveBeenCalledTimes(3);
        expect(suggestionsObserver.setSuggestions).toHaveBeenCalledWith({
          city: [verweggistanOostSuggestion]
        });
      });

      it('should not call the commandHandler if the processCommand property is false', function () {
        const fakeGuidanceMode = {
          model: guidanceData.model,
          suggestions: {},
          errors: {},
          processCommand: false
        };

        //Send back data with a company name and number but without a city.
        spyOn(guidanceModeDatasource, 'step').and.callFake(mockHelpers.resolvedPromise($q, fakeGuidanceMode));

        compile();

        guidanceFormObserver.requestNextStep();
        $rootScope.$apply();

        $timeout.flush();

        expect(commandHandler.handle).not.toHaveBeenCalled();
      });

      it('should not call the commandHandler if the processCommand property is not defined', function () {
        const fakeGuidanceMode = {
          model: guidanceData.model,
          suggestions: {},
          errors: {}
        };

        //Send back data with a company name and number but without a city.
        spyOn(guidanceModeDatasource, 'step').and.callFake(mockHelpers.resolvedPromise($q, fakeGuidanceMode));

        compile();

        guidanceFormObserver.requestNextStep();
        $rootScope.$apply();

        $timeout.flush();

        expect(commandHandler.handle).not.toHaveBeenCalled();
      });

      it('should call the commandHandler if the processCommand property is true', function () {
        const command = {
          "command": "navigate",
          "arguments": {
            "linkTo": "dashboard",
            "params": {
              "mainMenuKey": "sales-marketing",
              "dashboardId": "leads"
            }
          }
        };

        const fakeGuidanceMode = {
          model: guidanceData.model,
          suggestions: {},
          errors: {},
          processCommand: true,
          command
        };

        //Send back data with a company name and number but without a city.
        spyOn(guidanceModeDatasource, 'step').and.callFake(mockHelpers.resolvedPromise($q, fakeGuidanceMode));

        compile();

        guidanceFormObserver.requestNextStep();
        $rootScope.$apply();

        $timeout.flush();

        expect(commandHandler.handle).toHaveBeenCalledWith(command);
      });

      it('should handleValidationResponse when we have parentModel.', function () {
        //Set the initial model with a city that is not present in the backend response later
        guidanceData.model = {
          company: {
            name: "",
            number: "",
            city: ""
          },
          first_name: "Ken"
        };

        guidanceData.parentModel = {
          car: "Ford Focus RSRX"
        };

        //Send back data with a company name and number but without a city.
        spyOn(guidanceModeDatasource, 'step').and.callFake(mockHelpers.resolvedPromise($q, {
          model: {
            company: {
              name: "Exesser",
              number: "0123456789"
            }
          },
          parentModel: {
            country: "USA"
          }
        }));

        const guidanceController = compile();

        const fakeGuidanceAction = { focus: "city", value: "Antwerpen" };
        guidanceData.model.company.city = 'Antwerpen';

        // Call observer directly as 'mocking' this is to much extra work for such a simple observer.
        guidanceFormObserver.formValueChanged(fakeGuidanceAction);
        guidanceFormObserver.formValueChanged(fakeGuidanceAction);
        $rootScope.$apply();

        const request = {
          model: {
            company: {
              name: "",
              number: "",
              city: "Antwerpen"
            },
            first_name: "Ken"
          },
          parentModel: {
            car: "Ford Focus RSRX"
          },
          progress: guidanceController.guidanceData.progress,
          action: {
            event: "CHANGED",
            focus: "city",
            currentStep: "someKey",
            previousValue: null,
            changedFields: {
              company: {
                name: '',
                number: '',
                city: ''
              }
            }
          }
        };

        expect(guidanceModeDatasource.step).toHaveBeenCalledTimes(1);
        expect(guidanceModeDatasource.step).toHaveBeenCalledWith({ flowId: '42', recordId: '1337' }, request);
        //Check that the model has been extended with the company name and number and that the city is still set.
        expect(guidanceController.guidanceData.model).toEqual({
          company: {
            name: "Exesser",
            number: "0123456789",
            city: "Antwerpen"
          },
          first_name: "Ken"
        });

        expect(guidanceController.guidanceData.parentModel).toEqual({
          car: "Ford Focus RSRX",
          country: "USA"
        });
      });

      it('should handleRequestStepResponse when we have parentModel.', function () {
        //Set the initial model with a city that is not present in the backend response later
        guidanceData.model = {
          company: {
            name: "",
            number: "",
            city: ""
          },
          first_name: "Ken"
        };
        guidanceData.modelKey = 'drivers';
        guidanceData.modelId = 'driver-1';

        guidanceData.parentModel = {
          car: "Ford Focus RSRX",
          drivers: {
            "driver-1": {}
          }
        };

        //Send back data with a company name and number but without a city.
        spyOn(guidanceModeDatasource, 'step').and.callFake(mockHelpers.resolvedPromise($q, {
          model: {
            company: {
              name: "Exesser",
              number: "0123456789"
            }
          },
          form: [],
          progress: guidanceData.progress
        }));

        const guidanceController = compile();

        const fakeGuidanceAction = { focus: "city", value: "Antwerpen" };
        guidanceData.model.company.city = 'Antwerpen';

        // Call observer directly as 'mocking' this is to much extra work for such a simple observer.
        guidanceFormObserver.formValueChanged(fakeGuidanceAction);
        guidanceFormObserver.formValueChanged(fakeGuidanceAction);
        $rootScope.$apply();

        const request = {
          model: {
            company: {
              name: "",
              number: "",
              city: "Antwerpen"
            },
            first_name: "Ken"
          },
          parentModel: {
            car: "Ford Focus RSRX",
            drivers: {
              "driver-1": {}
            }
          },
          progress: guidanceController.guidanceData.progress,
          action: {
            event: "CHANGED",
            focus: "city",
            currentStep: "someKey",
            previousValue: null,
            changedFields: {
              company: {
                name: '',
                number: '',
                city: ''
              }
            }
          }
        };

        expect(guidanceModeDatasource.step).toHaveBeenCalledTimes(1);
        expect(guidanceModeDatasource.step).toHaveBeenCalledWith({ flowId: '42', recordId: '1337' }, request);
        //Check that the model has been extended with the company name and number and that the city is still set.
        expect(guidanceController.guidanceData.model).toEqual({
          company: {
            name: "Exesser",
            number: "0123456789"
          }
        });

        expect(guidanceController.guidanceData.parentModel).toEqual({
          car: "Ford Focus RSRX",
          drivers: {
            "driver-1": {
              company: {
                name: "Exesser",
                number: "0123456789"
              }
            }
          }
        });
      });

      describe("guidanceGuardian behavior", function () {
        it('should inform the "guidanceGuardian" to start guarding when "enableNavigateAwayGuard" is true', function () {
          // Reject so nothing happens
          spyOn(guidanceModeDatasource, 'step').and.callFake(mockHelpers.rejectedPromise($q));

          spyOn(guidanceGuardian, 'startGuard');

          compile(false, true);

          const fakeGuidanceAction = { focus: "city", value: "Verweggistan" };
          // Call observer directly as 'mocking' this is to much extra work for such a simple observer.
          guidanceFormObserver.formValueChanged(fakeGuidanceAction);
          $rootScope.$apply();

          expect(guidanceGuardian.startGuard).toHaveBeenCalledTimes(1);
          expect(guidanceGuardian.startGuard).toHaveBeenCalledWith(guidanceFormObserver);
        });

        it('should not inform the "guidanceGuardian" to start guarding when "enableNavigateAwayGuard" is false', function () {
          // Reject so nothing happens
          spyOn(guidanceModeDatasource, 'step').and.callFake(mockHelpers.rejectedPromise($q));

          spyOn(guidanceGuardian, 'startGuard');

          compile(false, false);

          const fakeGuidanceAction = { focus: "city", value: "Verweggistan" };

          // Call observer directly as 'mocking' this is to much extra work for such a simple observer.
          guidanceFormObserver.formValueChanged(fakeGuidanceAction);
          $rootScope.$apply();

          expect(guidanceGuardian.startGuard).not.toHaveBeenCalled();
        });
      });
    });

    describe('debounce rejection flow', function () {
      it('should not trigger changes within DEBOUNCE_TIME of each other', function (done) {
        spyOn(guidanceModeDatasource, 'step').and.callFake(mockHelpers.resolvedPromise($q, {}));

        const guidanceController = compile();

        //Two similar events are fired rapidly after each other
        const fakeGuidanceAction1 = { focus: "city", value: "Antwerpe" };
        const fakeGuidanceAction2 = { focus: "city", value: "Antwerpen" };

        guidanceFormObserver.formValueChanged(fakeGuidanceAction1);
        $rootScope.$apply();

        guidanceController.guidanceData.model.city = "Antwerpen";
        guidanceFormObserver.formValueChanged(fakeGuidanceAction2);
        $rootScope.$apply();

        setTimeout(function () {
          //After the timeout is done only the second action is sent to the backend
          expect(guidanceModeDatasource.step).toHaveBeenCalledTimes(1);
          expect(guidanceModeDatasource.step).toHaveBeenCalledWith({ flowId: '42', recordId: '1337' }, {
            model: guidanceController.guidanceData.model,
            progress: guidanceController.guidanceData.progress,
            action: {
              event: "CHANGED",
              focus: "city",
              currentStep: "someKey",
              previousValue: null,
              changedFields: { city: null }
            }
          });

          done();
        }, DEBOUNCE_TIME + 50);
      });
    });
  });

  describe('setConfirmGuidanceCallback', function () {
    it('should call the guidanceModeDatasource.step function with the action passed in the guidanceFormObserver.confirmGuidance and end guard if response is a command', function (done) {
      spyOn(guidanceModeDatasource, 'step').and.callFake(mockHelpers.resolvedPromise($q, { command: "nothing" }));
      spyOn(guidanceGuardian, 'endGuard');

      const guidanceController = compile(true, false, true);
      guidanceController.flowId = "AwesomeFlow";
      guidanceController.recordId = "42";

      const promise = guidanceFormObserver.confirmGuidance(CONFIRM_ACTION.CONFIRM);
      $rootScope.$apply();

      expect(guidanceModeDatasource.step).toHaveBeenCalledTimes(1);
      expect(guidanceModeDatasource.step).toHaveBeenCalledWith({ flowId: "AwesomeFlow", recordId: "42" }, {
        model: guidanceController.guidanceData.model,
        progress: guidanceController.guidanceData.progress,
        action: {
          event: CONFIRM_ACTION.CONFIRM,
          currentStep: 'someKey'
        },
        route: {
          linkTo: 'my-route',
          params: { parma1: "my-route-param-1" }
        }
      });

      expect(guidanceGuardian.endGuard).toHaveBeenCalledTimes(1);
      expect(guidanceGuardian.endGuard).toHaveBeenCalledWith(guidanceFormObserver);

      //Test that we get a promise back
      promise.then(function () {
        done();
      });
      $rootScope.$apply();
    });

    it('should call the guidanceModeDatasource.step function with the action passed in the guidanceFormObserver.confirmGuidance and reject the promise if response is NOT a command', function () {
      spyOn(guidanceModeDatasource, 'step').and.callFake(mockHelpers.resolvedPromise($q, { errors: "bla" }));
      spyOn(guidanceGuardian, 'endGuard');
      spyOn($q, 'reject').and.callFake(mockHelpers.rejectedPromise($q));

      const guidanceController = compile();
      guidanceController.flowId = "AwesomeFlow";
      guidanceController.recordId = "42";

      guidanceFormObserver.confirmGuidance(CONFIRM_ACTION.CONFIRM);
      $rootScope.$apply();

      expect(guidanceModeDatasource.step).toHaveBeenCalledTimes(1);
      expect(guidanceModeDatasource.step).toHaveBeenCalledWith({ flowId: "AwesomeFlow", recordId: "42" }, {
        model: guidanceController.guidanceData.model,
        progress: guidanceController.guidanceData.progress,
        action: {
          event: CONFIRM_ACTION.CONFIRM,
          currentStep: 'someKey'
        }
      });

      expect(guidanceGuardian.endGuard).not.toHaveBeenCalled();

      expect($q.reject).toHaveBeenCalledTimes(1);
      expect($q.reject).toHaveBeenCalledWith('The response is NOT a command.');
    });
  });

  describe('setRequestNextStepCallback and the backend response contains a form', function () {
    let fakeGuidanceMode;

    beforeEach(function () {
      fakeGuidanceMode = { progress: 'fakeProgress', grid: {}, form: { fake: "fake" } };
      spyOn(guidanceModeDatasource, 'step').and.callFake(mockHelpers.resolvedPromise($q, fakeGuidanceMode));
    });

    it('should call the guidanceModeDatasource.step function with the action passed in the guidanceFormObserver.requestNextStep', function () {
      const guidanceController = compile();
      guidanceController.flowId = "AwesomeFlow";
      guidanceController.recordId = "42";
      guidanceController.forms = [{ key: "mock" }];

      //Flush early so we keep our spy counters on 0 before we trigger the next step
      $timeout.flush();

      //Trigger action
      guidanceFormObserver.requestNextStep();

      expect(guidanceModeDatasource.step).toHaveBeenCalledTimes(1);
      expect(guidanceModeDatasource.step).toHaveBeenCalledWith({ flowId: 'AwesomeFlow', recordId: '42' }, {
        model: guidanceController.guidanceData.model,
        progress: guidanceController.guidanceData.progress,
        action: { event: "NEXT-STEP", currentStep: "someKey" }
      });

      $rootScope.$apply();

      const expectedGuidanceMode = angular.copy(fakeGuidanceMode);
      expectedGuidanceMode.errors = undefined;
      expectedGuidanceMode.suggestions = undefined;

      expect(guidanceController.guidanceData).toEqual(expectedGuidanceMode);
      expect(guidanceController.forms).toEqual([]);
    });

    it('should call the guidanceModeDatasource.step function without the current step if progress is undefined', function () {
      const guidanceController = compile();
      guidanceController.flowId = "AwesomeFlow";
      guidanceController.recordId = "42";
      guidanceController.forms = [{ key: "mock" }];

      //Set the progress to undefined
      guidanceController.guidanceData.progress = undefined;

      //Flush early so we keep our spy counters on 0 before we trigger the next step
      $timeout.flush();

      //Trigger action
      guidanceFormObserver.requestNextStep();

      expect(guidanceModeDatasource.step).toHaveBeenCalledTimes(1);
      expect(guidanceModeDatasource.step).toHaveBeenCalledWith({ flowId: 'AwesomeFlow', recordId: '42' }, {
        model: guidanceController.guidanceData.model,
        progress: undefined,
        action: { event: "NEXT-STEP" }
      });

      $rootScope.$apply();

      const expectedGuidanceMode = angular.copy(fakeGuidanceMode);
      expectedGuidanceMode.errors = undefined;
      expectedGuidanceMode.suggestions = undefined;

      expect(guidanceController.guidanceData).toEqual(expectedGuidanceMode);
      expect(guidanceController.forms).toEqual([]);
    });

    it('should call the guidanceModeDatasource.step function without the current step if progress.steps is undefined', function () {
      const guidanceController = compile();
      guidanceController.flowId = "AwesomeFlow";
      guidanceController.recordId = "42";
      guidanceController.forms = [{ key: "mock" }];

      //Set the progress.steps to undefined
      guidanceController.guidanceData.progress.steps = undefined;

      //Flush early so we keep our spy counters on 0 before we trigger the next step
      $timeout.flush();

      //Trigger action
      guidanceFormObserver.requestNextStep();

      expect(guidanceModeDatasource.step).toHaveBeenCalledTimes(1);
      expect(guidanceModeDatasource.step).toHaveBeenCalledWith({ flowId: 'AwesomeFlow', recordId: '42' }, {
        model: guidanceController.guidanceData.model,
        progress: {
          steps: undefined
        },
        action: { event: "NEXT-STEP" }
      });

      $rootScope.$apply();

      const expectedGuidanceMode = angular.copy(fakeGuidanceMode);
      expectedGuidanceMode.errors = undefined;
      expectedGuidanceMode.suggestions = undefined;

      expect(guidanceController.guidanceData).toEqual(expectedGuidanceMode);
      expect(guidanceController.forms).toEqual([]);
    });

    it('should call guidanceFormObserver.stepChangeOccurred with the fakeGuidanceMode', function () {
      const guidanceController = compile();
      guidanceController.flowId = "AwesomeFlow";
      guidanceController.recordId = "42";

      //Flush early so we keep our spy counters on 0 before we trigger the next step
      $timeout.flush();

      spyOn(guidanceFormObserver, 'stepChangeOccurred');

      //Trigger action
      progressBarObserver.clicked("someKey");
      $rootScope.$apply();

      // Check notifyObserversOfStepChange
      $timeout.flush();

      const expectedGuidanceMode = angular.copy(fakeGuidanceMode);
      expectedGuidanceMode.errors = undefined;
      expectedGuidanceMode.suggestions = undefined;

      expect(guidanceFormObserver.stepChangeOccurred).toHaveBeenCalledTimes(1);
      expect(guidanceFormObserver.stepChangeOccurred).toHaveBeenCalledWith(expectedGuidanceMode);
    });

    it('should call progressBarObserver.setProgressMetadata with the fakeProgress if guidanceController.informProgressBarObserver is true', function () {
      const guidanceController = compile();
      guidanceController.flowId = "AwesomeFlow";
      guidanceController.recordId = "42";

      //Flush early so we keep our spy counters on 0 before we trigger the next step
      $timeout.flush();

      spyOn(progressBarObserver, 'setProgressMetadata');

      //Trigger action
      guidanceFormObserver.requestNextStep();
      $rootScope.$apply();

      // Check notifyObserversOfStepChange
      $timeout.flush();

      expect(progressBarObserver.setProgressMetadata).toHaveBeenCalledTimes(1);
      expect(progressBarObserver.setProgressMetadata).toHaveBeenCalledWith('fakeProgress');
    });

    it('should call not call the progressBarObserver.setProgressMetadata with the fakeProgress if guidanceController.informProgressBarObserver is false', function () {
      const guidanceController = compile(false);
      guidanceController.flowId = "AwesomeFlow";
      guidanceController.recordId = "42";

      //Flush early so we keep our spy counters on 0 before we trigger the next step
      $timeout.flush();

      spyOn(progressBarObserver, 'setProgressMetadata');

      //Trigger action
      guidanceFormObserver.requestNextStep();
      $rootScope.$apply();

      // Check notifyObserversOfStepChange
      $timeout.flush();

      expect(progressBarObserver.setProgressMetadata).not.toHaveBeenCalled();
    });
  });

  describe('setRequestNextStepCallback and the backend response does not contains a form', function () {
    // if we don't receive a form from backend we assume that the current step has some errors
    // and the response contains only errors and suggestions
    let fakeGuidanceMode;

    beforeEach(function () {
      fakeGuidanceMode = {
        suggestions: {},
        errors: {
          city: [
            "That is not a real place."
          ]
        }
      };

      spyOn(guidanceModeDatasource, 'step').and.callFake(mockHelpers.resolvedPromise($q, fakeGuidanceMode));
    });

    it('should call the guidanceModeDatasource.step function with the action passed in the guidanceFormObserver.requestNextStep', function () {

      const guidanceController = compile();
      guidanceController.flowId = "AwesomeFlow";
      guidanceController.recordId = "42";
      guidanceController.forms = [{ key: "mock" }];

      //Flush early so we keep our spy counters on 0 before we trigger the next step
      $timeout.flush();

      spyOn(validationObserver, 'setErrors');
      spyOn(guidanceFormObserver, 'stepChangeOccurred');

      //Trigger action
      guidanceFormObserver.requestNextStep();

      expect(guidanceModeDatasource.step).toHaveBeenCalledTimes(1);
      expect(guidanceModeDatasource.step).toHaveBeenCalledWith({ flowId: 'AwesomeFlow', recordId: '42' }, {
        model: guidanceController.guidanceData.model,
        progress: guidanceController.guidanceData.progress,
        action: { event: "NEXT-STEP", currentStep: "someKey" }
      });

      $rootScope.$apply();
      $timeout.flush();

      expect(validationObserver.setErrors).toHaveBeenCalledTimes(2);
      expect(validationObserver.setErrors).toHaveBeenCalledWith({
        city: ["That is not a real place."]
      });

      expect(guidanceFormObserver.stepChangeOccurred).not.toHaveBeenCalled();
    });
  });

  describe('registerClickCallback', function () {
    it('should call the guidanceModeAction.step function with the stepId given in progressBarObserver.clicked', function () {
      const fakeGuidanceMode = { progress: 'fakeProgress', grid: {}, form: {} };
      spyOn(guidanceModeDatasource, 'step').and.callFake(mockHelpers.resolvedPromise($q, fakeGuidanceMode));

      const guidanceController = compile();
      guidanceController.flowId = "AwesomeFlow";
      guidanceController.recordId = "42";
      guidanceController.forms = [{ key: "mock" }];

      //Flush early so we keep our spy counters on 0 before we trigger the next step
      $timeout.flush();

      spyOn(guidanceFormObserver, 'stepChangeOccurred');
      spyOn(progressBarObserver, 'setProgressMetadata');

      //Trigger action
      progressBarObserver.clicked("fakeStepId");

      expect(guidanceModeDatasource.step).toHaveBeenCalledTimes(1);
      expect(guidanceModeDatasource.step).toHaveBeenCalledWith({ flowId: 'AwesomeFlow', recordId: '42' }, {
        model: guidanceController.guidanceData.model,
        progress: guidanceController.guidanceData.progress,
        action: { event: "NEXT-STEP-FORCED", nextStep: "fakeStepId", currentStep: "someKey" }
      });

      $rootScope.$apply();

      const expectedGuidanceMode = angular.copy(fakeGuidanceMode);
      expectedGuidanceMode.errors = undefined;
      expectedGuidanceMode.suggestions = undefined;

      expect(guidanceController.guidanceData).toEqual(expectedGuidanceMode);

      // Check notifyObserversOfStepChange
      $timeout.flush();

      expect(guidanceFormObserver.stepChangeOccurred).toHaveBeenCalledTimes(1);
      expect(guidanceFormObserver.stepChangeOccurred).toHaveBeenCalledWith(expectedGuidanceMode);

      expect(progressBarObserver.setProgressMetadata).toHaveBeenCalledTimes(1);
      expect(progressBarObserver.setProgressMetadata).toHaveBeenCalledWith('fakeProgress');

      expect(guidanceController.forms).toEqual([]);
    });
  });

  function compile(informProgressBarObserver = true, enableNavigateAwayGuard = false, enableGuidanceRecovery = false) {
    //Set the correct properties on the scope
    scope.guidanceFormObserver = guidanceFormObserver;
    scope.guidanceData = guidanceData;
    scope.flowId = '42';
    scope.recordId = '1337';

    element = angular.element(createTemplate(informProgressBarObserver, enableNavigateAwayGuard, enableGuidanceRecovery));
    element = $compile(element)(scope);
    $rootScope.$apply();
    return element.controller("guidance");
  }
});
