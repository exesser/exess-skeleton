'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp:basicFormlyForm component
 * @description
 * # basicFormlyForm
 *
 * The most simple guidance form. Simply renders a formly form.
 * Styling can be augmented by wrapping this in grid-wrappers.
 *
 * Component of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .component('basicFormlyForm', {
    templateUrl: 'es6/guidance-mode/guidance-forms/basic-formly-form/basic-formly-form.component.html',
    require: {
      guidanceObserversAccessor: "^guidanceObserversAccessor"
    },
    bindings: {
      formKey: '@'
    },
    controllerAs: 'basicFormlyFormController',
    controller: function($scope, guidanceFormControllerMixin) {
      const basicFormlyFormController = this;

      basicFormlyFormController.$onInit = function() {
        guidanceFormControllerMixin.apply({
          scope: $scope,
          controller: basicFormlyFormController,
          controllerAs: 'basicFormlyFormController',
          guidanceFormObserver: basicFormlyFormController.guidanceObserversAccessor.getGuidanceFormObserver()
        });
      };
    }
  });
