'use strict';

angular.module('digitalWorkplaceApp')
  .factory('wysiwygEditorToolbarFactory', function (wysiwygFontSizeButtonFactory, wysiwygInsertTableButtonFactory) {
    return { setToolbar };

    function setToolbar(taOptions) {
      wysiwygFontSizeButtonFactory.setFontSize('fontSize');
      wysiwygInsertTableButtonFactory.setInsertTable('insertTable');

      taOptions.toolbar = [
        ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'bold', 'italics', 'underline', 'strikeThrough', 'justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
        ['insertTable', 'ul', 'ol', 'insertLink', 'redo', 'undo', 'clear']
      ];
    }
  });

