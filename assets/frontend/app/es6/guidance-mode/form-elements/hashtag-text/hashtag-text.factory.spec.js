'use strict';

describe('Factory: hashtagTextFactory', function () {
  beforeEach(module('digitalWorkplaceApp'));

  // instantiate service
  let hashtagTextFactory;

  const text = 'hello big blue and green world!';

  beforeEach(inject(function (_hashtagTextFactory_, $state) {
    mockHelpers.blockUIRouter($state);

    hashtagTextFactory = _hashtagTextFactory_;
  }));

  it('should know if a caret position is at the end of a word.', function () {
    // On the first position of the sentence
    expect(hashtagTextFactory.isCaretAtEndOfWord(text, 0)).toBe(false);

    // After 'hello'
    expect(hashtagTextFactory.isCaretAtEndOfWord(text, 5)).toBe(true);

    // After the space of 'hello '
    expect(hashtagTextFactory.isCaretAtEndOfWord(text, 6)).toBe(false);

    // At the end of the sentence
    expect(hashtagTextFactory.isCaretAtEndOfWord(text, 31)).toBe(true);
  });

  it('should know how to get the last three words from a caret position.', function () {
    // On the first position of the sentence
    expect(hashtagTextFactory.threeWordsBackFromCaret(text, 0)).toBe('');

    // After 'hello'
    expect(hashtagTextFactory.threeWordsBackFromCaret(text, 5)).toBe('hello');

    // After 'big'
    expect(hashtagTextFactory.threeWordsBackFromCaret(text, 9)).toBe('hello big');

    // After 'blue'
    expect(hashtagTextFactory.threeWordsBackFromCaret(text, 14)).toBe('hello big blue');

    // At the end of the sentence
    expect(hashtagTextFactory.threeWordsBackFromCaret(text, 31)).toBe('and green world');

    // When there are line endings in the sentence it should also work.
    const multiLine = `This is a multi line\ntext and\n`;

    // At the end of a text
    expect(hashtagTextFactory.threeWordsBackFromCaret(multiLine, 30)).toBe('line text and');

    // Between a line break and a text
    expect(hashtagTextFactory.threeWordsBackFromCaret(multiLine, 26)).toBe('multi line text');
  });

  it('should know how to get a carets position.', function () {
    // Create a mock element which is API compliant to what 'caretPositionForElement' expects.
    const element = { setSelectionRange: _.noop, selectionStart: 5 };

    expect(hashtagTextFactory.caretPositionForElement(element)).toBe(5);

    expect(hashtagTextFactory.caretPositionForElement({})).toBe(false);
  });

  it('should know how to apply an autocompletion', function() {
    const text = 'The old! fox jumped over the lazy dog. The old! fox jumped over the pool.';
    const autocompletion = 'The quick';
    const trigger = 'The old!';

    const result = hashtagTextFactory.applyAutocompletion(text, autocompletion, trigger);
    expect(result).toBe('The quick fox jumped over the lazy dog. The quick fox jumped over the pool.');
  });
});
