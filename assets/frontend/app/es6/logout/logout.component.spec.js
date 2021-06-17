'use strict';

describe('Component: logout', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let element;
  let $rootScope;
  let $compile;
  let $state;
  let $q;

  let loginFactory;
  let tokenFactory;
  let commandHandler;

  const template = '<logout location="menu"></logout>';

  beforeEach(inject(function (_$rootScope_, _$compile_, _$state_, _loginFactory_, _$q_,
                              _commandHandler_, _tokenFactory_) {
    $rootScope = _$rootScope_;
    $compile = _$compile_;
    $state = _$state_;
    loginFactory = _loginFactory_;
    tokenFactory = _tokenFactory_;
    $q = _$q_;
    commandHandler = _commandHandler_;

    mockHelpers.blockUIRouter($state);
  }));

  function compile({ loggedIn }) {
    spyOn(tokenFactory, 'hasToken').and.returnValue(loggedIn);

    const scope = $rootScope.$new();

    element = angular.element(template);
    element = $compile(element)(scope);

    $rootScope.$apply();
  }

  it('should compile down to a logout button when the user is logged in', function () {
    compile({ loggedIn: true });

    const spanElements = element.find('span');
    expect(spanElements.length).toBe(2);

    const iconLogoutElement = $(spanElements[0]);
    const textElement = $(spanElements[1]);

    expect(iconLogoutElement.hasClass('icon-logout')).toBe(true);
    expect(textElement.text()).toBe('Logout');
  });

  it('should compile down to a empty element when the user is not logged in', function () {
    compile({ loggedIn: false });

    expect(element.find('nav__bottom').contents().length).toBe(0);
  });

  it('should log the user out when logout is clicked', function () {
    spyOn(loginFactory, 'logout').and.callFake(mockHelpers.resolvedPromise($q));
    spyOn(tokenFactory, 'removeToken');

    compile({ loggedIn: true });

    const aHrefElement = $(element.find('a'));

    aHrefElement.click();
    $rootScope.$apply();

    expect(tokenFactory.removeToken).toHaveBeenCalledTimes(1);

    expect($state.transitionTo).toHaveBeenCalledWith('login');
  });

  it('should handle command if rejected with logoutData when logout is clicked', function () {
    const logoutData = {
      command: 'test'
    };

    spyOn(loginFactory, 'logout').and.callFake(mockHelpers.resolvedPromise($q, logoutData));
    spyOn(commandHandler, 'handle');

    compile({ loggedIn: true });

    const aHrefElement = $(element.find('a'));

    aHrefElement.click();
    $rootScope.$apply();

    expect(commandHandler.handle).toHaveBeenCalledTimes(1);
    expect(commandHandler.handle).toHaveBeenCalledWith('test');
  });
});
