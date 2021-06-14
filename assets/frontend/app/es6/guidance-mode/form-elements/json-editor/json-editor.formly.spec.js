'use strict';

describe('Form type: json-editor', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let scope;
  let element;

  let $rootScope;
  let $compile;
  let $timeout;

  let validationObserver;
  let guidanceFormObserver;

  let elementIdGenerator;

  let jsonEditorDOMElement;
  let jsonEditorFormElementController;

  const template = '<formly-form form="form" model="model" fields="fields"></formly-form>';

  beforeEach(inject(function (_$rootScope_, _$compile_, _$timeout_, $state, ValidationObserver, GuidanceFormObserver,
                              _elementIdGenerator_) {
    $rootScope = _$rootScope_;
    $compile = _$compile_;
    $timeout = _$timeout_;

    elementIdGenerator = _elementIdGenerator_;

    validationObserver = new ValidationObserver();

    guidanceFormObserver = new GuidanceFormObserver();
    spyOn(guidanceFormObserver, 'formValueChanged');
    spyOn(elementIdGenerator, 'generateId').and.returnValue('field-fake-id');

    mockHelpers.blockUIRouter($state);
  }));

  afterEach(function () {
    jasmine.clock().uninstall();
  });

  function compile({ disabled = false, required = false, message = '[]', readonly = false } = {}) {
    scope = $rootScope.$new();

    scope.model = {
      "message": message
    };
    scope.fields = [{
      id: "message",
      key: "message",
      type: 'json-editor',
      templateOptions: {
        label: "Message",
        disabled,
        readonly: readonly,
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
    // Needed after each apply for "$scope.ngJsoneditor(instance);" in ng-jsoneditor.
    $timeout.flush();

    jsonEditorDOMElement = $('[ng-jsoneditor]', element);

    /**
     * Access to controller is needed in order to call "expandAll" so that our tests
     * can reference DOM which is then altered to contain contenteditable elements
     * against which "expect" is done.
     */
    if (readonly === false) {
      jsonEditorFormElementController = angular.element(jsonEditorDOMElement[0])
        .scope()
        .$parent
        .jsonEditorFormElementController;
    }
  }

  it('should have the correct element id', function () {
    compile();

    expect(elementIdGenerator.generateId).toHaveBeenCalledTimes(1);
    expect(elementIdGenerator.generateId).toHaveBeenCalledWith('message', guidanceFormObserver);
    expect(jsonEditorDOMElement.attr('id')).toBe('field-fake-id');
  });

  it('should update its internal state when the ngModel changes', function () {
    compile();

    scope.model.message = '[{"test": 1}]';
    $rootScope.$apply();
    $timeout.flush();

    jsonEditorFormElementController.expandAll();

    expect($('[contenteditable]', jsonEditorDOMElement)[0].innerText).toBe('test');
  });

  it('should not have contenteditable elements for empty ngModel change', function () {
    compile();

    scope.model.message = '';
    $rootScope.$apply();
    $timeout.flush();

    jsonEditorFormElementController.expandAll();

    expect($('[contenteditable]', jsonEditorDOMElement).length).toBe(0);
  });
  it('should display the value when is read only', function () {
    compile({ readonly: true, message: { "name": "WKY" } });

    expect($(element.find('pre')[0]).text()).toContain('"name": "WKY"');
  });
});
