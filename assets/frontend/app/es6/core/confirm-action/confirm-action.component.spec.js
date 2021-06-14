'use strict';

describe('Component: confirm-action', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let $rootScope;
  let $compile;

  let element;
  let controller;

  const template = `
    <confirm-action
      button-icon="icon"
      button-label="label"
      confirm-message="{{ controller.confirmMessage }}"
      action="controller.confirm()"
  </confirm-action>
 
  `;

  beforeEach(inject(function (_$rootScope_, _$compile_, $state) {
    mockHelpers.blockUIRouter($state);

    $rootScope = _$rootScope_;
    $compile = _$compile_;
  }));

  function compile(confirmMessage) {
    const scope = $rootScope.$new();

    controller = {
      confirm: jasmine.createSpy(),
      confirmMessage
    };

    scope.controller = controller;

    element = angular.element(template);
    element = $compile(element)(scope);

    $rootScope.$apply();
  }

  it('should render the template', function () {
    compile("");

    const buttonElement = $(element.find('a')[0]);
    expect(buttonElement.attr('name')).toBe('label');

    const buttonSpanElement = buttonElement.children('span');
    expect(buttonSpanElement.length).toBe(2);
    expect($(buttonSpanElement[0]).hasClass('icon')).toBeTruthy();
    expect($(buttonSpanElement[1]).text()).toBe('label');

    const section = $(element.find('section')[0]);
    expect(section.hasClass('ng-hide')).toBeTruthy();

    expect(controller.confirm).not.toHaveBeenCalled();
    buttonElement.click();
    expect(controller.confirm).toHaveBeenCalledTimes(1);
  });

  it('should show the confirm modal', function () {
    compile("Are you sure?");

    const buttonElement = $(element.find('a')[0]);
    const section = $(element.find('section')[0]);


    expect(section.hasClass('ng-hide')).toBeTruthy();
    expect(controller.confirm).not.toHaveBeenCalled();

    buttonElement.click();

    expect(controller.confirm).not.toHaveBeenCalled();
    expect(section.hasClass('ng-hide')).toBeFalsy();

    expect($(section.find('h5')[1]).text()).toBe('Are you sure?');

    const sectionButtons = section.find('a');
    const xButton = $(sectionButtons[0]);
    const yesButton = $(sectionButtons[1]);
    const noButton = $(sectionButtons[2]);

    xButton.click();
    expect(controller.confirm).not.toHaveBeenCalled();
    expect(section.hasClass('ng-hide')).toBeTruthy();

    buttonElement.click();
    expect(controller.confirm).not.toHaveBeenCalled();
    expect(section.hasClass('ng-hide')).toBeFalsy();

    noButton.click();
    expect(controller.confirm).not.toHaveBeenCalled();
    expect(section.hasClass('ng-hide')).toBeTruthy();

    buttonElement.click();
    expect(controller.confirm).not.toHaveBeenCalled();
    expect(section.hasClass('ng-hide')).toBeFalsy();

    yesButton.click();
    expect(controller.confirm).toHaveBeenCalledTimes(1);
    expect(section.hasClass('ng-hide')).toBeTruthy();
  });
});
