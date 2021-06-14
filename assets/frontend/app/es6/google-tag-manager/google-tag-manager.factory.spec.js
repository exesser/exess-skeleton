'use strict';

describe('Factory: googleTagManager', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let $rootScope;
  let element;
  let $window;
  let googleTagManager;

  const template = '<google-tag-manager></google-tag-manager>';

  beforeEach(inject(function (_$rootScope_, $compile, _googleTagManager_, _$window_) {
    $rootScope = _$rootScope_;
    googleTagManager = _googleTagManager_;
    $window = _$window_;

    const scope = $rootScope.$new();
    element = angular.element(template);
    element = $compile(element)(scope);
    $rootScope.$apply();
  }));

  it('should do a push', function () {
    expect(!_.isEmpty($window.dataLayer)).toBeTruthy();

    spyOn($window.dataLayer, 'push');
    let data = { clientnumber: '12345' };
    googleTagManager.push(data);

    expect($window.dataLayer.push).toHaveBeenCalledTimes(1);
    expect($window.dataLayer.push).toHaveBeenCalledWith(data);
  });

  it('should not do a push', function () {
    $window.dataLayer = undefined;
    expect(_.isEmpty($window.dataLayer)).toBeTruthy();
    googleTagManager.push({ clientnumber: '12345' });
  });
});
