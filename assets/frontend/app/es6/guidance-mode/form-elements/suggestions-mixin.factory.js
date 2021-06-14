'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp:suggestionsMixin factory
 * @description
 * # suggestionsMixin
 *
 * The suggestionsMixin factory abstracts the common functionality
 * related to subscribing the form elements to their appropriate
 * incoming suggestions.
 *
 * Factory of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .factory('suggestionsMixin', function (flashMessageContainer, $translate) {

    return { apply };

    /**
     * When 'apply' is called with a controller as input it
     * sets an empty suggestions array to the controller and registers
     * for field suggestions callbacks at the suggestionsObserver.
     * This suggestions array is then replaced by new incoming arrays.
     * If they are undefined, they are treated as empty.
     *
     * @throws Error if a 'key' property is not set on the form element controller.
     * @param controller Controller to set suggestions on.
     * @param suggestionsObserver the SuggestionsObserver instance for this form element.
     */
    function apply(controller, suggestionsObserver, hasCustomSuggestionClicked = false) {
      validate(controller);

      initialize(controller);

      registerFieldSuggestionsCallback(controller, suggestionsObserver);

      if (hasCustomSuggestionClicked === false) {
        addSuggestionClicked(controller);
      }
    }

    /**
     * Asserts that a key property has been set on the controller.
     * @throws Error if a key has not been set on the controller.
     * @param controller the controller
     */
    function validate(controller) {
      if (_.isEmpty(controller.key)) {
        throw new Error(`Error a form element controller must have a key, the current key is: ${controller.key}.`);
      }
      if (_.isEmpty(controller.guidanceObserversAccessor)) {
        throw new Error(`Error: a form element controller must have a guidanceObserversAccessor, the current guidanceObserversAccessor is: ${controller.guidanceObserversAccessor}.`);
      }
    }

    /**
     * Sets an empty suggestions array on the controller as the initial value.
     * @param controller the controller
     */
    function initialize(controller) {
      controller.suggestions = [];
    }

    /**
     * Registers for suggestions coming back for the key specified in the controller.
     * When the suggestions change the suggestions array on the controller is overwritten with a new array
     * containing the new suggestions of the field.
     * @param controller the controller
     * @param suggestionsObserver the suggestionsObserver to get the suggestions from
     */
    function registerFieldSuggestionsCallback(controller, suggestionsObserver) {
      suggestionsObserver.registerSuggestionsChangedCallback(function () {
        const suggestions = suggestionsObserver.getSuggestionsForKey(controller.key);
        if (_.isUndefined(suggestions) === false) {
          controller.suggestions = suggestions;
        }
      });
    }

    /**
     * Enriches the controller with a function called 'suggestionClicked'
     * which when a suggestion is clicked updates the model in the controller,
     * and it sends a formValueChangedEvent to the guidanceFormObserver.
     *
     * @param {controller} Controller to set internalModelValueChanged on.
     */
    function addSuggestionClicked(controller) {
      const guidanceFormObserver = controller.guidanceObserversAccessor.getGuidanceFormObserver();

      controller.suggestionClicked = function (suggestion) {
        // Apply all suggestions to the model that the back-end sent back.
        let changedFields = {};
        compare(controller.model, suggestion.model, changedFields, '');

        _.merge(controller.model, suggestion.model);

        if (_.has(suggestion, 'parentModel') && _.isEmpty(suggestion.parentModel) === false) {
          if (_.isUndefined(controller.parentModel)) {
            throw new Error(`Error: ${controller.key} element controller must have a parentModel`);
          }
          compare(controller.parentModel, suggestion.parentModel, changedFields, '');
          _.merge(controller.parentModel, suggestion.parentModel);
        }

        // Only send the current controller's value.
        const value = _.get(suggestion.model, controller.key);

        const action = {
          focus: controller.key,
          value
        };

        let message = getMessageForFlashMessage(changedFields, controller.key);
        if (!_.isEmpty(message) && _.get(suggestion, 'notifyChange', false)) {
          $translate('SUGGESTION_CHANGE', { diff: message }).then((text) => {
            flashMessageContainer.addMessageOfType(
              'INFORMATION',
              _.unescape(text),
              'INFORMATION_SUGGESTION_CHANGE'
            );
          });
        }

        guidanceFormObserver.formValueChanged(action, false);
      };
    }

    function compare(controllerModel, suggestionModel, changedFields, prefix) {
      const controllerModelKeys = _.keys(controllerModel);
      const suggestionModelKeys = _.keys(suggestionModel);

      _.forEach(_.difference(suggestionModelKeys, controllerModelKeys), (newKey) => {
        if (_.isObject(suggestionModel[newKey])) {
          compare({}, suggestionModel[newKey], changedFields, prefix + '::' + newKey);
          return;
        }

        changedFields[prefix + '::' + newKey] =
          isReallyEmpty(suggestionModel[newKey]) ? 'empty' : suggestionModel[newKey];
      });

      _.forEach(_.intersection(suggestionModelKeys, controllerModelKeys), (key) => {
        if (
          _.isEqual(suggestionModel[key], controllerModel[key])
          || (isReallyEmpty(suggestionModel[key]) && isReallyEmpty(controllerModel[key]))
        ) {
          return;
        }

        if (_.isObject(suggestionModel[key])) {
          compare(controllerModel[key], suggestionModel[key], changedFields, prefix + '::' + key);
          return;
        }

        changedFields[prefix + '::' + key] =
          (isReallyEmpty(controllerModel[key]) ? 'empty' : controllerModel[key])
          + " -> "
          + (isReallyEmpty(suggestionModel[key]) ? 'empty' : suggestionModel[key]);
      });
    }

    function getMessageForFlashMessage(changedFields, currentFieldKey) {
      let message = "";

      _.forEach(_.keys(changedFields), (fullKey) => {

        const keys = _.split(_.trim(fullKey, "::"), "::");

        if (_.indexOf(keys, currentFieldKey) > -1) {
          return;
        }

        message += getLabelFromFieldKey(keys.pop()) + ": " + changedFields[fullKey] + "; ";
      });

      return message;
    }

    function isReallyEmpty(value) {
      return _.isEmpty(value) && !_.isNumber(value);
    }

    function getLabelFromFieldKey(key) {
      return _.upperFirst(_.replace(_.trimEnd(_.split(key, '|').pop(), '_c'), /_/g, ' '));
    }
  });
