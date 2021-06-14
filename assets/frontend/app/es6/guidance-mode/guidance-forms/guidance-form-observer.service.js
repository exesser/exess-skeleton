'use strict';

(function() {

  /**
   * A GuidanceFormObserver can be instantiated to create an observer for the life-time of one single guidance mode.
   * Each guidanceDirective should contain its own GuidanceFormObserver so simultaneous guidances
   * (large guidances, modals, mini-guidances, etc.) do not overlap.
   * After the guidance is complete, the GuidanceFormObserver can be discarded.
   */
  class GuidanceFormObserver {

    /**
     * Constructs a new GuidanceFormObserver with empty callbacks.
     */
    constructor() {
      this.formControllerCreatedCallback = _.noop;

      this.formValueChangedCallback = _.noop;
      this.formValidityUpdateCallback = _.noop;

      this.requestNextStepCallback = _.noop;
      this.stepChangeOccurredCallbacks = [];
      this.confirmGuidanceCallback = _.noop;

      this.fullModel = {};
      this.parentModel = {};

      this.repeatableBlockKey = '';
    }

    /**
     * Let the observer know a FormController has been created.
     * @param {FormController} formController A FormController instance which represents the state of the form.
     */
    formControllerCreated(formController) {
      this.formControllerCreatedCallback(formController);
    }

    /**
     * Set a callback function to invoke when a FormController is created.
     * @param  {Function(FormController)} callback A function which takes a FormController instance as an argument.
     */
    setFormControllerCreatedCallback(callback) {
      this.formControllerCreatedCallback = callback;
    }

    /**
     * Inform the observer that a value in the form has changed.
     * @param {Object} guidanceAction An object describing the change that has been triggered.
     * @param noBackendInteraction boolean
     */
    formValueChanged(guidanceAction, noBackendInteraction = false) {
      this.formValueChangedCallback(guidanceAction, noBackendInteraction);
    }

    /**
     * Set a callback function to invoke when a value in the form changes.
     * @param {Function} callback A function which takes an object as an argument.
     */
    setFormValueChangedCallback(callback) {
      this.formValueChangedCallback = callback;

    }

    /**
     * Inform the observer of the latest form validity state.
     * If true, all the forms in the current step are valid.
     * @param valid whether or not all the forms of the active step are valid.
     */
    formValidityUpdate(valid) {
      this.formValidityUpdateCallback(valid);
    }

    /**
     * Set a callback function to invoke when the latest form validity state is in.
     * @param {Function} callback A function which takes a boolean as an argument.
     */
    setFormValidityUpdateCallback(callback) {
      this.formValidityUpdateCallback = callback;
    }

    /**
     * Inform the observer that the next step in the guidance has been requested to open.
     */
    requestNextStep() {
      this.requestNextStepCallback();
    }

    /**
     * Set a callback function to invoke when the next step of the guidance has been requested to open.
     * @param {Function} callback A function which does not take any arguments.
     */
    setRequestNextStepCallback(callback) {
      this.requestNextStepCallback = callback;
    }

    /**
     * Inform the observers that the step of the guidance has changed.
     * @param  {Function(GuidanceMode)} guidanceMode The new GuidanceMode which is available.
     */
    stepChangeOccurred(guidanceMode) {
      _.forEach(this.stepChangeOccurredCallbacks, function(callback) {
        callback(guidanceMode);
      });
    }

    /**
     * Add a callback function to invoke when the stepChangeOccurred function is invoked.
     * @param {Function(GuidanceMode)} callback A function which takes a GuidanceMode as a parameter.
     * @return {Function} deregister function. When called this will stop informing the given callback of step changes.
     */
    addStepChangeOccurredCallback(callback) {
      const stepChangeCallbacks = this.stepChangeOccurredCallbacks;
      stepChangeCallbacks.push(callback);

      return function deregister() {
        _.remove(stepChangeCallbacks, function(stepChangeCallback) {
          return stepChangeCallback === callback;
        });
      };
    }

    /**
     * Inform the observer that the guidance has been requested to be confirmed.
     * @param confirmAction String to indicate the confirmation action
     * @returns {Promise} a promise that is resolved with the command coming back from the backend
     */
    confirmGuidance(confirmAction) {
      return this.confirmGuidanceCallback(confirmAction);
    }

    /**
     * Set a callback function to invoke when a guidance is requested to be confirmed.
     * @param {Function} callback A function that takes a string as an argument.
     */
    setConfirmGuidanceCallback(callback) {
      this.confirmGuidanceCallback = callback;
    }

    /**
     * Set the form model.
     * @param {Object} m.
     */
    setFullModel(m) {
      this.fullModel = m;
    }

    /**
     * Get the form model.
     * @return Object.
     */
    getFullModel() {
      return this.fullModel;
    }

    /**
     * Set the form parent model.
     * @param {Object} m.
     */
    setParentModel(m) {
      this.parentModel = m;
    }

    /**
     * Get the form parent model.
     * @return Object.
     */
    getParentModel() {
      return this.parentModel;
    }

    /**
     * Set the repeatable block key.
     * @param {String} key.
     */
    setRepeatableBlockKey(key) {
      this.repeatableBlockKey = key;
    }

    /**
     * Get the repeatable block key.
     * @return String.
     */
    getRepeatableBlockKey() {
      return this.repeatableBlockKey;
    }
  }

  angular.module('digitalWorkplaceApp')
    .service('GuidanceFormObserver', function () {
      return GuidanceFormObserver;
    });
}());
