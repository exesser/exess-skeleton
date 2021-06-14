'use strict';

describe('Guidance presenter: embedded-guidance', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let $rootScope;
  let $compile;
  let $q;

  let guidanceModeDatasource;
  let guidanceFormObserverFactory;
  let guidanceFormObserver;
  let guidanceModeBackendState;
  let commandHandler;
  let CONFIRM_ACTION;

  let validationObserver;
  let suggestionsObserver;
  let element;
  let $stateParams;

  let guidanceMode;

  beforeEach(inject(function (_$rootScope_, _$compile_, $state, _guidanceModeDatasource_, _guidanceFormObserverFactory_,
                              GuidanceFormObserver, _CONFIRM_ACTION_, _$q_, _commandHandler_, ValidationObserver,
                              SuggestionsObserver, _$stateParams_, _guidanceModeBackendState_) {
    $rootScope = _$rootScope_;
    $compile = _$compile_;
    guidanceModeDatasource = _guidanceModeDatasource_;
    guidanceFormObserverFactory = _guidanceFormObserverFactory_;
    guidanceModeBackendState = _guidanceModeBackendState_;
    CONFIRM_ACTION = _CONFIRM_ACTION_;
    commandHandler = _commandHandler_;
    $q = _$q_;
    $stateParams = _$stateParams_;

    validationObserver = new ValidationObserver();
    suggestionsObserver = new SuggestionsObserver();

    guidanceFormObserver = new GuidanceFormObserver();
    spyOn(guidanceFormObserverFactory, 'createGuidanceFormObserver').and.returnValue(guidanceFormObserver);

    guidanceMode = {
      "errors": {},
      "suggestions": {},
      "grid": {
        "cssClasses": ["has-default-margins"],
        "columns": [{
          "size": "1-2",
          "rows": [{
            "size": "1-1",
            "type": "basicFormlyForm",
            "options": {
              "formKey": "a"
            }
          }]
        }, {
          "size": "1-2",
          "rows": [{
            "size": "1-1",
            "type": "basicFormlyForm",
            "options": {
              "formKey": "b"
            }
          }]
        }]
      },
      "guidance": {
        "title": "Create quote ",
        "loadingMessage": "Saving quote"
      },
      "form": {
        "a": {
          "fields": [{
            "id": "firstname",
            "label": "Lastname",
            "type": "TextField"
          }]
        },
        "b": {
          "fields": [{
            "id": "lastname",
            "label": "Firstname",
            "type": "TextField"
          }]
        }
      },
      "model": {
        "firstname": "Maarten",
        "lastname": "Hus",
        "dwp|company": { "account|name": "wky" }
      },
      "progress": {},
      "step": {
        "willSave": true
      }
    };

    mockHelpers.blockUIRouter($state);
  }));

  function compile({ template }) {
    const scope = $rootScope.$new();

    scope.controller = {
      guidanceParams: {
        useFilters: true
      }
    };

    element = angular.element(template);
    element = $compile(element)(scope);

    $rootScope.$apply();

    expect(guidanceFormObserverFactory.createGuidanceFormObserver).toHaveBeenCalledTimes(1);
  }

  describe('Guidance loads normally', function () {
    beforeEach(function () {
      $stateParams.modelKey = 'my-guidance';
      spyOn(guidanceModeDatasource, 'get').and.callFake(mockHelpers.resolvedPromise($q, guidanceMode));
      spyOn(guidanceModeBackendState, 'addBackendIsBusyFor');
    });

    afterEach(function () {
      expect(guidanceModeBackendState.addBackendIsBusyFor).not.toHaveBeenCalled();
      expect(guidanceModeDatasource.get).toHaveBeenCalledTimes(1);
      expect(guidanceModeDatasource.get).toHaveBeenCalledWith({
        recordType: "Quote",
        flowAction: "ReadOnly",
        flowId: "QuoteDeletion",
        recordId: "12",
        modelKey: 'my-guidance-QuoteDeletion'
      }, {
        useFilters: true
      });
    });

    it('should initialize the "guidance" component correctly', function () {
      const template = `
        <embedded-guidance
          record-type="Quote"
          flow-action="ReadOnly"
          flow-id="QuoteDeletion"
          record-id="12"
          guidance-params="controller.guidanceParams"
          show-primary-button="true"
          primary-button-title="CONFIRM"
          default-title="Quote"
          title-expression="{% lastname %} - {% firstname %}">
        </embedded-guidance>
      `;

      compile({ template });

      const guidanceElement = $(element.find('guidance'));

      expect(guidanceElement.attr('inform-progress-bar-observer')).toBe('false');
      expect(guidanceElement.attr('enable-navigate-away-guard')).toBe('embeddedGuidanceController.enableNavigateAwayGuard');
    });

    it('should show the guidance when loaded and hide the loading indicator', function () {
      const template = `
          <embedded-guidance
            record-type="Quote"
            flow-action="ReadOnly"
            flow-id="QuoteDeletion"
            record-id="12"
            guidance-params="controller.guidanceParams"
            show-primary-button="true"
            primary-button-title="CONFIRM"
            default-title="Quote"
            title-expression="{% lastname %} - {% firstname %}">
          </embedded-guidance>
      `;

      compile({ template });

      expect(element.find('guidance').length).toBe(1);

      const loadingElement = $(element.find('div.loading'));
      expect(loadingElement.hasClass('ng-hide')).toBe(true);
    });

    describe('primary button behavior', function () {
      it('should show the primary button when "show-primary-button" is true', function () {
        const template = `
          <embedded-guidance
            record-type="Quote"
            flow-action="ReadOnly"
            flow-id="QuoteDeletion"
            record-id="12"
            guidance-params="controller.guidanceParams"
            show-primary-button="true"
            primary-button-title="CONFIRM"
            default-title="Quote"
            title-expression="{% lastname %} - {% firstname %}">
          </embedded-guidance>
        `;

        compile({ template });

        const primaryButtonElement = $(element.find('a.button'));
        expect(primaryButtonElement.text()).toBe('CONFIRM');
        expect(primaryButtonElement.hasClass('ng-hide')).toBe(false);
      });

      it('should hide the primary button when "show-primary-button" is false', function () {
        const template = `
          <embedded-guidance
            record-type="Quote"
            flow-action="ReadOnly"
            flow-id="QuoteDeletion"
            record-id="12"
            guidance-params="controller.guidanceParams"
            show-primary-button="false"
            default-title="Quote"
            title-expression="{% lastname %} - {% firstname %}">
          </embedded-guidance>
        `;

        compile({ template });

        const primaryButtonElement = $(element.find('a.button'));
        expect(primaryButtonElement.hasClass('ng-hide')).toBe(true);
      });

      it('should disable / enable the primary button when form is invalid / valid', function () {
        spyOn(guidanceFormObserver, 'setFormValidityUpdateCallback');

        const template = `
          <embedded-guidance
            record-type="Quote"
            flow-action="ReadOnly"
            flow-id="QuoteDeletion"
            record-id="12"
            guidance-params="controller.guidanceParams"
            show-primary-button="true"
            primary-button-title="CONFIRM"
            default-title="Quote"
            title-expression="{% lastname %} - {% firstname %}">
          </embedded-guidance>
        `;

        compile({ template });

        expect(guidanceFormObserver.setFormValidityUpdateCallback).toHaveBeenCalledTimes(1);

        const formValidityUpdateCallback = guidanceFormObserver.setFormValidityUpdateCallback.calls.argsFor(0)[0];

        const primaryButtonElement = $(element.find('a.button'));

        //Indicate that the form is invalid
        formValidityUpdateCallback(false);
        $rootScope.$apply();

        expect(primaryButtonElement.attr('disabled')).toBe('disabled');

        //Indicate that the form is valid
        formValidityUpdateCallback(true);
        $rootScope.$apply();

        expect(primaryButtonElement.attr('disabled')).toBe(undefined);
      });

      it('should when the primary button is clicked save the guidance, and send the response to the command handler', function () {
        spyOn(guidanceFormObserver, 'setFormValidityUpdateCallback');
        // One of the results could be a navigate command.
        const navigateCommand = {
          "command": "navigate",
          "arguments": {
            "route": "dashboard",
            "params": {
              "mainMenuKey": "sales-marketing",
              "dashboardId": "leads"
            }
          }
        };

        const deferred = $q.defer();
        spyOn(guidanceFormObserver, 'confirmGuidance').and.returnValue(deferred.promise);
        spyOn(commandHandler, 'handle');

        const template = `
          <embedded-guidance
            record-type="Quote"
            flow-action="ReadOnly"
            flow-id="QuoteDeletion"
            record-id="12"
            guidance-params="controller.guidanceParams"
            show-primary-button="true"
            primary-button-title="CONFIRM"
            default-title="Quote"
            title-expression="{% lastname %} - {% firstname %}">
          </embedded-guidance>
        `;

        compile({ template });

        // make sure the form is valid otherwise the primary button is disabled
        const formValidityUpdateCallback = guidanceFormObserver.setFormValidityUpdateCallback.calls.argsFor(0)[0];
        formValidityUpdateCallback(true);

        // The loading element should be invisible since the guidance is loaded
        const loadingElement = $(element.find('div.loading'));
        expect(loadingElement.hasClass('ng-hide')).toBe(true);

        // The guidance should be visible
        expect(element.find('guidance').length).toBe(1);

        // Next click the primary button, and check if confirmGuidance is called
        const primaryButtonElement = $(element.find('a.button'));
        primaryButtonElement.click();
        $rootScope.$apply();

        expect(guidanceFormObserver.confirmGuidance).toHaveBeenCalledTimes(1);
        expect(guidanceFormObserver.confirmGuidance).toHaveBeenCalledWith(CONFIRM_ACTION.CONFIRM);

        // We should be loading right now since the promise is not yet resolved.
        expect(loadingElement.hasClass('ng-hide')).toBe(false);
        expect(loadingElement.find('h5').text()).toBe('Saving quote');

        // Because we are loading expect the guidance to be hidden.
        expect(element.find('.form__group').hasClass('ng-hide')).toBe(true);

        // Now resolve the promise to end the loading
        deferred.resolve(navigateCommand);
        $rootScope.$apply();

        // Expect the loading indicator to be gone.
        expect(loadingElement.hasClass('ng-hide')).toBe(true);

        // Expect the guidance to be visible.
        expect(element.find('guidance').length).toBe(1);
        expect(element.find('.form__group').hasClass('ng-hide')).toBe(false);

        expect(commandHandler.handle).toHaveBeenCalledTimes(1);
        expect(commandHandler.handle).toHaveBeenCalledWith(navigateCommand);
      });

      it('should when the primary button is clicked and the back-end errors out hide the loading indicator', function () {
        const deferred = $q.defer();
        spyOn(guidanceFormObserver, 'confirmGuidance').and.returnValue(deferred.promise);
        spyOn(guidanceFormObserver, 'setFormValidityUpdateCallback');

        const template = `
          <embedded-guidance
            record-type="Quote"
            flow-action="ReadOnly"
            flow-id="QuoteDeletion"
            record-id="12"
            guidance-params="controller.guidanceParams"
            show-primary-button="true"
            primary-button-title="CONFIRM"
            default-title="Quote"
            title-expression="{% lastname %} - {% firstname %}">
          </embedded-guidance>
        `;

        compile({ template });

        // make sure the form is valid otherwise the primary button is disabled
        const formValidityUpdateCallback = guidanceFormObserver.setFormValidityUpdateCallback.calls.argsFor(0)[0];
        formValidityUpdateCallback(true);

        // The loading element should be invisible since the guidance is loaded
        const loadingElement = $(element.find('div.loading'));
        expect(loadingElement.hasClass('ng-hide')).toBe(true);

        // The guidance should be visible
        expect(element.find('guidance').length).toBe(1);

        // Next click the primary button, and check if confirmGuidance is called
        const primaryButtonElement = $(element.find('a.button'));
        primaryButtonElement.click();
        $rootScope.$apply();

        expect(guidanceFormObserver.confirmGuidance).toHaveBeenCalledTimes(1);
        expect(guidanceFormObserver.confirmGuidance).toHaveBeenCalledWith(CONFIRM_ACTION.CONFIRM);

        // We should be loading right now since the promise is not yet resolved.
        expect(loadingElement.hasClass('ng-hide')).toBe(false);
        expect(loadingElement.find('h5').text()).toBe('Saving quote');

        // Because we are loading expect the guidance to be hidden.
        expect(element.find('.form__group').hasClass('ng-hide')).toBe(true);

        // Now reject the promise to end the loading
        deferred.reject();
        $rootScope.$apply();

        // Expect the loading indicator to be gone.
        expect(loadingElement.hasClass('ng-hide')).toBe(true);

        // Expect the guidance to be visible.
        expect(element.find('guidance').length).toBe(1);
        expect(element.find('.form__group').hasClass('ng-hide')).toBe(false);
      });

      it('should not save when the primary button is clicked and the form is invalid', function () {

        const deferred = $q.defer();
        spyOn(guidanceFormObserver, 'confirmGuidance').and.returnValue(deferred.promise);
        spyOn(guidanceFormObserver, 'setFormValidityUpdateCallback');

        const template = `
          <embedded-guidance
            record-type="Quote"
            flow-action="ReadOnly"
            flow-id="QuoteDeletion"
            record-id="12"
            guidance-params="controller.guidanceParams"
            show-primary-button="true"
            primary-button-title="CONFIRM"
            default-title="Quote"
            title-expression="{% lastname %} - {% firstname %}">
          </embedded-guidance>
        `;

        compile({ template });

        // make sure the form is invalid -> primary button is disabled
        const formValidityUpdateCallback = guidanceFormObserver.setFormValidityUpdateCallback.calls.argsFor(0)[0];
        formValidityUpdateCallback(false);

        // The loading element should be invisible since the guidance is loaded
        const loadingElement = $(element.find('div.loading'));
        expect(loadingElement.hasClass('ng-hide')).toBe(true);

        // The guidance should be visible
        expect(element.find('guidance').length).toBe(1);

        // Next click the primary button, and check if confirmGuidance is NOT called
        const primaryButtonElement = $(element.find('a.button'));
        primaryButtonElement.click();
        $rootScope.$apply();

        expect(guidanceFormObserver.confirmGuidance).not.toHaveBeenCalled();

        // The loading element should still be invisible since the form is invalid
        expect(loadingElement.hasClass('ng-hide')).toBe(true);

        // Expect the guidance to be visible.
        expect(element.find('guidance').length).toBe(1);
      });
    });

    describe('title behavior', function () {
      it('should set the title using the title-expression', function () {
        const template = `
          <embedded-guidance
            record-type="Quote"
            flow-action="ReadOnly"
            flow-id="QuoteDeletion"
            record-id="12"
            guidance-params="controller.guidanceParams"
            show-primary-button="false"
            default-title="Quote"
            title-expression="{% lastname %} {% firstname %} - {%dwp|company.account|name%}">
          </embedded-guidance>
        `;

        compile({ template });

        expect(element.find('h2').text()).toContain('Hus Maarten - wky');
      });

      it('should use the default title when title-expression is empty', function () {
        const template = `
          <embedded-guidance
            record-type="Quote"
            flow-action="ReadOnly"
            flow-id="QuoteDeletion"
            record-id="12"
            guidance-params="controller.guidanceParams"
            show-primary-button="false"
            default-title="Quote"
            title-expression="">
          </embedded-guidance>
        `;

        compile({ template });

        expect(element.find('h2').text()).toContain('Quote');
      });
    });
  });

  describe('Guidance is loading', function () {
    beforeEach(function () {
      _.unset($stateParams, 'modelKey');
    });

    it('should show the loading indicator when loading and hide the guidance', function () {
      const deferred = $q.defer();
      spyOn(guidanceModeDatasource, 'get').and.returnValue(deferred.promise);

      const template = `
          <embedded-guidance
            record-type="Quote"
            flow-action="ReadOnly"
            flow-id="QuoteDeletion"
            record-id="12"
            show-primary-button="true"
            guidance-params="controller.guidanceParams"
            primary-button-title="CONFIRM"
            default-title="Quote"
            title-expression="{% lastname %} - {% firstname %}">
          </embedded-guidance>
        `;

      compile({ template });

      expect(element.find('guidance').length).toBe(0);

      const loadingElement = $(element.find('div.loading'));
      expect(loadingElement.hasClass('ng-hide')).toBe(false);

      expect(guidanceModeDatasource.get).toHaveBeenCalledTimes(1);
      expect(guidanceModeDatasource.get).toHaveBeenCalledWith({
        recordType: "Quote",
        flowAction: "ReadOnly",
        flowId: "QuoteDeletion",
        recordId: "12"
      }, {
        useFilters: true
      });
    });
  });

  describe('Guidance is embedded in another guidance', function () {
    let stepChangeCallback;
    let errorsChangedCallback;
    let suggestionsChangedCallback;
    let scope;

    beforeEach(function () {
      guidanceMode.model.field1 = "field1-guidance";
      guidanceMode.parentModel = { field2: "field2-parent" };
      spyOn(guidanceModeDatasource, 'get').and.callFake(mockHelpers.resolvedPromise($q, guidanceMode));
      spyOn(guidanceModeBackendState, 'addBackendIsBusyFor');
      spyOn(guidanceModeBackendState, 'removeBackendIsBusyFor');

      //We need to return a fake deregister function to invoke when the scope is destroyed.
      spyOn(guidanceFormObserver, 'addStepChangeOccurredCallback').and.returnValue(_.noop);

      const template = `
          <embedded-guidance
            record-type="Quote"
            flow-action="ReadOnly"
            flow-id="QuoteDeletion"
            record-id="12"
            show-primary-button="true"
            guidance-params="controller.guidanceParams"
            primary-button-title="CONFIRM"
            default-title="Quote"
            title-expression="{% lastname %} - {% firstname %}"
            model-key="connections"
            model-id="123-456-789">
          </embedded-guidance>
        `;

      scope = $rootScope.$new();

      const guidanceFormObserverAccessorElement = mockHelpers.createGuidanceFormObserverAccessor({
        $compile,
        $rootScope,
        guidanceFormObserver,
        validationObserver,
        suggestionsObserver
      });

      scope.controller = {
        guidanceParams: {
          model: {
            name: "wky",
            "field1": "field1-guidanceParams",
            "field2": "field2-guidanceParams"
          }
        }
      };

      element = angular.element(template);
      element = $compile(element)(scope);
      guidanceFormObserverAccessorElement.append(element);

      spyOn(validationObserver, 'registerErrorsChangedCallback');
      spyOn(suggestionsObserver, 'registerSuggestionsChangedCallback');

      $rootScope.$apply();

      expect(guidanceFormObserver.addStepChangeOccurredCallback).toHaveBeenCalledTimes(1);
      stepChangeCallback = guidanceFormObserver.addStepChangeOccurredCallback.calls.argsFor(0)[0];

      expect(validationObserver.registerErrorsChangedCallback).toHaveBeenCalledTimes(1);
      errorsChangedCallback = validationObserver.registerErrorsChangedCallback.calls.argsFor(0)[0];

      expect(suggestionsObserver.registerSuggestionsChangedCallback).toHaveBeenCalledTimes(1);
      suggestionsChangedCallback = suggestionsObserver.registerSuggestionsChangedCallback.calls.argsFor(0)[0];
    });

    it('should call guidanceModeDatasource.get() with the right params only once after we have the parentModel', function () {
      const parentGuidance = {
        model: {
          company: "wky",
          connections: {
            "123-456-789": {
              "field1": "field1-parentModel"
            }
          }
        }
      };

      expect(guidanceModeDatasource.get).not.toHaveBeenCalled();
      expect(guidanceModeBackendState.addBackendIsBusyFor).toHaveBeenCalledTimes(1);
      expect(guidanceModeBackendState.removeBackendIsBusyFor).not.toHaveBeenCalled();

      stepChangeCallback(parentGuidance);
      $rootScope.$apply();

      expect(guidanceModeBackendState.removeBackendIsBusyFor).toHaveBeenCalledTimes(1);
      expect(guidanceModeDatasource.get).toHaveBeenCalledTimes(1);

      stepChangeCallback(parentGuidance);
      $rootScope.$apply();

      expect(guidanceModeDatasource.get).toHaveBeenCalledTimes(1);
      expect(guidanceModeDatasource.get).toHaveBeenCalledWith(
        {
          recordType: 'Quote',
          flowId: 'QuoteDeletion',
          flowAction: 'ReadOnly',
          recordId: '12'
        },
        {
          model: {
            name: 'wky',
            field1: 'field1-parentModel',
            field2: "field2-guidanceParams"
          },
          parentModel: {
            company: 'wky',
            connections: {
              "123-456-789": {
                field1: 'field1-parentModel'
              }
            }
          }
        }
      );

      expect(guidanceMode.model).toEqual({
        firstname: 'Maarten',
        lastname: 'Hus',
        "dwp|company": { "account|name": "wky" },
        field1: 'field1-guidance'
      });

      expect(scope.controller.guidanceParams.parentModel).toEqual({
        company: "wky",
        connections: {
          "123-456-789": {
            field1: "field1-guidance",
            firstname: 'Maarten',
            lastname: 'Hus',
            "dwp|company": { "account|name": "wky" }
          }
        },
        field2: 'field2-parent'
      });
    });

    it('should add the connection key on parentModel', function () {
      const parentGuidance = {
        model: {
          company: "wky"
        }
      };

      expect(guidanceModeDatasource.get).not.toHaveBeenCalled();

      stepChangeCallback(parentGuidance);
      $rootScope.$apply();

      expect(guidanceModeDatasource.get).toHaveBeenCalledTimes(1);

      stepChangeCallback(parentGuidance);
      $rootScope.$apply();

      expect(guidanceModeDatasource.get).toHaveBeenCalledTimes(1);
      expect(guidanceModeDatasource.get).toHaveBeenCalledWith(
        {
          recordType: 'Quote',
          flowId: 'QuoteDeletion',
          flowAction: 'ReadOnly',
          recordId: '12'
        },
        {
          model: {
            name: 'wky',
            field1: 'field1-guidanceParams',
            field2: "field2-guidanceParams"
          },
          parentModel: {
            company: 'wky'
          }
        }
      );

      expect(guidanceMode.model).toEqual({
        firstname: 'Maarten',
        lastname: 'Hus',
        "dwp|company": { "account|name": "wky" },
        field1: 'field1-guidance'
      });

      expect(scope.controller.guidanceParams.parentModel).toEqual({
        company: "wky",
        connections: {
          "123-456-789": {
            field1: "field1-guidance",
            firstname: 'Maarten',
            lastname: 'Hus',
            "dwp|company": { "account|name": "wky" }
          }
        },
        field2: 'field2-parent'
      });
    });

    it('should require the errors from parent guidance', function () {
      spyOn(validationObserver, 'getErrorsForKey').and.returnValue({ "firstname": ["Name is not ok"] });

      const parentGuidance = {
        model: {
          company: "wky",
          connections: {
            "123-456-789": {
              "field1": "field1-parentModel"
            }
          }
        }
      };

      stepChangeCallback(parentGuidance);
      $rootScope.$apply();

      errorsChangedCallback();
      expect(validationObserver.getErrorsForKey).toHaveBeenCalledTimes(1);
      expect(validationObserver.getErrorsForKey).toHaveBeenCalledWith('connections.123-456-789');

      errorsChangedCallback();
      expect(validationObserver.getErrorsForKey).toHaveBeenCalledTimes(2);
    });

    it('should require the suggestions from parent guidance', function () {
      spyOn(suggestionsObserver, 'getSuggestionsForKey').and.returnValue({ "firstname": ["Bogdan", "Maarten"] });

      const parentGuidance = {
        model: {
          company: "wky",
          connections: {
            "123-456-789": {
              "field1": "field1-parentModel"
            }
          }
        }
      };

      stepChangeCallback(parentGuidance);
      $rootScope.$apply();

      suggestionsChangedCallback();
      expect(suggestionsObserver.getSuggestionsForKey).toHaveBeenCalledTimes(1);
      expect(suggestionsObserver.getSuggestionsForKey).toHaveBeenCalledWith('connections.123-456-789');

      suggestionsChangedCallback();
      expect(suggestionsObserver.getSuggestionsForKey).toHaveBeenCalledTimes(2);
    });
  });
});
