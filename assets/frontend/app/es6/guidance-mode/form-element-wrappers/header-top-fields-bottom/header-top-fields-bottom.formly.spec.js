'use strict';

describe('Form element wrappers: header-top-fields-bottom', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let scope;
  let element;
  let validationWrapper;

  let validationObserver;
  let suggestionsObserver;
  let guidanceFormObserver;

  let $rootScope;
  let $compile;

  const template = '<formly-form form="form" model="model" fields="fields"/>';

  beforeEach(inject(function(_$rootScope_, _$compile_, $state, ValidationObserver, SuggestionsObserver, GuidanceFormObserver) {
    $rootScope = _$rootScope_;
    $compile = _$compile_;

    validationObserver = new ValidationObserver();
    suggestionsObserver = new SuggestionsObserver();
    guidanceFormObserver = new GuidanceFormObserver();

    scope = $rootScope.$new();
    scope.model = {};
    scope.fields = [
      {
        wrapper: "header-top-fields-bottom-wrapper",
        templateOptions: {
          label: "First name"
        },
        fieldGroup: [
          {
            key: 'first-name',
            type: 'input',
            templateOptions: {
              readonly: false
            }
          }
        ]
      }
    ];

    mockHelpers.blockUIRouter($state);

    const guidanceFormObserverAccessorElement = mockHelpers.createGuidanceFormObserverAccessor({
      $compile,
      $rootScope,
      guidanceFormObserver,
      validationObserver,
      suggestionsObserver
    });
    element = angular.element(template);
    element = $compile(element)(scope);
    guidanceFormObserverAccessorElement.append(element);
    $rootScope.$apply();

    validationWrapper = element.find("validation-wrapper");
  }));

  it('should render a validation-wrapper element and pass the fieldGroup and label as arguments', function() {
    expect(validationWrapper.attr('fields')).toBe('options.fieldGroup');
    expect(validationWrapper.attr("label")).toBe("First name");
  });

  it('should put the label in an h3 tag', function() {
    const header = element.find("h3");
    expect(header.length).toBe(1);
    expect(header.text().trim()).toBe('First name');
  });

  it('should transclude the form-elements inside the validation-wrapper element', function() {
    expect(validationWrapper.find("input").length).toBe(1);
  });
});
