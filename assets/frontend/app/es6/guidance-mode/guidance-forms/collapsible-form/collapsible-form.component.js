'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp:collapsibleForm component
 * @description
 * # collapsibleForm
 *
 * CollapsibleForm is a form in which items that can be collapsed / folded by clicking a button.
 *
 * Component of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .component('collapsibleForm', {
    templateUrl: 'es6/guidance-mode/guidance-forms/collapsible-form/collapsible-form.component.html',
    bindings: {
      items: '<'
    },
    controllerAs: 'collapsibleFormController',
    controller: _.noop
  });
