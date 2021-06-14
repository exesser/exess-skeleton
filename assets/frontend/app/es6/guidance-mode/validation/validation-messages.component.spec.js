'use strict';

describe('Component: validation-messages', function() {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let $rootScope;
  let $compile;

  let scope;
  let element;
  const template = `<validation-messages messages="messages"></validation-messages>`;

  beforeEach(inject(function(_$rootScope_, _$compile_, $state) {
    $rootScope = _$rootScope_;
    $compile = _$compile_;
    mockHelpers.blockUIRouter($state);

    scope = $rootScope.$new();
    scope.messages = [];

    element = angular.element(template);
    element = $compile(element)(scope);
    $rootScope.$apply();
  }));

  it('should render nothing if no messages are set.', function() {
    expect(element.find("span.error-message").length).toBe(0);
    expect(element.find("li.error-message").length).toBe(0);
  });

  it('should render a span if it receives one message', function() {
    scope.messages = ["Something is wrong."];
    $rootScope.$apply();

    const errorMessage = element.find("span.error-message");
    expect(errorMessage.length).toBe(1);
    expect(errorMessage.text()).toBe("Something is wrong.");
    expect(element.find("li.error-message").length).toBe(0);
  });

  it('should render a list if it receives multiple messages', function() {
    scope.messages = ["Something is wrong.", "Something is REALLY wrong."];
    $rootScope.$apply();

    const errorMessages = element.find("li.error-message");
    expect(errorMessages.length).toBe(2);
    expect($(errorMessages[0]).text()).toBe("Something is wrong.");
    expect($(errorMessages[1]).text()).toBe("Something is REALLY wrong.");
    expect(element.find("span.error-message").length).toBe(0);
  });
});
