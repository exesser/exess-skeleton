'use strict';

describe('Component: bestOfferAddressController', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let $rootScope;
  let $compile;
  let actionDatasource;
  let commandHandler;
  let addressData;

  let element;

  beforeEach(inject(function (_$rootScope_, _$compile_, $state, _actionDatasource_, _commandHandler_) {
    $rootScope = _$rootScope_;
    $compile = _$compile_;
    actionDatasource = _actionDatasource_;
    commandHandler = _commandHandler_;

    spyOn(actionDatasource, 'performAndHandle');
    spyOn(commandHandler, 'handle');

    mockHelpers.blockUIRouter($state);
  }));

  function compile(elec = true, gas = true) {
    const template = `<best-offer-address address="address"></best-offer-address>`;

    const scope = $rootScope.$new();

    const elecProduct = {
      "productType": "ELEC",
      "contractLineId": "93a0e756-6531-85a8-fd26-5a290bbf0fac",
      "ean": "541444923109042406",
      "productAction": {
        "id": "modal_best_offer_product_details",
        "recordId": "93a0e756-6531-85a8-fd26-5a290bbf0fac",
        "recordType": "AOS_Products_Quotes",
        "params": {
          "accountType": "B2C"
        }
      },
      "packageAction": {
        id: 'KEY_MODAL_BEST_OFFER_PACKAGE_DETAILS',
        recordId: 1234
      },
      "otherProductChangeAction": {
        "command": "navigate",
        "arguments": {
          "linkTo": "guidance-mode",
          "params": {
            "flowId": "product_change_tc1_revised",
            "recordId": "68fbacb3-bbe1-0179-f3e2-5a290bdbe33c",
            "recordType": "AOS_Contracts",
            "model": {
              "product_change_reason": "RETENTION_MANUAL"
            }
          }
        }
      },
      "package": "COMFORT_TEST",
      "product": "Electricity Fix B2C (TC1)",
      "metaData": [
        [
          {
            "label": "EAN",
            "value": "541444923109042406"
          },
          {
            "label": "Contract",
            "value": "1525",
            "action": {
              "id": "navigate_to_gf_contract_view_cockpit",
              "recordId": "68fbacb3-bbe1-0179-f3e2-5a290bdbe33c"
            }
          }
        ],
        [{
          label: "EAV night",
          value: "1939 kWh"
        }, {
          label: "Supply stop reason",
          value: "PRODUCT_CHANGE"
        }, {
          label: "End date",
          value: "28-02-2018"
        }]
      ],
      "offers": [
        {
          "id": "6c49077f-4156-0b3a-e1b4-5a13dd6ab8a8-a5af0d33-bf89-844e-8359-58b809e4082c-e95757ac-bb67-7d88-365e-5a1bddec5ede",
          "productAction": {
            "id": "modal_best_offer_offer_details",
            "recordId": "da341836-da17-bf8e-54e5-5a207513b2a1",
            "recordType": "PACK_TariffSheetPrices",
            "params": {
              "packageId": "6c49077f-4156-0b3a-e1b4-5a13dd6ab8a8",
              "productId": "a5af0d33-bf89-844e-8359-58b809e4082c",
              "tariffsheetId": "e95757ac-bb67-7d88-365e-5a1bddec5ede",
              "tariffSheetPriceId": "da341836-da17-bf8e-54e5-5a207513b2a1",
              "contractLineId": "93a0e756-6531-85a8-fd26-5a290bbf0fac",
              "accountType": "B2C"
            }
          },
          "package": "TC_ONLINE_B2C",
          "packageAction": null,
          "packageId": "6c49077f-4156-0b3a-e1b4-5a13dd6ab8a8",
          "product": "Electricity Floating B2C (TC1)",
          "productId": "a5af0d33-bf89-844e-8359-58b809e4082c",
          "pricePerYear": 1000,
          "savings": 100,
          "tariffSheetId": "e95757ac-bb67-7d88-365e-5a1bddec5ede",
          "action": {
            "command": "navigate",
            "arguments": {
              "linkTo": "guidance-mode",
              "params": {
                "flowId": "product_change_tc1_revised",
                "recordId": "68fbacb3-bbe1-0179-f3e2-5a290bdbe33c",
                "recordType": "AOS_Contracts",
                "model": {
                  "aos_products_quotes|package_id": "6c49077f-4156-0b3a-e1b4-5a13dd6ab8a8",
                  "aos_products_quotes|tariffsheet_id": "e95757ac-bb67-7d88-365e-5a1bddec5ede",
                  "product_change_reason": "RETENTION",
                  "aos_quotes_aos_contracts_1|id": "68fbacb3-bbe1-0179-f3e2-5a290bdbe33c",
                  "dwp|available_product_ids": [
                    "a5af0d33-bf89-844e-8359-58b809e4082c"
                  ],
                  "aos_products_quotes": {
                    "a5af0d33-bf89-844e-8359-58b809e4082c": {
                      "product_id": "a5af0d33-bf89-844e-8359-58b809e4082c",
                      "package_id": "6c49077f-4156-0b3a-e1b4-5a13dd6ab8a8",
                      "prev_contract_line_id": "93a0e756-6531-85a8-fd26-5a290bbf0fac",
                      "tariffsheet_id": "e95757ac-bb67-7d88-365e-5a1bddec5ede"
                    }
                  }
                }
              }
            }
          },
          "packageProperties": [
            "pack-prop                           "
          ],
          "packageProductProperties": [
            "1 year"
          ]
        },
        {
          "id": "5b72e0f5-2f9b-1fb7-8043-5a1bdf25c494-c0f94c2f-72e0-51b9-ce93-58930799ecf1-e95757ac-bb67-7d88-365e-5a1bddec5ede",
          "productAction": {
            "id": "modal_best_offer_offer_details",
            "recordId": "c21f1780-a9e4-72af-fb60-5a20754f0c89",
            "recordType": "PACK_TariffSheetPrices",
            "params": {
              "packageId": "5b72e0f5-2f9b-1fb7-8043-5a1bdf25c494",
              "productId": "c0f94c2f-72e0-51b9-ce93-58930799ecf1",
              "tariffsheetId": "e95757ac-bb67-7d88-365e-5a1bddec5ede",
              "tariffSheetPriceId": "c21f1780-a9e4-72af-fb60-5a20754f0c89",
              "contractLineId": "93a0e756-6531-85a8-fd26-5a290bbf0fac",
              "accountType": "B2C"
            }
          },
          "package": "TC_ZDAL_B2C",
          "packageAction": null,
          "packageId": "5b72e0f5-2f9b-1fb7-8043-5a1bdf25c494",
          "product": "Electricity Fix B2C (TC1)",
          "productId": "c0f94c2f-72e0-51b9-ce93-58930799ecf1",
          "pricePerYear": 2000,
          "savings": -20,
          "tariffSheetId": "e95757ac-bb67-7d88-365e-5a1bddec5ede",
          "action": {
            "command": "navigate",
            "arguments": {
              "linkTo": "guidance-mode",
              "params": {
                "flowId": "product_change_tc1_revised",
                "recordId": "68fbacb3-bbe1-0179-f3e2-5a290bdbe33c",
                "recordType": "AOS_Contracts",
                "model": {
                  "aos_products_quotes|package_id": "5b72e0f5-2f9b-1fb7-8043-5a1bdf25c494",
                  "aos_products_quotes|tariffsheet_id": "e95757ac-bb67-7d88-365e-5a1bddec5ede",
                  "product_change_reason": "RETENTION",
                  "aos_quotes_aos_contracts_1|id": "68fbacb3-bbe1-0179-f3e2-5a290bdbe33c",
                  "dwp|available_product_ids": [
                    "c0f94c2f-72e0-51b9-ce93-58930799ecf1"
                  ],
                  "aos_products_quotes": {
                    "c0f94c2f-72e0-51b9-ce93-58930799ecf1": {
                      "product_id": "c0f94c2f-72e0-51b9-ce93-58930799ecf1",
                      "package_id": "5b72e0f5-2f9b-1fb7-8043-5a1bdf25c494",
                      "prev_contract_line_id": "93a0e756-6531-85a8-fd26-5a290bbf0fac",
                      "tariffsheet_id": "e95757ac-bb67-7d88-365e-5a1bddec5ede"
                    }
                  }
                }
              }
            }
          },
          "packageProperties": null,
          "packageProductProperties": [
            "Green",
            "3 years"
          ]
        }
      ],
      "discounts": [
        {
          "id": "3dc202c7-7370-4848-788f-5a285bfe9e49",
          "name": "E-RetDF30E",
          "rates": [
            "-30 Year 0-12"
          ],
          "action": {
            "id": "modal_to_add_discounts_on_contract_lines",
            "params": {
              "model": {
                "dwp|discounts_on_contract_lines": {
                  "93a0e756-6531-85a8-fd26-5a290bbf0fac::3dc202c7-7370-4848-788f-5a285bfe9e49": {
                    "contractline_id": "93a0e756-6531-85a8-fd26-5a290bbf0fac",
                    "discount_id": "3dc202c7-7370-4848-788f-5a285bfe9e49",
                    "key": "93a0e756-6531-85a8-fd26-5a290bbf0fac::3dc202c7-7370-4848-788f-5a285bfe9e49",
                    "label": "Contract line 541444923109042406 (ELEC) - status: ACTIVE ( from 2017-12-01 to 2018-11-30 ) - Discount E-RetDF30E"
                  }
                }
              }
            }
          }
        },
        {
          "id": "cf32b6fe-b38a-d023-5bad-5a2860b8cd12",
          "name": "E-RetDTF10-10-10P",
          "rates": [
            "-10 Percentage 0-12",
            "-10 Percentage 12-24",
            "-10 Percentage 24-36"
          ],
          "action": {
            "id": "modal_to_add_discounts_on_contract_lines",
            "params": {
              "model": {
                "dwp|discounts_on_contract_lines": {
                  "93a0e756-6531-85a8-fd26-5a290bbf0fac::cf32b6fe-b38a-d023-5bad-5a2860b8cd12": {
                    "contractline_id": "93a0e756-6531-85a8-fd26-5a290bbf0fac",
                    "discount_id": "cf32b6fe-b38a-d023-5bad-5a2860b8cd12",
                    "key": "93a0e756-6531-85a8-fd26-5a290bbf0fac::cf32b6fe-b38a-d023-5bad-5a2860b8cd12",
                    "label": "Contract line 541444923109042406 (ELEC) - status: ACTIVE ( from 2017-12-01 to 2018-11-30 ) - Discount E-RetDTF10-10-10P"
                  }
                }
              }
            }
          }
        }
      ],
      "pricePerYear": 1581,
      "inclVat": true
    };

    const gasProduct = {
      "productType": "GAS",
      "contractLineId": "b222eeb9-04e8-8890-beff-5a290b130daf",
      "ean": "541445216589141400",
      "productAction": {
        "id": "modal_best_offer_product_details",
        "recordId": "b222eeb9-04e8-8890-beff-5a290b130daf",
        "recordType": "AOS_Products_Quotes",
        "params": {
          "accountType": "B2C"
        }
      },
      "packageAction": null,
      "otherProductChangeAction": {
        "command": "navigate",
        "arguments": {
          "linkTo": "guidance-mode",
          "params": {
            "flowId": "product_change_tc1_revised",
            "recordId": "68fbacb3-bbe1-0179-f3e2-5a290bdbe33c",
            "recordType": "AOS_Contracts",
            "model": {
              "product_change_reason": "RETENTION_MANUAL"
            }
          }
        }
      },
      "package": "COMFORT_TEST",
      "product": "Gas Fix B2C (TC1)",
      "metaData": [
        [
          {
            "label": "EAN",
            "value": "541445216589141400"
          },
          {
            "label": "Contract",
            "value": "1525",
            "action": {
              "id": "navigate_to_gf_contract_view_cockpit",
              "recordId": "68fbacb3-bbe1-0179-f3e2-5a290bdbe33c"
            }
          }
        ],
        [
          {
            "label": "EAV",
            "value": " kWh"
          }
        ]
      ],
      "offers": [
        {
          "id": "6c49077f-4156-0b3a-e1b4-5a13dd6ab8a8-e2e39e6c-7ad8-aac8-ce01-58977a42c0f2-e95757ac-bb67-7d88-365e-5a1bddec5ede",
          "productAction": {
            "id": "modal_best_offer_offer_details",
            "recordId": "9fa11a79-b2da-7934-b09e-5a207587840e",
            "recordType": "PACK_TariffSheetPrices",
            "params": {
              "packageId": "6c49077f-4156-0b3a-e1b4-5a13dd6ab8a8",
              "productId": "e2e39e6c-7ad8-aac8-ce01-58977a42c0f2",
              "tariffsheetId": "e95757ac-bb67-7d88-365e-5a1bddec5ede",
              "tariffSheetPriceId": "9fa11a79-b2da-7934-b09e-5a207587840e",
              "contractLineId": "b222eeb9-04e8-8890-beff-5a290b130daf",
              "accountType": "B2C"
            }
          },
          "package": "TC_ONLINE_B2C",
          "packageAction": null,
          "packageId": "6c49077f-4156-0b3a-e1b4-5a13dd6ab8a8",
          "product": "Gas Floating B2C (TC1)",
          "productId": "e2e39e6c-7ad8-aac8-ce01-58977a42c0f2",
          "pricePerYear": 10,
          "savings": 0,
          "tariffSheetId": "e95757ac-bb67-7d88-365e-5a1bddec5ede",
          "action": {
            "command": "navigate",
            "arguments": {
              "linkTo": "guidance-mode",
              "params": {
                "flowId": "product_change_tc1_revised",
                "recordId": "68fbacb3-bbe1-0179-f3e2-5a290bdbe33c",
                "recordType": "AOS_Contracts",
                "model": {
                  "aos_products_quotes|package_id": "6c49077f-4156-0b3a-e1b4-5a13dd6ab8a8",
                  "aos_products_quotes|tariffsheet_id": "e95757ac-bb67-7d88-365e-5a1bddec5ede",
                  "product_change_reason": "RETENTION",
                  "aos_quotes_aos_contracts_1|id": "68fbacb3-bbe1-0179-f3e2-5a290bdbe33c",
                  "dwp|available_product_ids": [
                    "e2e39e6c-7ad8-aac8-ce01-58977a42c0f2"
                  ],
                  "aos_products_quotes": {
                    "e2e39e6c-7ad8-aac8-ce01-58977a42c0f2": {
                      "product_id": "e2e39e6c-7ad8-aac8-ce01-58977a42c0f2",
                      "package_id": "6c49077f-4156-0b3a-e1b4-5a13dd6ab8a8",
                      "prev_contract_line_id": "b222eeb9-04e8-8890-beff-5a290b130daf",
                      "tariffsheet_id": "e95757ac-bb67-7d88-365e-5a1bddec5ede"
                    }
                  }
                }
              }
            }
          },
          "packageProperties": [
            "pack-prop                           "
          ],
          "packageProductProperties": [
            "1 year"
          ]
        },
        {
          "id": "5b72e0f5-2f9b-1fb7-8043-5a1bdf25c494-a6565bd4-e0ec-dee9-64fd-58aca11b3994-e95757ac-bb67-7d88-365e-5a1bddec5ede",
          "productAction": {
            "id": "modal_best_offer_offer_details",
            "recordId": "2b9921d0-8a34-9fcd-0d2b-5a2075455a3e",
            "recordType": "PACK_TariffSheetPrices",
            "params": {
              "packageId": "5b72e0f5-2f9b-1fb7-8043-5a1bdf25c494",
              "productId": "a6565bd4-e0ec-dee9-64fd-58aca11b3994",
              "tariffsheetId": "e95757ac-bb67-7d88-365e-5a1bddec5ede",
              "tariffSheetPriceId": "2b9921d0-8a34-9fcd-0d2b-5a2075455a3e",
              "contractLineId": "b222eeb9-04e8-8890-beff-5a290b130daf",
              "accountType": "B2C"
            }
          },
          "package": "TC_ZDAL_B2C",
          "packageAction": null,
          "packageId": "5b72e0f5-2f9b-1fb7-8043-5a1bdf25c494",
          "product": "Gas Fix B2C (TC1)",
          "productId": "a6565bd4-e0ec-dee9-64fd-58aca11b3994",
          "pricePerYear": 10,
          "savings": 0,
          "tariffSheetId": "e95757ac-bb67-7d88-365e-5a1bddec5ede",
          "action": {
            "command": "navigate",
            "arguments": {
              "linkTo": "guidance-mode",
              "params": {
                "flowId": "product_change_tc1_revised",
                "recordId": "68fbacb3-bbe1-0179-f3e2-5a290bdbe33c",
                "recordType": "AOS_Contracts",
                "model": {
                  "aos_products_quotes|package_id": "5b72e0f5-2f9b-1fb7-8043-5a1bdf25c494",
                  "aos_products_quotes|tariffsheet_id": "e95757ac-bb67-7d88-365e-5a1bddec5ede",
                  "product_change_reason": "RETENTION",
                  "aos_quotes_aos_contracts_1|id": "68fbacb3-bbe1-0179-f3e2-5a290bdbe33c",
                  "dwp|available_product_ids": [
                    "a6565bd4-e0ec-dee9-64fd-58aca11b3994"
                  ],
                  "aos_products_quotes": {
                    "a6565bd4-e0ec-dee9-64fd-58aca11b3994": {
                      "product_id": "a6565bd4-e0ec-dee9-64fd-58aca11b3994",
                      "package_id": "5b72e0f5-2f9b-1fb7-8043-5a1bdf25c494",
                      "prev_contract_line_id": "b222eeb9-04e8-8890-beff-5a290b130daf",
                      "tariffsheet_id": "e95757ac-bb67-7d88-365e-5a1bddec5ede"
                    }
                  }
                }
              }
            }
          },
          "packageProperties": null,
          "packageProductProperties": [
            "3 years"
          ]
        }
      ],
      "discounts": [
        {
          "id": "89e23eef-65eb-d7b7-4f74-5a44f68282d4",
          "name": "G-RetDF30E",
          "rates": [
            "-30 Year 0-12"
          ],
          "action": {
            "id": "modal_to_add_discounts_on_contract_lines",
            "params": {
              "model": {
                "dwp|discounts_on_contract_lines": {
                  "b222eeb9-04e8-8890-beff-5a290b130daf::89e23eef-65eb-d7b7-4f74-5a44f68282d4": {
                    "contractline_id": "b222eeb9-04e8-8890-beff-5a290b130daf",
                    "discount_id": "89e23eef-65eb-d7b7-4f74-5a44f68282d4",
                    "key": "b222eeb9-04e8-8890-beff-5a290b130daf::89e23eef-65eb-d7b7-4f74-5a44f68282d4",
                    "label": "Contract line 541445216589141400 (GAS) - status: ACTIVE ( from 2017-12-01 to 2018-11-30 ) - Discount G-RetDF30E"
                  }
                }
              }
            }
          }
        },
        {
          "id": "c2dbef07-a7f7-293e-689c-5a44f5205a63",
          "name": "G-RetDTF10-10-10P",
          "rates": [
            "-10 Percentage 0-12",
            "-10 Percentage 12-24",
            "-10 Percentage 24-36"
          ],
          "action": {
            "id": "modal_to_add_discounts_on_contract_lines",
            "params": {
              "model": {
                "dwp|discounts_on_contract_lines": {
                  "b222eeb9-04e8-8890-beff-5a290b130daf::c2dbef07-a7f7-293e-689c-5a44f5205a63": {
                    "contractline_id": "b222eeb9-04e8-8890-beff-5a290b130daf",
                    "discount_id": "c2dbef07-a7f7-293e-689c-5a44f5205a63",
                    "key": "b222eeb9-04e8-8890-beff-5a290b130daf::c2dbef07-a7f7-293e-689c-5a44f5205a63",
                    "label": "Contract line 541445216589141400 (GAS) - status: ACTIVE ( from 2017-12-01 to 2018-11-30 ) - Discount G-RetDTF10-10-10P"
                  }
                }
              }
            }
          }
        }
      ],
      "pricePerYear": 10
    };

    addressData = {
      "address": "Stadsvest 1831573012 WILSELE"
    };

    if (elec) {
      addressData.elecProduct = elecProduct;
    }

    if (gas) {
      addressData.gasProduct = gasProduct;
    }

    scope.address = addressData;

    element = angular.element(template);
    element = $compile(element)(scope);

    $rootScope.$apply();
  }

  it('should render correct the title and metadata', function () {
    compile();

    let productsDiv = element.find('.col-1-1');
    expect(productsDiv.length).toEqual(2);

    let elecProd = $(productsDiv[0]);
    let gasProd = $(productsDiv[1]);

    //translation base on product type
    expect(elecProd.find('h2.blue').text()).toContain('Electricity');
    expect(gasProd.find('h2.blue').text()).toContain('Gas');

    let metaDataCols = elecProd.find('.col-1-2');
    expect(metaDataCols.length).toEqual(2);

    let leftCols = $(metaDataCols[0]).find('.input');
    let rightCols = $(metaDataCols[1]).find('.input');

    expect(leftCols.length).toEqual(2);
    expect(rightCols.length).toEqual(3);

    expect($(leftCols[0]).find('label').text()).toContain('EAN');
    expect($(leftCols[0]).find('strong').text()).toContain(addressData.elecProduct.ean);
    expect($(leftCols[0]).find('a').length).toBe(0);

    expect($(leftCols[1]).find('label').text()).toContain(addressData.elecProduct.metaData[0][1].label);
    expect($(leftCols[1]).find('a').length).toBe(1);
    expect($(leftCols[1]).find('div').length).toBe(0);
    expect($(leftCols[1]).find('a').text()).toContain(addressData.elecProduct.metaData[0][1].value);

    expect(actionDatasource.performAndHandle).not.toHaveBeenCalled();
    $(leftCols[1]).find('a').click();
    expect(actionDatasource.performAndHandle).toHaveBeenCalledTimes(1);
    expect(actionDatasource.performAndHandle).toHaveBeenCalledWith(addressData.elecProduct.metaData[0][1].action);

    expect($(rightCols[0]).find('label').text()).toContain(addressData.elecProduct.metaData[1][0].label);
    expect($(rightCols[0]).find('strong').text()).toContain(addressData.elecProduct.metaData[1][0].value);
    expect($(rightCols[0]).find('a').length).toBe(0);

    expect($(rightCols[1]).find('label').text()).toContain(addressData.elecProduct.metaData[1][1].label);
    expect($(rightCols[1]).find('strong').text()).toContain(addressData.elecProduct.metaData[1][1].value);
    expect($(rightCols[1]).find('a').length).toBe(0);

    expect($(rightCols[2]).find('label').text()).toContain(addressData.elecProduct.metaData[1][2].label);
    expect($(rightCols[2]).find('strong').text()).toContain(addressData.elecProduct.metaData[1][2].value);
    expect($(rightCols[2]).find('a').length).toBe(0);
  });

  it('should render correct the list', function () {
    compile();

    let productsDiv = element.find('.col-1-1');
    let elecProd = $(productsDiv[0]);
    let table = $(elecProd.find('table')[0]);
    let trs = table.find('tr');

    expect(trs.length).toEqual(12);
    /*

    +-------+--------------------------------------+--------------------------------+-------------------------------+---------------+
    | tr0   | Current product                                                                                                       |
    +-------+--------------------------------------+--------------------------------+-------------------------------+---------------+
    | tr1   | PACKAGE                              | PRODUCT                        | PRICE PER YEA                                 |
    +-------+--------------------------------------+--------------------------------+-------------------------------+---------------+
    | tr2   | COMFORT_TEST                         | Electricity Fix B2C (TC1)	    | 1581 € / year (incl. VAT)                     |
    +-------+--------------------------------------+--------------------------------+-------------------------------+---------------+
    | tr3   | OFFERS (2)                                                                                                            |
    +-------+--------------------------------------+--------------------------------+-------------------------------+---------------+
    | tr4   | PACKAGE                              | PRODUCT                        | PRICE PER YEAR | SAVINGS      |               |
    +-------+--------------------------------------+--------------------------------+-------------------------------+---------------+
    | tr5   | TC_ONLINE_B2C                        | Electricity Floating B2C (TC1) | 1000 € / year  |	100 € / year| Not selected  |
    |       | pack-prop                            | 1 year                         |  (excl. VAT)   |  (excl. VAT) |               |
    +-------+--------------------------------------+--------------------------------+-------------------------------+---------------+
    | tr6   | TC_ZDAL_B2C                          | Electricity Fix B2C (TC1)      | 2000 € / year  | -20 € / year | Not selected  |
    |       |                                      | Green                          |  (excl. VAT)   |  (excl. VAT) |               |
    |       |                                      | 3 year                         |                |              |               |
    +-------+--------------------------------------+--------------------------------+-------------------------------+---------------+
    | tr7   | Other product change                                                                                                  |
    +-------+--------------------------------------+--------------------------------+-------------------------------+---------------+
    | tr8   | DISCOUNTS (2)                                                                                                         |
    +-------+--------------------------------------+--------------------------------+-------------------------------+---------------+
    | tr9   | DISCOUNT CODE                                                         | 	DISCOUNT                                    |
    +-------+--------------------------------------+--------------------------------+-------------------------------+---------------+
    | tr10  | E-RetDF30E                                                            | -30 Year 0-12                 | Not selected  |
    +-------+--------------------------------------+--------------------------------+-------------------------------+---------------+
    | tr11  | E-RetDTF10-10-10P                                                     | -10 Percentage 0-12           | Not selected  |
    |       |                 	                                                    | -10 Percentage 12-24          |               |
    |       |                 	                                                    | -10 Percentage 24-36          |               |
    +-------+--------------------------------------+--------------------------------+-------------------------------+---------------+
    */

    //TR 0
    expect($(trs[0]).find('td').length).toEqual(1);
    expect($(trs[0]).find('td').text()).toContain('Current product');

    //TR 1
    expect($(trs[1]).find('td').length).toEqual(0);
    expect($(trs[1]).find('th').length).toEqual(3);
    expect($($(trs[1]).find('th')[0]).text()).toContain('Package');
    expect($($(trs[1]).find('th')[1]).text()).toContain('Product');
    expect($($(trs[1]).find('th')[2]).text()).toContain('Price per year');

    //TR 2
    expect($(trs[2]).find('td').length).toEqual(3);

    const packageLink = $($($(trs[2]).find('td')[0]).find('a')[0]);
    expect(packageLink.text()).toContain('COMFORT_TEST');

    expect(actionDatasource.performAndHandle).not.toHaveBeenCalled();
    packageLink.click();
    expect(actionDatasource.performAndHandle).toHaveBeenCalledTimes(1);
    expect(actionDatasource.performAndHandle).toHaveBeenCalledWith({
      id: 'KEY_MODAL_BEST_OFFER_PACKAGE_DETAILS',
      recordId: 1234
    });

    const productLink = $($($(trs[2]).find('td')[1]).find('a')[0]);
    expect(productLink.text()).toContain('Electricity Fix B2C (TC1)');

    productLink.click();
    expect(actionDatasource.performAndHandle).toHaveBeenCalledTimes(2);
    expect(actionDatasource.performAndHandle).toHaveBeenCalledWith(addressData.elecProduct.packageAction);

    expect($($(trs[2]).find('td')[2]).text()).toContain('1581.00');
    expect($($(trs[2]).find('td')[2]).text()).toContain('€ / year');
    expect($($(trs[2]).find('td')[2]).text()).toContain('(incl. VAT)');

    //TR 3
    expect($(trs[3]).find('td').text()).toContain('offers');
    expect($(trs[3]).find('td').text()).toContain('(2)');

    //TR 4
    expect($(trs[4]).find('td').length).toEqual(0);
    let tr4ths = $(trs[4]).find('th');
    expect(tr4ths.length).toEqual(5);
    expect($(tr4ths[0]).text()).toContain('Package');
    expect($(tr4ths[1]).text()).toContain('Product');
    expect($(tr4ths[2]).text()).toContain('Price per year');
    expect($(tr4ths[3]).text()).toContain('Savings');
    expect($(tr4ths[4]).text()).toEqual('');

    //TR 5
    let tr5tds = $(trs[5]).find('td');
    expect(tr5tds.length).toEqual(5);

    expect($(tr5tds[0]).text()).toContain('TC_ONLINE_B2C');
    expect($(tr5tds[0]).text()).toContain('pack-prop');

    const offerProductLink = $($(tr5tds[1]).find('a')[0]);
    offerProductLink.click();
    expect(offerProductLink.text()).toContain('Electricity Floating B2C (TC1)');
    expect(actionDatasource.performAndHandle).toHaveBeenCalledTimes(3);
    expect(actionDatasource.performAndHandle).toHaveBeenCalledWith(addressData.elecProduct.offers[0].productAction);

    expect($(tr5tds[2]).text()).toContain('1000.00');
    expect($(tr5tds[2]).text()).toContain('€ / year');
    expect($(tr5tds[2]).text()).toContain('(excl. VAT)');
    expect($(tr5tds[3]).text()).toContain('100.00');
    expect($(tr5tds[3]).text()).toContain('€ / year');
    expect($(tr5tds[3]).text()).toContain('(excl. VAT)');
    expect($(tr5tds[3]).hasClass('bdt-green-text')).toBe(true);
    expect($(tr5tds[3]).hasClass('bdt-pink-text')).toBe(false);
    expect($(tr5tds[4]).text()).toContain('Not selected');

    //TR6 - same as TR5 (check only the class for savings)
    let tr6tds = $(trs[6]).find('td');
    expect($(tr6tds[3]).hasClass('bdt-green-text')).toBe(false);
    expect($(tr6tds[3]).hasClass('bdt-pink-text')).toBe(true);

    //TR 7
    expect($(trs[7]).find('td').length).toEqual(1);
    const otherProductChangeLink = $($(trs[7]).find('a')[0]);
    expect(otherProductChangeLink.text()).toContain('Other product change');

    expect(commandHandler.handle).not.toHaveBeenCalled();
    otherProductChangeLink.click();
    expect(actionDatasource.performAndHandle).toHaveBeenCalledTimes(3);
    expect(commandHandler.handle).toHaveBeenCalledTimes(1);
    expect(commandHandler.handle).toHaveBeenCalledWith(addressData.elecProduct.otherProductChangeAction);

    //TR 8
    expect($(trs[8]).find('td').text()).toContain('Discounts');
    expect($(trs[8]).find('td').text()).toContain('(2)');

    //TR 9
    expect($(trs[9]).find('td').length).toEqual(0);
    let tr9ths = $(trs[9]).find('th');
    expect(tr9ths.length).toEqual(2);
    expect($(tr9ths[0]).text()).toContain('Discount code');
    expect($(tr9ths[1]).text()).toContain('Discount');

    //TR 10
    let tr10tds = $(trs[10]).find('td');
    expect(tr10tds.length).toEqual(3);
    expect($(tr10tds[0]).text()).toContain('E-RetDF30E');
    expect($(tr10tds[1]).text()).toContain('-30 Year 0-12');
    expect($(tr10tds[2]).text()).toContain('Not selected');
  });

  it('should disable other offers/discounts correct and trigger the correct action', function () {
    compile();

    let checkboxes = element.find("input");
    expect(checkboxes.length).toEqual(8);

    let elecOfferTcOnline = $(checkboxes[0]);
    let elecOfferTcZdal = $(checkboxes[1]);
    let elecDiscountDF30 = $(checkboxes[2]);
    let elecDiscountDTF10 = $(checkboxes[3]);
    let gasOfferTcOnline = $(checkboxes[4]);
    let gasOfferTcZdal = $(checkboxes[5]);
    let gasDiscountDF30 = $(checkboxes[6]);
    let gasDiscountDTF10 = $(checkboxes[7]);

    elecOfferTcOnline.click(); // select first offer from elec

    expect(elecOfferTcOnline.prop('disabled')).toBe(false);
    expect(elecOfferTcZdal.prop('disabled')).toBe(false); //we can still select the other elec offer
    expect(elecDiscountDF30.prop('disabled')).toBe(true);
    expect(elecDiscountDTF10.prop('disabled')).toBe(true);
    expect(gasOfferTcOnline.prop('disabled')).toBe(false); // we can select the gas offer from same package
    expect(gasOfferTcZdal.prop('disabled')).toBe(true);
    expect(gasDiscountDF30.prop('disabled')).toBe(true);
    expect(gasDiscountDTF10.prop('disabled')).toBe(true);

    expect(elecOfferTcOnline.prop('checked')).toBe(true);

    elecOfferTcZdal.click(); // select the other offer from elec

    expect(elecOfferTcOnline.prop('disabled')).toBe(false); //we can still select the first elec offer
    expect(elecOfferTcZdal.prop('disabled')).toBe(false);
    expect(elecDiscountDF30.prop('disabled')).toBe(true);
    expect(elecDiscountDTF10.prop('disabled')).toBe(true);
    expect(gasOfferTcOnline.prop('disabled')).toBe(true);
    expect(gasOfferTcZdal.prop('disabled')).toBe(false); // we can select the gas offer from same package
    expect(gasDiscountDF30.prop('disabled')).toBe(true);
    expect(gasDiscountDTF10.prop('disabled')).toBe(true);

    elecOfferTcZdal.click(); // deselect offers

    expect(elecOfferTcOnline.prop('disabled')).toBe(false);
    expect(elecOfferTcZdal.prop('disabled')).toBe(false);
    expect(elecDiscountDF30.prop('disabled')).toBe(false);
    expect(elecDiscountDTF10.prop('disabled')).toBe(false);
    expect(gasOfferTcOnline.prop('disabled')).toBe(false);
    expect(gasOfferTcZdal.prop('disabled')).toBe(false);
    expect(gasDiscountDF30.prop('disabled')).toBe(false);
    expect(gasDiscountDTF10.prop('disabled')).toBe(false);

    elecDiscountDF30.click(); //select one discount

    expect(elecOfferTcOnline.prop('disabled')).toBe(true);
    expect(elecOfferTcZdal.prop('disabled')).toBe(true);
    expect(elecDiscountDF30.prop('disabled')).toBe(false); //we cans still select discounts
    expect(elecDiscountDTF10.prop('disabled')).toBe(false); //we cans still select discounts
    expect(gasOfferTcOnline.prop('disabled')).toBe(true);
    expect(gasOfferTcZdal.prop('disabled')).toBe(true);
    expect(gasDiscountDF30.prop('disabled')).toBe(false); //we cans still select discounts
    expect(gasDiscountDTF10.prop('disabled')).toBe(false); //we cans still select discounts

    elecDiscountDTF10.click(); //select the other discount from elec
    gasDiscountDF30.click(); //select a discount from gas

    expect(actionDatasource.performAndHandle).not.toHaveBeenCalled();

    let chooseDiscountButton = $(element.find('button')[0]);
    expect(chooseDiscountButton.text()).toContain('Choose discounts');
    chooseDiscountButton.click();

    expect(actionDatasource.performAndHandle).toHaveBeenCalledTimes(1);
    expect(actionDatasource.performAndHandle).toHaveBeenCalledWith({
      "id": "modal_to_add_discounts_on_contract_lines",
      "params": {
        "model": {
          "dwp|discounts_on_contract_lines": {
            "93a0e756-6531-85a8-fd26-5a290bbf0fac::cf32b6fe-b38a-d023-5bad-5a2860b8cd12": {
              "contractline_id": "93a0e756-6531-85a8-fd26-5a290bbf0fac",
              "discount_id": "cf32b6fe-b38a-d023-5bad-5a2860b8cd12",
              "key": "93a0e756-6531-85a8-fd26-5a290bbf0fac::cf32b6fe-b38a-d023-5bad-5a2860b8cd12",
              "label": "Contract line 541444923109042406 (ELEC) - status: ACTIVE ( from 2017-12-01 to 2018-11-30 ) - Discount E-RetDTF10-10-10P"
            },
            "b222eeb9-04e8-8890-beff-5a290b130daf::89e23eef-65eb-d7b7-4f74-5a44f68282d4": {
              "contractline_id": "b222eeb9-04e8-8890-beff-5a290b130daf",
              "discount_id": "89e23eef-65eb-d7b7-4f74-5a44f68282d4",
              "key": "b222eeb9-04e8-8890-beff-5a290b130daf::89e23eef-65eb-d7b7-4f74-5a44f68282d4",
              "label": "Contract line 541445216589141400 (GAS) - status: ACTIVE ( from 2017-12-01 to 2018-11-30 ) - Discount G-RetDF30E"
            }
          }
        }
      }
    });

    //deselect the discounts
    elecDiscountDTF10.click();
    gasDiscountDF30.click();

    //select tow offers
    elecOfferTcOnline.click();
    gasOfferTcOnline.click();

    //try to select also a disabled offer -  hijack
    gasOfferTcZdal.prop('disabled', false);
    gasOfferTcZdal.click();

    gasDiscountDTF10.prop('disabled', false);
    gasDiscountDTF10.click();

    let chooseOffersButton = $(element.find('button')[0]);
    expect(chooseOffersButton.text()).toContain('Choose best offer');
    expect(commandHandler.handle).not.toHaveBeenCalled();
    chooseOffersButton.click();

    expect(actionDatasource.performAndHandle).toHaveBeenCalledTimes(1);
    expect(commandHandler.handle).toHaveBeenCalledTimes(1);
    expect(commandHandler.handle).toHaveBeenCalledWith({
      "command": "navigate",
      "arguments": {
        "linkTo": "guidance-mode",
        "params": {
          "flowId": "product_change_tc1_revised",
          "recordId": "68fbacb3-bbe1-0179-f3e2-5a290bdbe33c",
          "recordType": "AOS_Contracts",
          "model": {
            "aos_products_quotes|package_id": "6c49077f-4156-0b3a-e1b4-5a13dd6ab8a8",
            "aos_products_quotes|tariffsheet_id": "e95757ac-bb67-7d88-365e-5a1bddec5ede",
            "product_change_reason": "RETENTION",
            "aos_quotes_aos_contracts_1|id": "68fbacb3-bbe1-0179-f3e2-5a290bdbe33c",
            "dwp|available_product_ids": [
              "a5af0d33-bf89-844e-8359-58b809e4082c",
              "e2e39e6c-7ad8-aac8-ce01-58977a42c0f2"
            ],
            "aos_products_quotes": {
              "a5af0d33-bf89-844e-8359-58b809e4082c": {
                "product_id": "a5af0d33-bf89-844e-8359-58b809e4082c",
                "package_id": "6c49077f-4156-0b3a-e1b4-5a13dd6ab8a8",
                "prev_contract_line_id": "93a0e756-6531-85a8-fd26-5a290bbf0fac",
                "tariffsheet_id": "e95757ac-bb67-7d88-365e-5a1bddec5ede"
              },
              "e2e39e6c-7ad8-aac8-ce01-58977a42c0f2": {
                "product_id": "e2e39e6c-7ad8-aac8-ce01-58977a42c0f2",
                "package_id": "6c49077f-4156-0b3a-e1b4-5a13dd6ab8a8",
                "prev_contract_line_id": "b222eeb9-04e8-8890-beff-5a290b130daf",
                "tariffsheet_id": "e95757ac-bb67-7d88-365e-5a1bddec5ede"
              }
            }
          }
        }
      }
    });
  });

  it('should render correct when has only elec', function () {
    compile(true, false);

    let productsDiv = element.find('.col-1-1');
    expect(productsDiv.length).toEqual(1);

    //translation base on product type
    expect(productsDiv.find('h2.blue').text()).toContain('Electricity');
  });

  it('should render correct when has only gas', function () {
    compile(false, true);

    let productsDiv = element.find('.col-1-1');
    expect(productsDiv.length).toEqual(1);

    //translation base on product type
    expect(productsDiv.find('h2.blue').text()).toContain('Gas');
  });
});
