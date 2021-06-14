'use strict';

describe('Dashboard item: dashboard-image-item', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let $rootScope;
  let actionDatasource;
  let $q;

  let imgElement;

  const template = `
    <dashboard-image-item
      src="http://www.42.nl/images/42-logo.svg"
      action="action">
    </dashboard-image-item>
  `;

  beforeEach(inject(function (_$rootScope_, $compile, $state, _actionDatasource_, _$q_) {
    $rootScope = _$rootScope_;
    actionDatasource = _actionDatasource_;
    $q = _$q_;

    mockHelpers.blockUIRouter($state);

    const scope = $rootScope.$new();
    scope.action = {
      id: "42",
      recordId: "1337",
      recordType: "image-item"
    };

    let element = angular.element(template);
    element = $compile(element)(scope);

    $rootScope.$apply();

    imgElement = $(element.find('img'));
  }));

  it('should compile down to an image with a source.', function () {
    expect(imgElement.attr('src')).toBe('http://www.42.nl/images/42-logo.svg');
  });

  it('should call the back-end when the image is clicked', function () {
    // The result of an Action POST to the backend is a command to execute for the frontend.
    const navigateCommand = {
      command: "navigate",
      arguments: {
        route: "dashboard",
        params: {
          mainMenuKey: "sales-marketing",
          dashboardId: "leads"
        }
      }
    };
    spyOn(actionDatasource, 'performAndHandle').and.callFake(mockHelpers.resolvedPromise($q, navigateCommand));

    imgElement.click();

    expect(actionDatasource.performAndHandle).toHaveBeenCalledTimes(1);
    expect(actionDatasource.performAndHandle).toHaveBeenCalledWith({
      id: "42",
      recordId: "1337",
      recordType: "image-item"
    });
  });
});
