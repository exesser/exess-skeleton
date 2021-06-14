'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.list-link-bold-bottom-two-liner-cell component
 * @description
 * # list-link-bold-bottom-two-liner-cell
 *
 * Creates a cell with two lines, first line is bold pink link and second line is normal.
 *
 * Example usage:
 * <list-link-bold-bottom-two-liner-cell
 *      line-1="WKY 2"
 *      line-2="BE012345678"
 *      link-to="dashboard"
 *      params='{"mainMenuKey":"sales-marketing","dashboardId":"account","recordId":"a8d6fbfd-58ce-4f9c-0afe-5735d87a01eb"}'>
 * </list-link-bold-bottom-two-liner-cell>
 *
 * Component of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .component('listLinkBoldBottomTwoLinerCell', {
    templateUrl: 'es6/list/cells/list-link-bold-bottom-two-liner-cell/list-link-bold-bottom-two-liner-cell.component.html',
    bindings: {
      line1: "@",
      line2: "@",
      linkTo: "@",
      params: "<"
    },
    controllerAs: 'listBoldBottomTwoLinerCellController',
    controller: function ($state, guidanceModalObserver) {
      const listLinkBoldBottomTwoLinerCellController = this;

      listLinkBoldBottomTwoLinerCellController.navigate = function () {
        // Sometimes the list is on a modal so first we have to reset the Modal and then navigate.
        guidanceModalObserver.resetModal();

        $state.go(listLinkBoldBottomTwoLinerCellController.linkTo, listLinkBoldBottomTwoLinerCellController.params);
      };
    }
  });
