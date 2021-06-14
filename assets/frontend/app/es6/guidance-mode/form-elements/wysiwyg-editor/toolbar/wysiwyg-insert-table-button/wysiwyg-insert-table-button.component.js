"use strict";

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.component:wysiwygInsertTableButton
 * @description
 * # wysiwygInsertTableButton
 * Component of the digitalWorkplaceApp
 *
 * This component creates a button to add a table in wysiwyg editor.
 *
 * Example usage:
 *
 * <wysiwyg-insert-table-button is-disabled="isDisabled()" editor="this.$editor()"></wysiwyg-insert-table-button>
 *
 */
angular.module('digitalWorkplaceApp')
  .component('wysiwygInsertTableButton', {
    templateUrl: 'es6/guidance-mode/form-elements/wysiwyg-editor/toolbar/wysiwyg-insert-table-button/wysiwyg-insert-table-button.component.html',
    bindings: {
      isDisabled: "<",
      insertTable: "<",
      editor: "<"
    },
    controllerAs: 'wysiwygInsertTableButtonController',
    controller: function () {
      const wysiwygInsertTableButtonController = this;

      wysiwygInsertTableButtonController.show = true;
      wysiwygInsertTableButtonController.settings = {
        buttonRows: _.range(1, 9),
        buttonCols: _.range(1, 7),
        showTable: false
      };

      wysiwygInsertTableButtonController.activeCell = {
        row: 0,
        col: 0
      };

      wysiwygInsertTableButtonController.cellIsActive = function (row, col) {
        return wysiwygInsertTableButtonController.activeCell.row >= row && wysiwygInsertTableButtonController.activeCell.col >= col;
      };

      wysiwygInsertTableButtonController.hoverIn = function (row, col) {
        wysiwygInsertTableButtonController.activeCell = { row, col };
      };

      wysiwygInsertTableButtonController.hoverOut = function () {
        wysiwygInsertTableButtonController.activeCell = { row: 0, col: 0 };
      };

      wysiwygInsertTableButtonController.addTable = function (row, col) {
        wysiwygInsertTableButtonController.settings.showTable = false;
        wysiwygInsertTableButtonController.editor.wrapSelection('insertHTML', generateTable(row, col));
      };

      function generateTable(rows, cols) {
        const tds = _.join(_.fill(Array(cols), '<td>&nbsp;</td>'), '');
        const trs = _.join(_.fill(Array(rows), '<tr>' + tds + '</tr>'), '');
        return '<table>' + trs + '</table><br>';
      }
    }
  });
