'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.factory:progressBarObserver factory
 * @description
 * # progressBarObserver
 *
 * ## Responsibility
 *
 * The progress bar component registers for changes in the 'progressMetadata'
 * to display these somewhere on the page. Every time new progressMetadata
 * comes in its callback is invoked:
 *
 * ```javascript
 * //Whenever new progress data comes in, use it to redraw the progress bar.
 * progressBarObserver.registerProgressMetadataCallback(function(progressMetadata) {
 *   progressBarController.progressMetadata = progressMetadata;
 * });
 * ```
 *
 * This part of code is responsible for updating the progress bar when
 * the current step of the guidance mode has changed. This data is set in
 * the guidance component:
 *
 * ```javascript
 * //The guidanceController.guidanceData is set to the latest step data when the step changes
 * //Afterwards we inform the progressBarObserver of the new progress data belonging to it.
 * progressBarObserver.setProgressMetadata(guidanceController.guidanceData.progress);
 * ```
 *
 * The user can click on steps in the progress bar component to change
 * the current step. When the user clicks a step, the following code
 * is executed:
 *
 * ```javascript
 * progressBarController.click = (step) => {
 *   if (step.canBeActivated && step.active === false) {
 *     progressBarObserver.clicked(step.key_c);
 *   }
 * };
 * ```
 *
 * This again sends out an event, to which the guidance component is registered:
 *
 * ```javascript
 * progressBarObserver.registerClickCallback(function (stepId) {
 *   requestStep({ event: ACTION_EVENT.NEXT_STEP_FORCED, nextStep: stepId }).then(handleRequestStepResponse);
 * });
 * ```
 *
 * When the guidance component receives this event, it will request the
 * next step on the backend and make that the active step. The progress bar
 * component is then again informed of the new progress metadata.
 *
 * ## Lifespan and cardinality
 *
 * The lifespan of the progressBarObserver is unbounded. It is created
 * when the application starts up and it remains alive during its entire
 * life span.The progress bar observer is used in the full screen guidance
 * mode only. There can only be one active at the same time. Other guidances
 * such as modals and mini-guidances are not intended to contain progress indicators.
 * If this is needed in the future, this observer needs revisioning.
 *
 * The cardinality of the progressbar observer is 1-to-1, on the one
 * side we have a progress bar component and on the other side we have
 * a guidance component. When we open another full screen guidance
 * mode there are new instantiations created for both and they each
 * replace the old observer callbacks.
 */
angular.module('digitalWorkplaceApp')
  .factory('progressBarObserver', function() {

    let progressMetadataCallback = _.noop;
    let clickCallback = _.noop;

    return {
      setProgressMetadata,
      registerProgressMetadataCallback,

      clicked,
      registerClickCallback
    };

    /**
     * Inform the observer of the new progress metadata.
     * It always sends a copy of the data to prevent other components from modifying the data
     * @param progressMetadata
     */
    function setProgressMetadata(progressMetadata) {
      progressMetadataCallback(progressMetadata);
    }

    /**
     * Register a callback method to invoke when the setProgressMetadata function is invoked.
     * @param callback function to invoke with the progress metadata as an argument
     */
    function registerProgressMetadataCallback(callback) {
      progressMetadataCallback = callback;
    }

    /**
     * Inform the observer that one of the steps in the progress bar have been clicked.
     * @param stepId id of the step that has been clicked
     */
    function clicked(stepId) {
      clickCallback(stepId);
    }

    /**
     * Register a callback method to invoke when the clicked function is invoked.
     * @param callback function to invoke with the step id as an argument
     */
    function registerClickCallback(callback) {
      clickCallback = callback;
    }
  });
