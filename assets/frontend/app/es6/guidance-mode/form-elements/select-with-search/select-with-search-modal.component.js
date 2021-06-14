"use strict";

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.selectWithSearchModal
 * @description
 * # selectWithSearchModal
 *
 * The selectWithSearchModal directive can be used to create a modal for select-with-search form element.
 * It then allows you to search for data there and select some records. The selected records are
 * store in "selectedResult". When you click submit the confirmCallback is called.
 *
 * Example usage:
 *
 * <select-with-search-modal
 *   selected-results="selectWithSearchFormElementController.selectedResults"
 *   confirm-callback="selectWithSearchFormElementController.updateModelData()"
 *   modal-title="Select your NACE codes"
 *   selected-results-title="Selected NACE codes"
 *   multiple-select="true"
 *   datasource-name="Nace">
 * </select-with-search-modal>
 *
 * Directive of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .component('selectWithSearchModal', {
    templateUrl: 'es6/guidance-mode/form-elements/select-with-search/select-with-search-modal.component.html',
    bindings: {
      modalTitle: "@",

      //The title to display above the selected results
      selectedResultsTitle: "@",

      //The datasource name to send to the backend when searching for data
      datasourceName: "@",

      multipleSelect: "<",

      params: "<",

      //Initial selected results. There is a two-way binding here because we use the modal component to manipulate the selectedResults array.
      selectedResults: "=",

      //This function will be called when we click top-right submit button
      //This will add the selected items to the ngModel and delete the modal
      confirmCallback: "&",

      //form full model
      fullModel: '<'
    },
    controllerAs: 'selectWithSearchModalController',
    controller: function (selectWithSearchDatasource, hotkeys) {
      const selectWithSearchModalController = this;

      //Set the initial configuration
      selectWithSearchModalController.query = "";
      selectWithSearchModalController.page = 1;
      selectWithSearchModalController.nonSelectedResults = [];
      selectWithSearchModalController.searchResults = {};

      hotkeys.add({
        combo: 'esc',
        description: 'Close the select with search modal.',
        allowIn: ['INPUT', 'SELECT', 'TEXTAREA'],
        callback: function () {
          selectWithSearchModalController.confirmCallback();
        }
      });

      updateList();

      /**
       * Add a given item to the selected results.
       * @param item a non selected result
       */
      selectWithSearchModalController.selectItem = (item) => {
        if (selectWithSearchModalController.multipleSelect === false) {
          selectWithSearchModalController.selectedResults = [];
        }

        selectWithSearchModalController.selectedResults.push(item);
        setNonSelectedResults();
      };

      /**
       * Removes a given item from the selected results.
       * @param item a selected result
       */
      selectWithSearchModalController.deselectItem = (item) => {
        selectWithSearchModalController.selectedResults = _.without(selectWithSearchModalController.selectedResults, item);
        setNonSelectedResults();
      };

      /**
       * Reset page number and update list when the 'Search' button is clicked.
       */
      selectWithSearchModalController.search = () => {
        selectWithSearchModalController.page = 1;
        updateList();
      };

      /**
       * Reset page number and update list when the 'Enter' key is pressed.
       */
      selectWithSearchModalController.onKeyUp = function (event) {
        const enterKeyCode = 13;
        if (event.keyCode === enterKeyCode) {
          selectWithSearchModalController.page = 1;
          updateList();
        }
      };

      /**
       * Request new list based on the entered search query and page number.
       * When the async request finishes the selectWithSearchModalController.searchResults property is changed to the outcome.
       */
      function updateList() {
        selectWithSearchDatasource.getSelectOptions(selectWithSearchModalController.datasourceName, {
          query: selectWithSearchModalController.query,
          page: selectWithSearchModalController.page,
          params: selectWithSearchModalController.params,
          fullModel: selectWithSearchModalController.fullModel
        }).then(function (data) {
          selectWithSearchModalController.searchResults = data;
          setNonSelectedResults();
        });
      }

      /**
       * Navigate to another page.
       */
      selectWithSearchModalController.setPage = (pageNumber) => {
        selectWithSearchModalController.page = pageNumber;
        updateList();
      };

      /**
       * Clears the query the user has entered.
       */
      selectWithSearchModalController.clearQuery = () => {
        selectWithSearchModalController.query = "";
        selectWithSearchModalController.page = 1;
        updateList();
      };

      /**
       * Sets the rows in the current search results that have not been selected.
       */
      function setNonSelectedResults() {
        selectWithSearchModalController.nonSelectedResults = _.reject(selectWithSearchModalController.searchResults.rows, function (result) {
          return _.some(selectWithSearchModalController.selectedResults, (selectedElement) => selectedElement.key === result.key);
        });
      }
    }
  });
