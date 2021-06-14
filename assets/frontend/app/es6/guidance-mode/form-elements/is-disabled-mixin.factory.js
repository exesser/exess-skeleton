'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp:isDisabledMixin factory
 * @description
 * # isDisabledMixin
 *
 * The isDisabledMixin factory abstracts the common functionality
 * related to the field status (disabled / active)
 *
 * Factory of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .factory('isDisabledMixin', function(guidanceModeBackendState, ACTION_EVENT, CONFIRM_ACTION) {

    return { apply };

    /**
     * When 'apply' is called with a controller as input it checks if the property 'isDisabled'
     * is available otherwise is sets the property as `false`.
     * It also create a new method on the controller: fieldIsDisabled.
     *
     * @throws Error if a 'key' property is not set on the form element controller.
     * @param controller Controller to set isDisabled method on it.
     */
    function apply(controller) {
      if (_.isUndefined(controller.isDisabled)) {
        controller.isDisabled = false;
      }

      validate(controller);

      addIsDisabled(controller);
    }

    /**
     * Asserts that a key property has been set on the controller.
     * @throws Error if a key has not been set on the controller.
     * @param controller the controller
     */
    function validate(controller) {
      if (_.isEmpty(controller.key)) {
        throw new Error(`Error: a form element controller must have a key, the current key is: ${controller.key}.`);
      }
    }

    /**
     * Enriches the controller with a function called 'fieldIsDisabled'
     * which returns if the fields should be disabled or not.
     *
     * @param controller Controller to set the new method: fieldIsDisabled.
     */
    function addIsDisabled(controller) {

      controller.fieldIsDisabled = function() {
        // disable the field if the backend says is disabled
        if (controller.isDisabled === true) {
          return true;
        }

        // don't disable the field is the backend is not busy
        if (!guidanceModeBackendState.getBackendIsBusy()) {
          return false;
        }

        const performedAction = guidanceModeBackendState.getPerformedAction();

        // disable fields also on event confirm
        if (_.get(performedAction, 'event', '') === CONFIRM_ACTION.CONFIRM) {
          return true;
        }

        //don't disable the field if the the performed action doesn't have the event "CHANGED" and a focus property
        if (_.isUndefined(performedAction.event) || _.isUndefined(performedAction.focus) || performedAction.event !== ACTION_EVENT.CHANGED) {
          return false;
        }

        // disable the field if it does not have the focus
        return controller.key !== performedAction.focus && controller.key !== performedAction.focus + '-field';
      };
    }
  });
