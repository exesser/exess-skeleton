'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.list-dropdown-cell component
 * @description
 * # list-dropdown-cell
 *
 * Creates a cell with a dropdown of values to show.
 *
 * Example usage:
 * <list-dropdown-cell
 *   default-option="3 contacts"
 *   dropdown-options='[{"label":"kristof vc","action":{"command":"navigate","arguments":{"linkTo":"focus-mode","params":{"mainMenuKey":"sales-marketing","focusModeId":"test","recordId":"kristof"}}}},
 *                     {"label":"birgit matthe","action":{"command":"navigate","arguments":{"linkTo":"focus-mode","params":{"mainMenuKey":"sales-marketing","focusModeId":"test","recordId":"birgit"}}}},
 *                     {"label":"blubber vis","action":{"command":"navigate","arguments":{"linkTo":"focus-mode","params":{"mainMenuKey":"sales-marketing","focusModeId":"test","recordId":"blubber"}}}}]'
 * ></list-dropdown-cell>
 *
 * Component of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .component('listDropdownCell', {
    templateUrl: 'es6/list/cells/list-dropdown-cell/list-dropdown-cell.component.html',
    bindings: {
      dropdownOptions: "<",
      defaultOption: "@"
    },
    controllerAs: 'listDropdownCellController',
    controller: function (commandHandler) {
      const listDropdownCellController = this;

      listDropdownCellController.navigate = function (selected) {
        if (!_.isEmpty(selected) && !_.isEmpty(selected.action)) {
          commandHandler.handle(selected.action);
        }
      };
    }
  });
