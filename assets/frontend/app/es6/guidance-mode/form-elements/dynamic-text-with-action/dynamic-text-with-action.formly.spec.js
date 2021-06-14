'use strict';

describe('Form type: dynamic-text-with-action', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let scope;
  let element;

  let $rootScope;
  let $compile;
  let actionDatasource;

  const template = '<formly-form model="model" fields="fields"></formly-form>';

  beforeEach(inject(function (_$rootScope_, _$compile_, _actionDatasource_, $state) {
    $rootScope = _$rootScope_;
    $compile = _$compile_;
    actionDatasource = _actionDatasource_;
    mockHelpers.blockUIRouter($state);

    scope = $rootScope.$new();

    scope.model = {
      "legal_form_c": "",
      "company_name_c": ""
    };
    scope.fields = [
      {
        id: "company",
        key: "label-and-text-with-action-field",
        type: 'dynamic-text-with-action',
        templateOptions: {
          unparsedFieldExpression: "{%company_name_c%} {%legal_form_c%}",
          action: {
            id: "do-something",
            recordId: "12345"
          }
        }
      }
    ];

    element = angular.element(template);
    element = $compile(element)(scope);
    $rootScope.$apply();
  }));

  it('should put the id on the a element', function () {
    expect(element.find('a').attr('id')).toBe('company-field');
  });

  it('should update its internal state when the ngModel changes', function () {
    const aHrefElementSelector = $(element.find("a")[0]);
    expect(aHrefElementSelector.text().trim()).toBe("");

    scope.model.legal_form_c = "bvba";
    $rootScope.$apply();
    expect(aHrefElementSelector.text().trim()).toBe("bvba");

    scope.model.company_name_c = "Exesser";
    $rootScope.$apply();
    expect(aHrefElementSelector.text().trim()).toBe("Exesser bvba");

    scope.model.legal_form_c = "";
    $rootScope.$apply();
    expect(aHrefElementSelector.text().trim()).toBe("Exesser");
  });

  it('should call actionDatasource.perform when the link is clicked', function () {
    const aHrefElementSelector = $(element.find("a")[0]);
    spyOn(actionDatasource, 'performAndHandle');

    aHrefElementSelector.click();

    expect(actionDatasource.performAndHandle).toHaveBeenCalledTimes(1);
    expect(actionDatasource.performAndHandle).toHaveBeenCalledWith({ id: 'do-something', recordId: '12345' }, false);
  });
});
