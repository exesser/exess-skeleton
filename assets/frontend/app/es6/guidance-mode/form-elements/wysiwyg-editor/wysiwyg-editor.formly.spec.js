'use strict';

describe('Form type: wysiwyg-editor', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let scope;
  let element;

  let $rootScope;
  let $compile;

  let validationObserver;
  let guidanceFormObserver;

  let ACTION_EVENT;
  let guidanceModeBackendState;
  let elementIdGenerator;

  let wysiwygEditor;
  let wysiwygEditorTextarea;

  const template = '<formly-form form="form" model="model" fields="fields"></formly-form>';

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
    spyOn(elementIdGenerator, 'generateId').and.returnValue('field-fake-id');

    mockHelpers.blockUIRouter($state);
  }));

  function compile({ disabled = false, required = false } = {}) {
    scope = $rootScope.$new();

    scope.model = {
      "message": ""
    };
    scope.fields = [{
      id: "message",
      key: "message",
      type: 'wysiwyg-editor',
      templateOptions: {
        label: "Message",
        disabled,
        readonly: false,
        required,
        noBackendInteraction: false
      }
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

    wysiwygEditor = $('text-angular', element);
    wysiwygEditorTextarea = $('> textarea', wysiwygEditor);
  }

  it('should have the correct element id', function () {
    compile();

    expect(elementIdGenerator.generateId).toHaveBeenCalledTimes(1);
    expect(elementIdGenerator.generateId).toHaveBeenCalledWith('message', guidanceFormObserver);
    expect(wysiwygEditor.attr('id')).toBe('field-fake-id');
  });

  it('should update its internal state when the ngModel changes', function () {
    compile();

    expect(wysiwygEditorTextarea.val()).toBe('');

    scope.model.message = 'test';
    $rootScope.$apply();

    expect(wysiwygEditorTextarea.val()).toBe('<p>test</p>');
  });

  it('should let the "guidanceFormObserver" know values have changed', function () {
    compile();

    wysiwygEditorTextarea.val('test').change();
    $rootScope.$apply();

    wysiwygEditorTextarea.val('test selectionBoundary_1519726477054_8631474617530774').change();
    $rootScope.$apply();

    wysiwygEditorTextarea.val('test').change();
    $rootScope.$apply();

    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(1);
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
      focus: 'message',
      value: '<p>test</p>'
    }, false);
  });

  it('should let the "guidanceFormObserver" know that the values have changed when noBackendInteraction is true', function () {
    compile();

    expect(guidanceFormObserver.formValueChanged).not.toHaveBeenCalled();
    expect(wysiwygEditorTextarea.val()).toBe('');

    scope.fields[0].templateOptions.noBackendInteraction = true;
    $rootScope.$apply();

    wysiwygEditorTextarea.val('123').change();
    $rootScope.$apply();

    expect(wysiwygEditorTextarea.val()).toBe('123');
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(1);
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
      focus: 'message',
      value: '<p>123</p>'
    }, true);
  });

  describe("the 'required' functionality", function () {
    it('should not set any errors when the field is not required', function () {
      compile();
      expect(scope.form.$valid).toBe(true);
    });

    it('should set errors when the field is required and the object is empty', function () {
      compile({ required: true });
      expect(scope.form.$valid).toBe(false);
      expect(scope.form.message.$error.required).toBe(true);
    });

    it('should remove errors when a value is set', function () {
      compile({ required: true });
      scope.model.message = 'test';
      $rootScope.$apply();

      expect(scope.form.$valid).toBe(true);
    });
  });

  describe("the 'readonly' functionality", function () {
    it('should made the field readonly if the templateOptions.readonly property evaluates to true', function () {
      compile();

      expect(wysiwygEditor.length).toBe(1);

      scope.fields[0].templateOptions.readonly = true;
      scope.model.message = 'test';
      $rootScope.$apply();

      wysiwygEditor = $('text-angular', element);

      expect(wysiwygEditor.length).toBe(0);
      expect($('div.non-editable-editor', element).text()).toBe('test');
    });
  });

  describe("the 'disabled' functionality", function () {
    it('should made the field disabled if the templateOptions.disabled property evaluates to true', function () {
      compile();

      expect(wysiwygEditorTextarea.prop('disabled')).toBe(false);

      scope.fields[0].templateOptions.disabled = true;
      $rootScope.$apply();

      expect(wysiwygEditorTextarea.prop('disabled')).toBe(true);
    });

    it('should make the field disabled if the backend is busy', function () {
      spyOn(guidanceModeBackendState, 'getBackendIsBusy').and.returnValue(true);
      spyOn(guidanceModeBackendState, 'getPerformedAction').and.returnValue({
        event: ACTION_EVENT.CHANGED,
        focus: 'field'
      });
      compile();

      expect(wysiwygEditorTextarea.prop('disabled')).toBe(true);
    });
  });
});
