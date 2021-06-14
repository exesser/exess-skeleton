'use strict';

describe('Component: listActionCell', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let $rootScope;
  let $compile;
  let actionDatasource;

  let element;

  const template = `
    <list-action-cell
      action="action"
      clickable="clickable"
      icon="{{ icon }}"
      label="{{ label }}">
    </list-action-cell>
  `;

  beforeEach(inject(function (_$rootScope_, _$compile_, $state, _actionDatasource_) {
    $rootScope = _$rootScope_;
    $compile = _$compile_;
    actionDatasource = _actionDatasource_;

    mockHelpers.blockUIRouter($state);
  }));

  function compile(actionConfiguration) {
    const scope = $rootScope.$new(true);
    _.extend(scope, actionConfiguration);
    element = angular.element(template);
    element = $compile(element)(scope);
    $rootScope.$apply();
  }

  it('should compile down to an clickable button', function () {
    compile({
      clickable: true,
      label: "open modal"
    });

    const anchor = element.find("a");
    expect(anchor).not.toBeUndefined();
    expect(anchor.text()).toContain('open modal');
    expect(anchor.attr('disabled')).toBeUndefined();
  });

  it('should compile down to a disabled button', function () {
    compile({
      clickable: false,
      label: "disabled button"
    });

    const anchor = element.find("a");
    expect(anchor).not.toBeUndefined();
    expect(anchor.text()).toContain('disabled button');
    expect(anchor.attr('disabled')).toBe('disabled');
  });

  it('should call the back-end when the action occurs for clickable actions.', function () {
    spyOn(actionDatasource, 'performAndHandle');

    compile({
      clickable: true,
      icon: "icon-quote",
      label: "open modal",
      action: {
        id: "1",
        recordId: "42",
        recordType: "lead"
      }
    });

    element.find("a").click();

    expect(actionDatasource.performAndHandle).toHaveBeenCalledTimes(1);
    expect(actionDatasource.performAndHandle).toHaveBeenCalledWith({
      id: "1",
      recordId: "42",
      recordType: "lead"
    });
  });

  it('should when the action is disabled not do anything.', function () {
    spyOn(actionDatasource, 'performAndHandle');

    compile({
      clickable: false,
      icon: "icon-quote",
      label: "open modal",
      action: {
        id: "1",
        recordId: "42",
        recordType: "lead"
      }
    });

    element.find("a").click();
    $rootScope.$apply();

    expect(actionDatasource.performAndHandle).not.toHaveBeenCalled();
  });
});
