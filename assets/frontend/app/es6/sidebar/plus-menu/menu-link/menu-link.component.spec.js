'use strict';

describe('Component: menuLink', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let $rootScope;
  let $state;
  let $q;
  let scope;

  let actionDatasource;
  let $compile;

  let element;
  let aHref;

  let template = `
    <menu-link
      action="action"
      clickable="clickable"
      label="business"
      icon="icon-werkbakken">
    </menu-link>
  `;

  beforeEach(inject(function (_$rootScope_, _$compile_, _$state_, _$q_, _actionDatasource_) {
    $q = _$q_;
    $state = _$state_;
    actionDatasource = _actionDatasource_;
    $compile = _$compile_;

    mockHelpers.blockUIRouter($state);
    $rootScope = _$rootScope_;
  }));

  function compile(clickable) {
    scope = $rootScope.$new();

    scope.action = {
      actionId: 'navigate_to_create_lead_guidance'
    };
    scope.clickable = clickable;

    element = angular.element(template);
    element = $compile(element)(scope);

    $rootScope.$apply();

    aHref = $(element.find('a')[0]);
  }

  it('should compile down to a link with a label and icon.', function () {
    compile(true);

    const spans = element.find('span');
    const icon = $(spans[0]);
    const label = $(spans[1]);

    expect(aHref.hasClass('accordion-button')).toBe(true);
    expect(aHref.hasClass('disabled')).toBe(false);

    expect(icon.hasClass('icon-werkbakken')).toBe(true);
    expect(label.text()).toBe('business');
  });

  it('should call actionDatasource.perform when the link is clicked', function () {
    compile(true);

    const navigateCommand = {
      "command": "navigate",
      "arguments": {
        "flowId": null,
        "recordId": null,
        "title": null,
        "confirmLabel": null,
        "loadingMessage": null,
        "errors": null,
        "suggestions": null,
        "grid": null,
        "guidance": null,
        "form": null,
        "model": null,
        "progress": null,
        "step": null,
        "linkTo": "guidance-mode",
        "params": {
          "flowId": "CreateLead",
          "recordId": null
        }
      }
    };

    spyOn(actionDatasource, 'performAndHandle').and.callFake(mockHelpers.resolvedPromise($q, navigateCommand));

    aHref.click();

    expect(actionDatasource.performAndHandle).toHaveBeenCalledTimes(1);
    expect(actionDatasource.performAndHandle).toHaveBeenCalledWith({ actionId: "navigate_to_create_lead_guidance" });
  });

  it('should NOT call actionDatasource.performAndHandle when the link is disabled and clicked.', function () {
    compile(false);

    expect(aHref.hasClass('disabled')).toBe(true);

    spyOn(actionDatasource, 'performAndHandle');

    aHref.click();

    expect(actionDatasource.performAndHandle).not.toHaveBeenCalled();
  });
});
