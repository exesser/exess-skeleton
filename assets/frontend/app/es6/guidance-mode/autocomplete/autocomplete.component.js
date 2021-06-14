'use strict';

(function (jquery) {
  /**
   * This component draws an autocomplete menu for the non-binding suggestions.
   * When a suggestion is clicked, the model is updated with the changes
   * described in this suggestion.
   *
   * For example, if we have an autocomplete like this:
   *
   *  <autocomplete
   *    for-elements="controller.elementId"
   *    suggestion-clicked="controller.suggestionClicked(suggestion)"
   *    suggestions="controller.suggestions"
   *    suggestion-left-text-property="label"
   *    suggestion-right-text-property="labelAddition">
   *  </autocomplete>
   *
   * With a suggestions array like this:
   *  [
   *    { "label": "Drop", "model": { "favorite_candy": "DROP" } },
   *    { "label": "Chocolate", "model": { "favorite_candy": "CHOCOLATE" } },
   *    { "label": "Apple", "model": { "favorite_candy": "APPLE" } }
   *  ]
   *
   * And a model like this:
   *  {
   *    "favorite_drink": "COLA"
   *    "favorite_candy": ""
   *  }
   *
   * And we click the second option, the result is a model like this:
   *  {
   *    "favorite_drink": "COLA"
   *    "favorite_candy": "DROP"
   *  }
   *
   * The autocomplete can be controlled via the keyboard you can use
   * the UP and DOWN keys to select a suggestion when the element is
   * active. Pressing the ENTER key selects the suggestion and applies it.
   *
   * These only work when one of 'elements', for which an <autocomplete> works
   * for, is active. The elements are provided via the 'for-elements' one-way
   * binding.
   *
   * The suggestions hide when the ESC key is pressed, or when a mouse
   * click occurs. These events do not depend on the element being active.
   *
   * Suggestions only appear when the field is active.
   */
  angular.module('digitalWorkplaceApp')
    .component('autocomplete', {
      templateUrl: 'es6/guidance-mode/autocomplete/autocomplete.component.html',
      bindings: {
        // The elements ids for which the autocomplete works. Must be an array!
        forElements: '<',

        // The suggestions to show.
        suggestions: '<',

        // The property of the a suggestion object which will be used as the left text of a suggestion.
        suggestionLeftTextProperty: '@',

        // The property of the a suggestion object which will be used as the right text of a suggestion.
        suggestionRightTextProperty: '@',

        // Callback for when the suggestion is clicked.
        suggestionClicked: '&'
      },
      controllerAs: 'autocompleteController',
      controller: function ($scope, $document, $element, elementPosition) {
        const autocompleteController = this;

        const UP_KEY = 38;
        const DOWN_KEY = 40;
        const ENTER_KEY = 13;

        const LEFT_KEY = 37;
        const RIGHT_KEY = 39;
        const ESC_KEY = 27;
        const BACKSPACE_KEY = 8;

        let ulElement = null;

        // The starting index of the selected suggestion.
        autocompleteController.selectedSuggestionIndex = 0;

        // Don't display the suggestion until the input was clicked.
        autocompleteController.showSuggestion = false;
        autocompleteController.mousedownHandler = _.noop();
        autocompleteController.keyupHandler = _.noop();

        /*
          Listen to document keyUp
        */
        autocompleteController.$onInit = function () {
          // Get a reference to the ulElement so we can scroll it.
          ulElement = $element.find('ul');

          autocompleteController.keyupHandler = function keyup(event) {
            // Do not do anything when we do not have suggestions, or we don't show them.
            if (_.isEmpty(autocompleteController.suggestions) && autocompleteController.showSuggestion === false) {
              return;
            }

            const keyCode = event.keyCode;

            if (_.includes([LEFT_KEY, RIGHT_KEY, ESC_KEY, BACKSPACE_KEY], keyCode)) {
              hideSuggestions(); // Hide the autocomplete
            } else if (isOneForElementActive()) { // Only respond to keys when input one of the 'forElements' is active.
              if (keyCode === UP_KEY || keyCode === DOWN_KEY) {
                moveSelection(keyCode);
              } else if (keyCode === ENTER_KEY) {
                const suggestion = autocompleteController.suggestions[autocompleteController.selectedSuggestionIndex];

                autocompleteController.onSuggestionClicked(suggestion);
              }
            }

            // Call $scope.$apply to trigger the change detection in angular.
            $scope.$apply();
          };

          $document.bind('keyup', autocompleteController.keyupHandler);

          /*
            When mouse is clicked on one of the 'forElements' show
            the suggestions. Otherwise hide the suggestions if the
            ulElement was not clicked.
          */

          autocompleteController.mousedownHandler = function mousedown(event) {
            if (_.includes(autocompleteController.forElements, _.get(event, 'toElement.id', _.get(event, 'target.id', '')))) {
              showSuggestions();

              $scope.$apply();
            } else if (ulElement[0].contains(event.target) === false) {
              hideSuggestions();

              $scope.$apply();
            }
          };

          $document.bind('mousedown', autocompleteController.mousedownHandler);
        };

        /*
          Destroy our document event bindings, otherwise they will keep
          on firing when the <autocomplete> is destroyed.
        */
        autocompleteController.$onDestroy = function () {
          $document.unbind('keyup', autocompleteController.keyupHandler);
          $document.unbind('mousedown', autocompleteController.mousedownHandler);
        };

        /*
          Whenever the suggestions change hideSuggestions the selectedSuggestionIndex
          and re-show the suggestions.
        */
        autocompleteController.$onChanges = function (changes) {
          const suggestions = _.get(changes, 'suggestions.currentValue', []);

          if (suggestions.length > 0) {
            if (isOneForElementActive()) {
              showSuggestions();
            } else {
              hideSuggestions();
            }
          }
        };

        /*
          Render the autocompletions above the 'input' element only
          when that element is below the fold.
        */
        autocompleteController.shouldRenderAbove = function () {
          return elementPosition.isAboveFold($element) === false;
        };

        /**
         * Update the model with the changes described in the suggestion.
         * @param suggestion Suggestion containing a model property which is a subset of the model
         */
        autocompleteController.onSuggestionClicked = function (suggestion) {
          autocompleteController.suggestionClicked({ suggestion });

          hideSuggestions();
        };

        function hideSuggestions() {
          autocompleteController.selectedSuggestionIndex = 0;

          autocompleteController.showSuggestion = false;
        }

        function showSuggestions() {
          autocompleteController.selectedSuggestionIndex = 0;

          autocompleteController.showSuggestion = true;
        }

        // Move the selected suggestion up or down based on the keyCode.
        function moveSelection(keyCode) {
          // Determine the next index based on UP_KEY or DOWN_KEY
          const mod = keyCode === UP_KEY ? -1 : 1;
          let nextIndex = autocompleteController.selectedSuggestionIndex + mod;

          // Make sure the index never goes out of bounds
          nextIndex = _.clamp(nextIndex, 0, autocompleteController.suggestions.length - 1);

          // Finally apply the index
          autocompleteController.selectedSuggestionIndex = nextIndex;

          const liElements = ulElement.find('li');

          // We want the <li> semi-centered so we ignore the heights of the previous two.
          const moveTo = _(liElements)
            .take(nextIndex - 2)
            .map((li) => jquery(li).height())
            .sum();

          ulElement.scrollTop(moveTo);
        }

        // Is one of our 'forElements' the current active element.
        function isOneForElementActive() {
          /*
            Do not handle the keyEvent if the active element is not the
            element we provide autocompletions for. This makes sure we only
            handle keys when the autocompleted element is focused / used.

            Note: document is used instead of $document because it misses 'activeElement'.
          */
          const activeElementId = _.get(document, 'activeElement.id', '');

          return _.includes(autocompleteController.forElements, activeElementId);
        }
      }
    });
})(window.$); //eslint-disable-line angular/window-service
