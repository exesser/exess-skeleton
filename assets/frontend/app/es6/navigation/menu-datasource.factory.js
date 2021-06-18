'use strict';

/**
 * @ngdoc service
 * @name digitalWorkplaceApp.menuDatasource
 * @description
 *
 * The menuDatasource is a factory whose job it is to provide
 * the data for the main menus and sub menus.
 *
 * Factory in the digitalWorkplaceApp.
 */
angular.module('digitalWorkplaceApp')
  .factory('menuDatasource', function($http, API_PATH, LOG_HEADERS_KEYS) {

    return { getMain, getSub };

    /**
     * Returns the main menus.
     * @return {Promise} A promise which resolves to the main menus.
     */
    function getMain() {
      const headers = {};
      headers[LOG_HEADERS_KEYS.DESCRIPTION] = 'Main menu';

      return $http.get(API_PATH + 'menu', {headers}).then(function(response) {
        return response.data.data;
      });
    }

    /**
     * Returns the sub menus for a certain main menu.
     * @param  {String} mainMenuKey The main menu key you want the sub menus for.
     * @return {Promise} A promise which resolves to the sub menus.
     */
    function getSub(mainMenuKey) {
      const headers = {};
      headers[LOG_HEADERS_KEYS.DESCRIPTION] = `Submenu for: ${mainMenuKey}`;

      return $http.get(API_PATH + `menu/${mainMenuKey}`, {headers}).then(function(response) {
        return response.data.data;
      });
    }
  });
