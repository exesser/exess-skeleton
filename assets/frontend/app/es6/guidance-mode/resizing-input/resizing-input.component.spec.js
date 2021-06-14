'use strict';

describe('Component: resizing-input', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let $rootScope;
  let $timeout;

  let element;
  let scope;

  let inputElement;

  const template = `
    <resizing-input
      ng-model='firstName'
      field-id="firstName"
      is-disabled="isDisabled"
      is-readonly="isReadonly"
      placeholder="First name">
    </resizing-input>
  `;

  beforeEach(inject(function ($state, $compile, _$rootScope_, _$timeout_, GuidanceFormObserver) {
    mockHelpers.blockUIRouter($state);

    $rootScope = _$rootScope_;
    $timeout = _$timeout_;

    scope = $rootScope.$new();
    scope.firstName = "";
    scope.isDisabled = false;
    scope.isReadonly = false;

    const guidanceFormObserver = new GuidanceFormObserver();
    const guidanceFormObserverAccessorElement = mockHelpers.createGuidanceFormObserverAccessor({
      $compile,
      $rootScope,
      guidanceFormObserver
    });

    element = angular.element(template);
    element = $compile(element)(scope);
    guidanceFormObserverAccessorElement.append(element);
    $rootScope.$apply();

    inputElement = element.find("input");
  }));

  it('should update its internal state when the ngModel changes', function() {
    expect(inputElement.val()).toBe("");

    scope.firstName = "Jan";
    $rootScope.$apply();

    expect(inputElement.val()).toBe("Jan");
    expect(inputElement.attr('id')).toBe("first-name-field");
  });

  it('should change the model value when typing in a value', function() {
    expect(scope.firstName).toBe("");

    inputElement.val("Jan").change();
    $rootScope.$apply();

    expect(scope.firstName).toBe("Jan");
  });

  // Unfortunately it seems like the width() call always returns 0 so instead we just check that the width contains the text and the input
  // takes over the width.
  describe('the span', function() {
    it('should initially contain the label', function() {
      expect(element.find("span").text()).toBe("First name");
    });

    it('should contain the value after the user added something', function() {
      scope.firstName = "Jan";
      $rootScope.$apply();
      expect(element.find("span").text()).toBe("Jan");
    });

    it('contain the value after the user added something and the label if the user has subsequently removed the content', function() {
      scope.firstName = "Jan";
      $rootScope.$apply();
      expect(element.find("span").text()).toBe("Jan");

      scope.firstName = "";
      $rootScope.$apply();
      expect(element.find("span").text()).toBe("First name");
    });
  });

  describe('the input element', function() {
    it('should set the width of the span as its own width after a change', function() {
      scope.firstName = "Jan";
      element.find("span").width("42");
      $rootScope.$apply();
      $timeout.flush();

      expect(inputElement.css("width")).toBe('62px');
    });
  });

  describe("the 'disabled' functionality", function() {
    it('should made the field readonly if the isDisabled property evaluates to true', function() {
      expect(inputElement.prop('readonly')).toBe(false);

      scope.isDisabled = true;
      $rootScope.$apply();

      expect(inputElement.prop('readonly')).toBe(true);
    });
  });

  describe("the 'readonly' functionality", function() {
    it('should made the field read-only if the isDisabled property evaluates to true', function() {
      expect(element.find('input').length).toBe(1);
      expect(element.find('strong').length).toBe(0);

      scope.isReadonly = true;
      scope.firstName = "This is read only";
      $rootScope.$apply();

      expect(element.find('input').length).toBe(0);

      const strong = $(element.find('strong')[0]);
      expect(strong.attr('id')).toBe('first-name-field');
      expect(strong.text()).toBe('This is read only');
    });
  });
});
