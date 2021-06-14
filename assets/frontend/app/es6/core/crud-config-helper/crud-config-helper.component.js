'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.crudConfigHelper component
 * @description
 * # crudConfigHelper
 *
 * The crudConfigHelper component is a tool to help configurators.
 *
 * <crud-config-helper></crud-config-helper>
 *
 * Component of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .component('crudConfigHelper', {
    templateUrl: 'es6/core/crud-config-helper/crud-config-helper.component.html',
    controllerAs: 'crudConfigHelperController',
    controller: function (crudConfigHelperService) {
      const crudConfigHelperController = this;

      crudConfigHelperController.selectedRecordName = null;

      crudConfigHelperController.$onInit = function () {
        crudConfigHelperService.loadRecords();
      };

      crudConfigHelperController.getRecordsName = function () {
        return crudConfigHelperService.getRecordsName();
      };

      crudConfigHelperController.showSelectedRecord = function () {
        return _.isEmpty(crudConfigHelperController.selectedRecordName) === false;
      };
    }
  });
