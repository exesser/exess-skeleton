'use strict';

describe('Component: menuButton', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let $rootScope;
  let $state;
  let scope;

  let $compile;

  let element;

  let template = `
    <menu-button
      button='{
              "buttonGroup": false, 
              "enabled": true,
              "label": "Business",
              "icon": "icon-werkbakken",
              "sort_order": "1",
              "action": {
                "id": "navigate_to_create_lead_guidance"
              }
            }'>
    </menu-button>
  `;

  beforeEach(inject(function (_$rootScope_, _$compile_, _$state_) {
    $state = _$state_;
    $compile = _$compile_;

    mockHelpers.blockUIRouter($state);
    $rootScope = _$rootScope_;

    scope = $rootScope.$new();

    element = angular.element(template);
    element = $compile(element)(scope);

    $rootScope.$apply();
  }));

  it('should compile down to a link.', function () {
    const menuLink = element.find('menu-link');
    expect(menuLink.attr('label')).toBe('Business');
  });
});
