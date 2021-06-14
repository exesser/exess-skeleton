'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.flashMessage component
 * @description
 * # flashMessage
 *
 * The flashMessage component represents a single flash message.
 * It takes a type, text and a function to invoke when manually closing the message as input.
 *
 * Example usage:
 *
 * <flash-message
 *   type="ERROR"
 *   text="The server blew up attempting to process your request."
 *   close="close()">
 * </flash-message>
 *
 * Component of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .component('flashMessage', {
    templateUrl: 'es6/core/flash-message/flash-message.component.html',
    bindings: {
      type: "@",
      text: "@",
      close: "&"
    },
    controllerAs: 'flashMessageController'
  });
