'use strict';

angular.module('digitalWorkplaceApp')
  .factory('wysiwygInsertTableButtonFactory', function (taRegisterTool) {
    return { setInsertTable };

    function setInsertTable(buttonKey) {
      taRegisterTool(buttonKey, {
        tooltiptext: 'Insert Table',
        display: '<wysiwyg-insert-table-button is-disabled="isDisabled()" editor="this.$editor()"></wysiwyg-insert-table-button>',
        action: function () {
        }
      });
    }
  });