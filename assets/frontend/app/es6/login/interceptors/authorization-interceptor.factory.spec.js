'use strict';

describe('httpInterceptor: authorizationInterceptor', function () {
  beforeEach(module('digitalWorkplaceApp'));

  // instantiate service
  let authorizationInterceptor;

  let $q;
  let $state;
  let commandHandler;
  let tokenFactory;

  beforeEach(inject(function (_authorizationInterceptor_, _$q_, _$state_, _commandHandler_, _tokenFactory_) {
    authorizationInterceptor = _authorizationInterceptor_;

    $q = _$q_;
    $state = _$state_;
    commandHandler = _commandHandler_;
    tokenFactory = _tokenFactory_;

    spyOn($state, 'go');
    spyOn($q, 'reject');
  }));

  it('should go to login state when status is 401', function () {
    const rejection = { status: 401};

    authorizationInterceptor.responseError(rejection);

    expect($state.go).toHaveBeenCalledWith('login');

    expect($q.reject).toHaveBeenCalledTimes(1);
    expect($q.reject).toHaveBeenCalledWith(rejection);
  });

  it('should simply reject when status is not 401', function () {
    const rejection = { status: 200 };

    authorizationInterceptor.responseError(rejection);

    expect($state.go).not.toHaveBeenCalled();

    expect($q.reject).toHaveBeenCalledTimes(1);
    expect($q.reject).toHaveBeenCalledWith(rejection);
  });

  it('should handle command if rejected with data when status is 401', function () {
    spyOn(commandHandler, 'handle');

    const rejection = {
      status: 401,
      data: {
        command: 'test'
      }
    };

    authorizationInterceptor.responseError(rejection);

    expect(commandHandler.handle).toHaveBeenCalledTimes(1);
    expect(commandHandler.handle).toHaveBeenCalledWith('test');
  });

  it('should not add an authorization token if not available', function () {
    const config = { url: 'getList', headers: {}};
    const response = authorizationInterceptor.request(config);

    expect(response).toEqual(response);
  });

  it('should add an authorization token if available', function () {
    tokenFactory.setToken('bla-bla-bla');
    const config = { url: 'getList', headers: {}};
    const response = authorizationInterceptor.request(config);
    const expectedResponse = angular.copy(config);
    expectedResponse.headers.Authorization = 'Bearer bla-bla-bla';

    expect(response).toEqual(expectedResponse);
  });
});
