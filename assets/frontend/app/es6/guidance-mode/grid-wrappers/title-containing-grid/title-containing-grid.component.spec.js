'use strict';

describe('Directive: titleContainingGrid', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let $rootScope;
  let $compile;
  let element;
  let scope;
  let guidanceFormObserver;

  let deregisterStepChangeSpy;

  beforeEach(inject(function (_$rootScope_, _$compile_, $state, GuidanceFormObserver) {
    mockHelpers.blockUIRouter($state);

    $rootScope = _$rootScope_;
    $compile = _$compile_;

    guidanceFormObserver = new GuidanceFormObserver();

    deregisterStepChangeSpy = jasmine.createSpy("deregisterStepChange");
    spyOn(guidanceFormObserver, 'addStepChangeOccurredCallback').and.returnValue(deregisterStepChangeSpy);

    scope = $rootScope.$new();
    scope.emptyGrid = {
      columns: []
    };
  }));

  describe('constructor initialisation function', function () {

    it('should assign a grid from the grid json', function () {
      const template = '<title-containing-grid grid="emptyGrid" default-title="Default title" title-expression="{% title %}"></title-containing-grid>';
      compileDirective(template);
      expect(guidanceFormObserver.addStepChangeOccurredCallback).toHaveBeenCalled();
    });
  });

  describe("renders titles that", function () {

    it('should show the default title if the title-expression evaluates to an empty string.', function () {
      const template = '<title-containing-grid grid="emptyGrid" default-title="Default title" title-expression="{% title %}"></title-containing-grid>';
      compileDirective(template);
      setModel({
        title: ""
      });

      expect($(element.find("h2")).text()).toBe('Default title');
    });

    it('should show the default title if no title-expression is given.', function () {
      const template = '<title-containing-grid grid="emptyGrid" default-title="Default title"></title-containing-grid>';
      compileDirective(template);
      setModel({});

      expect($(element.find("h2")).text()).toBe('Default title');
    });

    it("should show the evaluated title if it doesn't result in an empty string.", function () {
      const template = '<title-containing-grid grid="emptyGrid" default-title="Default title" title-expression="{% title %}"></title-containing-grid>';
      compileDirective(template);
      setModel({
        title: "Cool title"
      });

      expect($(element.find("h2")).text()).toBe('Cool title');
    });
  });

  it('should pass an onDestroy function on which a callback can be registered that is invoked when the scope is destroyed', function () {
    const template = '<title-containing-grid grid="emptyGrid" default-title="Default title" title-expression="{% title %}"></title-containing-grid>';
    //First we create the directive
    compileDirective(template);

    //Destroy the scope and check that the deregisterStepChangeSpy function is called.
    scope.$destroy();
    expect(deregisterStepChangeSpy).toHaveBeenCalledTimes(1);
  });

  function compileDirective(template) {
    const guidanceFormObserverAccessorElement = mockHelpers.createGuidanceFormObserverAccessor({
      $compile,
      $rootScope,
      guidanceFormObserver
    });
    element = angular.element(template);
    element = $compile(element)(scope);
    guidanceFormObserverAccessorElement.append(element);
    $rootScope.$apply();

    expect(guidanceFormObserver.addStepChangeOccurredCallback).toHaveBeenCalledTimes(1);
  }

  function setModel(model) {
    const callbackFunction = guidanceFormObserver.addStepChangeOccurredCallback.calls.allArgs()[0][0]; //First call, first argument
    callbackFunction({ model });
    $rootScope.$apply();
  }
});
