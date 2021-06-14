'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.factory:guidanceModalObserver factory
 * @description
 * # guidanceModalObserver
 *
 * ## Responsibility
 *
 * The guidanceModalObserver observer is responsible for indicating
 * that we want a guidance modal to be opened. It contains only one
 * event: 'openModal' with the modalData and confirmAction as an argument.
 * This confirmation action string is needed because in the case of the
 * crud-list we want to open modals that don't immediately save data
 * on the backend. When the modal finishes, the data is sent to the backend
 * like with any other guidance. The response is given back as a result
 * of the 'openModal' function call.
 *
 * ## Lifespan and cardinality
 *
 * The lifespan of the miniGuidanceModeObserver is unbounded.
 * It is created when the application starts up and it remains alive
 * during its entire lifespan.
 *
 * The modal can be triggered by the commandHandler or the crud-list.
 *
 * The modalComponent stays alive during the lifecycle of the application
 * but it is reset and thus hidden after confirming or hiding.
 */
angular.module('digitalWorkplaceApp')
  .factory('guidanceModalObserver', function() {

    let openModalCallback = _.noop;
    let resetModalCallback = _.noop;

    return {
      openModal,
      resetModal,
      registerOpenModalCallback,
      registerResetModalCallback
    };

    /**
     * Opens a modal from the given modalData.
     * @returns {Promise} a promise that can be resolved or rejected.
     */
    function openModal(modalData, confirmAction) {
      return openModalCallback(modalData, confirmAction);
    }

    /**
     * Reset the modal.
     */
    function resetModal() {
      resetModalCallback();
    }

    /**
     * Register a callback that is invoked when the openModal function is called.
     * @param callback function
     */
    function registerOpenModalCallback(callback) {
      openModalCallback = callback;
    }

    /**
     * Register a callback that is invoked when the resetModal function is called.
     * @param callback function
     */
    function registerResetModalCallback(callback) {
      resetModalCallback = callback;
    }
  });
