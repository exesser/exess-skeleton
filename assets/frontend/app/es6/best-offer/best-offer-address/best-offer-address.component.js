'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.component:bestOfferAddress
 * @description
 * # bestOfferAddress
 * Component of the digitalWorkplaceApp
 *
 * This component shows the details of an address for best offer tool.
 *
 * For example:
 *  <best-offer-address address="{}"></best-offer-address>
 *
 * Component of the digital workplace.
 */
angular.module('digitalWorkplaceApp')
  .component('bestOfferAddress', {
    templateUrl: 'es6/best-offer/best-offer-address/best-offer-address.component.html',
    transclude: true,
    bindings: {
      address: '<'
    },
    controllerAs: 'bestOfferAddressController',
    controller: function (actionDatasource, commandHandler, translateFilter) {
      const bestOfferAddressController = this;

      bestOfferAddressController.products = [];
      bestOfferAddressController.offers = [];
      bestOfferAddressController.discounts = [];

      bestOfferAddressController.selectedOffers = {};
      bestOfferAddressController.selectedDiscounts = {};

      bestOfferAddressController.currentValue = 0;
      bestOfferAddressController.newValue = 0;
      bestOfferAddressController.inclVat = true;

      bestOfferAddressController.$onInit = function () {
        populateProducts();
        populateOffers();
        populateDiscounts();
      };

      function populateProducts() {
        if (
          _.has(bestOfferAddressController.address, 'elecProduct')
          && _.isNull(bestOfferAddressController.address.elecProduct) === false
        ) {
          bestOfferAddressController.products.push(bestOfferAddressController.address.elecProduct);
        }

        if (
          _.has(bestOfferAddressController.address, 'gasProduct')
          && _.isNull(bestOfferAddressController.address.gasProduct) === false
        ) {
          bestOfferAddressController.products.push(bestOfferAddressController.address.gasProduct);
        }
      }

      function populateOffers() {
        _.forEach(bestOfferAddressController.products, (product) => {
          bestOfferAddressController.offers.push(..._.map(product.offers, (offer) => {
            offer.productPricePerYear = product.pricePerYear;
            offer.productType = product.productType;
            offer.contractLineId = product.contractLineId;
            return offer;
          }));
        });
      }

      function populateDiscounts() {
        _.forEach(bestOfferAddressController.products, (product) => {
          bestOfferAddressController.discounts.push(..._.map(product.discounts, (discount) => {
            discount.productPricePerYear = product.pricePerYear;
            discount.productType = product.productType;
            discount.contractLineId = product.contractLineId;
            return discount;
          }));
        });
      }

      bestOfferAddressController.disableOffers = function (offerId) {
        if (_.isEmpty(bestOfferAddressController.selectedDiscounts) === false) {
          return true;
        }

        if (_.isEmpty(bestOfferAddressController.selectedOffers)) {
          return false;
        }

        const currentOffer = _.find(bestOfferAddressController.offers, (offer) => {
          return offer.id === offerId;
        });

        const selectedOffers = bestOfferAddressController.getSelectedOffers();

        const selectedOffersWithoutThisPackage = _.filter(selectedOffers, (offer) => {
          return offer.packageId !== currentOffer.packageId && offer.productType !== currentOffer.productType;
        });

        return _.isEmpty(selectedOffersWithoutThisPackage) === false;
      };

      bestOfferAddressController.disableDiscounts = function () {
        return _.isEmpty(bestOfferAddressController.selectedOffers) === false;
      };

      bestOfferAddressController.selectOffer = function (productType, offerId) {
        if (bestOfferAddressController.disableOffers(offerId)) {
          return;
        }

        bestOfferAddressController.selectedDiscounts = {};

        if (bestOfferAddressController.offerIsSelected(offerId)) {
          _.unset(bestOfferAddressController.selectedOffers, productType);
          return;
        }

        bestOfferAddressController.selectedOffers[productType] = offerId;
      };

      bestOfferAddressController.selectDiscount = function (productType, discountId) {
        if (bestOfferAddressController.disableDiscounts()) {
          return;
        }

        bestOfferAddressController.selectedOffers = {};

        if (bestOfferAddressController.discountIsSelected(discountId)) {
          _.unset(bestOfferAddressController.selectedDiscounts, productType);
          return;
        }

        bestOfferAddressController.selectedDiscounts[productType] = discountId;
      };

      bestOfferAddressController.offerIsSelected = function (offerId) {
        return _.includes(bestOfferAddressController.selectedOffers, offerId);
      };

      bestOfferAddressController.discountIsSelected = function (discountId) {
        return _.includes(bestOfferAddressController.selectedDiscounts, discountId);
      };

      bestOfferAddressController.shouldDisplayOfferFooter = function () {
        return _.isEmpty(bestOfferAddressController.selectedOffers) === false;
      };

      bestOfferAddressController.shouldDisplayDiscountsFooter = function () {
        return _.isEmpty(bestOfferAddressController.selectedDiscounts) === false;
      };

      bestOfferAddressController.getCurrentPriceBaseOnOffers = function () {
        bestOfferAddressController.currentValue = _.round(_.sumBy(bestOfferAddressController.getSelectedOffers(), 'productPricePerYear'), 2);
        bestOfferAddressController.newValue = _.round(_.sumBy(bestOfferAddressController.getSelectedOffers(), 'pricePerYear'), 2);
        bestOfferAddressController.inclVat =  _.get(_.first(bestOfferAddressController.getSelectedOffers()), 'inclVat', true);

        return bestOfferAddressController.currentValue;
      };

      bestOfferAddressController.getCurrentPriceBaseOnDiscounts = function () {
        bestOfferAddressController.currentValue = _.round(_.sumBy(bestOfferAddressController.getSelectedDiscounts(), 'productPricePerYear'), 2);

        return bestOfferAddressController.currentValue;
      };

      bestOfferAddressController.getSavings = function () {
        return _.round(_.subtract(bestOfferAddressController.currentValue, bestOfferAddressController.newValue), 2);
      };

      bestOfferAddressController.getSelectedOffers = function () {
        return _.filter(bestOfferAddressController.offers, (offer) => {
          return bestOfferAddressController.offerIsSelected(offer.id);
        });
      };

      bestOfferAddressController.getSelectedDiscounts = function () {
        return _.filter(bestOfferAddressController.discounts, (discount) => {
          return bestOfferAddressController.discountIsSelected(discount.id);
        });
      };

      bestOfferAddressController.getVatText = function (inclVat) {
        return '(' + translateFilter('BEST_OFFER.PRICE.' + (inclVat ? 'INCL' : 'EXCL') + '_VAT') + ')';
      };

      bestOfferAddressController.onChooseBestOffer = function () {
        let action = _.mergeWith({}, ..._.map(bestOfferAddressController.getSelectedOffers(), (offer) => {
          return offer.action;
        }), bestOfferAddressController.customizerMerge);

        bestOfferAddressController.action(action);
      };

      bestOfferAddressController.onChooseBestDiscounts = function () {
        let action = _.mergeWith({}, ..._.map(bestOfferAddressController.getSelectedDiscounts(), (discount) => {
          return discount.action;
        }), bestOfferAddressController.customizerMerge);

        bestOfferAddressController.action(action);
      };

      bestOfferAddressController.action = function (action) {
        if (_.has(action, 'command')) {
          commandHandler.handle(action);
          return;
        }

        actionDatasource.performAndHandle(action);
      };

      bestOfferAddressController.customizerMerge = function (objValue, srcValue) {
        if (_.isArray(objValue)) {
          return objValue.concat(srcValue);
        }
      };
    }
  });
