'use strict';

describe('Form type: radio', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let scope;
  let element;

  let $rootScope;
  let $compile;

  let validationObserver;
  let guidanceFormObserver;

  let guidanceModeBackendState;
  let elementIdGenerator;
  let ACTION_EVENT;

  const template = '<formly-form form="form" model="model" fields="fields"/>';

  let radioOpenElement;
  let radioClosedElement;

  beforeEach(inject(function (_$rootScope_, _$compile_, $state, ValidationObserver, GuidanceFormObserver,
                              _guidanceModeBackendState_, _ACTION_EVENT_, _elementIdGenerator_) {
    $rootScope = _$rootScope_;
    $compile = _$compile_;

    guidanceModeBackendState = _guidanceModeBackendState_;
    elementIdGenerator = _elementIdGenerator_;
    ACTION_EVENT = _ACTION_EVENT_;

    validationObserver = new ValidationObserver();

    guidanceFormObserver = new GuidanceFormObserver();
    spyOn(guidanceFormObserver, 'formValueChanged');
    spyOn(elementIdGenerator, 'generateId').and.callFake(function (id) {
      return id + "-fake-id";
    });

    mockHelpers.blockUIRouter($state);

    scope = $rootScope.$new();

    scope.model = {
      door: {
        status: "CLOSED"
      }
    };
    scope.fields = [
      {
        wrapper: "label-left-fields-right-wrapper",
        templateOptions: {
          label: "Door status"
        },
        fieldGroup: [
          {
            id: "door.status",
            key: "door.status",
            type: "radio",
            templateOptions: {
              noBackendInteraction: false,
              label: "Open",
              value: "OPEN",
              disabled: false,
              readonly: false
            }
          },
          {
            id: "door.status_CLOSED",
            key: "door.status",
            type: "radio",
            templateOptions: {
              noBackendInteraction: false,
              label: "Closed",
              value: "CLOSED",
              disabled: false,
              readonly: false
            }
          }
        ]
      }];

    const guidanceFormObserverAccessorElement = mockHelpers.createGuidanceFormObserverAccessor({
      $compile,
      $rootScope,
      validationObserver,
      guidanceFormObserver
    });
    element = angular.element(template);
    element = $compile(element)(scope);
    guidanceFormObserverAccessorElement.append(element);
    $rootScope.$apply();

    const radios = element.find('input');

    expect(radios.length).toBe(2);

    radioOpenElement = $(radios[0]);
    radioClosedElement = $(radios[1]);
  }));

  it('should have the correct element id', function () {
    expect(elementIdGenerator.generateId).toHaveBeenCalledTimes(2);
    expect(elementIdGenerator.generateId).toHaveBeenCalledWith('door.status', guidanceFormObserver);
    expect(elementIdGenerator.generateId).toHaveBeenCalledWith('door.status_CLOSED', guidanceFormObserver);

    expect(radioOpenElement.attr('id')).toBe('door.status-fake-id');
    expect(radioClosedElement.attr('id')).toBe('door.status_CLOSED-fake-id');
  });

  it('should render a radio group', function () {
    expect(radioOpenElement.val()).toBe("OPEN");
    expect(radioClosedElement.val()).toBe("CLOSED");

    let mainLabel = $(element.find('label')[0]);
    expect(mainLabel.text()).toBe('Door status');

    let labels = element.find('.input__radio');

    let openLabelElement = $(labels[0]);
    let closeLabelElement = $(labels[1]);

    expect(openLabelElement.text()).toContain('Open');
    expect(closeLabelElement.text()).toContain('Closed');
  });

  it('should update its internal state when the ngModel changes', function () {
    expect(radioOpenElement.is(':checked')).toBe(false);
    expect(radioClosedElement.is(':checked')).toBe(true);

    scope.model.door.status = "OPEN";
    $rootScope.$apply();

    expect(radioOpenElement.is(':checked')).toBe(true);
    expect(radioClosedElement.is(':checked')).toBe(false);
  });

  it('should let the "guidanceFormObserver" know values have changed', function () {
    expect(guidanceFormObserver.formValueChanged).not.toHaveBeenCalled();

    expect(scope.model.door.status).toBe("CLOSED");

    radioOpenElement.prop("checked", true).click();
    $rootScope.$apply();

    expect(scope.model.door.status).toBe("OPEN");

    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(1);
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
      focus: 'door.status',
      value: "OPEN"
    }, false);

    radioClosedElement.prop("checked", true).click();
    $rootScope.$apply();

    expect(scope.model.door.status).toBe("CLOSED");

    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(2);
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
      focus: 'door.status',
      value: "CLOSED"
    }, false);
  });

  it('should let the "guidanceFormObserver" know that the values have changed when noBackendInteraction is true', function () {
    expect(guidanceFormObserver.formValueChanged).not.toHaveBeenCalled();
    expect(scope.model.door.status).toBe("CLOSED");

    scope.fields[0].fieldGroup[0].templateOptions.noBackendInteraction = true;
    scope.fields[0].fieldGroup[1].templateOptions.noBackendInteraction = true;
    $rootScope.$apply();

    radioOpenElement.prop("checked", true).click();
    $rootScope.$apply();

    expect(scope.model.door.status).toBe("OPEN");
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(1);
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
      focus: 'door.status',
      value: "OPEN"
    }, true);
  });

  describe("the 'disabled' functionality", function () {
    it('should make the elements disabled when templateOptions.disabled is true', function () {
      expect(scope.model.door.status).toBe("CLOSED");

      expect(radioOpenElement.prop('disabled')).toBe(false);
      expect(radioClosedElement.prop('disabled')).toBe(false);

      scope.fields[0].fieldGroup[0].templateOptions.disabled = true;
      scope.fields[0].fieldGroup[1].templateOptions.disabled = true;
      $rootScope.$apply();

      expect(radioOpenElement.prop('disabled')).toBe(true);
      expect(radioClosedElement.prop('disabled')).toBe(true);
    });

    it('should make the field disabled if the backend is busy', function () {
      spyOn(guidanceModeBackendState, 'getBackendIsBusy').and.returnValue(true);
      spyOn(guidanceModeBackendState, 'getPerformedAction').and.returnValue({
        event: ACTION_EVENT.CHANGED,
        focus: 'field'
      });
      $rootScope.$apply();

      expect(radioOpenElement.prop('disabled')).toBe(true);
      expect(radioClosedElement.prop('disabled')).toBe(true);
    });
  });

  describe("the 'readonly' functionality", function () {
    it('should make the elements disabled when templateOptions.readonly is true', function () {
      expect(scope.model.door.status).toBe("CLOSED");

      expect(radioOpenElement.prop('disabled')).toBe(false);
      expect(radioClosedElement.prop('disabled')).toBe(false);

      scope.fields[0].fieldGroup[0].templateOptions.readonly = true;
      scope.fields[0].fieldGroup[1].templateOptions.readonly = true;
      $rootScope.$apply();

      expect(radioOpenElement.prop('disabled')).toBe(true);
      expect(radioClosedElement.prop('disabled')).toBe(true);
    });
  });

  describe('the validation functionality', function () {
    it('should set the field to erroneous if field errors occur', function () {
      expect(scope.form.$valid).toBe(true);
      validationObserver.setErrors({
        'door.status': ["An error occurred."]
      });
      $rootScope.$apply();
      expect(scope.form.$valid).toBe(false);
      expect(scope.form['door.status'].$error.BACK_END_ERROR).toBe(true);
    });
  });
});
