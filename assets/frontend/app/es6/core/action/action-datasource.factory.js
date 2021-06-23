'use strict';

/**
 * @ngdoc service
 * @name digitalWorkplaceApp.actionDatasource
 * @description
 *
 * The actionDatasource is a factory whose job it is to perform 'Actions'
 * based on the parameters that were given.
 *
 * Factory in the digitalWorkplaceApp.
 */
angular.module('digitalWorkplaceApp')
  .factory('actionDatasource', function (API_PATH, $http, commandHandler, LOG_HEADERS_KEYS) {

    return { perform, performAndHandle };

    /**
     * Performs the actionId action and gives the postBody in the POST body.
     *
     * @param  {object} The postBody that need to be sent, one the properties must be the actionId.
     * @throws {Error} If there is no actionId provided in the postBody object.
     * @return {Promise} A promise which resolves when the action has been completed.
     */
    function perform(postBody) {
      const { id } = postBody;

      if (_.isUndefined(id)) {
        throw new Error("actionDatasource: 'perform' must have an 'id' property defined in the postBody.");
      }

      const headers = {};
      headers[LOG_HEADERS_KEYS.DESCRIPTION] = `Perform action: ${id}`;

      return $http.post(API_PATH + `action/${id}`, postBody, {headers}).then(function(response) {
        return response.data.data;
      });
    }

    /**
     * Performs the actionId action and gives the postBody in the POST body.
     * After the back-end has responded it gives the data to the commandHandler
     * in order to do the processing.
     *
     * Basically 'performAndHandle' is a fire and forget version of
     * perform, which has a default handling implementation.
     *
     * @param  {object} postBody The postBody that need to be sent, one the properties must be the actionId.
     * @param  {boolean} newWindow A navigate command can be opened in new window.
     * @throws {Error} If there is no actionId provided in the postBody object.
     */
    function performAndHandle(postBody, newWindow = false) {
      perform(postBody).then((response) => {
        if (newWindow && _.has(response, 'arguments')) {
          response.arguments.newWindow = newWindow;
        }
        commandHandler.handle(response);
      });
    }
  });
