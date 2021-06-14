'use strict';

describe('Component: collapsibleForm', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let $rootScope;
  let $compile;
  let element;

  let guidanceFormObserver;

  /*
    Let the guidanceFormControllerMixin (triggered by the underlying
    collapsible-form-items do nothing. We test this separately in the
    collapsible-form-item test.
  */
  beforeEach(module('digitalWorkplaceApp', function($provide) {
    $provide.provider('guidanceFormControllerMixin', function () {
      this.$get = function() {
        return { apply: _.noop };
      };
    });
  }));

  beforeEach(inject(function (_$rootScope_, _$compile_, $state, GuidanceFormObserver) {
    mockHelpers.blockUIRouter($state);

    $rootScope = _$rootScope_;
    $compile = _$compile_;
    guidanceFormObserver = GuidanceFormObserver;

    const scope = $rootScope.$new();
    scope.items = [
      {
        label: "Form A",
        formKey: "a"
      }, {
        label: "Form B",
        formKey: "b"
      }
    ];

    const guidanceFormObserverAccessorElement = mockHelpers.createGuidanceFormObserverAccessor({ $compile, $rootScope, guidanceFormObserver });

    element = angular.element(`<collapsible-form items="items"></collapsible-form>`);
    element = $compile(element)(scope);

    guidanceFormObserverAccessorElement.append(element);

    $rootScope.$apply();
  }));

  it('should render collapsibleFormItem directives.', function () {
    const collapsibleFormItems = element.find("collapsible-form-item");
    expect(collapsibleFormItems.length).toBe(2);

    const firstItem = $(collapsibleFormItems[0]);
    const secondItem = $(collapsibleFormItems[1]);

    expect(firstItem.attr('label')).toBe("Form A");
    expect(firstItem.attr('form-key')).toBe("a");

    expect(secondItem.attr('label')).toBe("Form B");
    expect(secondItem.attr('form-key')).toBe("b");
  });
});
