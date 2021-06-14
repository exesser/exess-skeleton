'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp:hashtagTextFormElement component
 * @description
 * # hashtagTextFormElement
 *
 * The hashtag-text element allows the user to type in a textual description
 * which contains in it some hashtags. The hashtags tell the other users of
 * the system what the text is about by glancing at the tags.
 *
 * The user is assisted with adding these hashtag through an autocompletion.
 * The autocompletion is requested from the back-end through the hashtag-datasource.
 * Then a the hashtag-text-factory is put to work it handles applying the
 * autocompletion on the correct place in the text.
 *
 * Example usage:
 *
 * <hashtag-text-form-element
 *   ng-model
 *   id="{{ options.id }}" <!-- The id for the form element, used for e2e testing -->
 *   key="{{ options.key }}" <!-- The key to bind to in the model -->
 *   is-disabled="options.templateOptions.disabled" <!-- Expression that disables this field when it evaluates to true -->
 *   is-readonly="false" <!-- Expression that makes the field readonly when it evaluates to true -->
 *   no-backend-interaction="false" <!-- Indicates whether or not to make a call to backend when the field is changed -->
 *   datasource-name="{{ options.templateOptions.datasourceName }}"> <!-- The name of the datasouce where the hashtags come from -->
 * </hashtag-text-form-element>
 *
 * The reason the 'hashtagTextFormElement' is necessary, and why the
 * 'hashtag-text' formly types uses this Component, is because we need to
 * require the 'guidanceObserversAccessor' in order to obtain the
 * validationObserver.
 */
