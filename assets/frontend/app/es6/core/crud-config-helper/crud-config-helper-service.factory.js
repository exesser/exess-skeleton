"use strict";

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.factory:crudConfigHelperService factory
 * @description
 * # crudConfigHelperService
 */
angular.module('digitalWorkplaceApp')
  .factory('crudConfigHelperService', function ($window, crudConfigHelperDatasource) {

    const storageConfigHelperKey = "CONFIG_HELPER_RECORDS";

    let records = [];
    return {
      getRecordsName,
      getRecordByName,
      getRecordFields,
      getRecordRelations,
      getRecordRelation,
      loadRecords
    };

    function loadRecords() {
      if (_.isEmpty(records)) {
        loadRecordsFromSession();
      }

      if (_.isEmpty(records)) {
        loadRecordsFromBackend();
      }
    }

    function getRecordsName() {
      return _.sortBy(_.map(records, 'recordName'));
    }

    function getRecordByName(recordName) {
      return _.find(records, { recordName });
    }

    function getRecordFields(recordName) {
      return _.get(getRecordByName(recordName), 'fields', []);
    }

    function getRecordRelations(recordName) {
      return _.sortBy(_.get(getRecordByName(recordName), 'relations', []), ['record', 'name']);
    }

    function getRecordRelation(recordName, relationName) {
      return _.find(getRecordRelations(recordName), { name: relationName });
    }

    //private
    function loadRecordsFromSession() {
      let sessionData = $window.sessionStorage.getItem(storageConfigHelperKey);
      if (_.isNull(sessionData)) {
        return [];
      }

      sessionData = angular.fromJson(sessionData);

      if ((_.now() - sessionData.cacheTime) > 86400000) {
        return [];
      }

      records = _.get(sessionData, 'records', []);
    }

    function loadRecordsFromBackend() {
      crudConfigHelperDatasource.getRecordsInformation().then(function (data) {
        records = _.sortBy(data, ['recordName']);

        let sessionData = { cacheTime: _.now(), records };
        $window.sessionStorage.setItem(storageConfigHelperKey, angular.toJson(sessionData));
      });
    }

  });
