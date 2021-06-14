'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.component:crudListFormElementButton
 * @description
 * # crudListFormElementButton
 * Component of the digitalWorkplaceApp
 *
 * Simple button for the crud-list-form-element.
 * It is given an index and a 'buttonClicked' callback function which it calls immediately when you click the button.
 *
 * For example, the following template will call the parent controller's removeRow function with the index 42 when clicked:
 *
 * <crud-list-form-element-button
 *   icon-class="icon-remove"
 *   row-index='42'
 *   button-clicked='parentController.removeRow(index)'>
 * </crud-list-form-element-button>
 *
 * Component of the digital workplace
 */
angular.module('digitalWorkplaceApp')
  .component('crudListFormElementButton', {
    templateUrl: 'es6/guidance-mode/form-elements/crud-list/crud-list-form-element-button.component.html',
    controllerAs: 'crudListFormElementButtonController',
    bindings: {
      iconClass: "@",
      rowIndex: "<",
      buttonClicked: "&"
    },
    controller: function() {
      const crudListFormElementButtonController = this;

      crudListFormElementButtonController.click = function() {
        crudListFormElementButtonController.buttonClicked({ index: crudListFormElementButtonController.rowIndex });
      };
    }
  });
