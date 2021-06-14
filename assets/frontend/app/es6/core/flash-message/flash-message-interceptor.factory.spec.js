'use strict';

describe('httpInterceptor: flashMessageInterceptor', function () {
  beforeEach(module('digitalWorkplaceApp'));

  // instantiate service
  let flashMessageInterceptor;

  let $q;
  let flashMessageContainer;

  const responseWithFlashes = {
    data: {
      flashMessages: [
        { type: 'WARNING', text: 'A warning', group: 'price' },
        { type: 'SUCCESS', text: 'A success', group: '' }
      ]
    }
  };

  const responseWithEmptyFlashes = { data: { flashMessages: [] } };

  const responseWithNoFlashMessagesKey = { data: {} };

  beforeEach(inject(function (_flashMessageInterceptor_, _$q_, _flashMessageContainer_) {
    $q = _$q_;

    flashMessageInterceptor = _flashMessageInterceptor_;
    flashMessageContainer = _flashMessageContainer_;

    spyOn($q, 'reject');
    spyOn(flashMessageContainer, 'addMessageOfType');
  }));

  describe('response success', function () {
    it('should display flash messages when "flashMessages" is not empty', function () {
      const result = flashMessageInterceptor.response(responseWithFlashes);

      expect(flashMessageContainer.addMessageOfType).toHaveBeenCalledTimes(2);
      expect(flashMessageContainer.addMessageOfType).toHaveBeenCalledWith('WARNING', 'A warning', 'price');
      expect(flashMessageContainer.addMessageOfType).toHaveBeenCalledWith('SUCCESS', 'A success', '');

      expect(result).toBe(responseWithFlashes);
    });

    it('should not display flash messages when "flashMessages" is empty', function () {
      const result = flashMessageInterceptor.response(responseWithEmptyFlashes);

      expect(flashMessageContainer.addMessageOfType).not.toHaveBeenCalled();

      expect(result).toBe(responseWithEmptyFlashes);
    });

    it('should not display flash messages when "flashMessages" is not defined', function () {
      const result = flashMessageInterceptor.response(responseWithNoFlashMessagesKey);

      expect(flashMessageContainer.addMessageOfType).not.toHaveBeenCalled();

      expect(result).toBe(responseWithNoFlashMessagesKey);
    });
  });

  describe('responseError', function () {
    it('should display flash messages when "flashMessages" is not empty', function () {
      flashMessageInterceptor.responseError(responseWithFlashes);

      expect(flashMessageContainer.addMessageOfType).toHaveBeenCalledTimes(2);
      expect(flashMessageContainer.addMessageOfType).toHaveBeenCalledWith('WARNING', 'A warning', 'price');
      expect(flashMessageContainer.addMessageOfType).toHaveBeenCalledWith('SUCCESS', 'A success', '');

      expect($q.reject).toHaveBeenCalledTimes(1);
      expect($q.reject).toHaveBeenCalledWith(responseWithFlashes);
    });

    it('should not display flash messages when "flashMessages" is empty', function () {
      flashMessageInterceptor.responseError(responseWithEmptyFlashes);

      expect(flashMessageContainer.addMessageOfType).not.toHaveBeenCalled();

      expect($q.reject).toHaveBeenCalledTimes(1);
      expect($q.reject).toHaveBeenCalledWith(responseWithEmptyFlashes);
    });

    it('should not display flash messages when "flashMessages" is not defined', function () {
      flashMessageInterceptor.responseError(responseWithNoFlashMessagesKey);

      expect(flashMessageContainer.addMessageOfType).not.toHaveBeenCalled();

      expect($q.reject).toHaveBeenCalledTimes(1);
      expect($q.reject).toHaveBeenCalledWith(responseWithNoFlashMessagesKey);
    });
  });
});
