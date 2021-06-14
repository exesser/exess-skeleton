'use strict';

describe('Component: flashMessageRenderer', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let $rootScope;

  let element;
  let flashMessages;

  let flashMessageContainer;

  const template = "<flash-message-renderer></flash-message-renderer>";

  beforeEach(inject(function (_$rootScope_, $compile, _flashMessageContainer_) {
    $rootScope = _$rootScope_;
    flashMessageContainer = _flashMessageContainer_;

    spyOn(flashMessageContainer, 'getMessages').and.returnValue([
      { type: "ERROR", text: "Something went horribly wrong.", group: '' }
    ]);

    const scope = $rootScope.$new();
    element = angular.element(template);
    element = $compile(element)(scope);
    $rootScope.$apply();

    expect(flashMessageContainer.getMessages).toHaveBeenCalledTimes(1);
    flashMessages = element.find("flash-message");
  }));

  it('should render a message', function () {
    expect(flashMessages.length).toBe(1);
    expect($(flashMessages[0]).find("p").text()).toBe("Something went horribly wrong.");
    expect($(flashMessages[0]).find("div").hasClass('is-error')).toBe(true);
  });

  it('should call flashMessageContainer.removeMessage when manually clicking the close button', function () {
    spyOn(flashMessageContainer, 'removeMessage');
    expect(flashMessageContainer.removeMessage).not.toHaveBeenCalled();

    $(flashMessages[0]).find(".action-close").click();

    expect(flashMessageContainer.removeMessage).toHaveBeenCalledTimes(1);
  });
});
