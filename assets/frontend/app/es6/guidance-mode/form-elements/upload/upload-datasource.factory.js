'use strict';

/**
 * @ngdoc service
 * @name digitalWorkplaceApp.uploadDatasource
 * @description
 *
 * The uploadDatasource is a factory whose job it is to perform operations with files.
 *
 * Factory in the digitalWorkplaceApp.
 */
angular.module('digitalWorkplaceApp')
  .factory('uploadDatasource', function (API_URL, $http, LOG_HEADERS_KEYS) {

    return {removeFile};

      /**
       * Performs deleting file.
       *
       * @params {object} The postBody that need to be sent, including docGuid and model.
       * @return {Promise} A promise which resolves when the action has been completed.
       */
    function removeFile(params) {
      const headers = {};
      headers[LOG_HEADERS_KEYS.DESCRIPTION] = `Remove file`;

      return $http.post(
        API_URL + 'filedelete',
        params,
        {headers}
      ).then(function (response) {
        return response.data.data;
      });
    }
  });