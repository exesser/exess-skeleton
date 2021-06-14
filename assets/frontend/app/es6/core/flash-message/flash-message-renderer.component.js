'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.flashMessageRenderer component
 * @description
 * # flashMessageRenderer
 *
 * The flashMessageRenderer component renders flash messages that are
 * available on flashMessageContainer. These messages disappear when they are
 * closed by the user.
 *
 * <flash-message-renderer></flash-message-renderer>
 *
 * Component of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .component('flashMessageRenderer', {
    templateUrl: 'es6/core/flash-message/flash-message-renderer.component.html',
    controllerAs: 'flashMessageRendererController',
    controller: function (flashMessageContainer) {
      const flashMessageRendererController = this;

      flashMessageRendererController.messages = flashMessageContainer.getMessages();

      /**
       * Closes a message, it is removed from the container list and not shown anymore.
       * @param message the message to remove.
       */
      flashMessageRendererController.closeMessage = function (message) {
        flashMessageContainer.removeMessage(message);
      };
    }
  });
