'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp:modelChangedMixin factory
 * @description
 * # modelChangedMixin
 *
 * The modelChangedMixin factory abstracts the common functionality
 * related to managing the internal model and external model value
 * of a form-element.
 *
 * Most elements have an internal model value which must update whenever
 * the external model value changes. Also whenever the internal value
 * changes the external model must be updated, and the guidanceFormObserver
 * must be notified of the change.
 *
 * Factory of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .factory('modelChangedMixin', function () {

    return { apply };

    /**
     * When 'apply' is called with a controller it makes sure that the
     * controller will react to changes on the internal and external
     * model of the form element.
     *
     * @throws Error if a 'key', 'ngModel' or 'guidanceObserversAccessor' property is not set on the form element controller.
     * @param {controller} Controller to set suggestions on.
     * @param {string} controllerAs name of the controller
     * @param {scope} $scope the scope of the controller
     * @param {boolean} shouldListenToExternalModelChanges add watch on on model value
     * @param suggestionsObserver the SuggestionsObserver instance for this form element.
     */
    function apply(controller, controllerAs, $scope, shouldListenToExternalModelChanges = true) {
      validate(controller);

      if (shouldListenToExternalModelChanges) {
        listenToExternalModelChanges(controller, controllerAs, $scope);
      }

      addInternalModelValueChanged(controller);
    }

    /**
     * Asserts that a guidanceObserversAccessor, and key has been set on the controller.
     *
     * @throws Error if a 'key', 'ngModel' or 'guidanceObserversAccessor' property is not set on the controller.
     * @param controller the controller
     */
    function validate(controller) {
      if (_.isEmpty(controller.key)) {
        throw new Error(`Error: a form element controller must have a key, the current key is: ${controller.key}.`);
      }
      if (_.isEmpty(controller.guidanceObserversAccessor)) {
        throw new Error(`Error: a form element controller must have a guidanceObserversAccessor, the current guidanceObserversAccessor is: ${controller.guidanceObserversAccessor}.`);
      }
      if (_.isEmpty(controller.ngModel)) {
        throw new Error(`Error: a form element must have an ngModel instance, the current value is: ${controller.ngModel}.`);
      }
    }

    /**
     * Listens to external model changes and sets the internal models
     * value when the event occurs.
     *
     * @param {controller} Controller to set suggestions on.
     * @param {string} controllerAs name of the controller
     * @param {scope} $scope the scope of the controller
     */
    function listenToExternalModelChanges(controller, controllerAs, $scope) {
      $scope.$watch(`${controllerAs}.ngModel.$viewValue`, function (value) {
        controller.internalModelValue = value;
      });
    }

    /**
     * Enriches the controller with a function called 'internalModelValueChanged'
     * which when the internal model changes it updates the external model.
     *
     * It conditionally sends a formValueChangedEvent to the guidanceFormObserver.
     *
     * If the form element's controller has noBackendInteraction enabled,
     * and there are no errors on the controller, then it will not inform
     * the back-end of form value changes.
     *
     * The reason it also takes the errorMessages into account is because
     * these could have come from the back-end. Then it is the back-ends
     * job to clear the errors. If we never inform the back-end it
     * cannot clear the errors.
     *
     * @param {controller} Controller to set internalModelValueChanged on.
     */
    function addInternalModelValueChanged(controller) {
      const guidanceFormObserver = controller.guidanceObserversAccessor.getGuidanceFormObserver();

      controller.internalModelValueChanged = function () {
        let internalValue = controller.internalModelValue;
        if (_.isString(internalValue)) {
          internalValue = _.trim(internalValue);
        }

        controller.ngModel.$setViewValue(internalValue);

        const noBackendInteraction = controller.noBackendInteraction && _.isEmpty(controller.errorMessages);

        const action = {
          focus: controller.key,
          value: internalValue
        };

        guidanceFormObserver.formValueChanged(action, noBackendInteraction);
      };
    }
  });
