'use strict';

describe('Component: basicFormlyForm', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let $rootScope;
  let $compile;
  let element;

  let guidanceFormObserver;

  const template = '<basic-formly-form form-key="formX"></basic-formly-form>';

  beforeEach(inject(function (_$rootScope_, _$compile_, $state, suggestionsMixin, validationMixin, GuidanceFormObserver, guidanceFormControllerMixin) {
    mockHelpers.blockUIRouter($state);

    $rootScope = _$rootScope_;
    $compile = _$compile_;
    guidanceFormObserver = new GuidanceFormObserver();

    // Mock the suggestions-mixin so we don't have to create a fake suggestionsObserver which is not relevant for this test
    mockHelpers.createSuggestionsMixinMock(suggestionsMixin);

    // Mock the validation-mixin so we don't have to create a fake validationObserver which is not relevant for this test
    mockHelpers.createValidationMixinMock(validationMixin);

    // Mock the guidanceFormControllerMixin and set some default values
    mockHelpers.createGuidanceFormControllerMixinMock({
      guidanceFormControllerMixin,
      model: {
        companyName: "Jan"
      },
      fields: [{
        type: "input",
        key: "companyName",
        defaultValue: undefined,
        templateOptions: {
          label: "First name",
          required: true,
          readonly: false
        }
      }]
    });

    const guidanceFormObserverAccessorElement = mockHelpers.createGuidanceFormObserverAccessor({ $compile, $rootScope, guidanceFormObserver });
    const scope = $rootScope.$new();
    element = angular.element(template);
    element = $compile(element)(scope);
    guidanceFormObserverAccessorElement.append(element);
    $rootScope.$apply();
  }));

  it('should render a form containing a large-input in a card.', function () {
    expect($(element.find("input")).val()).toBe('Jan');
  });
});
