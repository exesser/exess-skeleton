'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.component:bestOfferAccordionWrapper
 * @description
 * # bestOfferAccordionWrapper
 * Component of the digitalWorkplaceApp
 *
 * This component shows a header with a label which can be opened and closed
 * to show / hide content.
 *
 * For example:
 *  <best-offer-accordion-wrapper is-open="true" label="Address"
 *    content
 *  </best-offer-accordion-wrapper>
 *
 * Component of the digital workplace.
 */
angular.module('digitalWorkplaceApp')
  .component('bestOfferAccordionWrapper', {
    templateUrl: 'es6/best-offer/best-offer-accordion-wrapper/best-offer-accordion-wrapper.component.html',
    transclude: true,
    bindings: {
      label: '@',
      isOpen: '<?'
    },
    controllerAs: 'bestOfferAccordionWrapperController',
    controller: function () {
      const bestOfferAccordionWrapperController = this;

      // At first the accordion is closed.
      bestOfferAccordionWrapperController.isOpen = _.get(bestOfferAccordionWrapperController, 'isOpen', false);

      /**
       * Opens the accordion if it is currently closed, and vice versa.
       */
      bestOfferAccordionWrapperController.toggle = function () {
        bestOfferAccordionWrapperController.isOpen = !bestOfferAccordionWrapperController.isOpen;
      };
    }
  });
