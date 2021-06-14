"use strict";

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.confirmAction
 * @description
 * # confirmAction
 *
 * The confirmAction directive can be used to create a modal for select-with-search form element.
 * It then allows you to search for data there and select some records. The selected records are
 * store in "selectedResult". When you click submit the confirmCallback is called.
 *
 * Example usage:
 *
 * <confirm-action
 *   button-icon="icon"
 *   button-label="label"
 *   confirm-message="confirm message?"
 *   action="controller.confirm()"
 * </confirm-action>
 *
 * Directive of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .component('confirmAction', {
    templateUrl: 'es6/core/confirm-action/confirm-action.component.html',
    bindings: {
      action: "&",
      buttonIcon: "@",
      buttonLabel: "@",
      confirmMessage: "@"
    },
    controllerAs: 'confirmActionController',
    controller: function () {
      const confirmActionController = this;
      confirmActionController.showConfirmBox = false;

      confirmActionController.buttonClicked = function () {
        if (_.isEmpty(confirmActionController.confirmMessage)) {
          confirmActionController.triggerAction();
          return;
        }

        confirmActionController.showConfirmBox = true;
      };

      confirmActionController.triggerAction = function () {
        confirmActionController.close();
        confirmActionController.action();
      };

      confirmActionController.close = function () {
        confirmActionController.showConfirmBox = false;
      };
    }
  });
