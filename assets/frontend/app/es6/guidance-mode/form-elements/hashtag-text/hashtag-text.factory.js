'use strict';

/**
 * @ngdoc service
 * @name digitalWorkplaceApp.hashtagTextFactory
 * @description
 * # hashtagTextFactory
 *
 * The hashtagTextFactory helps the hashtag-text-form-element by handling
 * things such as getting the last three words from a text, based
 * on the cursor position.
 *
 * Factory in the digitalWorkplaceApp.
 */
angular.module('digitalWorkplaceApp')
  .factory('hashtagTextFactory', function ($window, $document) {

    return {
      isCaretAtEndOfWord,
      threeWordsBackFromCaret,
      caretPositionForElement,
      caretPositionForEditableDiv,
      applyAutocompletion
    };

    /**
     * Returns whether or not the caret position in the text is at the
     * end of a word.
     *
     * @param  {String}  text  The text which holds the caret
     * @param  {Number}  caret The position in the text of the caret
     * @return {Boolean}       Whether or not the caret is at the end of a word.
     */
    function isCaretAtEndOfWord(text, caret) {
      const letter = clearHtmlTags(text).charAt(caret);

      return letter === '' || letter === ' ' || letter.charCodeAt() === 160;
    }

    /**
     * Takes a text and a caret position and returns the closest
     * three words to the left of the caret. If only one or two
     * words can be found before the caret, only those words are
     * returned.
     *
     * @param  {String}  text  The text which holds the caret
     * @param  {Number}  caret The position in the text of the caret
     * @return {String}        A string containing the three (or less) words before the caret.
     */
    function threeWordsBackFromCaret(text, caret) {
      const partialText = clearHtmlTags(text).substring(0, caret);

      return _(partialText).words().takeRight(3).join(' ');
    }

    function clearHtmlTags(text) {
      return text.replace(/<[^>]*>/g, '');
    }

    /**
     * Returns the caret position for an HTML element. If the element
     * cannot provide a caret position, this can happen if the browser
     * doesn't support it, it returns the boolean false.
     *
     *
     * @param  {Element} element The HTML element you want the caret for.
     * @return {Number|Boolean} The caret position or false when it could not retrieve the caret position.
     */
    function caretPositionForElement(element) {
      if (element.setSelectionRange) {
        return element.selectionStart;
      } else {
        return false;
      }
    }

    /**
     * Returns the caret position for an editable DIV. If the element
     * cannot provide a caret position, this can happen if the browser
     * doesn't support it, it returns the boolean false.
     *
     *
     * @param  {Element} editableDiv The DIV element you want the caret for.
     * @return {Number|Boolean} The caret position or false when it could not retrieve the caret position.
     */
    function caretPositionForEditableDiv(editableDiv) {
      const w3 = !_.isUndefined($window.getSelection);
      const ie = !_.isUndefined($document.selection) && $document.selection.type !== 'Control';

      if (w3) {
        let range = $window.getSelection().getRangeAt(0);
        let preCaretRange = range.cloneRange();
        preCaretRange.selectNodeContents(editableDiv);
        preCaretRange.setEnd(range.endContainer, range.endOffset);
        return preCaretRange.toString().length - 1;
      }

      if (ie) {
        let textRange = $document.selection.createRange();
        let preCaretTextRange = $document.body.createTextRange();
        preCaretTextRange.expand(editableDiv);
        preCaretTextRange.setEndPoint('EndToEnd', textRange);
        return preCaretTextRange.text.length;
      }

      return false;
    }

    /**
     * Applies an autocompletion on a text, the result is a new text
     * which is autocompleted.
     *
     * Note: the 'applyAutocompletion' replaces all occurrences of the
     * trigger with the autocompletion within the text!
     *
     * @param  {String} text           The complete text that needs to be autocompleted.
     * @param  {String} autocompletion The autocompletion text which needs to be added in the text.
     * @param  {String} trigger        The subselection of the complete text which trigger the autocompletion and which is replaced.
     * @return {String}                The new autocompleted text.
     */
    function applyAutocompletion(text, autocompletion, trigger) {
      // Creates a regex which will replace all occurrences for a string.
      const regex = new RegExp(trigger.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&'), 'g');

      /*
        Now apply the regex to replace all instances of 'trigger' with
        the autocompletion. This might seem a bit excessive and it is
        but it is the most 'trustworthy' approach.

        Originally we did try to replace the text based on the position
        of the cursor. This proved to be problematic  because the cursors
        position 'jumps' quite erratically when the user types fast
        and uses a lot of shortcuts. This caused the 'replacement' not
        to work as expected so this approach was dropped in favor for
        the replace all.

        There is a downside to this approach: because we replace all
        occurrences of 'trigger' with 'autocomplete' we might 'replace'
        something that the user does not want us to replace. The chances
        of the user typing the same text twice and wanting replacement
        for one but not the other is however very small, so we take
        this for granted.
      */
      return text.replace(regex, autocompletion);
    }
  });
