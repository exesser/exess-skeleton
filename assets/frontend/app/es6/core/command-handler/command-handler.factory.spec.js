'use strict';

describe('Service: commandHandlerFactory', function () {
  beforeEach(module('digitalWorkplaceApp'));

  // instantiate service
  let commandHandler;
  let guidanceModalObserver;
  let miniGuidanceModeObserver;
  let listObserver;
  let progressBarObserver;
  let modelSession;

  let $q;
  let $state;
  let $rootScope;
  let $log;
  let $timeout;
  let previousState;
  let replaceSpecialCharacters;
  let $window;
  let guidanceGuardian;

  beforeEach(inject(function (_commandHandler_, _guidanceModalObserver_, _miniGuidanceModeObserver_,
                              _listObserver_, _$q_, _$state_, _$rootScope_, _$log_, _$timeout_, _previousState_,
                              _$window_, _progressBarObserver_, _modelSession_, _replaceSpecialCharacters_,
                              _guidanceGuardian_) {
    commandHandler = _commandHandler_;
    guidanceModalObserver = _guidanceModalObserver_;
    miniGuidanceModeObserver = _miniGuidanceModeObserver_;
    listObserver = _listObserver_;
    progressBarObserver = _progressBarObserver_;
    modelSession = _modelSession_;
    $timeout = _$timeout_;
    previousState = _previousState_;
    replaceSpecialCharacters = _replaceSpecialCharacters_;
    $window = _$window_;
    guidanceGuardian = _guidanceGuardian_;

    $q = _$q_;
    $state = _$state_;
    $rootScope = _$rootScope_;
    $log = _$log_;

    spyOn(guidanceModalObserver, 'resetModal');
    spyOn(guidanceGuardian, 'resetGuardian');
    spyOn(replaceSpecialCharacters, 'replaceArraySign').and.callThrough();

    mockHelpers.blockUIRouter($state);
  }));

  it('should throw an error for an unsupported command', function () {
    expect(function () {
      commandHandler.handle({
        command: "solveHaltingProblem",
        arguments: {}
      });
    }).toThrow(new Error("Unsupported command 'solveHaltingProblem'."));
  });

  it('should be able to open a modal', function () {
    const deferred = $q.defer();
    spyOn(guidanceModalObserver, 'openModal').and.returnValue(deferred.promise);

    expect(replaceSpecialCharacters.replaceArraySign).not.toHaveBeenCalled();

    commandHandler.handle({
      command: "openModal",
      arguments: { name: "Ken Block" }
    });

    expect(guidanceModalObserver.resetModal).toHaveBeenCalledTimes(1);
    $timeout.flush();

    expect(guidanceModalObserver.openModal).toHaveBeenCalledTimes(1);
    expect(guidanceModalObserver.openModal).toHaveBeenCalledWith({ name: "Ken Block" });

    expect(replaceSpecialCharacters.replaceArraySign).toHaveBeenCalledTimes(1);
    expect(replaceSpecialCharacters.replaceArraySign).toHaveBeenCalledWith({ name: "Ken Block" });

    deferred.resolve({
      "command": "navigate",
      "arguments": {
        "linkTo": "dashboard",
        "params": {
          "mainMenuKey": "sales-marketing",
          "dashboardId": "leads"
        }
      }
    });
    $rootScope.$apply();
    $timeout.flush();

    expect(guidanceGuardian.resetGuardian).not.toHaveBeenCalled();
    expect($state.go).toHaveBeenCalledTimes(1);
    expect($state.go).toHaveBeenCalledWith('dashboard', {
      "mainMenuKey": "sales-marketing",
      "dashboardId": "leads"
    }, { reload: true });
  });

  it('should be able to open a popup', function () {
    const deferred = $q.defer();
    spyOn(guidanceModalObserver, 'openModal').and.returnValue(deferred.promise);

    // expect(replaceSpecialCharacters.replaceArraySign).not.toHaveBeenCalled();

    commandHandler.handle({
      command: "popUpMessage",
      arguments: { message: "Ken Block", title: "Info"  }
    });

    expect(guidanceModalObserver.resetModal).toHaveBeenCalledTimes(1);
    $timeout.flush();

    expect(guidanceModalObserver.openModal).toHaveBeenCalledTimes(1);
    expect(guidanceModalObserver.openModal).toHaveBeenCalledWith({
      title: 'Info',
      grid: {columns: [{rows: [{type: 'paragraph', options: {text: 'Ken Block'}}]}]}
    });
  });

  it('should be able to open a mini-guidance that can be resolved', function () {
    const deferred = $q.defer();
    spyOn(miniGuidanceModeObserver, 'openMiniGuidance').and.returnValue(deferred.promise);

    commandHandler.handle({
      command: "openMiniGuidance",
      arguments: {}
    });

    expect(guidanceModalObserver.resetModal).toHaveBeenCalledTimes(1);
    $timeout.flush();

    expect(miniGuidanceModeObserver.openMiniGuidance).toHaveBeenCalled();

    deferred.resolve({
      "command": "navigate",
      "arguments": {
        "linkTo": "dashboard",
        "force": true,
        "params": {
          "mainMenuKey": "sales-marketing",
          "dashboardId": "accounts"
        }
      }
    });
    $rootScope.$apply();
    $timeout.flush();

    expect(guidanceGuardian.resetGuardian).toHaveBeenCalledTimes(1);
    expect($state.go).toHaveBeenCalledTimes(1);
    expect($state.go).toHaveBeenCalledWith('dashboard', {
      "mainMenuKey": "sales-marketing",
      "dashboardId": "accounts"
    }, { reload: true });
  });

  it('should be able to refresh the page', function () {
    $state.current = 'fake-state';
    $state.params = { param1: 42 };

    commandHandler.handle({
      command: "reloadPage",
      arguments: {}
    });
    expect(guidanceModalObserver.resetModal).toHaveBeenCalledTimes(1);
    $timeout.flush();
    $rootScope.$apply();

    expect($state.go).toHaveBeenCalledTimes(1);
    expect($state.go).toHaveBeenCalledWith('fake-state', { param1: 42 }, { reload: true });
  });

  it('should be able to navigate to another page', function () {
    commandHandler.handle({
      "command": "navigate",
      "arguments": {
        "linkTo": "dashboard",
        "params": {
          "mainMenuKey": "sales-marketing",
          "dashboardId": "leads"
        }
      }
    });
    expect(guidanceModalObserver.resetModal).toHaveBeenCalledTimes(1);
    $timeout.flush();

    $rootScope.$apply();

    expect($state.go).toHaveBeenCalledTimes(1);
    expect($state.go).toHaveBeenCalledWith('dashboard', {
      "mainMenuKey": "sales-marketing",
      "dashboardId": "leads"
    }, { reload: true });
  });

  it('should be able to navigate to another page in new window', function () {
    spyOn($window, 'open');

    commandHandler.handle({
      "command": "navigate",
      "arguments": {
        "newWindow": true,
        "linkTo": "dashboard",
        "params": {
          "mainMenuKey": "sales-marketing",
          "dashboardId": "leads"
        }
      }
    });
    expect(guidanceModalObserver.resetModal).toHaveBeenCalledTimes(1);
    $timeout.flush();

    $rootScope.$apply();

    expect($window.open).toHaveBeenCalledTimes(1);
    expect($window.open).toHaveBeenCalledWith('#/sales-marketing/dashboard/leads/', '_blank');
  });

  it('should be able to navigate to another page when a model is added', function () {
    spyOn(modelSession, 'setModel');
    const model = {
      "name": "Ken Block",
      "number": "43"
    };

    commandHandler.handle({
      command: "navigate",
      arguments: {
        linkTo: "dashboard",
        params: {
          mainMenuKey: "sales-marketing",
          dashboardId: "leads",
          model
        }
      }
    });
    expect(guidanceModalObserver.resetModal).toHaveBeenCalledTimes(1);
    $timeout.flush();

    $rootScope.$apply();

    expect(modelSession.setModel).toHaveBeenCalledTimes(1);
    expect(modelSession.setModel).toHaveBeenCalledWith(jasmine.any(String), model);

    const modelKey = modelSession.setModel.calls.mostRecent().args[0];
    expect(modelKey.length).toEqual(10);

    expect($state.go).toHaveBeenCalledTimes(1);
    expect($state.go).toHaveBeenCalledWith("dashboard", {
      mainMenuKey: "sales-marketing",
      dashboardId: "leads",
      modelKey
    }, { reload: true });
  });

  it('should change the model session key when dwp|guidanceFlowId is on model', function () {
    spyOn(modelSession, 'setModel');
    const model = {
      "name": "Ken Block",
      "number": "43",
      "dwp|guidanceFlowId": "my-guidance"
    };

    commandHandler.handle({
      command: "navigate",
      arguments: {
        linkTo: "dashboard",
        params: {
          mainMenuKey: "sales-marketing",
          dashboardId: "leads",
          model
        }
      }
    });
    expect(guidanceModalObserver.resetModal).toHaveBeenCalledTimes(1);
    $timeout.flush();

    $rootScope.$apply();

    expect(modelSession.setModel).toHaveBeenCalledTimes(1);
    expect(modelSession.setModel).toHaveBeenCalledWith(jasmine.any(String), model);

    const modelKey = modelSession.setModel.calls.mostRecent().args[0];
    expect(modelKey.length).toEqual(22);
    expect(modelKey).toContain('-my-guidance');

    expect($state.go).toHaveBeenCalledTimes(1);
    expect($state.go).toHaveBeenCalledWith("dashboard", {
      mainMenuKey: "sales-marketing",
      dashboardId: "leads",
      modelKey: _.replace(modelKey, '-my-guidance', '')
    }, { reload: true });
  });

  it('should be able to reload a list', function () {
    spyOn(listObserver, 'reloadList');

    commandHandler.handle({
      "command": "reloadList",
      "arguments": {
        "listKey": "AwesomeList"
      }
    });
    expect(guidanceModalObserver.resetModal).toHaveBeenCalledTimes(1);
    $timeout.flush();

    $rootScope.$apply();

    expect(listObserver.reloadList).toHaveBeenCalledTimes(1);
    expect(listObserver.reloadList).toHaveBeenCalledWith('AwesomeList');
  });

  it('should be able to handle doing nothing', function () {
    commandHandler.handle({
      "command": "nothing",
      arguments: {}
    });
    expect(guidanceModalObserver.resetModal).toHaveBeenCalledTimes(1);
    $timeout.flush();

    expect($log.log.logs[0][0]).toBe('commandHandler: was told to do nothing.');
  });

  it('should be able to go to the previous page', function () {
    spyOn(previousState, 'navigateTo');

    commandHandler.handle({
      "command": "previousPage",
      "arguments": {}
    });
    expect(guidanceModalObserver.resetModal).toHaveBeenCalledTimes(1);
    $timeout.flush();

    expect(previousState.navigateTo).toHaveBeenCalledTimes(1);
  });

  it('should be able to open a link to an external page', function () {
    spyOn($window.location, 'assign');

    commandHandler.handle({
      "command": "openLink",
      "arguments": {
        "link": "http://42.nl"
      }
    });
    expect(guidanceModalObserver.resetModal).toHaveBeenCalledTimes(1);
    $timeout.flush();

    expect($window.onbeforeunload).toBeNull();
    expect($window.location.assign).toHaveBeenCalledTimes(1);
    expect($window.location.assign).toHaveBeenCalledWith('http://42.nl');

    commandHandler.handle({
      "command": "openLink",
      "arguments": {
        "link": "http://42.nl",
        "newTab": false
      }
    });
    $timeout.flush();

    expect($window.location.assign).toHaveBeenCalledTimes(2);
  });

  it('should be able to open a link to an external page in a new tab', function () {
    spyOn($window, 'open');

    commandHandler.handle({
      "command": "openLink",
      "arguments": {
        "link": "http://42.nl",
        "newTab": true
      }
    });
    $timeout.flush();

    expect($window.open).toHaveBeenCalledTimes(1);
    expect($window.open).toHaveBeenCalledWith('http://42.nl', '_blank');
  });

  it('should be able to change a flow step', function () {
    spyOn(progressBarObserver, 'clicked');

    commandHandler.handle({
      "command": "changeStep",
      "arguments": {
        "stepId": "CQFA_BILLING"
      }
    });
    expect(guidanceModalObserver.resetModal).toHaveBeenCalledTimes(1);
    $timeout.flush();

    expect(progressBarObserver.clicked).toHaveBeenCalledTimes(1);
    expect(progressBarObserver.clicked).toHaveBeenCalledWith('CQFA_BILLING');
  });
});
