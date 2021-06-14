'use strict';

describe('Component: miniGuidanceMode', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let $rootScope;
  let $q;
  let $timeout;

  let miniGuidanceModeObserver;
  let guidanceFormObserver;
  let topActionState;
  let sidebarObserver;
  let SIDEBAR_ELEMENT;
  let CONFIRM_ACTION;
  let miniGuidanceModeStatus;
  let DEBOUNCE_TIME;

  let element;
  let registerOpenMiniGuidanceCallback;
  let guidanceData;
  let spyOnMiniGuidanceModeStatusGetGuidanceData;

  // Keeps track of lodashes original debounce function
  let lodashDebounce;

  // Keeps track of all the functions that went through the debounce.
  let debouncedFunctions;

  const template = '<mini-guidance-mode></mini-guidance-mode>';

  beforeEach(inject(function ($compile, _$rootScope_, _miniGuidanceModeObserver_, _topActionState_, _sidebarObserver_,
                              _SIDEBAR_ELEMENT_, _CONFIRM_ACTION_, guidanceFormObserverFactory, GuidanceFormObserver,
                              $controller, $state, _$q_, _$timeout_, _miniGuidanceModeStatus_, _DEBOUNCE_TIME_) {
    $rootScope = _$rootScope_;
    $q = _$q_;
    $timeout = _$timeout_;

    miniGuidanceModeObserver = _miniGuidanceModeObserver_;
    topActionState = _topActionState_;
    sidebarObserver = _sidebarObserver_;
    SIDEBAR_ELEMENT = _SIDEBAR_ELEMENT_;
    CONFIRM_ACTION = _CONFIRM_ACTION_;
    DEBOUNCE_TIME = _DEBOUNCE_TIME_;
    miniGuidanceModeStatus = _miniGuidanceModeStatus_;

    guidanceFormObserver = new GuidanceFormObserver();
    spyOn(guidanceFormObserverFactory, 'createGuidanceFormObserver').and.returnValue(guidanceFormObserver);

    spyOn(miniGuidanceModeObserver, 'registerOpenMiniGuidanceCallback');
    spyOn(guidanceFormObserver, 'stepChangeOccurred');
    spyOn(topActionState, 'setMiniGuidanceCanBeOpened').and.callThrough();
    spyOn(sidebarObserver, 'openSidebarElement');

    spyOn(miniGuidanceModeStatus, 'setGuidanceData');
    spyOn(miniGuidanceModeStatus, 'updateModel');
    spyOnMiniGuidanceModeStatusGetGuidanceData = spyOn(miniGuidanceModeStatus, 'getGuidanceData').and.returnValue({});

    const scope = $rootScope.$new();
    element = angular.element(template);
    element = $compile(element)(scope);

    guidanceData = {
      "title": "UNQUALIFY LEAD",
      "confirmLabel": "UNQUALIFY LEAD",
      "grid": {
        "columns": [{
          "size": "1-1",
          "hasMargin": false,
          "rows": [{
            "size": "1-1",
            "type": "basicFormlyForm",
            "options": {
              "formKey": "a"
            }
          }]
        }]
      },
      "form": {
        "type_c": "DEFAULT",
        "id": "8b42beb4-5ab2-018f-bb1f-56a8afa918ba",
        "key_c": "CLS1",
        "name": "Create Lead Step 1",
        "active": false,
        "activatable": true,
        "disabled": false,
        "progressPercentage": 50,
        "a": {
          "fields": [{
            "id": "first_name",
            "label": "First name",
            "default": "",
            "type": "LargeTextField",
            "module": "leads",
            "moduleField": "first_name"
          }]
        }
      },
      "model": {
        "first_name": "Sander"
      },
      "progress": {
        "steps": [
          {
            "key_c": "someKey",
            "active": true
          }
        ]
      },
      flowId: 42,
      recordId: 1337
    };

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

  describe('before onInit', function () {
    it(`should open the guidance stored in session`, function () {
      spyOnMiniGuidanceModeStatusGetGuidanceData.and.returnValue(guidanceData);

      $rootScope.$apply();

      expect(element.find('basic-formly-form').length).toBe(1);

      expect(topActionState.setMiniGuidanceCanBeOpened).toHaveBeenCalledTimes(2);
      expect(topActionState.setMiniGuidanceCanBeOpened).toHaveBeenCalledWith(true);

      expect(sidebarObserver.openSidebarElement).toHaveBeenCalledTimes(1);
      expect(sidebarObserver.openSidebarElement).toHaveBeenCalledWith(SIDEBAR_ELEMENT.MINI_GUIDANCE);

      const guidanceElement = $(element.find('guidance'));

      expect(guidanceElement.attr('inform-progress-bar-observer')).toBe('false');
      expect(guidanceElement.attr('enable-navigate-away-guard')).toBe('false');
    });
  });

  describe('after onInit', function () {

    beforeEach(function () {
      $rootScope.$apply();

      expect(registerOpenMiniGuidanceCallback).not.toBe(null);
      expect(topActionState.setMiniGuidanceCanBeOpened).toHaveBeenCalledTimes(1);
      expect(topActionState.setMiniGuidanceCanBeOpened).toHaveBeenCalledWith(false);

      expect(miniGuidanceModeObserver.registerOpenMiniGuidanceCallback).toHaveBeenCalledTimes(1);
      registerOpenMiniGuidanceCallback = miniGuidanceModeObserver.registerOpenMiniGuidanceCallback.calls.argsFor(0)[0];
    });

    it(`should open the miniGuidance and render the grid`, function () {

      const promise = registerOpenMiniGuidanceCallback(guidanceData);
      $rootScope.$apply();

      //Check that we indeed got a defer as return.
      expect(_.isFunction(promise.then)).toBe(true);

      $rootScope.$apply();
      $timeout.flush();

      expect(element.find('basic-formly-form').length).toBe(1);

      expect(topActionState.setMiniGuidanceCanBeOpened).toHaveBeenCalledTimes(2);
      expect(topActionState.setMiniGuidanceCanBeOpened).toHaveBeenCalledWith(true);

      expect(sidebarObserver.openSidebarElement).toHaveBeenCalledTimes(1);
      expect(sidebarObserver.openSidebarElement).toHaveBeenCalledWith(SIDEBAR_ELEMENT.MINI_GUIDANCE);

      const guidanceElement = $(element.find('guidance'));

      expect(guidanceElement.attr('inform-progress-bar-observer')).toBe('false');
      expect(guidanceElement.attr('enable-navigate-away-guard')).toBe('false');

    });

    it(`should update the model in session when is changing`, function () {
      registerOpenMiniGuidanceCallback(guidanceData);
      $rootScope.$apply();
      $timeout.flush();

      expect(miniGuidanceModeStatus.updateModel).not.toHaveBeenCalled();

      guidanceData.model = {
        "first_name": "Wky"
      };
      $rootScope.$apply();

      expect(miniGuidanceModeStatus.updateModel).toHaveBeenCalledTimes(1);
      expect(miniGuidanceModeStatus.updateModel).toHaveBeenCalledWith(guidanceData.model);
    });

    it(`should first rest the miniGuidance if is open and we open a new one`, function () {
      spyOn(topActionState, 'miniGuidanceCanBeOpened').and.returnValue(true);
      spyOn(sidebarObserver, 'closeAllSidebarElements');

      registerOpenMiniGuidanceCallback(guidanceData);
      $rootScope.$apply();

      expect(miniGuidanceModeStatus.setGuidanceData).toHaveBeenCalledTimes(1);
      expect(miniGuidanceModeStatus.setGuidanceData).toHaveBeenCalledWith({});

      expect(topActionState.setMiniGuidanceCanBeOpened).toHaveBeenCalledTimes(2);
      expect(topActionState.setMiniGuidanceCanBeOpened).toHaveBeenCalledWith(false);

      expect(sidebarObserver.closeAllSidebarElements).toHaveBeenCalledTimes(1);

      $timeout.flush();

      expect(miniGuidanceModeStatus.setGuidanceData).toHaveBeenCalledTimes(2);
      expect(miniGuidanceModeStatus.setGuidanceData).toHaveBeenCalledWith(guidanceData);
    });

    describe('when confirm is clicked', function () {
      it("should resolve the promise with the model as argument, and reset the miniGuidance when there are no errors", function () {
        spyOn(sidebarObserver, 'closeAllSidebarElements');

        const confirmGuidancePromise = $q.defer();

        spyOn(guidanceFormObserver, 'confirmGuidance').and.returnValue(confirmGuidancePromise.promise);

        //There are no errors in the form.
        guidanceData.errors = {};
        guidanceData.model = {
          "firstName": "Jan"
        };

        const promise = registerOpenMiniGuidanceCallback(guidanceData);
        $rootScope.$apply();
        $timeout.flush();

        //It should resolve the promise with the model as argument when calling 'confirm'
        let promiseResolved = false;
        promise.then(function (response) {
          expect(response.action).toBe("navigate");
          expect(response.arguments).toEqual({
            "route": "dashboard",
            "params": {
              "mainMenuKey": "sales-marketing",
              "dashboardId": "leads"
            }
          });

          promiseResolved = true;
        });

        element.find(".form__footer button.button").click();
        $rootScope.$apply();

        expect(element.find('.form__footer > div.badge.badge-position.loading').length).toBe(1);

        confirmGuidancePromise.resolve({
          "action": "navigate",
          "arguments": {
            "route": "dashboard",
            "params": {
              "mainMenuKey": "sales-marketing",
              "dashboardId": "leads"
            }
          }
        });
        $rootScope.$apply();

        expect(promiseResolved).toBe(true);
        expect(element.find('.form__footer > div.badge.badge-position.loading').length).toBe(0);

        expect(guidanceFormObserver.confirmGuidance).toHaveBeenCalledTimes(1);
        expect(guidanceFormObserver.confirmGuidance).toHaveBeenCalledWith(CONFIRM_ACTION.CONFIRM);

        //The basic-formly-form should be removed as the guidanceData the grid is drawn from is gone.
        expect(element.find('basic-formly-form').length).toBe(0);

        expect(topActionState.setMiniGuidanceCanBeOpened).toHaveBeenCalledTimes(3);
        expect(topActionState.setMiniGuidanceCanBeOpened).toHaveBeenCalledWith(false);

        expect(sidebarObserver.closeAllSidebarElements).toHaveBeenCalledTimes(1);
      });

      it("should not resolve the promise or reset the miniGuidance when there are errors", function () {
        spyOn(guidanceFormObserver, 'setFormValidityUpdateCallback');

        const promise = registerOpenMiniGuidanceCallback(guidanceData);
        $rootScope.$apply();
        $timeout.flush();

        expect(guidanceFormObserver.setFormValidityUpdateCallback).toHaveBeenCalledTimes(1);
        const formValidityUpdateCallback = guidanceFormObserver.setFormValidityUpdateCallback.calls.argsFor(0)[0];

        //Indicate that the form is invalid
        formValidityUpdateCallback(false);

        //It should not resolve the promise with the model as argument when calling 'confirm'
        promise.then(function () {
          fail("This promise should not have been resolved.");
        });

        //Try to confirm the invalid form
        element.find(".form__footer button.button").click();
        $rootScope.$apply();

        //We expect the miniGuidance still to be open.
        expect(element.find('basic-formly-form').length).toBe(1);
      });

      it("should hide the loading indicator if the back-end errors out", function () {
        spyOn(sidebarObserver, 'closeAllSidebarElements');

        const confirmGuidancePromise = $q.defer();

        spyOn(guidanceFormObserver, 'confirmGuidance').and.returnValue(confirmGuidancePromise.promise);

        //There are no errors in the form.
        guidanceData.errors = {};
        guidanceData.model = {
          "firstName": "Jan"
        };

        registerOpenMiniGuidanceCallback(guidanceData);
        $rootScope.$apply();
        $timeout.flush();

        element.find(".form__footer button.button").click();
        $rootScope.$apply();

        expect(element.find('.form__footer > div.badge.badge-position.loading').length).toBe(1);

        confirmGuidancePromise.reject();
        $rootScope.$apply();

        expect(element.find('.form__footer > div.badge.badge-position.loading').length).toBe(0);
      });
    });

    it("should reject the promise when calling 'cancel' and afterwards reset the miniGuidance", function () {
      spyOn(sidebarObserver, 'closeAllSidebarElements');

      const promise = registerOpenMiniGuidanceCallback(guidanceData);
      $rootScope.$apply();
      $timeout.flush();

      //It should reject the promise
      let promiseRejected = false;
      promise.catch(function () {
        promiseRejected = true;
      });

      element.find(".form__footer button.button-light").click();
      $rootScope.$apply();

      expect(promiseRejected).toBe(true);
      expect(element.find('basic-formly-form').length).toBe(0);
      expect(sidebarObserver.closeAllSidebarElements).toHaveBeenCalledTimes(1);
    });

    describe('setFormValidityUpdateCallback', function () {
      it('should disable the confirm button if some forms are invalid', function () {
        spyOn(guidanceFormObserver, 'setFormValidityUpdateCallback');
        registerOpenMiniGuidanceCallback(guidanceData, CONFIRM_ACTION.CONFIRM);
        $rootScope.$apply();
        $timeout.flush();
        const formValidityUpdateCallback = guidanceFormObserver.setFormValidityUpdateCallback.calls.argsFor(0)[0];

        //Initially the form is valid
        expect(element.find(".form__footer button.button").prop('disabled')).toBe(false);

        //When the form becomes invalid the confirm button is disabled
        formValidityUpdateCallback(false);
        $rootScope.$apply();
        expect(element.find(".form__footer button.button").prop('disabled')).toBe(true);

        //When the form becomes valid again the confirm button is re-enabled
        formValidityUpdateCallback(true);
        $rootScope.$apply();
        expect(element.find(".form__footer button.button").prop('disabled')).toBe(false);
      });
    });
  });
});
