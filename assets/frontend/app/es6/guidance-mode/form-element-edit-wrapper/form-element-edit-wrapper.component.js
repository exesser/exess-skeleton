'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.component:formElementEditWrapper
 * @description
 * # formElementEditWrapper
 * Component of the digitalWorkplaceApp
 *
 * This component shows an edit button which opens the CRUD page for the guidance field.
 */
angular.module('digitalWorkplaceApp')
  .component('formElementEditWrapper', {
    templateUrl: 'es6/guidance-mode/form-element-edit-wrapper/form-element-edit-wrapper.component.html',
    transclude: true,
    bindings: {
      fields: "<"
    },
    controllerAs: 'formElementEditWrapperController',
    controller: function (navigationHistoryContainer) {
      const formElementEditWrapperController = this;

      formElementEditWrapperController.guid = null;
      formElementEditWrapperController.hoverEdit = false;

      formElementEditWrapperController.$onInit = function() {
        formElementEditWrapperController.guid = _.get(
          formElementEditWrapperController.fields,
          '0.templateOptions.guid',
          null
        );
      };

      formElementEditWrapperController.click = function() {
        $window.open('/#/CRUD/dashboard/CrudRecordView/4abe018e-a071-faf1-c6cd-5be0b29a3909?recordType=ExEss%5CCms%5CEntity%5CFlowField', '_blank');
      }

      formElementEditWrapperController.showEditButton = function () {
        return (formElementEditWrapperController.hoverEdit
          && !_.isEmpty(formElementEditWrapperController.guid)
          && navigationHistoryContainer.getShowEditIcon());
      };

      formElementEditWrapperController.hoverIn = function(){
        formElementEditWrapperController.hoverEdit = true;
        console.log('hoverIn');
        console.log(formElementEditWrapperController.showEditButton());
      };

      formElementEditWrapperController.hoverOut = function(){
        formElementEditWrapperController.hoverEdit = false;
      };

    }
  });
