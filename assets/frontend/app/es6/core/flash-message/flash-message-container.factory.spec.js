'use strict';

describe('Factory: flashMessageContainer', function () {
  beforeEach(module('digitalWorkplaceApp'));

  // instantiate service
  let flashMessageContainer;

  beforeEach(inject(function (_flashMessageContainer_) {
    flashMessageContainer = _flashMessageContainer_;
  }));

  it('should NOT have any messages when is instantiated', function () {
    expect(flashMessageContainer.getMessages().length).toBe(0);
  });

  it('should add messages', function () {
    expect(flashMessageContainer.getMessages().length).toBe(0);

    flashMessageContainer.addMessageOfType("ERROR", "Oh snap! Change a few things up and try submitting again.", "price");
    expect(flashMessageContainer.getMessages().length).toBe(1);

    flashMessageContainer.addMessageOfType("WARNING", "This is a warning, it will be yellow.", "");
    flashMessageContainer.addMessageOfType("SUCCESS", "This is a success, it will be green", "");
    flashMessageContainer.addMessageOfType("INFORMATION", "This is information, it will be blue", "");

    expect(flashMessageContainer.getMessages().length).toBe(4);
    expect(flashMessageContainer.getMessages()).toEqual([
      { type: 'ERROR', text: 'Oh snap! Change a few things up and try submitting again.', group: 'price' },
      { type: 'WARNING', text: 'This is a warning, it will be yellow.', group: '' },
      { type: 'SUCCESS', text: 'This is a success, it will be green', group: '' },
      { type: 'INFORMATION', text: 'This is information, it will be blue', group: '' }
    ]);
  });

  it('should remove a message from the list when we try to add one which already exists and add it to the bottom', function () {
    flashMessageContainer.addMessageOfType("ERROR", "Oh snap! Change a few things up and try submitting again.", "price");
    flashMessageContainer.addMessageOfType("WARNING", "This is a warning, it will be yellow.", "");
    flashMessageContainer.addMessageOfType("SUCCESS", "This is a success, it will be green", "");
    flashMessageContainer.addMessageOfType("INFORMATION", "This is information, it will be blue", "");

    flashMessageContainer.addMessageOfType("WARNING", "This is a warning, it will be yellow.", "");

    expect(flashMessageContainer.getMessages().length).toBe(4);
    expect(flashMessageContainer.getMessages()).toEqual([
      { type: 'ERROR', text: 'Oh snap! Change a few things up and try submitting again.', group: 'price' },
      { type: 'SUCCESS', text: 'This is a success, it will be green', group: '' },
      { type: 'INFORMATION', text: 'This is information, it will be blue', group: '' },
      { type: 'WARNING', text: 'This is a warning, it will be yellow.', group: '' }
    ]);
  });

  it('should store only the last message of a group if the group is not empty', function () {
    flashMessageContainer.addMessageOfType("ERROR", "Oh snap! Change a few things up and try submitting again.", "price");
    flashMessageContainer.addMessageOfType("WARNING", "This is a warning, it will be yellow.", "");
    flashMessageContainer.addMessageOfType("SUCCESS", "This is a success, it will be green", "price");
    flashMessageContainer.addMessageOfType("INFORMATION", "This is information, it will be blue", "");

    expect(flashMessageContainer.getMessages().length).toBe(3);
    expect(flashMessageContainer.getMessages()).toEqual([
      { type: 'WARNING', text: 'This is a warning, it will be yellow.', group: '' },
      { type: 'SUCCESS', text: 'This is a success, it will be green', group: 'price' },
      { type: 'INFORMATION', text: 'This is information, it will be blue', group: '' }
    ]);
  });

  it('should remove a message from the list', function () {
    flashMessageContainer.addMessageOfType("ERROR", "Oh snap! Change a few things up and try submitting again.", "");
    flashMessageContainer.addMessageOfType("WARNING", "This is a warning, it will be yellow.", "");
    flashMessageContainer.addMessageOfType("SUCCESS", "This is a success, it will be green", "");
    flashMessageContainer.addMessageOfType("INFORMATION", "This is information, it will be blue", "");

    expect(flashMessageContainer.getMessages().length).toBe(4);

    flashMessageContainer.removeMessage({ type: 'SUCCESS', text: 'Bla', group: '' });
    expect(flashMessageContainer.getMessages().length).toBe(4);

    flashMessageContainer.removeMessage({ type: 'SUCCESS', text: 'This is a success, it will be green', group: '' });

    expect(flashMessageContainer.getMessages().length).toBe(3);
    expect(flashMessageContainer.getMessages()).toEqual([
      { type: 'ERROR', text: 'Oh snap! Change a few things up and try submitting again.', group: '' },
      { type: 'WARNING', text: 'This is a warning, it will be yellow.', group: '' },
      { type: 'INFORMATION', text: 'This is information, it will be blue', group: '' }
    ]);
  });

  it('should cleare the list', function () {
    flashMessageContainer.addMessageOfType("ERROR", "Oh snap! Change a few things up and try submitting again.", "");
    flashMessageContainer.addMessageOfType("WARNING", "This is a warning, it will be yellow.", "");
    flashMessageContainer.addMessageOfType("SUCCESS", "This is a success, it will be green", "");
    flashMessageContainer.addMessageOfType("INFORMATION", "This is information, it will be blue", "");

    expect(flashMessageContainer.getMessages().length).toBe(4);

    flashMessageContainer.clearMessages();
    expect(flashMessageContainer.getMessages().length).toBe(0);
  });
});