angular.module('digitalWorkplaceApp')
  .component('hashtagTextFormElement', {
    templateUrl: 'es6/guidance-mode/form-elements/hashtag-text/hashtag-text-form-element.component.html',
    require: {
      ngModel: "ngModel",
      guidanceObserversAccessor: '^guidanceObserversAccessor'
    },
    bindings: {
      id: "@",
      key: "@",
      //The datasource name to send to the back-end when searching for data
      datasourceName: "@",
      isDisabled: "<",
      isReadonly: "<",
      displayWysiwyg: "<",
      noBackendInteraction: "<"
    },
    controllerAs: 'hashtagTextFormElementController',
    controller: function ($scope, $timeout, validationMixin, isDisabledMixin, hashtagDatasource, hashtagTextFactory, modelChangedMixin,
                          $element, promiseUtils, DEBOUNCE_TIME, elementIdGenerator) {
      const hashtagTextFormElementController = this;

      const UP_KEY = 38;
      const DOWN_KEY = 40;
      const ENTER_KEY = 13;

      // Represents the inner model in which we store the text and the tags.
      hashtagTextFormElementController.internalModelValue = {
        text: '', // The text as a string.
        tags: []  // The tags which are an array of strings.
      };

      // Keeps track of the autocompletions for the text.
      hashtagTextFormElementController.textAutocompletions = [];

      // Keeps track of which query was used to get the 'textAutocompletions'
      hashtagTextFormElementController.textAutocompletionQuery = '';

      // Keeps track of the autocompletions for the hashtags.
      hashtagTextFormElementController.hashtagAutocompletions = [];

      /*
       Wrap 'hashtagDatasource.search' so only the response or the last
       request is used when two or more request are pending at the
       same time.

       We should not use hashtagDatasource.search directly anymore.

       We must do this twice once for the '<textarea>' autocomplete and
       once for the '<input>' autocomplete. If we do not do this and
       simple used one 'useLatest' they would interfere with each other.
       */
      const latestHashtagDatasourceSearchForTextArea = promiseUtils.useLatest(hashtagDatasource.search);
      const latestHashtagDatasourceSearchForInput = promiseUtils.useLatest(hashtagDatasource.search);

      hashtagTextFormElementController.$onInit = function () {
        const validationObserver = hashtagTextFormElementController.guidanceObserversAccessor.getValidationObserver();
        validationMixin.apply(hashtagTextFormElementController, validationObserver);

        isDisabledMixin.apply(hashtagTextFormElementController);

        modelChangedMixin.apply(hashtagTextFormElementController, 'hashtagTextFormElementController', $scope, false);

        const guidanceFormObserver = hashtagTextFormElementController.guidanceObserversAccessor.getGuidanceFormObserver();
        hashtagTextFormElementController.elementId = elementIdGenerator.generateId(hashtagTextFormElementController.id, guidanceFormObserver);

        /**
         * This is called when we need to determine if the value of an
         * input is empty.
         *
         * For instance, the required directive does this to work out if
         * the input has data or not.
         *
         * The default `$isEmpty` function checks whether the value is
         * `undefined`, `''`, `null` or `NaN`.
         *
         * In the case of an hashtagText, the default $isEmpty function
         * would consider an empty object non-empty, but in the context
         * of an hashtagText we consider an hashtagText empty if the
         * text is empty, and we ignore the tags altogether.
         *
         * See Angular's $isEmpty function for more information.
         *
         * @param {*} value The value of the input to check for emptiness.
         * @returns {boolean} True if `value` is "empty".
         */
        hashtagTextFormElementController.ngModel.$isEmpty = function (value) {
          return _.isEmpty(_.get(value, 'text'));
        };
      };

      /*
       The hashtagText form element retrieves the initial non-undefined
       ngModel $viewValue and sets this to hashtagTextFormElementController.internalModelValue.
       That is the field which contains the tags and the text.
       Because we bind to properties on the same object we do not need
       to propagate them out again.

       Every time the ngModel.$viewValue changes after the first initial
       setting we send out formValueChanged events.

       Unfortunately we cannot add a callback to the $viewChangeListeners
       here because we need to do an object equality check.

       Afterwards we trigger the re-evaluation of the validation. If
       we don't do this the empty check is not performed again.
       */
      $scope.$watch('hashtagTextFormElementController.ngModel.$viewValue', function (newValue, oldValue) {
        if (_.isObject(newValue)) {
          hashtagTextFormElementController.internalModelValue = newValue;

          // When the previous value was already an object and it is unequal to the new value, a change has occurred.
          // We don't use ngChange for this since it could also have been triggered by a suggestion.
          if (_.isObject(oldValue) && _.isEqual(newValue, oldValue) === false) {
            hashtagTextFormElementController.internalModelValueChanged();
          }
        }
        hashtagTextFormElementController.ngModel.$validate();
      }, true);

      /*
       Do not allow the use of the UP and DOWN keys when suggestions
       are shown. Otherwise the cursor will be moved in the <textarea>
       which will feel strange, and aborts the autocompletion.
       */
      hashtagTextFormElementController.textareaKeyDown = function (event) {
        const keyCode = event.keyCode;

        if (!_.isEmpty(hashtagTextFormElementController.textAutocompletions) && _.includes([UP_KEY, DOWN_KEY, ENTER_KEY], keyCode)) {
          event.preventDefault();
        }
      };

      /*
       When the user types in the textarea try to autocomplete him by
       getting autocompletions from the back-end.
       */
      hashtagTextFormElementController.textareaKeyPress = _.debounce(function textareaKeyPress() {
        $timeout(function () {
          // The <textarea> which holds the 'text'.
          const textAreaElement = $element.find('textarea')[0];

          const text = hashtagTextFormElementController.internalModelValue.text;
          let caret = hashtagTextFactory.caretPositionForElement(textAreaElement);

          if (hashtagTextFormElementController.displayWysiwyg) {
            caret = hashtagTextFactory.caretPositionForEditableDiv($element.find("div[contenteditable]")[0]);
          }

          if (caret !== false) {
            if (hashtagTextFactory.isCaretAtEndOfWord(text, caret)) {
              const query = _.trimEnd(hashtagTextFactory.threeWordsBackFromCaret(text, caret), ' p');

              if (_.isEmpty(query) === false) {
                const dataSourceName = hashtagTextFormElementController.datasourceName;

                /*
                 Clear the autocompletions now so the old autocompletions
                 will not be visible anymore.
                 */
                hashtagTextFormElementController.textAutocompletions = [];

                latestHashtagDatasourceSearchForTextArea(dataSourceName, query).then((completions) => {
                  hashtagTextFormElementController.textAutocompletionQuery = query;
                  hashtagTextFormElementController.textAutocompletions = completions;
                });
              }
            }
          }
        }, 100);
      }, DEBOUNCE_TIME);

      /*
       Apply an autocompletion by changing the 'text' and adding the
       'tag' to the list of tags. Also clear the completions when they
       are clicked.
       */
      hashtagTextFormElementController.applyTextAutocompletion = function (completion) {
        // The <textarea> which holds the 'text'.
        const textAreaElement = $element.find('textarea')[0];

        const text = hashtagTextFormElementController.internalModelValue.text;
        const autocompletion = completion.replacement;
        const trigger = hashtagTextFormElementController.textAutocompletionQuery;

        const result = hashtagTextFactory.applyAutocompletion(text, autocompletion, trigger);

        hashtagTextFormElementController.internalModelValue.text = result;

        addTag(completion);

        textAreaElement.focus();

        hashtagTextFormElementController.textAutocompletions = [];
      };

      hashtagTextFormElementController.getFieldIdForSuggestions = function() {
        if (!hashtagTextFormElementController.displayWysiwyg) {
          return hashtagTextFormElementController.elementId;
        }

        const textarea = $element.find("div[contenteditable]");

        if (!_.isEmpty(textarea)) {
          return textarea[0].id;
        }

        return null;
      };

      // Remove tag when 'x' is clicked.
      hashtagTextFormElementController.removeTag = function (tagId) {
        const removed = _.filter(hashtagTextFormElementController.internalModelValue.tags, (tag) => tag.id !== tagId);
        hashtagTextFormElementController.internalModelValue.tags = removed;
      };

      /*
       Do not allow the use of the UP and DOWN keys when suggestions
       are shown. Otherwise the cursor will be moved in the <textarea>
       which will feel strange, and aborts the autocompletion.
       */
      hashtagTextFormElementController.inputKeyDown = function (event) {
        const keyCode = event.keyCode;

        if (!_.isEmpty(hashtagTextFormElementController.hashtagAutocompletions) && _.includes([UP_KEY, DOWN_KEY], keyCode)) {
          event.preventDefault();
        }
      };

      /*
       When the user types in the add-tag input try to autocomplete
       him by getting autocompletions from the back-end.
       */
      hashtagTextFormElementController.inputKeyPress = _.debounce(function inputKeyPress(event) {
        const query = event.target.value;

        const dataSourceName = hashtagTextFormElementController.datasourceName;

        latestHashtagDatasourceSearchForInput(dataSourceName, query).then((completions) => {
          hashtagTextFormElementController.hashtagAutocompletions = completions;
        });
      }, DEBOUNCE_TIME);

      /*
       Clear the input element, add the tag, and empty the autocompletions
       for the hashtag input element.
       */
      hashtagTextFormElementController.applyHashtagAutocompletion = function (completion) {
        // The <input> from which the user can manually add a tag.
        const hashtagInputElement = $element.find('input')[0];

        hashtagInputElement.value = '';

        addTag(completion);

        hashtagTextFormElementController.hashtagAutocompletions = [];
      };

      // Adds a tag, but only if the tag is not in the list of tags yet.
      function addTag(completion) {
        const tag = { id: completion.id, hashtag: completion.hashtag };

        if (_.isUndefined(_.find(hashtagTextFormElementController.internalModelValue.tags, tag))) {
          hashtagTextFormElementController.internalModelValue.tags.push(tag);
        }
      }
    }
  });
