'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp:guidanceFormControllerMixin factory
 * @description
 * # guidanceFormControllerMixin
 *
 * The 'guidanceFormControllerMixin' extends a 'form' with some basic
 * functionality which all forms have in common. See the description
 * of 'apply'.
 *
 * Factory of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .factory('guidanceFormControllerMixin', function (formlyFieldsTranslator) {

    return { apply };

    /**
     * When 'apply' is called the controller is extended with this
     * mixin the following things happen.
     *
     *  1. The controller is validated to see if it has a 'formKey'.
     *  2. The controller's model and fields are initialized.
     *  3. Registers for when a stepChangeOccurred callback is made,
     *     it will then updated the model, the fields, and listen
     *     for field changes for those fields.
     *  4. Tell the 'guidanceFormObserver' that the formController
     *     was successfully created when it is done initializing.
     *
     * @param  {[type]} options.scope                The scope on which to $watch for changes.
     * @param  {[type]} options.controller           The controller of the form
     * @param  {[type]} options.controllerAs         The controller's name.
     * @param  {[type]} options.guidanceFormObserver The guidanceFormObserver 'instance' which needs to be informed of changes.
     */
    function apply({ scope, controller, controllerAs, guidanceFormObserver }) {
      validate(controller);

      initialize(controller);

      registerStepChangeOccurredCallback(scope, controller, controllerAs, guidanceFormObserver);

      registerFormController(scope, controller, controllerAs, guidanceFormObserver);
    }

    function validate(controller) {
      if (_.isEmpty(controller.formKey)) {
        throw new Error(`Error a GuidanceFormController must have a formKey, the current formKey is: ${controller.formKey}.`);
      }
    }

    function initialize(controller) {
      controller.model = {};  // Will keep track of the model of a Guidance.
      controller.fields = []; // The fields in the form for this particular step.
    }

    // Whenever the step changes re-render the form again.
    function registerStepChangeOccurredCallback(scope, controller, controllerAs, guidanceFormObserver) {
      const stepChangeCallbackDeregister = guidanceFormObserver.addStepChangeOccurredCallback(function ({ model, form, parentModel }) {
        // Set the model and fields for the form this guidance-form belongs to
        controller.model = model;
        controller.options = { formState: { parentModel: parentModel } };
        controller.fields = formlyFieldsTranslator.translate(_.get(form, `${controller.formKey}.fields`, {}));
      });

      controller.$onDestroy = function () {
        stepChangeCallbackDeregister();
      };
    }

    // Notify the formController that a form has been created
    function registerFormController(scope, controller, controllerAs, guidanceFormObserver) {
      // Limit to one registration per form.
      let registered = false;

      scope.$watch(`${controllerAs}.form`, function () {
        if (_.isEmpty(controller.form) === false && registered === false) {
          guidanceFormObserver.formControllerCreated(controller.form);
          registered = true;
        }
      }, true);
    }
  });
