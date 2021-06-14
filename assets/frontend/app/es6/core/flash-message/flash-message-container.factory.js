"use strict";

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.factory:flashMessageContainer factory
 * @description
 * # flashMessageContainer
 *
 * The flashMessageContainer is responsible for store the messages we receive from backend.
 * If we receive multiple messages with the same group we store only the last one.
 */
angular.module('digitalWorkplaceApp')
  .factory('flashMessageContainer', function () {

    let messages = [];

    return {
      addMessageOfType,
      getMessages,
      clearMessages,
      removeMessage
    };

    /**
     * Store the message on this container.
     *
     * For example:
     *  { type: 'ERROR', text: 'Oh snap! Change a few things up and try submitting again.', group: 'price-calculation' }
     *
     * @param type the type of message that is added
     * @param text the text to render inside the message
     * @param group the group of the message
     */
    function addMessageOfType(type, text, group) {
      const message = { type, text, group };

      /*
       Before we add the new message we must delete all the other messages that are from the same group.
       We only want to display the last message for each group.
       */
      if (!_.isEmpty(group)) {
        _.remove(messages, { group });
      }

      // Delete the similar messages before we add this one.
      _.remove(messages, message);

      messages.push(message);
    }

    /**
     * Get a list with all the available flash messages.
     *
     * @returns {Array}
     */
    function getMessages() {
      return messages;
    }

    /**
     * Empty the container.
     */
    function clearMessages() {
      messages = [];
    }

    /**
     * Remove a message from the list.
     *
     * @param message the message we want to remove
     */
    function removeMessage(message) {
      _.remove(messages, message);
    }
  });
