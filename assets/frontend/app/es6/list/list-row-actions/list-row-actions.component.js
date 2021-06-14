'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp:listRowActions component
 * @description
 * # listRowActions
 *
 * Creates a black bar with actions buttons.
 * The actions are retrieved from the back-end by calling the
 * listDatasource with the provided record-type and record-id.
 *
 * For example:
 *
 * <list-row-actions
 *   record-type="lead"
 *   record-id="1337"
 *   id="account__123-123-345"
 *   grid-key="action-bar">
 * </list-row-actions>
 *
 * Component of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .component('listRowActions', {
    templateUrl: 'es6/list/list-row-actions/list-row-actions.component.html',
    bindings: {
      recordType: "@",
      recordId: "@",
      gridKey: "@",
      id: "@",
      actionData: "<"
    },
    controllerAs: 'listRowActionsController',
    controller: function(listDatasource, listObserver) {
      const listRowActionsController = this;

      listRowActionsController.actions = [];

      listDatasource.getActionButtons({
        recordType: listRowActionsController.recordType,
        recordId: listRowActionsController.recordId,
        actionData: listRowActionsController.actionData
      }).then(function (buttons) {
        listRowActionsController.buttons = buttons;
      });

      listRowActionsController.closeClicked = function () {
        listObserver.toggleExtraRowContentPlaceholder(listRowActionsController.recordType, listRowActionsController.gridKey, listRowActionsController.id);
      };
    }
  });
