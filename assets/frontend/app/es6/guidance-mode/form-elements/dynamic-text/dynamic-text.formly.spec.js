'use strict';

describe('Form type: dynamic-text', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let scope;
  let element;

  let $rootScope;
  let $compile;

  const template = '<formly-form model="model" fields="fields"></formly-form>';

  beforeEach(inject(function (_$rootScope_, _$compile_, $state) {
    $rootScope = _$rootScope_;
    $compile = _$compile_;
    mockHelpers.blockUIRouter($state);

    scope = $rootScope.$new();

    scope.model = {
      "legal_form_c": "",
      "company_name_c": "",
      "formatted_number": ""
    };
    scope.fields = [
      {
        id: "company",
        key: "label-and-text-field",
        type: 'dynamic-text',
        templateOptions: {
          unparsedFieldExpression: "{%company_name_c%} {%legal_form_c%} {%formatted_number%}"
        }
      }
    ];

    element = angular.element(template);
    element = $compile(element)(scope);
    $rootScope.$apply();
  }));

  it('should put the id on the strong element', function() {
    expect(element.find('strong').attr('id')).toBe('company-field');
  });

  it('should update its internal state when the ngModel changes', function() {
    const strongElementSelector = $(element.find("strong")[0]);
    expect(strongElementSelector.text().trim()).toBe("");

    scope.model.legal_form_c = "bvba";
    $rootScope.$apply();
    expect(strongElementSelector.text().trim()).toBe("bvba");

    scope.model.company_name_c = "Exesser";
    $rootScope.$apply();
    expect(strongElementSelector.text().trim()).toBe("Exesser bvba");

    scope.model.legal_form_c = "";
    $rootScope.$apply();
    expect(strongElementSelector.text().trim()).toBe("Exesser");

    scope.model.company_name_c = "";
    $rootScope.$apply();
    expect(strongElementSelector.text().trim()).toBe("");

    scope.model.formatted_number = "500.33";
    $rootScope.$apply();
    expect(strongElementSelector.text().trim()).toBe("500,33");

    scope.model.formatted_number = "500.33.55";
    $rootScope.$apply();
    expect(strongElementSelector.text().trim()).toBe("500.33.55");
  });
});
