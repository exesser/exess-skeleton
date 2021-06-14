'use strict';

describe('Factory: wysiwygEditorToolbarFactory', function () {
  beforeEach(module('digitalWorkplaceApp'));

  let wysiwygEditorToolbarFactory;
  let wysiwygFontSizeButtonFactory;
  let wysiwygInsertTableButtonFactory;

  beforeEach(inject(function (_wysiwygEditorToolbarFactory_, _wysiwygFontSizeButtonFactory_, _wysiwygInsertTableButtonFactory_) {
    wysiwygEditorToolbarFactory = _wysiwygEditorToolbarFactory_;
    wysiwygFontSizeButtonFactory = _wysiwygFontSizeButtonFactory_;
    wysiwygInsertTableButtonFactory = _wysiwygInsertTableButtonFactory_;

    spyOn(wysiwygFontSizeButtonFactory, 'setFontSize');
    spyOn(wysiwygInsertTableButtonFactory, 'setInsertTable');

  }));

  it('should update the toolbar buttons and set the custom ones', function () {
    let taOptions = { toolbar: ["bla"] };

    expect(wysiwygFontSizeButtonFactory.setFontSize).not.toHaveBeenCalled();
    expect(wysiwygInsertTableButtonFactory.setInsertTable).not.toHaveBeenCalled();

    wysiwygEditorToolbarFactory.setToolbar(taOptions);

    expect(taOptions.toolbar).toEqual([
      ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'bold', 'italics', 'underline', 'strikeThrough', 'justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
      ['insertTable', 'ul', 'ol', 'insertLink', 'redo', 'undo', 'clear']
    ]);

    expect(wysiwygFontSizeButtonFactory.setFontSize).toHaveBeenCalled();
    expect(wysiwygInsertTableButtonFactory.setInsertTable).toHaveBeenCalled();

    expect(wysiwygFontSizeButtonFactory.setFontSize).toHaveBeenCalledWith('fontSize');
    expect(wysiwygInsertTableButtonFactory.setInsertTable).toHaveBeenCalledWith('insertTable');
  });
});
