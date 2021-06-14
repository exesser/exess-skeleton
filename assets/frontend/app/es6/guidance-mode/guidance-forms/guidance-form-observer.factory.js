'use strict';

/**
 * @ngdoc factory
 * @name digitalWorkplaceApp.factory:guidanceFormObserverFactory
 * @description
 *
 * # Guidance form observer
 *
 * ## Responsibility
 *
 * The guidance form observer is the beating heart of the guidances
 * and is used in the large guidances, modals, mini guidances,
 * embedded-guidances and filters.
 *
 * It is responsible for handling the following events:
 *
 * **Form controller created**
 *
 * Whenever a new formly FormController is created it is sent to the
 * guidanceFormObserver. This happens in the guidanceFormControllerMixin,
 * and as such there is a formControllerCreated callback sent out for
 * each guidanceForm that is rendered.
 *
 * For example in the following form:
 *
 *     ----------------------------------
 *     |           |                    |
 *     |           |                    |
 *     |           | BasicFormlyForm  2 |
 *     |  Basic    |                    |
 *     |  formly   |                    |
 *     |  form     ---------------------|
 *     |    1      |                    |
 *     |           |                    |
 *     |           | BasicFormlyForm  3 |
 *     |           |                    |
 *     ---------------------------------|
 *
 * A controller registering to the formControllerCreated callback
 * receives an invocation for all three form elements when they
 * are created.
 *
 * There can only be one subscriber for this event at a time, that is
 * the guidanceComponent the observer belongs to. It is used to let the
 * guidanceFormObserver know whether or not all the current forms are valid.
 * The forms are stored in an array in the guidanceComponent and are cleared
 * again when the step changes. Then the same event is called again for
 * the new guidance forms.
 *
 * **Form value changed**
 *
 * The formValueChanged event is fired whenever a field in the form
 * changes its value. It is sent in the following form:
 *
 * ```json
 * {
 *   "focus": "answer",
 *   "value": "42"
 * }
 * ```
 *
 * Focus is the key of the field and value is the new value.
 *
 * There can only be one subscriber for this event. This is the guidanceComponent.
 * When the event callback is invoked a validation request will be performed.
 * There is a debounce on this to limit the amount of backend requests that are made.
 *
 * The backend can indicate that a formValueChanged event should not happen
 * for certain fields. This is done via the 'noBackendInteraction' value
 * in the form definition.
 *
 * **Form validity update**
 *
 * The formValidityUpdate event is fired when a validation result comes
 * back. A form is only considered valid if all fields are valid. The
 * callback is simply invoked with a boolean, so it cannot be used to
 * know what fields are invalid. To send the specific field errors
 * to the correct fields the validationObserver is used.
 *
 * There can only be one subscriber for this event at a time. That is
 * either the largeGuidanceModeController, embeddedGuidance, miniGuidanceController
 * or modalController. Each of these controllers have their own
 * guidanceFormObserver when the guidance is started so that is how
 * it is separated.
 *
 * **Request next step**
 *
 * The requestNextStep event is invoked when the user clicks on the 'Next'
 * button in the largeGuidanceModeController. It signals the
 * guidanceComponent that the user wishes to go to the next step.
 * This then triggers the retrieval of that step from the backend.
 *
 * There can only be one subscriber for this event. This is the guidanceComponent.
 *
 * **Step change occurred**
 *
 * The stepChangeOccurred event is triggered when the guidanceComponent
 * has loaded a new step. It is called with the result from the backend
 * and as such it includes the model and the forms. Subscribers of this
 * event can use it to gain access to the latest formModel object for example.
 * It is also used in the guidanceFormControllerMixin to render the
 * fields belonging to the guidance-form it is put in.
 *
 * There can be many subscribers for this event. Some of these form
 * elements do not survive for the length of an entire guidance mode,
 * they simply want the data of that step because they *belong* to that
 * step. This is the case for guidance-forms and titleContainingGrid
 * for example. Once these elements are obsolete there is no point in
 * sending it the events anymore. In order to deal with this the
 * addStepChangeOccurredCallback returns a deregister function that
 * you can call when the lifecycle of that component ends.
 *
 * For example:
 *
 * ```javascript
 * let stepChangeDeregisterFunction;
 *
 * exampleController.$onInit = function() {
 *   stepChangeDeregisterFunction = guidanceFormObserver.addStepChangeOccurredCallback(function({ model }) {
 *     exampleController.model = model;
 *   });
 * };
 *
 * exampleController.$onDestroy = function() {
 *   stepChangeDeregisterFunction();
 * };
 * ```
 *
 * **Confirm guidance**
 *
 * The confirmGuidance event is the last event for a given guidance. It is called with a confirmation action string,
 * which could be 'CONFIRM' or 'CONFIRM-MODAL' for example. When invoked, the model is sent to the backend with the confirmation action string as one of the arguments.
 * After the confirm request succeeds the guidanceFormObserver can be discarded.
 *
 * There can only be one subscriber to this event. This is the guidanceComponent.
 *
 * **Make form model available for form elements**
 *
 * Because sometimes the form elements need to have access to the full model we store this in guidanceFormObserver.
 * For example the 'selectWithSearch' form element need the full model to filter the select options base on the values you have entered in the previews fields.
 *
 * ### Lifespan and cardinality ###
 *
 * The lifespan of the guidanceFormObserver is bound to one specific
 * guidance mode. It is created when the guidance mode is opened and
 * the lifecycle ends when the guidance is either confirmed or the user
 * navigates away from the guidance.
 *
 * The cardinality of the subscribers and publishers depends on the
 * specific event. See the explanations for that under the
 * 'responsibility' paragraph.
 */
angular.module('digitalWorkplaceApp')
  .factory('guidanceFormObserverFactory', function(GuidanceFormObserver) {

    return { createGuidanceFormObserver };

    /**
     * Creates a new guidance form observer.
     * @returns {GuidanceFormObserver} instance of a GuidanceFormObserver
     */
    function createGuidanceFormObserver() {
      return new GuidanceFormObserver();
    }
  });
