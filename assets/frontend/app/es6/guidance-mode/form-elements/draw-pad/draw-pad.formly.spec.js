'use strict';

describe('Form type: draw-pad', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let scope;
  let element;

  let $rootScope;
  let $compile;
  let $timeout;

  let guidanceFormObserver;
  let elementIdGenerator;

  let guidanceModeBackendState;
  let ACTION_EVENT;

  let canvasElement;
  let signatureContainerElement;

  const template = '<formly-form form="form" model="model" fields="fields"/>';
  const originalCanvasValue = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAoAAAAMCAYAAABbayygAAAAU0lEQVQoU2NkIBIwEqmOYZApDGBgYOiHur2QgYFhA4iNzY0gCX+owgsMDAyGuBQuYGBgiIcq3MjAwACyAauJCgwMDAVQhRMYGBge4FKINWipH44ANOgIDYYaE6gAAAAASUVORK5CYII=";

  beforeEach(inject(function (_$rootScope_, _$compile_, _$timeout_, $state, ValidationObserver, SuggestionsObserver,
                              GuidanceFormObserver, _guidanceModeBackendState_, _ACTION_EVENT_, _elementIdGenerator_) {
    $rootScope = _$rootScope_;
    $compile = _$compile_;
    $timeout = _$timeout_;

    guidanceModeBackendState = _guidanceModeBackendState_;
    elementIdGenerator = _elementIdGenerator_;
    ACTION_EVENT = _ACTION_EVENT_;

    guidanceFormObserver = new GuidanceFormObserver();

    spyOn(guidanceFormObserver, 'formValueChanged');
    spyOn(elementIdGenerator, 'generateId').and.returnValue('field-fake-id');

    mockHelpers.blockUIRouter($state);
  }));

  function compile({ originalCV = originalCanvasValue, required = false } = {}) {
    scope = $rootScope.$new();

    scope.model = {
      "signature": originalCV
    };
    scope.fields = [
      {
        id: 'signature',
        key: 'signature',
        type: 'draw-pad',
        templateOptions: {
          disabled: false,
          readonly: false,
          noBackendInteraction: false,
          width: 10,
          height: 12,
          required
        }
      }
    ];

    const guidanceFormObserverAccessorElement = mockHelpers.createGuidanceFormObserverAccessor({
      $compile,
      $rootScope,
      guidanceFormObserver
    });
    element = angular.element(template);
    element = $compile(element)(scope);
    guidanceFormObserverAccessorElement.append(element);
    $rootScope.$apply();
    $timeout.flush();

    signatureContainerElement = $(element.find("div.signature")[0]);
    canvasElement = $(element.find("canvas")[0]);
  }

  function updateCanvasValue() {
    var canvas = element.find("canvas");
    var ctx = canvas[0].getContext("2d");
    ctx.beginPath();
    ctx.moveTo(0, 0);
    ctx.lineTo(5, 5);
    ctx.stroke();

    canvasElement.trigger('mouseup');
    $rootScope.$apply();
    $timeout.flush();

    return "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAoAAAAMCAYAAABbayygAAAACXBIWXMAAAsTAAALEwEAmpwYAAAB90lEQVQYGQHsARP+AAAAAP8AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAP8AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAP8AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAP8AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAP8AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAwI8E/D17flsAAAAASUVORK5CYII=";
  }

  it('should have the correct element id', function () {
    compile({ originalCV: '' });

    expect(elementIdGenerator.generateId).toHaveBeenCalledTimes(1);
    expect(elementIdGenerator.generateId).toHaveBeenCalledWith('signature', guidanceFormObserver);
    expect(signatureContainerElement.attr('id')).toBe('field-fake-id');
  });

  it('should let the "guidanceFormObserver" know that the value has changed', function () {
    compile();

    expect(guidanceFormObserver.formValueChanged).not.toHaveBeenCalled();

    canvasElement.trigger('mouseup');
    $rootScope.$apply();
    $timeout.flush();

    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(1);
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
      focus: 'signature',
      value: "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAoAAAAMCAYAAABbayygAAAACXBIWXMAAAsTAAALEwEAmpwYAAAB90lEQVQYGQHsARP+AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAewAAeY6GccAAAAASUVORK5CYII="
    }, false);

    const newValue = updateCanvasValue();

    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(2);
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
      focus: 'signature',
      value: newValue
    }, false);
  });

  it('should let the "guidanceFormObserver" know that the values have changed when noBackendInteraction is true', function () {
    compile();

    expect(guidanceFormObserver.formValueChanged).not.toHaveBeenCalled();

    scope.fields[0].templateOptions.noBackendInteraction = true;

    canvasElement.trigger('mouseup');
    $rootScope.$apply();
    $timeout.flush();

    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(1);
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
      focus: 'signature',
      value: "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAoAAAAMCAYAAABbayygAAAACXBIWXMAAAsTAAALEwEAmpwYAAAB90lEQVQYGQHsARP+AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAewAAeY6GccAAAAASUVORK5CYII="
    }, true);
  });

  it('should delete the image when the clear button is clicked', function () {
    compile();
    let clearButton = $(element.find('a')[0]);

    expect(guidanceFormObserver.formValueChanged).not.toHaveBeenCalled();
    expect(clearButton.hasClass('ng-hide')).toBe(false);

    clearButton.click();

    expect(clearButton.hasClass('ng-hide')).toBe(true);
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(1);
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
      focus: 'signature',
      value: ""
    }, false);
  });

  it('should reset the image when the reset button is clicked', function () {
    compile();
    let resetButton = $(element.find('a')[1]);

    expect(guidanceFormObserver.formValueChanged).not.toHaveBeenCalled();
    expect(resetButton.hasClass('ng-hide')).toBe(true);

    const newValue = updateCanvasValue();

    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(1);
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
      focus: 'signature',
      value: newValue
    }, false);
    expect(resetButton.hasClass('ng-hide')).toBe(false);

    resetButton.click();

    expect(resetButton.hasClass('ng-hide')).toBe(true);
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(2);
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
      focus: 'signature',
      value: originalCanvasValue
    }, false);
  });

  describe("the 'disabled' functionality", function () {
    it('should display a image if the templateOptions.disabled property evaluates to true or readonly is true', function () {
      compile();

      let imgs = element.find('img');
      expect(imgs.length).toEqual(0);

      scope.fields[0].templateOptions.disabled = true;
      $rootScope.$apply();

      imgs = element.find('img');
      expect(imgs.length).toEqual(1);
      expect($(imgs[0]).attr('src')).toEqual(originalCanvasValue);
    });

    it('should display a image if the backend is busy', function () {
      spyOn(guidanceModeBackendState, 'getBackendIsBusy').and.returnValue(true);
      spyOn(guidanceModeBackendState, 'getPerformedAction').and.returnValue({
        event: ACTION_EVENT.CHANGED,
        focus: 'field'
      });
      compile();

      const imgs = element.find('img');
      expect(imgs.length).toEqual(1);
      expect($(imgs[0]).attr('src')).toEqual(originalCanvasValue);
    });
  });

  describe("the 'required' functionality", function () {
    it('should not set any errors when the field is not required', function () {
      compile();
      expect(scope.form.$valid).toBe(true);
    });

    it('should set errors when the field is required and the object is empty', function () {
      compile({ required: true });

      expect(scope.form.$valid).toBe(true);

      let clearButton = $(element.find('a')[0]);
      clearButton.click();
      $rootScope.$apply();

      expect(scope.form.$valid).toBe(false);
      expect(scope.form.signature.$error.required).toBe(true);
    });
  });
});
