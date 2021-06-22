'use strict';

/**
 * @ngdoc service
 * @name digitalWorkplaceApp.listDatasource
 * @description
 *
 * The listDatasource is a factory whose job it is to provide
 * the data for lists, but also list actions.
 *
 * Factory in the digitalWorkplaceApp.
 */
angular.module('digitalWorkplaceApp')
  .factory('listDatasource', function (API_PATH, API_URL, $http, LOG_HEADERS_KEYS) {

    return {
      getList,
      getExtraRowContent,
      getActionButtons,
      exportToCSV
    };

    /**
     * Gets the contents for a list based on the provided listKey.
     * @param  {String} options.listKey The listKey you want the list for.
     * @param  {Object} options.params The parameters describing the page, such as query, filters and paging info.
     * @return {Promise} A promise which resolves to the data for the list.
     */
    function getList({ listKey, params }) {
      const headers = {};
      headers[LOG_HEADERS_KEYS.DESCRIPTION] = `List: ${listKey}`;

      return $http.post(API_PATH + `list/${listKey}`, params, {headers}).then(function (response) {
        return response.data.data;
      });
    }

    /**
     * Gets the extra row content for a a specific itemId, which belongs
     * to a list inside of a grid.
     * @param  {[type]} options.gridKey The gridKey you want extra row content for.
     * @param  {[type]} options.listKey The listKey you want extra row content for.
     * @param  {[type]} options.itemId  The itemId of the row you want extra row content for.
     * @param  {Object} options.actionData  The actionData to pass back into the result
     * @return {Promise} A promise which resolves to the extra row content for that row.
     */
    function getExtraRowContent({ gridKey, listKey, itemId, actionData}) {
      const headers = {};
      headers[LOG_HEADERS_KEYS.DESCRIPTION] = `List: ${listKey} | getExtraRow: ${gridKey} | recordId: ${itemId}`;

      return $http.post(API_URL + `ListExtraRowContent/${gridKey}/${listKey}/${itemId}`,  { actionData }, {headers}).then(function (response) {
        return response.data.data;
      });
    }

    /**
     * Gets the action buttons for a recordId of a recordType which can be performed
     * on a row inside a list.
     * @param  {String} options.recordType The recordType of the row you want the actions for.
     * @param  {String} options.recordId   The recordId of the row you want the actions for.
     * @param  {Object} options.actionData  The actionData to pass back into the result
     * @return {Promise} A promise which resolves to the action buttons for a particular row.
     */
    function getActionButtons({ recordType, recordId, actionData }) {
      const headers = {};
      headers[LOG_HEADERS_KEYS.DESCRIPTION] = `List | getActionButtons | recordId: ${recordId} | recordType: ${recordType}`;

      return $http.post(API_PATH + `list/${recordType}/row/bar/${recordId}`, { actionData }, {headers}).then(function (response) {
        return response.data.data.buttons;
      });
    }

    /**
     * Exports the contents of a list based on the provided listKey.
     * @param  {String} options.listKey The listKey you want the list for.
     * @param  {Object} options.params The parameters describing the page, such as query, filters and paging info.
     * @param  {Object} options.recordIds The selected records that need to be exported.
     * @return {Promise} A promise which resolves to a command.
     */
    function exportToCSV({ listKey, params }) {
      const headers = {};
      headers[LOG_HEADERS_KEYS.DESCRIPTION] = `List: ${listKey} | exportToCSV`;

      return $http.post(API_PATH + `list/${listKey}/export/csv`, params, {headers}).then(function (response) {
        return response.data.data;
      });
    }
  });
