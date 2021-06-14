'use strict';

describe('Service: currentUserFactory', function () {
  beforeEach(module('digitalWorkplaceApp'));

  let userInstance;
  let displayLoginInstance;

  // instantiate service
  let currentUserFactory;

  beforeEach(inject(function (_currentUserFactory_) {
    currentUserFactory = _currentUserFactory_;
    userInstance = {id: 0, username: 'kristofvc', firstname: 'kristof', lastname: 'vc', email: 'zever@gezever.be', role: 'ADMIN'};
  }));

  it('should define the currentUserfactory', function () {
    expect(!!currentUserFactory).toBe(true);
  });

  it('should know if the user is logged in', function () {
    expect(currentUserFactory.isLoggedIn()).toBe(false);
    currentUserFactory.setUser(userInstance);
    expect(currentUserFactory.isLoggedIn()).toBe(true);
  });

  it('should get / set the user', function () {
    currentUserFactory.setUser(userInstance);
    expect(currentUserFactory.getUser()).toBe(userInstance);
  });

  it('should set currentUser to null by default', function () {
    expect(currentUserFactory.getUser()).toBe(null);
  });

  it('should get / set the displayLogin', function () {
    currentUserFactory.setDisplayLogin(displayLoginInstance);
    expect(currentUserFactory.getDisplayLogin()).toBe(displayLoginInstance);
  });
});
