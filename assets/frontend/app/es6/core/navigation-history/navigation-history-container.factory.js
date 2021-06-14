"use strict";

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.factory:navigationHistoryContainer factory
 * @description
 * # navigationHistoryContainer
 *
 * The navigationHistoryContainer is responsible for store the state history.
 * If we receive multiple items with same state we store only the last one.
 */
angular.module('digitalWorkplaceApp')
  .factory('navigationHistoryContainer', function ($window) {
    const storageActionsKey = "HISTORY_ACTIONS_KEY";
    const storageShowActionsKey = "HISTORY_SHOW_ACTIONS_KEY";
    const storageShowEditIconKey = "CRUD_SHOW_EDIT_ICON_KEY";

    let actions = $window.sessionStorage.getItem(storageActionsKey);
    if (_.isNull(actions)) {
      actions = [];
    } else {
      actions = angular.fromJson(actions);
    }

    let showActions = $window.sessionStorage.getItem(storageShowActionsKey);
    if (_.isNull(showActions)) {
      showActions = true;
    }

    let showEditIcon = $window.localStorage.getItem(storageShowEditIconKey);
    if (_.isNull(showActions)) {
      showEditIcon = false;
    }

    return {
      addAction,
      getActions,
      setShowEditIcon,
      getShowEditIcon,
      setShowActions,
      getShowActions
    };

    /**
     * Store the action on this container.
     *
     * @param label the label that is displayed on render
     * @param $state the state of the page
     */
    function addAction(label, $state) {
       // Before we add the new item we must delete all the others with same label.
      _.remove(actions, {label});

      actions.push({ label, stateName: $state.current.name, stateParams: angular.copy($state.params) });
      $window.sessionStorage.setItem(storageActionsKey, angular.toJson(actions));
    }

    /**
     * Get the history list.
     *
     * @returns {Array}
     */
    function getActions() {
      return actions;
    }

    /**
     * @param value bool - indicates if the actions are visible
     */
    function setShowActions(newShowActions) {
      showActions = newShowActions;
      $window.sessionStorage.setItem(storageShowActionsKey, showActions);
    }

    /**
     * @returns boolean
     */
    function getShowActions() {
      return showActions === true || showActions === 'true';
    }

    /**
     * @param value bool - indicates if the edit icon should be displayed
     */
    function setShowEditIcon(newShowActions) {
      showEditIcon = newShowActions;
      $window.localStorage.setItem(storageShowEditIconKey, showEditIcon);
    }

    /**
     * @returns boolean
     */
    function getShowEditIcon() {
      return showEditIcon === true || showEditIcon === 'true';
    }
  });
