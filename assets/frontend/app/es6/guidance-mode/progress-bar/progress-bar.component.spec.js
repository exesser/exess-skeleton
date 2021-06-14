'use strict';

describe('Component: progressBar', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let $rootScope;
  let $compile;
  let progressBarObserver;

  let scope;
  let element;
  let progressMetadata;

  let links;
  let step1Link;
  let step2Link;
  let step2subStep1Link;
  let step3Link;

  const template = '<progress-bar params="params" flow-name="CreateLead"></progress-bar>';

  beforeEach(inject(function (_$rootScope_, _$compile_, $state, _progressBarObserver_) {
    $rootScope = _$rootScope_;
    $compile = _$compile_;
    progressBarObserver = _progressBarObserver_;

    mockHelpers.blockUIRouter($state);

    compile();

    links = element.find('ul li a');
    step1Link = $(links[0]);
    step2Link = $(links[1]);
    step2subStep1Link = $(links[2]);
    step3Link = $(links[3]);
  }));

  function compile(additionalScopeData) {
    scope = $rootScope.$new(true);

    if (angular.isObject(additionalScopeData)) {
      angular.merge(scope, additionalScopeData);
    }

    element = angular.element(template);
    element = $compile(element)(scope);
    $rootScope.$apply();

    progressMetadata = {
      "progressPercentage": 33.33333333333333,
      "steps": [
        //This step has already been finished and can thus be reactivated.
        {
          "id": "stepId1",
          "key_c": "stepId1",
          "name": "Complete Customer Data",
          "active": false,
          "canBeActivated": true,
          "disabled": false
        },

        //This step is currently active and can thus not be activated.
        {
          "id": "stepId2",
          "key_c": "stepId2",
          "name": "Manage Opportunity",
          "active": true,
          "canBeActivated": true,
          "disabled": false,
          "substeps": [
            {
              "id": "step2subStepId1",
              "name": "Substep of Manage Opportunity",
              "active": false,
              "canBeActivated": false,
              "disabled": true
            }
          ]
        },

        //This step is currently inactive and has not been finished yet so it cannot be activated as step2 has not been finished.
        {
          "id": "stepId3",
          "key_c": "CO4L3",
          "name": "Contract & Meter details",
          "active": false,
          "canBeActivated": false,
          "disabled": true
        }
      ]
    };

    progressBarObserver.setProgressMetadata(progressMetadata);
    $rootScope.$apply();
  }

  it('should render a progress bar with multiple steps when observer is provided with data', function () {
    expect(_.trim(step1Link.text())).toBe('Complete Customer Data');
    expect(_.trim(step2Link.text())).toBe('Manage Opportunity');
    expect(_.trim(step2subStep1Link.text())).toBe('Substep of Manage Opportunity');
    expect(_.trim(step3Link.text())).toBe('Contract & Meter details');

    expect(step1Link.attr('class')).toContain('status-done');
    expect(step2Link.attr('class')).toContain('status-active');
    expect(step2subStep1Link.attr('class')).toContain('status-disabled');
    expect(step3Link.attr('class')).toContain('status-disabled');
  });

  it('should notify the observer when clicking on a step', function () {
    spyOn(progressBarObserver, 'clicked');

    step1Link.click();

    expect(progressBarObserver.clicked).toHaveBeenCalledTimes(1);
    expect(progressBarObserver.clicked).toHaveBeenCalledWith('stepId1');
  });

  it('should not notify the observer when clicking on an inactive step', function () {
    spyOn(progressBarObserver, 'clicked');

    step2subStep1Link.click();

    expect(progressBarObserver.clicked).not.toHaveBeenCalled();
  });

  it('should not notify the observer when clicking the current step', function () {
    spyOn(progressBarObserver, 'clicked');

    step2Link.click();

    expect(progressBarObserver.clicked).not.toHaveBeenCalled();
  });

  it('should not contain image', function () {
    expect(element.find('li').length).toEqual(4);
    expect(element.find('img').length).toEqual(0);
  });

  it('should contain image', function () {
    compile({params: {image: "something"}});
    expect(element.find('img[src="something"]').length).toEqual(1);
    expect(element.find('li').length).toEqual(5);
  });
});
