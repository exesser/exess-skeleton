"use strict";

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.factory:guidanceModeBackendState factory
 * @description
 * # guidanceModeBackendState
 * in this factory we are keeping the state of the backend (busy or not)
 * and the performed action if is busy.
 */
angular.module('digitalWorkplaceApp')
  .factory('guidanceModeBackendState', function () {

    let backendIsBusy = false;
    let performedAction = {};
    let backendIsBusyForKey = [];

    return {
      setBackendIsBusy,
      getBackendIsBusy,
      getPerformedAction,
      addBackendIsBusyFor,
      removeBackendIsBusyFor
    };

    /**
     * Sets the state of the backend.
     * @param {boolean} isBusy
     * @param {object} action
     */
    function setBackendIsBusy(isBusy, action = {}) {
      backendIsBusy = isBusy;
      performedAction = action;
    }

    /**
     * Set backend busy for a specific key.
     * This means we have to remove that specific key to be able to enable save button.
     * @param {string} key
     */
    function addBackendIsBusyFor(key) {
      backendIsBusyForKey.push(key);
    }

    /**
     * Remove backend busy for a specific key.
     * @param {string} key
     */
    function removeBackendIsBusyFor(key) {
      _.remove(backendIsBusyForKey, function (k) {
        return k === key;
      });
    }

    /**
     * Returns the state of the backend.
     * @returns {boolean} current state
     */
    function getBackendIsBusy() {
      if (!_.isEmpty(backendIsBusyForKey)) {
        return true;
      }

      return backendIsBusy;
    }

    /**
     * Returns the last performed action.
     * @returns {object} performed action
     */
    function getPerformedAction() {
      return performedAction;
    }
  });
