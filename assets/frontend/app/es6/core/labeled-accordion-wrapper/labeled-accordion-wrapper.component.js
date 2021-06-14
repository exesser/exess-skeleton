'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.component:labeledAccordionWrapper
 * @description
 * # labeledAccordionWrapper
 * Component of the digitalWorkplaceApp
 *
 * This component shows a header with a label which can be opened and closed
 * to show / hide content.
 *
 * For example:
 *  <labeled-accordion-wrapper is-open="true" label="Create Lead" icon="icon-werkbakken">
 *    <ul>
 *      <menu-link link-to="create-lead" label="business" icon="icon-werkbakken" />
 *      <menu-link link-to="create-lead" label="household" icon="icon-werkbakken" />
 *    </ul>
 *  </labeled-accordion-wrapper>
 *
 * Component of the digital workplace.
 */
angular.module('digitalWorkplaceApp')
  .component('labeledAccordionWrapper', {
    templateUrl: 'es6/core/labeled-accordion-wrapper/labeled-accordion-wrapper.component.html',
    transclude: true,
    bindings: {
      label: '@',
      isOpen: '<?',
      icon: '@'
    },
    controllerAs: 'labeledAccordionWrapperController',
    controller: function() {
      const labeledAccordionWrapperController = this;

      // At first the accordion is closed.
      labeledAccordionWrapperController.isOpen = _.get(labeledAccordionWrapperController, 'isOpen', false);

      /**
       * Opens the accordion if it is currently closed, and vice versa.
       */
      labeledAccordionWrapperController.toggle = function() {
        labeledAccordionWrapperController.isOpen = !labeledAccordionWrapperController.isOpen;
      };
    }
  });
