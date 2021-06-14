"use strict";

/**
 * @ngdoc factory
 * @name digitalWorkplaceApp.guidanceModeDatasource
 * @description
 *
 * The guidanceModeDatasource is a factory whose job it is to provide
 * the data for the guidance modes.
 *
 * Factory in the digitalWorkplaceApp.
 */
angular.module('digitalWorkplaceApp')
  .factory('guidanceModeDatasource', function (API_URL, $http, LOG_HEADERS_KEYS, modelSession, replaceSpecialCharacters) {

    return {get, step};

    /**
     * Gets the information of a guidance-mode.
     *
     * @param  {String} options.recordType The record type of the guidance mode which is requested.
     * @param  {String} options.flow       The flowId of the guidance mode which is requested.
     * @param  {String} options.recordId   The recordId of the guidance mode which is requested.
     * @param  {String} options.flowAction The flowAction of the guidance mode which is requested.
     * @param  {String} options.modelKey   The model session key.
     * @param  {Object} postBody           The post body which is sent.
     * @return {Promise} A promise which resolves to the data for the guidance mode.
     */
    function get({recordType, flowId, recordId, flowAction, modelKey}, postBody = {}) {
      if (_.isEmpty(modelKey) === false) {
        postBody.model = _.merge({}, modelSession.getModel(modelKey), _.get(postBody, 'model', {}));
      }

      const url = buildUrl([recordType ? encodeURIComponent(recordType) : null, flowId, recordId, flowAction]);
      const headers = {};
      headers[LOG_HEADERS_KEYS.DESCRIPTION] = `Guidance: ${flowId}`;

      return doPost(url, headers, postBody);
    }

    /**
     * Triggers a step on a particular guidance mode to move to the next
     * step of the guidance or to finish it.
     *
     * @param  {String} options.flowId     The flowId of the guidance mode which request a step.
     * @param  {String} options.recordId   The recordId of the guidance mode which is requested.
     * @param  {Object} postBody           The post body which is sent.
     * @return {Promise} A promise which resolves to the data for the guidance mode step.
     */
    function step({flowId, recordId}, postBody) {
      const url = buildUrl([flowId, recordId]);
      const headers = {};
      headers[LOG_HEADERS_KEYS.DESCRIPTION] = `Guidance: ${flowId} | step | recordId: ${recordId}`;

      return doPost(url, headers, postBody);
    }

    function doPost(url, headers, postBody) {
      postBody = replaceSpecialCharacters.replaceArraySign(postBody, false);
      return $http.post(url, postBody, {headers}).then(function (response) {
        if (_.has(response, 'data.data.parentModel') && _.has(postBody, 'parentModel')) {
          response.data.data.parentModel = difference(response.data.data.parentModel, postBody.parentModel);
        }
        return replaceSpecialCharacters.replaceArraySign(response.data.data);
      });
    }

    /**
     * Builds a parameterized url with optional parameters. When a parameter is non-empty, it is put on the url.
     * @param parameters array of optional parameters in order they need to be put on the url.
     * @returns {String}
     */
    function buildUrl(parameters) {
      return _.reduce(parameters, function (url, parameter) {
        const stringParameter = _.toString(parameter);
        return _.isEmpty(stringParameter) ? url : url + `/${parameter}`;
      }, API_URL + 'Flow');
    }

    function difference(object, base) {
      function changes(object, base) {
        return _.transform(object, function (result, value, key) {
          if (!_.isEqual(value, base[key])) {
            result[key] = (_.isObject(value) && _.isObject(base[key])) ? changes(value, base[key]) : value;
          }
        });
      }

      return changes(object, base);
    }
  });
