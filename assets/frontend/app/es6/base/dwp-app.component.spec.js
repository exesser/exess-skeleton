'use strict';

describe('Component: dwp-app', function () {
  // load the component's module
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let $rootScope;
  let $compile;
  let $state;

  let element;
  let controller;

  const template = `
    <dwp-app
      options="controller.options">
    </dwp-app>
  `;

  beforeEach(inject(function (_$rootScope_, _$compile_, _$state_) {
    $rootScope = _$rootScope_;
    $compile = _$compile_;
    $state = _$state_;

    mockHelpers.blockUIRouter($state);
  }));

  function compile(options) {
    const scope = $rootScope.$new();

    controller = {
      options
    };

    scope.controller = controller;

    element = angular.element(template);
    element = $compile(element)(scope);

    $rootScope.$apply();
  }

  it('should navigate to an initial page when set.', function () {
    compile({
      initialPage: {
        linkTo: 'guidance-mode',
        params: {
          flowId: 'CUPQ'
        }
      }
    });

    expect($state.go).toHaveBeenCalledTimes(1);
    expect($state.go).toHaveBeenCalledWith('guidance-mode', { flowId: 'CUPQ' }, { reload: true });
  });

  it('should not navigate when no initialPage is set.', function () {
    compile({
      initialPage: undefined
    });

    expect($state.go).not.toHaveBeenCalled();
  });
});
