'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp:validationMessages component
 * @description
 * # validationMessages
 *
 * This component renders a tooltip with either a single validation
 * message in a span or a list of validation messages inside it.
 *
 * When there is a single error it will display a singular tooltip:
 *
 * <small class="tooltip-up">
 *  <span class="error-message">Something is wrong</span>
 * </small>
 *
 * If there are multiple errors the tooltip will have a list:
 *
 * <small class="tooltip-up">
 *   <ul>
 *     <li class="error-message">Something is wrong</li>
 *     <li class="error-message">Something is REALLY wrong</li>
 *   </ul>
 * </small>
 *
 * Example configuration:
 *
 * <validation-messages
 *   messages='["Something is wrong"]'>
 * </validation-messages>
 */
angular.module('digitalWorkplaceApp')
  .component('validationMessages', {
    templateUrl: 'es6/guidance-mode/validation/validation-messages.component.html',
    bindings: {
      messages: "<"
    },
    controllerAs: 'validationMessagesController',
    controller: _.noop
  });
