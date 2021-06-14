'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.crudConfigHelperFilterField component
 * @description
 * # crudConfigHelperFilterField
 *
 * <crud-config-helper-filter-field filter-expressio="" fields="{}"></crud-config-helper-filter-field>
 *
 * Component of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .component('crudConfigHelperFilterField', {
    templateUrl: 'es6/core/crud-config-helper/crud-config-helper-filter-field.component.html',
    bindings: {
      filterExpression: "=",
      fields: "="
    },
    controllerAs: 'crudConfigHelperFilterFieldController',
    controller: function () {
      const crudConfigHelperFilterFieldController = this;

      crudConfigHelperFilterFieldController.enum = null;
      crudConfigHelperFilterFieldController.filterField = null;
      crudConfigHelperFilterFieldController.filterValue = null;

      crudConfigHelperFilterFieldController.getSortedFields = function () {
        return _.sortBy(crudConfigHelperFilterFieldController.fields, ["name"]);
      };

      crudConfigHelperFilterFieldController.fieldChanged = function () {
        let fieldDetails = getFieldDetails();

        if (!_.isEmpty(fieldDetails.enumValues)) {
          crudConfigHelperFilterFieldController.enum = angular.copy(fieldDetails.enumValues);
          crudConfigHelperFilterFieldController.filterValue = "";
        } else if (fieldDetails.type === 'boolean') {
          crudConfigHelperFilterFieldController.enum = [{ key: 0, value: "FALSE" }, { key: 1, value: "TRUE" }];
          crudConfigHelperFilterFieldController.filterValue = 0;
        } else {
          crudConfigHelperFilterFieldController.enum = null;
        }

        crudConfigHelperFilterFieldController.generateExpression();
      };

      crudConfigHelperFilterFieldController.generateExpression = function () {

        if (
          _.isEmpty(crudConfigHelperFilterFieldController.filterField)
          || (
            _.isEmpty(crudConfigHelperFilterFieldController.filterValue)
            && !_.isNumber(crudConfigHelperFilterFieldController.filterValue)
          )
        ) {
          crudConfigHelperFilterFieldController.filterExpression = '';
          return;
        }

        let fieldDetails = getFieldDetails();

        let value = crudConfigHelperFilterFieldController.filterValue;
        if (_.isString(value) || fieldDetails.type === 'string') {
          value = "'" + value + "'";
        }

        crudConfigHelperFilterFieldController.filterExpression = crudConfigHelperFilterFieldController.filterField + "=" + value;
      };

      function getFieldDetails() {
        return _.find(
          crudConfigHelperFilterFieldController.fields,
          { "name": crudConfigHelperFilterFieldController.filterField }
        );
      }
    }
  });
