"use strict";

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.flow:formlyTranslatorUtils
 * @description
 * # formlyTranslatorUtils
 *
 * Contains utility functions for helping translate back-end types to
 * front-end Formly types. The reason this utility library exists
 * is to make testing 'it' easier, but it also unloads some of the
 * complexity from the 'formlyFieldsTranslator'.
 *
 * Factory of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .factory('formlyTranslatorUtils', function() {

    return { translateField, applyWrapperOrDefault };

    /**
     * Translates a single 'field' from back-end type to a front-end
     * Formly format.
     *
     * @param  field   The field which needs to be translated to a formlyType
     * @param  options [description]
     * @return
     */
    function translateField(field, options) {
      // Read the 'options' and provide their default values if there are none.
      const typeOverride = _.get(options, 'typeOverride', false);
      const defaultTemplateOptions = _.get(options, 'defaultTemplateOptions', {});
      const defaultWrapper = _.get(options, 'defaultWrapper', false);
      const removeLabel = _.get(options, 'removeLabel', false);

      const type = _.isEmpty(typeOverride) ? field.type : typeOverride;

      // Create the base formlyField object, which we will manipulate later.
      const formlyField = {
        id: field.id,
        key: field.id,
        type
      };

      /*
        The templateOptions are all 'other' properties which are not
        part of the 'formlyField' or are manipulated later on in
        this function.

        All other properties from the 'field' will simply be put
        in the templateOptions and are considered properties that
        need to be displayed in the 'types' template.
      */
      formlyField.templateOptions = _.omit(field, [
        'id',
        'type',
        'hideExpression',
        'validation',
        'expressionProperties',
        'fieldExpression',
        'className',
        'wrapper',
        'orientation',
        'multiple'
      ]);

      // Mix the 'defaultOptions' and the validation rules into the templateOptions as well.
      formlyField.templateOptions = _.merge({}, defaultTemplateOptions, formlyField.templateOptions, field.validation);

      /*
        Some types such as the 'checkbox / bool' count on the 'wrapper'
        to render the label for them. In that case the label must be
        removed from the templateOptions themselves.
      */
      if (removeLabel) {
        delete formlyField.templateOptions.label;
      }

      /*
        Convert all 'fieldExpression' to 'unparsedFieldExpression' to
        indicate that they have yet to be parsed by the expressionTransformer.
      */
      if (_.isEmpty(field.fieldExpression) === false) {
        formlyField.templateOptions.unparsedFieldExpression = field.fieldExpression;
      }

      /*
        Convert all 'multiple' to 'multipleSelect'
        We cannot use 'multiple' because it is a HTML attribute which is replaced during minification.
       */
      if (_.has(field, 'multiple') === true) {
        formlyField.templateOptions.multipleSelect = field.multiple;
      }

      // Only add expressionProperties when present.
      if (_.isEmpty(field.expressionProperties) === false) {
        formlyField.expressionProperties = field.expressionProperties;
      }

      // Only add the className when present
      if (_.isEmpty(field.className) === false) {
        formlyField.className = field.className;
      }

      // If the 'field' provides an 'orientation' get the wrapper and apply it.
      if (_.isEmpty(field.orientation) === false) {
        const wrapper = orientationToWrapper(field);

        return wrapField(wrapper, field, [formlyField]);
      }

      // If the field has a default wrapper apply it.
      if (_.isEmpty(defaultWrapper) === false) {
        return wrapField(defaultWrapper, field, [formlyField]);
      }

      /*
        We apply hideExpressions on the field level when the type is
        not wrapped by for example the 'label-left-fields-right-wrapper'
      */
      if (_.isUndefined(field.hideExpression) === false) {
        formlyField.hideExpression = field.hideExpression;
      }

      return formlyField;
    }

    /**
     * Takes a back-end field definition and either applies a Formly
     * wrapper on it when the field has a "orientation" property, or when
     * the "orientation" is empty wraps the field with a default wrapper.
     *
     * @param  {Object}             field          The back-end field which will be wrapped.
     * @param  {[FormlyFieldGroup]} fieldGroup     The Formly fieldGroup to wrap in the wrapper.
     * @param  {string}             defaultWrapper The default wrapper to apply when there is no "orientation" property.
     * @return {FormlyWrapper} The wrapped object.
     */
    function applyWrapperOrDefault(field, fieldGroup, defaultWrapper) {
      if (_.isEmpty(field.orientation) === false) {
        const wrapper = orientationToWrapper(field);
        return wrapField(wrapper, field, fieldGroup);
      } else {
        return wrapField(defaultWrapper, field, fieldGroup);
      }
    }

    /* ===== Helpers ===== */

    // Wraps a field in a wrapper of a given type. This is used to visually enhance the appearance in the front-end.
    function wrapField(wrapper, field, fieldGroup) {
      return {
        wrapper,
        templateOptions: {
          label: field.label
        },
        hideExpression: field.hideExpression,
        fieldGroup
      };
    }

    // Converts a 'orientation' direction to a formly wrapper type.
    function orientationToWrapper(field) {
      switch (field.orientation) {
        case 'label-top':
          return 'label-top-fields-bottom-wrapper';
        case 'label-left':
          return 'label-left-fields-right-wrapper';
        case 'header-top':
          return 'header-top-fields-bottom-wrapper';
        default:
          throw new Error(`Error during conversion to formly types, unknown orientation type: "${field.orientation}". Field id: "${field.id}"`);
      }
    }
  });
