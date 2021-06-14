'use strict';

describe('Factory: progressBarObserver', function () {

  beforeEach(module('digitalWorkplaceApp'));

  let progressBarObserver;

  beforeEach(inject(function (_progressBarObserver_) {
    progressBarObserver = _progressBarObserver_;
  }));

  it('should register stepMetadata callback.', function () {
    const observer = jasmine.createSpy('observer');

    progressBarObserver.registerProgressMetadataCallback(observer);

    progressBarObserver.setProgressMetadata('stepMetadata');

    expect(observer).toHaveBeenCalledTimes(1);
    expect(observer).toHaveBeenCalledWith('stepMetadata');
  });

  it('should register clicked callback.', function () {
    const observer = jasmine.createSpy('observer');

    progressBarObserver.registerClickCallback(observer);

    progressBarObserver.clicked('clicked');

    expect(observer).toHaveBeenCalledTimes(1);
    expect(observer).toHaveBeenCalledWith('clicked');
  });
});
