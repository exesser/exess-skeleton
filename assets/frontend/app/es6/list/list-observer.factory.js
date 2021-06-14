'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.factory:listObserver factory
 * @description
 * # listObserver
 *
 * ## Responsibility
 *
 * The list observer is responsible for the interaction of the dynamic
 * list and the outside world.
 *
 * It is responsible for handling the following events:
 *
 * **Toggle list row selection**
 *
 * The toggleListRowSelection event is fired when the selection checkbox
 * is checked for one specific row in a dynamic list. It is triggered by the
 * listCheckboxCellComponent and processed by the listComponent.
 * It takes a list key, item id to indicate the specific row and a
 * boolean which is true if it is now checked and false if it is now
 * unchecked. The listComponent uses these events to know what items
 * are currently selected.
 *
 * There can be one toggleListRowSelection callback per list key.
 * If another list with the same list key calls registerToggleListRowSelectionCallback
 * or if you come back to the same page later the previous handler is
 * overwritten.
 *
 * **Toggle all list rows selections**
 *
 * The toggleListRowSelection event is fired when the 'select all'
 * checkbox is checked It is triggered by the listComponent and processed
 * by the listCheckboxCellComponent. It takes a list key and a boolean
 * to indicate whether all rows are now selected or deselected.
 * The listCheckboxCellComponent uses these events to either check or
 * uncheck the checkboxes.
 *
 * The registerToggleAllListRowsSelectionsCallback callbacks are grouped
 * per list key. When a toggleListRowSelection event is fired for a specific
 * list key the callbacks that have been registered for that list key,
 * which are all the rows of that list, are invoked.
 *
 * The registerToggleAllListRowsSelectionsCallback function returns a
 * deregister function. When the listCheckboxCellComponent is destroyed,
 * it deregisters its callback so it is not informed anymore when you
 * go back to the page later.
 *
 * **Toggle extra row content placeholder**
 *
 * The toggleExtraRowContentPlaceholder event is fired when the plus
 * button for a specific list row is clicked. It is triggered by the
 * listPlusCellComponent and handled by the listComponent. It takes a
 * list key, grid key and an item id to indicate the specific row. The
 * grid key refers to a specific grid in the backend. For example,
 * 'action-bar' is the black bar with action items on it. When the user
 * clicks the plus button the content for the extra row is retrieved
 * from the backend and added to the view.
 *
 * There can be one toggleListRowSelection callback per list key.
 * If another list with the same list key calls
 * registerToggleListRowSelectionCallback or if you come back
 * to the same page later the previous handler is overwritten.
 *
 * **Reload list**
 *
 * The reloadList event can fired by commands coming back from the backend.
 * It takes a list key as a parameter and is handled by the listComponent.
 * When fired, the listComponent rerenders the rentire list.
 *
 * There can be one toggleListRowSelection callback per list key.
 * If another list with the same list key calls registerToggleListRowSelectionCallback or if you come back to the same page later the previous handler is overwritten.
 *
 * ## Lifespan and cardinality
 *
 * The lifespan of the listObserver is unbounded. It is created when the
 * application starts up and it remains alive during its entire life span.
 *
 * The cardinality of the subscribers and publishers depends on the
 * specific event. See the explanations for that under the 'responsibility'
 * paragraph.
 */
angular.module('digitalWorkplaceApp')
  .factory('listObserver', function() {

    let toggleListRowSelectionCallbacks = {};
    let toggleAllListRowsSelectionsCallbacks = {};
    let toggleExtraRowContentPlaceholderCallback = {};
    let reloadListCallback = {};

    return {
      toggleListRowSelection,
      registerToggleListRowSelectionCallback,

      toggleAllListRowsSelections,
      registerToggleAllListRowsSelectionsCallback,

      toggleExtraRowContentPlaceholder,
      registerToggleExtraRowContentPlaceholderCallback,

      reloadList,
      registerReloadListCallback
    };

    /**
     * Inform the observer when an a list row with a specific itemId is either selected or unselected.
     * @param listKey the key of the list the itemId belongs to
     * @param itemId the id of the given row
     * @param itemSelected whether or not the item is selected
     */
    function toggleListRowSelection(listKey, itemId, itemSelected) {
      if (_.has(toggleListRowSelectionCallbacks, listKey)) {
        toggleListRowSelectionCallbacks[listKey](itemId, itemSelected);
      }
    }

    /**
     * Register a callback function to invoke when the toggleListRowSelection function is invoked.
     * @param listKey
     * @param callback function to invoke with the itemId and itemSelected as arguments
     */
    function registerToggleListRowSelectionCallback(listKey, callback) {
      toggleListRowSelectionCallbacks[listKey] = callback;
    }

    /**
     * Inform the observer of the new changes on select list.
     * @param listKey
     * @param itemSelected
     */
    function toggleAllListRowsSelections(listKey, itemSelected) {
      _.forEach(toggleAllListRowsSelectionsCallbacks[listKey], function(callback) {
        callback(itemSelected);
      });
    }

    /**
     * Register a callback function to invoke when the toggleAllListRowsSelections function is invoked.
     * @param listKey
     * @param callback function to invoke with the itemSelected as an argument
     * @return {Function} deregister function. When called this will stop informing the given callback of toggleAllListRowsSelections events.
     */
    function registerToggleAllListRowsSelectionsCallback(listKey, callback) {
      if (_.has(toggleAllListRowsSelectionsCallbacks, listKey) === false) {
        toggleAllListRowsSelectionsCallbacks[listKey] = [];
      }
      toggleAllListRowsSelectionsCallbacks[listKey].push(callback);

      return function() {
        _.remove(toggleAllListRowsSelectionsCallbacks[listKey], function(toggleAllListRowsSelectionsCallback) {
          return toggleAllListRowsSelectionsCallback === callback;
        });
      };
    }

    /**
     * Inform the observer to toggle an extra grid for a given itemId.
     * @param listKey
     * @param gridKey
     * @param itemId
     * @param actionData
     */
    function toggleExtraRowContentPlaceholder(listKey, gridKey, itemId, actionData) {
      if (_.has(toggleExtraRowContentPlaceholderCallback, listKey)) {
        toggleExtraRowContentPlaceholderCallback[listKey](gridKey, itemId, actionData);
      }
    }

    /**
     * Register a callback function to invoke when the toggleExtraRowContentPlaceholder function is invoked.
     * @param listKey
     * @param callback function to invoke with the itemId as an argument
     */
    function registerToggleExtraRowContentPlaceholderCallback(listKey, callback) {
      toggleExtraRowContentPlaceholderCallback[listKey] = callback;
    }

    /**
     * Inform the observer that the list with the listKey must be reloaded.
     * @param listKey
     */
    function reloadList(listKey) {
      if (_.has(reloadListCallback, listKey)) {
        reloadListCallback[listKey]();
      }
    }

    /**
     * Register a callback function to invoke when the reloadList function is invoked.
     * @param listKey
     * @param callback function to invoke with the itemId and itemSelected as arguments
     */
    function registerReloadListCallback(listKey, callback) {
      reloadListCallback[listKey] = callback;
    }
  });
