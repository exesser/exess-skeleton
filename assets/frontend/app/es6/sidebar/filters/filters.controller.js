'use strict';


/**
 * This controller renders the filters when you open a page that supports it.
 * It is listening to the filtersObserver.setFilterData() method and requests the filters for this page when it is invoked.
 * When the scope is destroyed the contents are cleared again.
 */
angular.module('digitalWorkplaceApp')
  .controller('FiltersController', function(formlyFieldsTranslator, filtersObserver, sidebarObserver, filterDatasource, $scope,
                                            guidanceFormObserverFactory, validationObserverFactory, suggestionsObserverFactory,
                                            topActionState, listStatus, DEBOUNCE_TIME) {
    const filtersController = this;

    filtersController.model = {};
    filtersController.originalModel = {};
    filtersController.fieldGroups = [];
    filtersController.guidanceFormObserver = null;

    topActionState.setFiltersCanBeOpened(false);

    filtersObserver.registerSetFilterDataCallback(function(filterKey, listKey) {
      if (!topActionState.filtersCanBeOpened()) {
        filtersController.guidanceFormObserver = guidanceFormObserverFactory.createGuidanceFormObserver();
        filtersController.validationObserver = validationObserverFactory.createValidationObserver();
        filtersController.suggestionsObserver = suggestionsObserverFactory.createSuggestionsObserver();

        updateFilterList(filterKey, listKey);

        /*
         * When the filters controller is destroyed we close the sidebar, indicate that the filters cannot be opened
         * anymore and remove the top level guidanceFormObserver, validationObserver and suggestionsObserver.
         */
        $scope.$on("$destroy", function() {
          sidebarObserver.closeAllSidebarElements();
          topActionState.setFiltersCanBeOpened(false);
        });
      }
    });

    filtersController.resetModel = function () {
      // Creating a copy so the originalModel is not mutated when model is.
      filtersController.model = angular.copy(filtersController.originalModel);
    };

    function updateFilterList(filterKey, listKey) {
      filterDatasource.get({ filterKey, listKey }).then(function(data) {
        filtersController.model = data.model;

        // Creating a copy so the originalModel is not mutated when model is.
        filtersController.originalModel = angular.copy(filtersController.model);

        /*
          I'm requesting the filters for this list from sessionStorage,
          if there are no available filters for this listKey I receive
          an 'undefined'.  So I'm only replacing the filterController.model
          when I receive filters.
        */
        const sessionStorageModel = listStatus.getFilters(listKey);

        if (_.isUndefined(sessionStorageModel) === false) {
          filtersController.model = sessionStorageModel;
        }

        filtersController.fieldGroups = _.map(data.fieldGroups, (fieldGroup) => {
          fieldGroup.fields = formlyFieldsTranslator.translate(fieldGroup.fields);
          return fieldGroup;
        });

        topActionState.setFiltersCanBeOpened(true);

        $scope.$watch('filtersController.model', _.debounce(function(newValue, oldValue) {
          if (_.isEqual(oldValue, newValue) === false) {
            filtersObserver.filtersHaveChanged(listKey, newValue);
            listStatus.setFilters(listKey, newValue);
          }
        }, DEBOUNCE_TIME), true);
      });
    }
  });
