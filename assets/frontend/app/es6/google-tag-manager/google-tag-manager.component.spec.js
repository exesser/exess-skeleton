'use strict';

describe('Component: googleTagManager', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let $rootScope;
  let element;
  let $window;

  const template = '<google-tag-manager></google-tag-manager>';

  beforeEach(inject(function (_$rootScope_, $compile, _$window_) {
    $rootScope = _$rootScope_;
    $window = _$window_;

    const scope = $rootScope.$new();
    element = angular.element(template);
    element = $compile(element)(scope);
    $rootScope.$apply();
  }));

  it('should set the dataLayer', function () {
    expect(!_.isEmpty($window.dataLayer)).toBeTruthy();
  });
});
