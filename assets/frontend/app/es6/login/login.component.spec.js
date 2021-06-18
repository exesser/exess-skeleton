'use strict';

describe('Component: login', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let element;
  let $rootScope;
  let $compile;
  let $state;
  let $q;
  let $timeout;

  let loginFactory;
  let tokenFactory;
  let currentUserFactory;
  let userDatasource;
  let commandHandler;

  let $translate;

  let $analytics;
  let googleTagManager;

  const template = '<login></login>';

  beforeEach(inject(function (_$rootScope_, _$compile_, _$state_, _loginFactory_, _currentUserFactory_, _tokenFactory_,
                              _userDatasource_, _$translate_, LANGUAGE, _$q_, _commandHandler_,
                              _$analytics_, _googleTagManager_, _$timeout_) {
    $rootScope = _$rootScope_;
    $compile = _$compile_;
    $state = _$state_;
    loginFactory = _loginFactory_;
    tokenFactory = _tokenFactory_;
    currentUserFactory = _currentUserFactory_;
    userDatasource = _userDatasource_;
    $translate = _$translate_;
    commandHandler = _commandHandler_;
    $q = _$q_;
    $timeout = _$timeout_;
    $analytics = _$analytics_;
    googleTagManager = _googleTagManager_;

    mockHelpers.blockUIRouter($state);
  }));

  function compile() {
    const scope = $rootScope.$new();

    element = angular.element(template);
    element = $compile(element)(scope);

    $rootScope.$apply();
  }

  it('should compile down to a login modal when autologin fails', function () {
    spyOn(userDatasource, 'current').and.callFake(mockHelpers.rejectedPromise($q));

    compile();

    expect(userDatasource.current).toHaveBeenCalledTimes(1);

    expect(element.find('h1').text()).toBe('Welcome to NOVA');

    expect($(element.find('div.alert.is-error')).hasClass('ng-hide')).toBe(true);

    expect(element.find('input[type=text]').length).toBe(1);
    expect(element.find('input[type=password]').length).toBe(1);
    expect(element.find('input[type=submit]').length).toBe(1);
  });

  it('should skip the login modal when autologin succeeds', function () {
    spyOn(userDatasource, 'current').and.callFake(mockHelpers.resolvedPromise($q, { username: "Tony" }));
    spyOn(currentUserFactory, 'setUser');

    compile();
    $timeout.flush();

    expect(userDatasource.current).toHaveBeenCalledTimes(1);

    expect(currentUserFactory.setUser).toHaveBeenCalledTimes(1);
    expect(currentUserFactory.setUser).toHaveBeenCalledWith({ username: "Tony" });

    expect($state.go).toHaveBeenCalledTimes(1);
    expect($state.go).toHaveBeenCalledWith('dashboard', { mainMenuKey: 'start', dashboardId: 'home' });
  });

  it('should call the commandHandler when the response contains a command', function () {
    const command = {
      command: "openModal",
      arguments: {
        flowId: "ask"
      }
    };

    spyOn(userDatasource, 'current').and.callFake(mockHelpers.resolvedPromise($q, { username: "Tony", command }));
    spyOn(currentUserFactory, 'setUser');
    spyOn(commandHandler, 'handle');

    compile();
    $timeout.flush();
    expect(userDatasource.current).toHaveBeenCalledTimes(1);

    expect(commandHandler.handle).toHaveBeenCalledTimes(1);
    expect(commandHandler.handle).toHaveBeenCalledWith(command);
  });

  describe('login button', function () {
    let submitElement;
    let currentSpy;

    beforeEach(function () {
      currentSpy = spyOn(userDatasource, 'current').and.callFake(mockHelpers.rejectedPromise($q));

      compile();

      submitElement = $(element.find('input[type=submit]'));

      expect(userDatasource.current).toHaveBeenCalledTimes(1);
    });

    it('should log the user in when the username and password are correct', function () {
      const loginPromise = $q.defer();
      spyOn(loginFactory, 'login').and.returnValue(loginPromise.promise);
      loginPromise.resolve({data: {data: {token:"token-value"}}});
      spyOn(tokenFactory, 'setToken');

      currentSpy.and.callFake(mockHelpers.resolvedPromise($q, { username: "Tony" }));
      spyOn(currentUserFactory, 'setUser');

      submitElement.click();
      $rootScope.$apply();

      expect(loginFactory.login).toHaveBeenCalledTimes(1);
      expect(loginFactory.login).toHaveBeenCalledWith('superadmin', 'ch4ng3m3pl5');

      expect(tokenFactory.setToken).toHaveBeenCalledTimes(1);
      expect(tokenFactory.setToken).toHaveBeenCalledWith('token-value');

      // Once for the rejected autologin and once for the successful login
      expect(userDatasource.current).toHaveBeenCalledTimes(2);

      expect(currentUserFactory.setUser).toHaveBeenCalledTimes(1);
      expect(currentUserFactory.setUser).toHaveBeenCalledWith({ username: "Tony" });

      expect($state.go).toHaveBeenCalledTimes(1);
      expect($state.go).toHaveBeenCalledWith('dashboard', { mainMenuKey: 'start', dashboardId: 'home' });
    });

    it('should show the error message when the username and password are incorrect', function () {
      const loginSpy = spyOn(loginFactory, 'login').and.callFake(mockHelpers.rejectedPromise($q));

      submitElement.click();
      $rootScope.$apply();

      expect(loginFactory.login).toHaveBeenCalledTimes(1);
      expect(loginFactory.login).toHaveBeenCalledWith('superadmin', 'ch4ng3m3pl5');

      const errorElement = $(element.find('div.alert.is-error'));

      expect(errorElement.hasClass('ng-hide')).toBe(false);
      expect(errorElement.text()).toContain('Username and password are incorrect');

      // Check if error message is hidden again when retrying

      loginSpy.and.callFake(function () {
        // Create never resolved promise so error message is shown.
        return $q.defer().promise;
      });

      submitElement.click();
      $rootScope.$apply();

      expect(errorElement.hasClass('ng-hide')).toBe(true);
    });

    it('should show the error message that the response returns', function () {
      const loginSpy = spyOn(loginFactory, 'login').and.callFake(mockHelpers.rejectedPromise(
        $q,
        { "data": { "status": 400, "message": "test-message" } }
      ));

      submitElement.click();
      $rootScope.$apply();

      expect(loginFactory.login).toHaveBeenCalledTimes(1);
      expect(loginFactory.login).toHaveBeenCalledWith('superadmin', 'ch4ng3m3pl5');

      const errorElement = $(element.find('div.alert.is-error'));

      expect(errorElement.hasClass('ng-hide')).toBe(false);
      expect(errorElement.text()).toContain('test-message');

      // Check if error message is hidden again when retrying

      loginSpy.and.callFake(function () {
        // Create never resolved promise so error message is shown.
        return $q.defer().promise;
      });

      submitElement.click();
      $rootScope.$apply();

      expect(errorElement.hasClass('ng-hide')).toBe(true);
    });
  });

  describe('translations', function () {
    let submitElement;
    let currentSpy;

    beforeEach(function () {
      currentSpy = spyOn(userDatasource, 'current').and.callFake(mockHelpers.rejectedPromise($q));

      compile();

      submitElement = $(element.find('input[type=submit]'));

      expect(userDatasource.current).toHaveBeenCalledTimes(1);
    });

    it('should set the default language', function () {
      spyOn(loginFactory, 'login').and.callFake(mockHelpers.resolvedPromise($q));
      currentSpy.and.callFake(mockHelpers.resolvedPromise($q, { username: "Tony" }));
      spyOn($translate, 'use').and.returnValue('en_BE');

      submitElement.click();
      $rootScope.$apply();

      expect($translate.use).toHaveBeenCalledTimes(1);
      expect($translate.use).toHaveBeenCalledWith();
    });

    it('should set the preferred language', function () {
      spyOn(loginFactory, 'login').and.callFake(mockHelpers.resolvedPromise($q));
      currentSpy.and.callFake(mockHelpers.resolvedPromise($q, { username: "Tony", preferredLanguage: "nl_BE" }));
      spyOn($translate, 'use');

      submitElement.click();
      $rootScope.$apply();

      expect($translate.use).toHaveBeenCalledTimes(2);
      expect($translate.use).toHaveBeenCalledWith();
      expect($translate.use).toHaveBeenCalledWith('nl_BE');
    });
  });

  describe('analytics', function () {
    let submitElement;
    let currentSpy;

    beforeEach(function () {
      currentSpy = spyOn(userDatasource, 'current').and.callFake(mockHelpers.rejectedPromise($q));

      compile();

      submitElement = $(element.find('input[type=submit]'));

      expect(userDatasource.current).toHaveBeenCalledTimes(1);
    });

    it('should set the username', function () {
      spyOn(loginFactory, 'login').and.callFake(mockHelpers.resolvedPromise($q));
      currentSpy.and.callFake(mockHelpers.resolvedPromise($q, { username: "Tony" }));

      spyOn($analytics, 'setUsername');
      spyOn(googleTagManager, 'push');

      submitElement.click();
      $rootScope.$apply();

      expect($analytics.setUsername).toHaveBeenCalledTimes(1);
      expect($analytics.setUsername).toHaveBeenCalledWith('Tony');
    });
  });
});
