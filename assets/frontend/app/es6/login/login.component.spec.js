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

  beforeEach(inject(function (_$rootScope_, _$compile_, _$state_, _loginFactory_, _tokenFactory_,
                              _userDatasource_, _$translate_, LANGUAGE, _$q_, _commandHandler_,
                              _$analytics_, _googleTagManager_, _$timeout_) {
    $rootScope = _$rootScope_;
    $compile = _$compile_;
    $state = _$state_;
    loginFactory = _loginFactory_;
    tokenFactory = _tokenFactory_;
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
    spyOn(tokenFactory, 'hasToken').and.returnValue(false);

    compile();

    expect(tokenFactory.hasToken).toHaveBeenCalledTimes(1);

    expect(element.find('h1').text()).toBe('Welcome to NOVA');

    expect($(element.find('div.alert.is-error')).hasClass('ng-hide')).toBe(true);

    expect(element.find('input[type=text]').length).toBe(1);
    expect(element.find('input[type=password]').length).toBe(1);
    expect(element.find('input[type=submit]').length).toBe(1);
  });

  it('should skip the login modal when autologin succeeds', function () {
    spyOn(tokenFactory, 'hasToken').and.returnValue(true);

    compile();

    expect(tokenFactory.hasToken).toHaveBeenCalledTimes(1);
  });

  describe('login button', function () {
    let submitElement;
    let currentSpy;

    beforeEach(function () {
      spyOn(tokenFactory, 'hasToken').and.returnValue(false);
      currentSpy = spyOn(userDatasource, 'getUserPreferences').and.callFake(mockHelpers.rejectedPromise($q));

      compile();

      submitElement = $(element.find('input[type=submit]'));

      expect(tokenFactory.hasToken).toHaveBeenCalledTimes(1);
    });

    it('should log the user in when the username and password are correct', function () {
      const loginPromise = $q.defer();
      spyOn(loginFactory, 'login').and.returnValue(loginPromise.promise);
      loginPromise.resolve({data: {data: {token:"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJ1c2VySWQiOiJzdXBlcmFkbWluIiwiZXhwIjoxNjI0MDA2MzcwfQ.i62-7hutcuWkelD6yKRMevSxCNt9lTHE18BfZByxtvIU9n5tA1W2aLA9eEOKAY4E6f7bsCgbG2iyGWMO6mNIGA"}}});
      spyOn($analytics, 'setUsername');
      spyOn($translate, 'use');
      currentSpy.and.callFake(mockHelpers.resolvedPromise($q, { user_name: "Tony" , preferredLanguage: "nl_BE"}));

      submitElement.click();
      $rootScope.$apply();

      expect($translate.use).toHaveBeenCalledTimes(2);
      expect($translate.use).toHaveBeenCalledWith();
      expect($translate.use).toHaveBeenCalledWith('nl_BE');

      expect($analytics.setUsername).toHaveBeenCalledTimes(1);
      expect($analytics.setUsername).toHaveBeenCalledWith('superadmin');

      expect(loginFactory.login).toHaveBeenCalledTimes(1);
      expect(loginFactory.login).toHaveBeenCalledWith('superadmin', 'ch4ng3m3pl5');

      // Once for the rejected autologin and once for the successful login
      expect(userDatasource.getUserPreferences).toHaveBeenCalledTimes(1);

      expect($state.go).toHaveBeenCalledTimes(1);
      expect($state.go).toHaveBeenCalledWith('dashboard', { mainMenuKey: 'start', dashboardId: 'home' });
    });

    it('should call the commandHandler when the response contains a command', function () {
      const loginPromise = $q.defer();
      spyOn(loginFactory, 'login').and.returnValue(loginPromise.promise);
      loginPromise.resolve({data: {data: {token:"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJ1c2VySWQiOiJzdXBlcmFkbWluIiwiZXhwIjoxNjI0MDA2MzcwfQ.i62-7hutcuWkelD6yKRMevSxCNt9lTHE18BfZByxtvIU9n5tA1W2aLA9eEOKAY4E6f7bsCgbG2iyGWMO6mNIGA"}}});
      spyOn($analytics, 'setUsername');
      spyOn($translate, 'use');
      spyOn(commandHandler, 'handle');

      const command = {
        command: "openModal",
        arguments: {
          flowId: "ask"
        }
      };

      currentSpy.and.callFake(mockHelpers.resolvedPromise($q, { user_name: "Tony", command }));

      submitElement.click();
      $rootScope.$apply();
      $timeout.flush();

      expect(commandHandler.handle).toHaveBeenCalledTimes(1);
      expect(commandHandler.handle).toHaveBeenCalledWith(command);
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
});
