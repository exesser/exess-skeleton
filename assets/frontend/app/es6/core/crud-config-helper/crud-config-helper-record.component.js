'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.crudConfigHelperRecord component
 * @description
 * # crudConfigHelperRecord
 *
 * The crudConfigHelperRecord component renders a record data
 *
 * <crud-config-helper-record record-name="Accounts" prefix="aos_quotes"></crud-config-helper-record>
 *
 * Component of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .component('crudConfigHelperRecord', {
    templateUrl: 'es6/core/crud-config-helper/crud-config-helper-record.component.html',
    bindings: {
      recordName: "=",
      prefix: "@"
    },
    controllerAs: 'crudConfigHelperRecordController',
    controller: function (crudConfigHelperService) {
      const crudConfigHelperRecordController = this;

      crudConfigHelperRecordController.selectedRelation = null;
      crudConfigHelperRecordController.selectedField = null;
      crudConfigHelperRecordController.relationFilters = [];

      crudConfigHelperRecordController.relationChanged = function () {
        crudConfigHelperRecordController.selectedField = null;
        crudConfigHelperRecordController.relationFilters = [];
      };

      crudConfigHelperRecordController.fieldChanged = function () {
        crudConfigHelperRecordController.selectedRelation = null;
        crudConfigHelperRecordController.relationFilters = [];
      };

      crudConfigHelperRecordController.getFields = function () {
        return _.sortBy(crudConfigHelperService.getRecordFields(crudConfigHelperRecordController.recordName), ["name"]);
      };

      crudConfigHelperRecordController.getRelations = function () {
        return crudConfigHelperService.getRecordRelations(crudConfigHelperRecordController.recordName);
      };

      crudConfigHelperRecordController.getCompiledId = function () {
        if (!_.isEmpty(crudConfigHelperRecordController.selectedRelation)) {
          return '';
        }

        let prefix = crudConfigHelperRecordController.getPrefix();
        if (!_.isEmpty(prefix)) {
          prefix += "|";
        }

        if (!_.isEmpty(crudConfigHelperRecordController.selectedField)) {
          return prefix + crudConfigHelperRecordController.selectedField;
        }

        return crudConfigHelperRecordController.getPrefix();
      };

      crudConfigHelperRecordController.getPrefix = function () {
        let prefix = crudConfigHelperRecordController.prefix;
        if (!_.isEmpty(prefix)) {
          prefix += "|";
        }

        if (!_.isEmpty(crudConfigHelperRecordController.selectedRelation)) {
          let relation = crudConfigHelperService.getRecordRelation(
            crudConfigHelperRecordController.recordName,
            crudConfigHelperRecordController.selectedRelation
          );

          let id = prefix + crudConfigHelperRecordController.selectedRelation;

          let filters = _.compact(_.map(crudConfigHelperRecordController.relationFilters, 'expression')).join(';');
          if (!_.isEmpty(filters)) {
            id += "(" + filters + ")";
          }

          if (_.get(relation, 'multiRelation', false)) {
            id += "[]";
          }

          return id;
        }

        return crudConfigHelperRecordController.prefix;

      };

      crudConfigHelperRecordController.getSelectedRecordName = function () {
        let relation = crudConfigHelperService.getRecordRelation(
          crudConfigHelperRecordController.recordName,
          crudConfigHelperRecordController.selectedRelation
        );

        return _.get(relation, 'record', "NONE");
      };

      crudConfigHelperRecordController.showSelectedRecord = function () {
        return _.isEmpty(crudConfigHelperRecordController.selectedRelation) === false;
      };

      crudConfigHelperRecordController.addFilters = function () {
        _.remove(crudConfigHelperRecordController.relationFilters, function (filter) {
          return _.isEmpty(filter.expression);
        });

        crudConfigHelperRecordController.relationFilters.push({expression: ""});
      };

      crudConfigHelperRecordController.removeFilter = function (filter) {
        _.remove(crudConfigHelperRecordController.relationFilters, filter);
      };

      crudConfigHelperRecordController.getRelationFields = function () {
        return crudConfigHelperService.getRecordFields(crudConfigHelperRecordController.getSelectedRecordName());
      };
    }
  });
