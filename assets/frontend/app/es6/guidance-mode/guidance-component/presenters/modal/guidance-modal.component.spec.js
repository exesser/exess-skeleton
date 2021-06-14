'use strict';

describe('Component: modal', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let $rootScope;
  let $q;
  let $log;
  let $timeout;
  let CONFIRM_ACTION;

  let element;

  let guidanceModalObserver;
  let guidanceFormObserverFactory;
  let guidanceFormObserver;
  let guidanceGuardian;
  let actionDatasource;

  let registerOpenModalCallback;

  let modalData;

  const template = '<guidance-modal></guidance-modal>';

  beforeEach(inject(function ($compile, _$rootScope_, _guidanceModalObserver_, _guidanceFormObserverFactory_,
                              GuidanceFormObserver, $controller, $state, _$q_, _$timeout_, _$log_, _CONFIRM_ACTION_,
                              _guidanceGuardian_, _actionDatasource_) {
    $rootScope = _$rootScope_;
    $q = _$q_;
    $log = _$log_;
    $timeout = _$timeout_;
    CONFIRM_ACTION = _CONFIRM_ACTION_;
    guidanceGuardian = _guidanceGuardian_;
    actionDatasource = _actionDatasource_;

    guidanceModalObserver = _guidanceModalObserver_;

    guidanceFormObserverFactory = _guidanceFormObserverFactory_;
    guidanceFormObserver = new GuidanceFormObserver();
    spyOn(guidanceFormObserverFactory, 'createGuidanceFormObserver').and.returnValue(guidanceFormObserver);

    mockHelpers.blockUIRouter($state);

    spyOn(guidanceModalObserver, 'registerOpenModalCallback');
    spyOn(guidanceFormObserver, 'stepChangeOccurred');

    const scope = $rootScope.$new();
    element = angular.element(template);
    element = $compile(element)(scope);
    $rootScope.$apply();

    expect(guidanceModalObserver.registerOpenModalCallback).toHaveBeenCalledTimes(1);
    registerOpenModalCallback = guidanceModalObserver.registerOpenModalCallback.calls.argsFor(0)[0];

    modalData = {
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
  }));

  it('should open the modal and render the grid', function () {
    expect(registerOpenModalCallback).not.toBe(null);

    const promise = registerOpenModalCallback(modalData);

    //Check that we indeed got a defer as return.
    expect(_.isFunction(promise.then)).toBe(true);

    $rootScope.$apply();
    $timeout.flush();

    expect(element.find('section.view__modal').length).toBe(1);
    expect(element.find('basic-formly-form').length).toBe(1);

    const guidanceElement = $(element.find('guidance'));

    expect(guidanceElement.attr('inform-progress-bar-observer')).toBe('false');
    expect(guidanceElement.attr('enable-navigate-away-guard')).toBe('true');
  });

  it('should hide the confirm button when the label is unset', function () {
    const modalDataWithoutLabel = angular.copy(modalData);
    _.unset(modalDataWithoutLabel, 'confirmLabel');
    registerOpenModalCallback(modalDataWithoutLabel);

    $rootScope.$apply();
    $timeout.flush();

    expect(element.find(".modal__actions > a.button").hasClass('ng-hide')).toBe(true);
  });

  it('should hide the confirm button when the label is empty', function () {
    const modalDataEmptyLabel = angular.copy(modalData);
    modalDataEmptyLabel.confirmLabel = "";
    registerOpenModalCallback(modalDataEmptyLabel);

    $rootScope.$apply();
    $timeout.flush();

    expect(element.find(".modal__actions > a.button").hasClass('ng-hide')).toBe(true);
  });

  it('should hide the cancel button when there is no cancelLabel', function () {
    registerOpenModalCallback(modalData);

    $rootScope.$apply();
    $timeout.flush();

    expect($(element.find(".modal__actions > a.button")[1]).hasClass('ng-hide')).toBe(true);
  });

  it('should show the cancel button when there is a cancelLabel', function () {
    const modalDataWithCancelLabel = angular.copy(modalData);
    modalDataWithCancelLabel.cancelLabel = 'NO';
    registerOpenModalCallback(modalDataWithCancelLabel);

    $rootScope.$apply();
    $timeout.flush();

    const cancelButton = $(element.find(".modal__actions > a.button")[1]);
    expect(cancelButton.hasClass('ng-hide')).toBe(false);
    expect(cancelButton.text()).toContain('NO');
  });

  describe('when confirm is clicked', function () {
    it('should take the specified confirmAction and send it to guidanceFormObserver.confirmGuidance', function () {
      spyOn(guidanceFormObserver, 'confirmGuidance').and.returnValue($q.defer().promise);
      registerOpenModalCallback(modalData, CONFIRM_ACTION.CONFIRM_CREATE_LIST_ROW);
      $rootScope.$apply();

      $(element.find(".modal__actions > a.button")[0]).click();
      $rootScope.$apply();

      expect(guidanceFormObserver.confirmGuidance).toHaveBeenCalledTimes(1);
      expect(guidanceFormObserver.confirmGuidance).toHaveBeenCalledWith(CONFIRM_ACTION.CONFIRM_CREATE_LIST_ROW);
    });

    it('should take the default confirmAction and send it to guidanceFormObserver.confirmGuidance', function () {
      spyOn(guidanceFormObserver, 'confirmGuidance').and.returnValue($q.defer().promise);
      registerOpenModalCallback(modalData);
      $rootScope.$apply();

      $(element.find(".modal__actions > a.button")[0]).click();
      $rootScope.$apply();

      expect(guidanceFormObserver.confirmGuidance).toHaveBeenCalledTimes(1);
      expect(guidanceFormObserver.confirmGuidance).toHaveBeenCalledWith(CONFIRM_ACTION.CONFIRM);
    });

    it("should resolve the promise with the model as argument and reset the modal when there are no errors", function () {
      const confirmGuidancePromise = $q.defer();

      spyOn(guidanceFormObserver, 'confirmGuidance').and.returnValue(confirmGuidancePromise.promise);

      //There are no errors in the form.
      modalData.errors = {};
      modalData.model = {
        "firstName": "Jan"
      };
      const promise = registerOpenModalCallback(modalData, CONFIRM_ACTION.CONFIRM);
      $rootScope.$apply();

      //It should resolve the promise with the model as argument when calling 'confirm'
      let promiseResolved = false;
      promise.then(function (response) {
        expect(response.action).toBe("navigate");
        expect(_.isObject(response.arguments)).toBe(true);
        expect(response.arguments).toEqual({
          "route": "dashboard",
          "params": {
            "mainMenuKey": "sales-marketing",
            "dashboardId": "leads"
          }
        });
        promiseResolved = true;
      });

      $(element.find(".modal__actions > a.button")[0]).click();
      $rootScope.$apply();

      const loading = $(element.find('.modal__actions > div.badge.badge-position.loading'));
      expect(loading.hasClass('ng-hide')).toBe(false);

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
      $timeout.flush();

      expect(promiseResolved).toBe(true);

      expect(guidanceFormObserver.confirmGuidance).toHaveBeenCalledTimes(1);
      expect(guidanceFormObserver.confirmGuidance).toHaveBeenCalledWith(CONFIRM_ACTION.CONFIRM);

      expectModalReset();
    });

    it("should not resolve the promise or reset the modal when there are errors", function () {
      spyOn(guidanceFormObserver, 'setFormValidityUpdateCallback');
      const promise = registerOpenModalCallback(modalData);
      $rootScope.$apply();
      expect(guidanceFormObserver.setFormValidityUpdateCallback).toHaveBeenCalledTimes(1);
      const formValidityUpdateCallback = guidanceFormObserver.setFormValidityUpdateCallback.calls.argsFor(0)[0];

      //Indicate that the form is invalid
      formValidityUpdateCallback(false);

      //It should not resolve the promise with the model as argument when calling 'confirm'
      promise.then(function () {
        fail("This promise should not have been resolved.");
      });

      $(element.find(".modal__actions > a.button")[0]).click();
      $rootScope.$apply();

      //We expect modalData not to have changed and the modal still to be open.
      expect(element.find('section.view__modal').length).toBe(1);
      expect(element.find('basic-formly-form').length).toBe(1);
    });

    it("should hide the loading indicator if the back-end errors out", function () {
      const confirmGuidancePromise = $q.defer();

      spyOn(guidanceFormObserver, 'confirmGuidance').and.returnValue(confirmGuidancePromise.promise);

      //There are no errors in the form.
      modalData.errors = {};
      modalData.model = {
        "firstName": "Jan"
      };

      registerOpenModalCallback(modalData, CONFIRM_ACTION.CONFIRM);
      $rootScope.$apply();

      $(element.find(".modal__actions > a.button")[0]).click();
      $rootScope.$apply();

      const loading = $(element.find('.modal__actions > div.badge.badge-position.loading'));
      expect(loading.hasClass('ng-hide')).toBe(false);

      confirmGuidancePromise.reject();
      $rootScope.$apply();

      expect(loading.hasClass('ng-hide')).toBe(true);
    });
  });

  describe('when cancel is clicked', function () {
    it('should log when the cancelCommandKey is missing', function () {
      const modalDataWithCancelLabel = angular.copy(modalData);
      modalDataWithCancelLabel.cancelLabel = 'NO';
      registerOpenModalCallback(modalDataWithCancelLabel);
      spyOn($log, 'error');
      $rootScope.$apply();
      $timeout.flush();

      const cancelButton = $(element.find(".modal__actions > a.button")[1]);
      cancelButton.click();
      expect($log.error).toHaveBeenCalledTimes(1);
      expect($log.error).toHaveBeenCalledWith('Action is not configured correctly! When you have a `cancelLabel` you also need a `cancelCommandKey`.');
    });

    it('should call actionDatasource.performAndHandle when we have cancelCommandKey', function () {
      const modalDataWithCancelLabel = angular.copy(modalData);
      modalDataWithCancelLabel.cancelLabel = 'NO';
      modalDataWithCancelLabel.cancelCommandKey = 'theCancelCommandKey';
      registerOpenModalCallback(modalDataWithCancelLabel);
      spyOn(actionDatasource, 'performAndHandle');

      $rootScope.$apply();
      $timeout.flush();

      const cancelButton = $(element.find(".modal__actions > a.button")[1]);
      cancelButton.click();
      expect(actionDatasource.performAndHandle).toHaveBeenCalledTimes(1);
      expect(actionDatasource.performAndHandle).toHaveBeenCalledWith({ id: "theCancelCommandKey" });
    });
  });

  describe('when close (X) is clicked', function () {

    it("should reject the promise and afterwards reset the modal", function () {
      spyOn(guidanceGuardian, 'endGuard');

      const promise = registerOpenModalCallback(modalData, CONFIRM_ACTION.CONFIRM);
      $rootScope.$apply();

      //It should reject the promise
      let promiseRejected = false;
      promise.catch(function () {
        promiseRejected = true;
      });

      element.find("a.button.icon-close").click();
      $rootScope.$apply();
      expect(promiseRejected).toBe(true);
      expectModalReset();

      expect(guidanceGuardian.endGuard).toHaveBeenCalledTimes(1);
      expect(guidanceGuardian.endGuard).toHaveBeenCalledWith(guidanceFormObserver);
    });

    it('should call actionDatasource.performAndHandle when we have closeCommandKey', function () {
      const modalDataWithCloseCommand = angular.copy(modalData);
      modalDataWithCloseCommand.closeCommandKey = 'theCloseCommandKey';
      registerOpenModalCallback(modalDataWithCloseCommand);
      spyOn(actionDatasource, 'performAndHandle');

      $rootScope.$apply();
      $timeout.flush();

      element.find("a.button.icon-close").click();
      expect(actionDatasource.performAndHandle).toHaveBeenCalledTimes(1);
      expect(actionDatasource.performAndHandle).toHaveBeenCalledWith({ id: "theCloseCommandKey" });
    });
  });

  describe('warnings', function () {
    it('should be displayed if they are set in the modalData', function () {
      modalData.warningText = "Attention, this action can't be reversed.";
      registerOpenModalCallback(modalData);
      $rootScope.$apply();

      expect(element.find(element.find(".is-warning")).length).toBe(1);
      expect(element.find(".is-warning").text().trim()).toBe("Attention, this action can't be reversed.");
    });

    it('should not be displayed if a warningText is an empty string in the modalData', function () {
      modalData.warningText = "";
      registerOpenModalCallback(modalData);
      $rootScope.$apply();

      expect(element.find(element.find(".is-warning")).length).toBe(0);
    });

    it('should not be displayed if a warningText is undefined in the modalData', function () {
      modalData.warningText = undefined;
      registerOpenModalCallback(modalData);
      $rootScope.$apply();

      expect(element.find(element.find(".is-warning")).length).toBe(0);
    });

    it('should not be displayed if a warningText is null in the modalData', function () {
      modalData.warningText = null;
      registerOpenModalCallback(modalData);
      $rootScope.$apply();

      expect(element.find(element.find(".is-warning")).length).toBe(0);
    });
  });

  describe('setFormValidityUpdateCallback', function () {
    it('should disable the confirm button if some forms are invalid', function () {
      spyOn(guidanceFormObserver, 'setFormValidityUpdateCallback');
      registerOpenModalCallback(modalData);
      $rootScope.$apply();
      const formValidityUpdateCallback = guidanceFormObserver.setFormValidityUpdateCallback.calls.argsFor(0)[0];

      //Initially the form is valid
      expect(element.find(".modal__actions > a.button").attr("disabled")).toBe(undefined);

      //When the form becomes invalid the confirm button is disabled
      formValidityUpdateCallback(false);
      $rootScope.$apply();
      expect(element.find(".modal__actions > a.button").attr("disabled")).toBe("disabled");

      //When the form becomes valid again the confirm button is re-enabled
      formValidityUpdateCallback(true);
      $rootScope.$apply();
      expect(element.find(".modal__actions > a.button").attr("disabled")).toBe(undefined);
    });
  });

  function expectModalReset() {
    expect(element.find('section.view__modal').length).toBe(0);
  }
});
