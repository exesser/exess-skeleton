'use strict';

describe('Component: focusModeContent', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let $rootScope;
  let element;

  let controller;
  let backArrowButton;
  let topArrowButton;
  let slideAnimation;

  let $stateChangeSuccess;

  let template = `
    <focus-mode-content title="Awesome Copter"
                        back-arrow-clicked="controller.backArrowClicked()"
                        show-top-arrow="true"
                        top-arrow-clicked="controller.topArrowClicked()">
      <p>Content of the focus mode.</p>
    </focus-mode-content>
  `;

  beforeEach(inject(function (_$rootScope_, $compile, $state, _slideAnimation_) {
    mockHelpers.blockUIRouter($state);

    $rootScope = _$rootScope_;
    slideAnimation = _slideAnimation_;

    spyOn(slideAnimation, 'open');

    controller = {
      backArrowClicked: jasmine.createSpy(),
      topArrowClicked: jasmine.createSpy()
    };

    const scope = $rootScope.$new();
    scope.controller = controller;

    element = angular.element(template);
    element = $compile(element)(scope);
    $rootScope.$apply();

    const buttons = element.find('.top > a');
    backArrowButton = $(buttons[0]);
    topArrowButton = $(buttons[1]);

    spyOn($rootScope, '$on').and.callFake((listener, f) => {
      if (listener === '$stateChangeSuccess') {
        $stateChangeSuccess = f;
      }
    });
  }));

  it('should compile down to a focus mode with transcluded content in it, and open the slide animation on init.', function () {
    expect(element.find('p').text()).toBe('Content of the focus mode.');
    expect(element.find('h4').text()).toBe('Awesome Copter');

    expect(slideAnimation.open).toHaveBeenCalledTimes(1);
  });

  describe('when the back button is clicked', function () {
    it('should inform the controller', function () {
      spyOn(slideAnimation, 'close');

      backArrowButton.click();
      $rootScope.$apply();

      expect(controller.backArrowClicked).toHaveBeenCalledTimes(1);
    });

    it('should trigger the slideAnimation when the $stateChangeSuccess is fired', function () {
      spyOn(slideAnimation, 'close');

      backArrowButton.click();
      $rootScope.$apply();

      // Initially we expect the slideAnimation not to have been called.
      expect(slideAnimation.close).not.toHaveBeenCalled();

      // Then we trigger the $stateChangeSuccess event.
      $stateChangeSuccess(undefined, { name: 'focus-mode' }, undefined, { name: 'dashboard' }, { query: 'Hi guys' });

      // Now we expect that the slideAnimation has been called.
      expect(slideAnimation.close).toHaveBeenCalledTimes(1);
    });
  });

  describe('when the top button is clicked', function () {
    it('should inform the controller', function () {
      spyOn(slideAnimation, 'close');

      topArrowButton.click();
      $rootScope.$apply();

      expect(controller.topArrowClicked).toHaveBeenCalledTimes(1);
    });

    it('should trigger the slideAnimation when the $stateChangeSuccess is fired', function () {
      spyOn(slideAnimation, 'close');

      topArrowButton.click();
      $rootScope.$apply();

      // Initially we expect the slideAnimation not to have been called.
      expect(slideAnimation.close).not.toHaveBeenCalled();

      // Then we trigger the $stateChangeSuccess event.
      $stateChangeSuccess(undefined, { name: 'focus-mode' }, undefined, { name: 'dashboard' }, { query: 'Hi guys' });

      // Now we expect that the slideAnimation has been called.
      expect(slideAnimation.close).toHaveBeenCalledTimes(1);
    });
  });

});
