"use strict";

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.factory:primaryButtonObserver factory
 * @description
 * # primaryButtonObserver
 *
 * ## Responsibility
 *
 * In large guidance modes there is a button that either displays 'next' or 'confirm'
 * depending on whether there is a next step in the guidance available or
 * if the current step is the last one. The primary button can be clicked
 * to either go to the next step or confirm the guidance.
 *
 * The primaryButtonObserver's setPrimaryButton data event is responsible
 * for setting the 'primaryButtonData', which contains the title (next or confirm)
 * and whether or not the button is enabled. This call is delegated to the
 * topActionState factory. It is called from the guidanceDirective when a step
 * has loaded.
 *
 * There is also a resetPrimaryButtonData event which sets the primary
 * button back to its initial empty state. This is called when the
 * guidance is complete and we want to hide the button.
 *
 * Furthermore there is a primaryButtonClicked event that is fired
 * when the primary button is clicked. It informs the guidanceDirective that the user wishes
 * to navigate to the next step or confirm the guidance.
 *
 * ## Lifespan and cardinality
 *
 * The lifespan of the primaryButtonObserver is unbounded. It is
 * created when the application starts up and it remains alive during
 * its entire lifespan.
 *
 * The guidanceDirective registers to the primaryButtonClicked event.
 * There can only be one listener at a time. When a new guidance is
 * opened the earlier callback is overwritten.
 */
angular.module('digitalWorkplaceApp')
  .factory('primaryButtonObserver', function(topActionState) {

    let primaryButtonClickedCallback = _.noop;

    return {
      setPrimaryButtonData,
      resetPrimaryButtonData,

      primaryButtonClicked,
      setPrimaryButtonClickedCallback
    };

    /**
     * Informs the primaryButtonClicked callback that the primary button has been clicked.
     */
    function primaryButtonClicked() {
      primaryButtonClickedCallback();
    }

    /**
     * Sets the primaryButtonClicked callback to the given callback.
     * @param callback function to call when primaryButtonClicked is called.
     */
    function setPrimaryButtonClickedCallback(callback) {
      primaryButtonClickedCallback = callback;
    }

    /**
     * Sets the given primaryButton data on the topActionState.
     * @param primaryButtonData primary button data
     */
    function setPrimaryButtonData(primaryButtonData) {
      topActionState.setPrimaryButtonData(primaryButtonData);
    }

    /**
     * Resets the given primaryButton data on the topActionState.
     */
    function resetPrimaryButtonData() {
      topActionState.resetPrimaryButtonData();
    }
  });
