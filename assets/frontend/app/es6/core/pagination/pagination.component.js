'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.component:pagination
 * @description
 * # pagination
 * Component of the digitalWorkplaceApp
 *
 * The component shows pagination controls given the totalPages, pageSize
 * the total number of results and the current page.
 *
 * The pagination controls consist of:
 *
 *  1. Previous arrow to go to previous page
 *  2. ... To go to the first page
 *  3. Page number to go to previous page
 *  4. The current page highlighted.
 *  5. Page number to go to next page
 *  6. ... To go to the final page
 *  7. Next arrow to go to next page.
 *
 * Visually this looks something like this:
 *
 * < .. 3 [4] 5 ... >
 *
 * The actual switching of the page does not happen in the pagination
 * component, that is delegated to the host component. Whenever a page
 * changes the 'pageChanged' event is fired.
 *
 * The pagination also shows a text telling the user what the position
 * of the pagination currently is. For example in the EN translation
 * this is: "showing rows 100 to 101 from 101 results".
 *
 * For example:
 *
 * <pagination
 *   total-pages="controller.pagination.pages"
 *   current-page="controller.pagination.page"
 *   page-size="controller.pagination.pageSize"
 *   total-results="controller.pagination.total"
 *   page-changed="controller.setPage(page)">
 * </pagination>
 *
 * Component of the digital workplace.
 */
angular.module('digitalWorkplaceApp')
  .component('pagination', {
    templateUrl: 'es6/core/pagination/pagination.component.html',
    bindings: {
      totalPages: '<',
      currentPage: '<',
      pageSize: '<',
      totalResults: '<',
      pageChanged: '&'
    },
    controllerAs: 'paginationController',
    controller: function($translate) {
      const paginationController = this;

      // The text staying on which page the user is and how many there are still left.
      paginationController.text = "";

      // Whenever the bindings change recalculate the text
      paginationController.$onChanges = function() {
        const translationText = paginationController.hasTotalResults() ? 'PAGINATION_TEXT' : 'PAGINATION_TEXT_NO_TOTAL';

        const translateData = {
          start: start(),
          end: end(),
          total: paginationController.totalResults
        };

        $translate(translationText, translateData).then((text) => {
          paginationController.text = _.unescape(text);
        });
      };

      paginationController.onPageChanged = function(page) {
        paginationController.pageChanged({ page });
      };

      function start() {
        return (paginationController.currentPage - 1) * paginationController.pageSize + 1;
      }

      function end() {
        const end = paginationController.currentPage * paginationController.pageSize;

        // Don't let 'end' be bigger than the 'totalResults'
        if (paginationController.hasTotalResults()) {
          return Math.min(end, paginationController.totalResults);
        }

        return end;
      }

      paginationController.hasTotalResults = function () {
        return _.isNumber(paginationController.totalResults);
      };
    }
  });
