'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.bestOffer component
 * @description
 * # list
 *
 * <best-offer record-id="12345678-1234-1234-1234-123456789012"></best-offer>
 *
 * Component of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .component('bestOffer', {
    templateUrl: 'es6/best-offer/best-offer.component.html',
    controllerAs: 'bestOfferController',
    bindings: {
      recordId: "@"
    },
    controller: function (bestOfferDatasource, promiseUtils) {
      const bestOfferController = this;
      bestOfferController.addresses = [];
      bestOfferController.scripting = '';
      bestOfferController.accountLabel = '';

      const latestBestOfferDatasourceGetBestOffers = promiseUtils.useLatest(bestOfferDatasource.getBestOffers);

      bestOfferController.$onInit = function () {
        latestBestOfferDatasourceGetBestOffers(bestOfferController.recordId).then(function (data) {
          bestOfferController.addresses = data.addresses;
          bestOfferController.scripting = data.scripting;
          bestOfferController.accountLabel = data.accountLabel;
        });
      };
    }
  });
