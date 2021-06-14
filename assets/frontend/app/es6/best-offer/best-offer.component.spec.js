'use strict';

describe('Component: bestOffer', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let scope;
  let element;

  let $rootScope;
  let $compile;
  let $q;
  let $timeout;

  let bestOfferDatasource;

  const template = '<best-offer record-id="accountId"></best-offer>';

  beforeEach(inject(function (_$rootScope_, _$compile_, _$timeout_, _$q_, $state, $stateParams, _bestOfferDatasource_) {
    $rootScope = _$rootScope_;
    $compile = _$compile_;
    $timeout = _$timeout_;
    $q = _$q_;

    bestOfferDatasource = _bestOfferDatasource_;

    mockHelpers.blockUIRouter($state);

    const bestOfferResponse = {
      "addresses": [
        {
          "address": "Londenstraat 58380 ZEEBRUGGE",
          "elecProduct": {},
          "gasProduct": {}
        },
        {
          "address": "Veldkant, 2550 KONTICH",
          "elecProduct": {},
          "gasProduct": {}
        }
      ],
      "scripting": "scripting-text",
      "accountLabel": "Gold"
    };

    spyOn(bestOfferDatasource, 'getBestOffers').and.callFake(mockHelpers.resolvedPromise($q, bestOfferResponse));

    scope = $rootScope.$new();

    element = angular.element(template);
    element = $compile(element)(scope);

    $rootScope.$apply();

    $timeout.flush();

    expect(bestOfferDatasource.getBestOffers).toHaveBeenCalledTimes(1);
    expect(bestOfferDatasource.getBestOffers).toHaveBeenCalledWith('accountId');
  }));

  it('should display the accountLabel and scripting.', function () {
    const divs = element.find('.card div');
    const smallElement = element.find('.card small');
    expect(divs.length).toBe(3);
    expect($(divs[1]).hasClass('a-customer-type--gold')).toBe(true);
    expect($(divs[1]).hasClass('a-customer-type')).toBe(true);
    expect(smallElement.hasClass('tooltip-left is-dark')).toBe(true);
    expect(smallElement.text()).toContain('Gold account');
    expect($(divs[2]).text()).toContain('scripting-text');
  });

  it('should display the addresses.', function () {
    const addresses = element.find('best-offer-accordion-wrapper');
    expect(addresses.length).toBe(2);

    const address1 = $(addresses[0]);
    const headerLink1 = $(address1.find('a.m-collapsable__header')[0]);
    const span1 = $(headerLink1.find('span')[0]);
    expect(headerLink1.text()).toContain('Londenstraat 58380 ZEEBRUGGE');
    expect(span1.hasClass('icon-arrow-down')).toBe(true);
    expect(address1.find('best-offer-address').length).toBe(1);

    const address2 = $(addresses[1]);
    const headerLink2 = $(address2.find('a.m-collapsable__header')[0]);
    const span2 = $(headerLink2.find('span')[0]);
    expect(headerLink2.text()).toContain('Veldkant, 2550 KONTICH');
    expect(span2.hasClass('icon-arrow-down')).toBe(true);
    expect(address2.find('best-offer-address').length).toBe(1);
  });
});
