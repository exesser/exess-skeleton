'use strict';

describe('Component: centeredGuidanceGrid', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let $rootScope;
  let $compile;
  let element;

  const template = '<centered-guidance-grid grid="awesomeGrid"></centered-guidance-grid>';

  beforeEach(inject(function (_$rootScope_, _$compile_, $state, guidanceFormControllerMixin) {
    mockHelpers.blockUIRouter($state);

    $rootScope = _$rootScope_;
    $compile = _$compile_;

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
          required: true
        }
      }]
    });

    const scope = $rootScope.$new();
    scope.awesomeGrid = {
      "columns": [{
        "size": "1-4",
        "hasMargin": false,
        "rows": [{
          "size": "1-1",
          "type": "awesomeThing",
          "options": {
            "title": "hello",
            "description": "world"
          }
        }]
      }]
    };

    element = angular.element(template);
    element = $compile(element)(scope);

    $rootScope.$apply();
  }));

  it('should render a grid and wrap it in vertical alignment divs.', function () {
    const viewGuidance = element.find("div.view__guidance");
    expect(viewGuidance.length).toBe(1);

    const vCenterOuter = viewGuidance.find("div.v-center-outer");
    expect(vCenterOuter.length).toBe(1);

    const vCenter = vCenterOuter.find("div.v-center");
    expect(vCenter.length).toBe(1);

    const vCenterInner = vCenter.find("div.v-center-inner");
    expect(vCenterInner.length).toBe(1);

    const gridlr = vCenterInner.find("gridlr");
    expect(gridlr.length).toBe(1);
    expect(gridlr.text()).not.toBe("");
  });
});
