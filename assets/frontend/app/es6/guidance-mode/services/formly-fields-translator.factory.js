"use strict";

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.flow:formlyFieldsTranslator
 * @description
 * # formlyFieldsTranslator
 *
 * Translates an array of input field definitions in the back-end format to the Angular Formly format required by our application.
 *
 * If the array of fields contains a type that is no longer supported, a warning is logged and the field is ignored.
 * If it contains a type that has never been supported, an error is thrown.
 *
 * Factory of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .factory('formlyFieldsTranslator', function ($log, formlyTranslatorUtils) {

    const translators = {
      // Standard translations
      varchar: {
        typeOverride: 'input',
        defaultTemplateOptions: { hasBorder: true, readonly: false, noBackendInteraction: false },
        defaultWrapper: 'label-left-fields-right-wrapper'
      },
      TextField: {
        typeOverride: 'input',
        defaultTemplateOptions: { hasBorder: true, readonly: false, noBackendInteraction: false },
        defaultWrapper: 'label-left-fields-right-wrapper'
      },
      'resizing-input': {
        typeOverride: 'resizing-input',
        defaultTemplateOptions: { hasBorder: true, readonly: false, noBackendInteraction: false },
        defaultWrapper: 'label-left-fields-right-wrapper'
      },
      email: {
        typeOverride: 'input',
        defaultTemplateOptions: { hasBorder: true, readonly: false, noBackendInteraction: false },
        defaultWrapper: 'label-left-fields-right-wrapper'
      },
      phone: {
        typeOverride: 'input',
        defaultTemplateOptions: { hasBorder: true, readonly: false, noBackendInteraction: false },
        defaultWrapper: 'label-left-fields-right-wrapper'
      },
      date: {
        typeOverride: 'datepicker',
        defaultTemplateOptions: {
          hasBorder: true,
          hasTime: false,
          readonly: false,
          noBackendInteraction: false
        },
        defaultWrapper: 'label-left-fields-right-wrapper'
      },
      datetime: {
        typeOverride: 'datepicker',
        defaultTemplateOptions: {
          hasBorder: true,
          hasTime: true,
          readonly: false,
          noBackendInteraction: false
        },
        defaultWrapper: 'label-left-fields-right-wrapper'
      },
      bool: {
        typeOverride: 'checkbox',
        defaultTemplateOptions: { hasBorder: true, readonly: false, noBackendInteraction: false },
        defaultWrapper: 'label-left-fields-right-wrapper',
        removeLabel: true
      },
      toggle: {
        typeOverride: 'toggle',
        defaultTemplateOptions: { hasBorder: true, readonly: false, noBackendInteraction: false },
        defaultWrapper: 'label-left-fields-right-wrapper',
        removeLabel: true
      },
      upload: {
        typeOverride: 'upload',
        defaultTemplateOptions: { hasBorder: true, readonly: false, noBackendInteraction: false, maxFileSizeMB: 10 },
        defaultWrapper: 'label-left-fields-right-wrapper'
      },
      LabelAndText: {
        typeOverride: 'dynamic-text',
        defaultWrapper: 'label-left-fields-right-wrapper'
      },
      LabelAndAction: {
        typeOverride: 'dynamic-text-with-action',
        defaultWrapper: 'label-left-fields-right-wrapper'
      },
      hashtagText: {
        defaultWrapper: 'label-top-fields-bottom-wrapper',
        defaultTemplateOptions: {readonly: false, noBackendInteraction: false, displayWysiwyg: false }
      },
      LargeTextField: {
        typeOverride: 'large-input',
        defaultTemplateOptions: { hasBorder: true, readonly: false, noBackendInteraction: false },
        defaultWrapper: 'header-top-fields-bottom-wrapper'
      },
      range: {
        defaultWrapper: 'label-left-fields-right-wrapper',
        defaultTemplateOptions: { readonly: false, noBackendInteraction: false }
      },
      selectWithSearch: {
        typeOverride: 'select-with-search',
        defaultWrapper: 'label-top-fields-bottom-wrapper',
        defaultTemplateOptions: { readonly: false, readonlyJoin: ", ", noBackendInteraction: false }
      },
      textarea: {
        defaultTemplateOptions: { hasBorder: true, readonly: false, noBackendInteraction: false },
        defaultWrapper: 'label-top-fields-bottom-wrapper'
      },
      'json-editor': {
        defaultTemplateOptions: { hasBorder: true, readonly: false, noBackendInteraction: false },
        defaultWrapper: 'label-top-fields-bottom-wrapper'
      },
      wysiwyg: {
        typeOverride: 'wysiwyg-editor',
        defaultTemplateOptions: { readonly: false, noBackendInteraction: false },
        defaultWrapper: 'label-top-fields-bottom-wrapper'
      },
      drawPad: {
        typeOverride: 'draw-pad',
        defaultTemplateOptions: { readonly: false, noBackendInteraction: false, width: 400, height: 280 },
        defaultWrapper: 'label-top-fields-bottom-wrapper'
      },
      image: {
        typeOverride: 'image',
        defaultTemplateOptions: {
          text: '',
          action: '',
          actionText: '',
          actionModel: {},
          actionParams: {},
          imageUrl: ''
        },
        defaultWrapper: 'label-top-fields-bottom-wrapper'
      },

      // Specific translation factories, each has wrapper:
      enum: enumType,
      address: addressType,
      InputFieldGroup: inputFieldGroup,
      IconCheckboxGroup: iconCheckboxGroup,
      radioGroup: radioGroup,
      checkboxGroup: checkToggleGroup('CHECKBOX'),
      toggleGroup: checkToggleGroup('TOGGLE'),

      // Translations that have no wrappers
      tariffCalculation: {
        typeOverride: 'tariff-calculation',
        defaultTemplateOptions: { hasBorder: true, hideButtonsConditions: {} }
      },
      "crud-list": {
        defaultTemplateOptions: { readonly: false }
      }
    };

    // Types that were once used but not anymore.
    const removedTypes = [
      "dynamicEnum",      // Replaced by enum and suggestions.
      "hidden",           // Better placed in model instead.
      "connectionEditor", // We're now using the list type for this.
      "quoteComponents",  // We're now using the tariff-calculation for this
      "tariffOverview"    // This is simply a non-editable version of the tariffCalculation type. Created a property for this.
    ];

    return { translate };

    /**
     * Translates an array of back-end field definitions to the format required by this application.
     *
     * @param {Array} fields the input fields in the format supplied by the back-end
     * @returns {Array} translated fields
     */
    function translate(fields) {
      return _(fields)
        .reject(hasBeenRemoved) // Take out the form elements that are no longer supported
        .map(translateToFormlyField) // Convert the rest to formly fields
        .value();
    }

    /**
     * Checks whether or not the given field has been removed from the application.
     * These fields are not shown and a warning is logged to the console.
     *
     * @param field field in the format of the back-end
     * @returns {boolean} true if the field has been removed, false otherwise
     */
    function hasBeenRemoved(field) {
      const removed = _.includes(removedTypes, field.type);

      if (removed) {
        $log.warn(`Warning the type: "${field.type}" has been removed. Field id: "${field.id}"`);
      }
      return removed;
    }

    /**
     * Translates a single field in the back-end format to the Formly
     * format required by this application.
     *
     * @param field field in the format supplied by the back-end
     * @returns {Object} translated field
     * @throws Error if the given field is of a non-supported type
     */
    function translateToFormlyField(field) {
      // Retrieve the appropriate translator for the given type
      const translator = translators[field.type];

      // If the translator cannot be found, the field is not supported and we throw an error.
      if (_.isUndefined(translator)) {
        throw new Error(`Error during conversion to formly types, unsupported type '${field.type}'. Field id: '${field.id}'`);
      }

      /*
       If the factory is a function execute it to transform the field.
       If the factory is an object is is an options for the the
       'formlyTranslatorUtils.translateField' translator.
       */
      if (_.isFunction(translator)) {
        return translator(field);
      } else {
        return formlyTranslatorUtils.translateField(field, translator);
      }
    }

    /**
     * Converts a field in the back-end format to the address type.
     * @param field field in the back-end format
     * @returns {Object} address field definition for given input field
     */
    function addressType(field) {
      field.fields = _.mapValues(field.fields, (childField) => {
        // By default display everything but the countries.
        const defaultDisplay = true;
        childField.display = _.get(childField, 'display', defaultDisplay);
        if (childField.id === 'address_country') {
          childField.enumValues = convertEnumToFrontendOptions(childField);
        }
        return _.mapKeys(childField, (value, key) => key === 'id' ? 'key' : key);
      });

      return formlyTranslatorUtils.translateField(field, {
        defaultTemplateOptions: {
          hasBorder: true,
          readonly: false,
          noBackendInteraction: false
        },
        defaultWrapper: 'label-left-fields-right-wrapper'
      });
    }

    function convertEnumToFrontendOptions(field, checkboxes = false) {
      return _.map(field.enumValues, function (enumOption) {
        let option = {
          name: enumOption.value,
          value: enumOption.key
        };

        if (checkboxes) {
          option.disabled = _.get(enumOption, 'disabled', false);
        }

        return option;
      });
    }

    /**
     * Converts a field in the back-end format to the enum type.
     * @param field field in the back-end format
     * @returns {Object} enum field definition for given input field
     */
    function enumType(field) {
      field.multiple = field.multiple === true;
      field.checkboxes = _.get(field, 'checkboxes', false);
      field.options = convertEnumToFrontendOptions(field, field.checkboxes);

      delete field.enumValues;

      return formlyTranslatorUtils.translateField(field, {
        typeOverride: 'select',
        defaultTemplateOptions: { hasBorder: true, readonly: false, sortEnums: true, noBackendInteraction: false },
        defaultWrapper: 'label-left-fields-right-wrapper'
      });
    }

    /**
     * Converts an input field group in the back-end format to a header-top-fields-bottom-wrapper with the input fields inside it.
     * @param field field in the back-end format
     * @returns {Object} header-top-fields-bottom-wrapped field definitions for the given input fields
     */
    function inputFieldGroup(field) {
      const fieldGroup = _.map(field.fields, (childField) => {
        childField.className = 'inline-block';
        childField.expressionProperties = field.expressionProperties;
        childField.readonly = _.get(field, 'readonly', false);
        childField.noBackendInteraction = _.get(field, 'noBackendInteraction', false);

        return formlyTranslatorUtils.translateField(childField, { addHasBorder: true });
      });

      return formlyTranslatorUtils.applyWrapperOrDefault(field, fieldGroup, 'header-top-fields-bottom-wrapper');
    }

    /**
     * Converts a icon checkbox group in the back-end format to a checkable-icons-group-wrapper.
     * @param field field in the back-end format
     * @returns {{wrapper: string, hideExpression: *, fieldGroup: Array}} icon-checkbox-group wrapped icon-checkbox definitions
     */
    function iconCheckboxGroup(field) {
      const fieldGroup = _.map(field.fields, (childField) => {
        childField.className = 'inline-block';
        childField.expressionProperties = field.expressionProperties;
        childField.readonly = _.get(field, 'readonly', false);
        childField.noBackendInteraction = _.get(field, 'noBackendInteraction', false);

        return formlyTranslatorUtils.translateField(childField, {
          typeOverride: 'checkable-icon'
        });
      });

      return formlyTranslatorUtils.applyWrapperOrDefault(field, fieldGroup, 'checkable-icons-group-wrapper');
    }

    /**
     * Converts a radio group in the back-end format to an label-left-fields-right-wrapper with the radio options inside it.
     * @param field field in the back-end format
     * @returns {{wrapper: string, hideExpression: *, fieldGroup: Array}} label-left-fields-right wrapped radio definitions
     */
    function radioGroup(field) {
      const fieldGroup = _.map(field.enumValues, (enumOption) => {
        return {
          id: field.id + "_" + enumOption.key,
          key: field.id,
          type: "radio",
          expressionProperties: field.expressionProperties,
          templateOptions: {
            label: enumOption.value,
            value: enumOption.key,
            readonly: _.get(field, 'readonly', false),
            noBackendInteraction: _.get(field, 'noBackendInteraction', false)
          },
          className: 'inline-block'
        };
      });

      return formlyTranslatorUtils.applyWrapperOrDefault(field, fieldGroup, 'label-left-fields-right-wrapper');
    }

    /**
     * Creates a factory function for either a toggleGroup or a checkbox group.
     * @param {"CHECKBOX" | "TOGGLE"} mode, can either be a checkbox or toggle
     * @returns {Function} Factory function to create either checkbox or toggle groups
     */
    function checkToggleGroup(mode) {
      return function (field) {
        const fieldGroup = _.map(field.enumValues, function (enumOption) {
          const firstPart = _.isEmpty(field.id) ? '' : `${field.id}.`;
          const jointKey = `${firstPart}${enumOption.key}`;

          return {
            id: jointKey,
            key: jointKey,
            type: mode.toLowerCase(),
            expressionProperties: field.expressionProperties,
            templateOptions: {
              label: enumOption.value,
              readonly: _.get(field, 'readonly', false),
              noBackendInteraction: _.get(field, 'noBackendInteraction', false)
            },
            className: 'inline-block'
          };
        });

        return formlyTranslatorUtils.applyWrapperOrDefault(field, fieldGroup, 'label-left-fields-right-wrapper');
      };
    }
  });
