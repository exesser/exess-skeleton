'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp:collapsibleFormItem component
 * @description
 * # collapsibleFormItem
 *
 * CollapsibleFormItem is one single collapsible item that can be used in a collapsibleForm.
 *
 * Component of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .component('collapsibleFormItem', {
    templateUrl: 'es6/guidance-mode/guidance-forms/collapsible-form/collapsible-form-item.component.html',
    require: {
      guidanceObserversAccessor: "^guidanceObserversAccessor"
    },
    bindings: {
      label: "@",
      formKey: "@"
    },
    controllerAs: 'collapsibleFormItemController',
    controller: function($scope, guidanceFormControllerMixin) {
      const collapsibleFormItemController = this;

      collapsibleFormItemController.$onInit = function() {
        guidanceFormControllerMixin.apply({
          scope: $scope,
          controller: collapsibleFormItemController,
          controllerAs: 'collapsibleFormItemController',
          guidanceFormObserver: collapsibleFormItemController.guidanceObserversAccessor.getGuidanceFormObserver()
        });
      };
    }
  });
