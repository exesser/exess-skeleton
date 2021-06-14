'use strict';

describe('Factory: guidanceModeDatasource', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(mockHelpers.logHeadersInterceptor);

  // instantiate service
  let guidanceModeDatasource;

  let API_URL;
  let LOG_HEADERS_KEYS;
  let $http;
  let $httpBackend;
  let $q;
  let modelSession;
  let replaceSpecialCharacters;

  beforeEach(inject(function (_guidanceModeDatasource_, _API_URL_, _LOG_HEADERS_KEYS_, _$http_, _$httpBackend_, _$q_,
                              _modelSession_, _replaceSpecialCharacters_) {
    guidanceModeDatasource = _guidanceModeDatasource_;
    API_URL = _API_URL_;
    LOG_HEADERS_KEYS = _LOG_HEADERS_KEYS_;
    $http = _$http_;
    $httpBackend = _$httpBackend_;
    $q = _$q_;
    modelSession = _modelSession_;
    replaceSpecialCharacters = _replaceSpecialCharacters_;

    spyOn(replaceSpecialCharacters, 'replaceArraySign').and.callThrough();
  }));

  describe('get function', function () {
    it('should send a correct request to retrieve a GuidanceMode', function () {
      const mockResponse = {
        "status": 200,
        "data": {
          "errors": {},
          "suggestions": {},
          "grid": {
            "columns": [{
              "size": "1-1",
              "cssClasses": [],
              "hasMargin": false,
              "rows": [{
                "size": "1-1",
                "grid": {
                  "columns": [{
                    "size": "1-2",
                    "hasMargin": false,
                    "rows": [{
                      "size": "1-3",
                      "type": "blockForm",
                      "cssClasses": [
                        "card",
                        "blue"
                      ],
                      "options": {
                        "formKey": "r0"
                      }
                    }, {
                      "size": "2-3",
                      "type": "titleContainingGrid",
                      "cssClasses": [
                        "card"
                      ],
                      "options": {
                        "formKey": "r1c1",
                        "defaultTitle": "Company details",
                        "titleExpression": "{%company_name_c%}"
                      }
                    }]
                  }, {
                    "size": "1-2",
                    "hasMargin": false,
                    "rows": [{
                      "size": "1-1",
                      "type": "blockTitleForm",
                      "cssClasses": [
                        "card"
                      ],
                      "options": {
                        "formKey": "r1c2",
                        "defaultTitle": "Contact person",
                        "titleExpression": "{%first_name%} {%last_name%}"
                      }
                    }]
                  }]
                }
              }]
            }]
          },
          "guidance": {
            "title": "Create lead",
            "loadingMessage": "Saving Lead"
          },
          "form": {
            "type_c": "DEFAULT",
            "id": "8b42beb4-5ab2-018f-bb1f-56a8afa918ba",
            "key_c": "ST1_LEAD_CREATE",
            "name": "Create new lead",
            "active": false,
            "canBeActivated": true,
            "disabled": false,
            "progressPercentage": 50,
            "r0": {
              "fields": [{
                "id": "status",
                "default": "OPEN",
                "type": "hidden"
              }, {
                "id": "company_name_c",
                "label": "Company name",
                "type": "LargeTextField",
                "module": "Leads",
                "moduleField": "company_name_c",
                "hasBorder": false
              }, {
                "id": "record_type",
                "default": "B2B",
                "type": "hidden"
              }, {
                "id": "active_lead_c",
                "default": "true",
                "type": "hidden"
              }, {
                "id": "accounts|opportunities|opportunity_status_c",
                "default": "Prospecting",
                "type": "hidden"
              }, {
                "id": "lead_source",
                "default": "manual entry",
                "type": "hidden"
              }, {
                "id": "dwp|returnModule",
                "default": "Leads",
                "type": "hidden"
              }, {
                "id": "contactPerson",
                "label": "Contact Person",
                "type": "InputFieldGroup",
                "hasBorder": false,
                "fields": [{
                  "id": "first_name",
                  "label": "First name",
                  "type": "resizing-input"
                }, {
                  "id": "last_name",
                  "label": "Last name",
                  "type": "resizing-input"
                }]
              }]
            }
          },
          "model": {
            "status": "OPEN",
            "company_name_c": null,
            "record_type": "B2B",
            "active_lead_c": "true",
            "accounts|opportunities|opportunity_status_c": "Prospecting",
            "lead_source": "manual entry",
            "dwp|returnModule": "Leads",
            "contactPerson": {
              "first_name": null,
              "last_name": null
            },
            "legal_form_c": null,
            "nace_code_c": null,
            "addresses_leads": {
              "address_type": "Lead Address",
              "address_street": null,
              "address_number": null,
              "address_addition": null,
              "address_bus": null,
              "address_postalcode": null,
              "address_city": null,
              "address_country": "BE"
            },
            "company_number_c": null,
            "function_c": null,
            "gender_c": null,
            "leads_contact_details|phone": null,
            "leads_contact_details|mobile": null,
            "leads_contact_details|email": null,
            "language_c": "NL",
            "future_contact_date_c": "",
            "baseModule": "Leads"
          },
          "progress": {
            "steps": [{
              "type_c": "DEFAULT",
              "id": "8b42beb4-5ab2-018f-bb1f-56a8afa918ba",
              "key_c": "ST1_LEAD_CREATE",
              "name": "Create new lead",
              "active": true,
              "canBeActivated": true,
              "disabled": false,
              "progressPercentage": 50,
              "valid": {
                "result": true,
                "errors": []
              }
            }]
          },
          "step": {
            "willSave": true,
            "done": null,
            "next": {
              "nextStep": null,
              "actionId": null,
              "recordId": null,
              "lastStep": null,
              "mainMenuKey": null,
              "dashId": null
            }
          }
        },
        "message": "Success"
      };

      $httpBackend.expectPOST(API_URL + 'Flow/Accounts/CQFA', { useFilters: true }, function (headers) {
        return headers[LOG_HEADERS_KEYS.DESCRIPTION] === 'Guidance: CQFA';
      }).respond(mockResponse);
      expect(replaceSpecialCharacters.replaceArraySign).not.toHaveBeenCalled();

      let promiseResolved = false;
      guidanceModeDatasource.get({
        recordType: "Accounts",
        flowId: "CQFA"
      }, { useFilters: true }).then(function (data) {
        expect(data).toEqual(mockResponse.data);
        promiseResolved = true;
      });

      expect(replaceSpecialCharacters.replaceArraySign).toHaveBeenCalledTimes(1);
      expect(replaceSpecialCharacters.replaceArraySign).toHaveBeenCalledWith({ useFilters: true }, false);
      $httpBackend.flush();

      expect(promiseResolved).toBe(true);
      $httpBackend.verifyNoOutstandingExpectation();
      $httpBackend.verifyNoOutstandingRequest();
      expect(replaceSpecialCharacters.replaceArraySign).toHaveBeenCalledTimes(2);
      expect(replaceSpecialCharacters.replaceArraySign).toHaveBeenCalledWith(mockResponse.data);
    });

    it('should send a correct request to retrieve a GuidanceMode when we pass a modelKey', function () {
      spyOn(modelSession, "getModel").and.returnValue({
        name: "Ken Block",
        number: "43"
      });
      const mockResponse = {
        "status": 200,
        "data": {
          "errors": {},
          "suggestions": {},
          "form": {},
          "model": {}
        },
        "message": "Success"
      };

      $httpBackend.expectPOST(API_URL + 'Flow/Accounts/CQFA', {
        useFilters: true,
        model: { name: "Ken Block", number: "43" }
      }, function (headers) {
        return headers[LOG_HEADERS_KEYS.DESCRIPTION] === 'Guidance: CQFA';
      }).respond(mockResponse);

      let promiseResolved = false;
      guidanceModeDatasource.get({
          recordType: "Accounts",
          flowId: "CQFA",
          modelKey: "ABCDE12345"
        }, { useFilters: true }
      ).then(function (data) {
        expect(data).toEqual(mockResponse.data);
        promiseResolved = true;
      });

      $httpBackend.flush();

      expect(modelSession.getModel).toHaveBeenCalledTimes(1);
      expect(modelSession.getModel).toHaveBeenCalledWith("ABCDE12345");

      expect(promiseResolved).toBe(true);
      $httpBackend.verifyNoOutstandingExpectation();
      $httpBackend.verifyNoOutstandingRequest();
    });

    it('should create a URL based on available parameters', function () {
      //We're not interesting in returning anything in this test, just checking the parameters.
      spyOn($http, 'post').and.returnValue($q.defer().promise);

      //Four filled in variables
      checkInputParametersCreateUrl(['a', 'b', 'c', 'd'], API_URL + "Flow/a/b/c/d");

      //Three filled in variables
      checkInputParametersCreateUrl(['a', '', 'c', 'd'], API_URL + "Flow/a/c/d");
      checkInputParametersCreateUrl(['a', 'b', '', 'd'], API_URL + "Flow/a/b/d");
      checkInputParametersCreateUrl(['a', 'b', 'c', ''], API_URL + "Flow/a/b/c");
      checkInputParametersCreateUrl(['', 'b', 'c', 'd'], API_URL + "Flow/b/c/d");

      //Two filled in variables
      checkInputParametersCreateUrl(['a', 'b', '', ''], API_URL + "Flow/a/b");
      checkInputParametersCreateUrl(['a', '', 'c', ''], API_URL + "Flow/a/c");
      checkInputParametersCreateUrl(['a', '', '', 'd'], API_URL + "Flow/a/d");
      checkInputParametersCreateUrl(['', 'b', 'c', ''], API_URL + "Flow/b/c");
      checkInputParametersCreateUrl(['', 'b', '', 'd'], API_URL + "Flow/b/d");
      checkInputParametersCreateUrl(['', '', 'c', 'd'], API_URL + "Flow/c/d");

      //One filled in variable
      checkInputParametersCreateUrl(['a', '', '', ''], API_URL + "Flow/a");
      checkInputParametersCreateUrl(['', 'b', '', ''], API_URL + "Flow/b");
      checkInputParametersCreateUrl(['', '', 'c', ''], API_URL + "Flow/c");
      checkInputParametersCreateUrl(['', '', '', 'd'], API_URL + "Flow/d");

      //No filled in variable
      checkInputParametersCreateUrl(['', '', '', ''], API_URL + "Flow");

      /*
       There are 2^4=16 options (4 variables that can either be empty
       or non-empty) so we should in total have that many calls.
       */
      expect($http.post).toHaveBeenCalledTimes(16);
    });

    /*
     Utility function to test that a given state of input parameters
     (delivered in an array for legibility) produce a given url.
     */
    function checkInputParametersCreateUrl(inputParameters, expectedUrl) {
      guidanceModeDatasource.get({
        recordType: inputParameters[0],
        flowId: inputParameters[1],
        recordId: inputParameters[2],
        flowAction: inputParameters[3]
      });
      expect($http.post.calls.mostRecent().args[0]).toBe(expectedUrl);
    }
  });

  describe('step function', function () {
    it('should send the correct request to step through a GuidanceMode', function () {
      const mockResponse = {
        "status": 201,
        "data": {
          "errors": {},
          "suggestions": {},
          "grid": {
            "columns": [{
              "size": "1-4",
              "hasMargin": false,
              "cssClasses": [
                "progressbar"
              ],
              "rows": [{
                "size": "1-1",
                "type": "progressBar",
                "options": {
                  "title": "Kitchen Sink"
                }
              }]
            }, {
              "size": "3-4",
              "cssClasses": [
                "guidance"
              ],
              "hasMargin": false,
              "rows": [{
                "size": "1-1",
                "type": "centeredGuidanceGrid",
                "options": {
                  "grid": {
                    "columns": [{
                      "size": "1-1",
                      "rows": [{
                        "size": "1-2",
                        "type": "titleContainingGrid",
                        "cssClasses": [
                          "card"
                        ],
                        "options": {
                          "titleExpression": "",
                          "grid": {
                            "cssClasses": [
                              "has-default-margins"
                            ],
                            "columns": [{
                              "size": "1-2",
                              "rows": [{
                                "size": "1-1",
                                "type": "basicFormlyForm",
                                "options": {
                                  "formKey": "r1c1"
                                }
                              }]
                            }, {
                              "size": "1-2",
                              "rows": [{
                                "size": "1-1",
                                "type": "basicFormlyForm",
                                "options": {
                                  "formKey": "r1c2"
                                }
                              }]
                            }]
                          }
                        }
                      }]
                    }]
                  }
                }
              }]
            }]
          },
          "guidance": {
            "title": "Create quote ",
            "loadingMessage": "Saving quote"
          },
          "form": {
            "type_c": "DEFAULT",
            "id": "dfedb225-30b6-877e-c87e-579b050fe905",
            "key_c": "CQFA_CONNECTION",
            "name": "Connection details",
            "active": false,
            "canBeActivated": true,
            "disabled": false,
            "progressPercentage": 50,
            "r1c1": {
              "fields": [{
                "id": "aos_products_quotes|product_type_c",
                "label": "Product type",
                "type": "LabelAndText",
                "fieldExpression": "{%aos_products_quotes_I_product_type_c%}",
                "default": ""
              }, {
                "id": "aos_products_quotes|meter_type_c",
                "label": "Meter type",
                "type": "LabelAndText",
                "fieldExpression": "{%aos_products_quotes_I_meter_type_c%}",
                "default": ""
              }, {
                "id": "aos_products_quotes|meter_configuration_c",
                "label": "Meter configuration",
                "type": "LabelAndText",
                "hideExpression": "model[\"aos_products_quotes|product_type_c\"] !== 'ELEC'",
                "fieldExpression": "{%aos_products_quotes_I_meter_configuration_c%}",
                "default": ""
              }, {
                "id": "aos_products_quotes|usage_single_c",
                "label": "Usage gas (kWh)",
                "type": "LabelAndText",
                "hideExpression": "model[\"aos_products_quotes|product_type_c\"] !== 'GAS'",
                "fieldExpression": "{%aos_products_quotes_I_usage_single_c%}",
                "default": ""
              }, {
                "id": "aos_products_quotes|usage_single_c",
                "label": "Usage total hours (kWh)",
                "type": "LabelAndText",
                "hideExpression": "model[\"aos_products_quotes|meter_configuration_c\"] !== 'SINGLE' || model[\"aos_products_quotes|meter_configuration_c\"] !== 'SINGLE_EXCL'",
                "fieldExpression": "{%aos_products_quotes_I_usage_single_c%}",
                "default": ""
              }, {
                "id": "aos_products_quotes|usage_high_c",
                "label": "Usage high (kWh)",
                "type": "LabelAndText",
                "hideExpression": "model[\"aos_products_quotes|meter_configuration_c\"] !== 'DOUBLE' || model[\"aos_products_quotes|meter_configuration_c\"] !== 'DOUBLE_EXCL'",
                "fieldExpression": "{%aos_products_quotes_I_usage_high_c%}",
                "default": ""
              }, {
                "id": "aos_products_quotes|usage_low_c",
                "label": "Usage low (kWh)",
                "type": "LabelAndText",
                "hideExpression": "model[\"oas_products_quotes|meter_configuration_c\"] !== 'DOUBLE' || model[\"oas_products_quotes|meter_configuration_c\"] !== 'DOUBLE_EXCL'",
                "fieldExpression": "{%aos_products_quotes_I_usage_low_c%}",
                "default": ""
              }, {
                "id": "aos_products_quotes|usage_exl_night_c",
                "label": "Usage excl night (kWh)",
                "type": "LabelAndText",
                "hideExpression": "model[\"oas_products_quotes|meter_configuration_c\"] !== 'DOUBLE_EXCL' || model[\"oas_products_quotes|meter_configuration_c\"] !== 'SINGLE_EXCL'",
                "fieldExpression": "{%aos_products_quotes_I_usage_exl_night_c%}",
                "default": ""
              }, {
                "id": "aos_products_quotes|kwMax_c",
                "label": "kW Max",
                "type": "LabelAndText",
                "hideExpression": "model[\"oas_products_quotes|meter_type_c\"] !== 'AMR'",
                "fieldExpression": "{%aos_products_quotes_I_kwMax_c%}",
                "default": ""
              }, {
                "id": "aos_products_quotes|reset_point_c",
                "label": "Reset point (!))",
                "default": "false",
                "type": "bool"
              }, {
                "id": "aos_products_quotes|market_mock_c",
                "label": "MM should respond? (!)",
                "default": "true",
                "type": "bool"
              }]
            },
            "cardList": [
              "r1c1",
              "r1c2"
            ],
            "r1c2": {
              "fields": [{
                "id": "aos_products_quotes|addresses_aos_products_quotes",
                "label": "Delivery address",
                "type": "address",
                "hasBorder": false,
                "fields": {
                  "type": {
                    "id": "address_type",
                    "default": "Delivery address"
                  },
                  "street": {
                    "id": "address_street",
                    "label": "Street",
                    "default": ""
                  },
                  "houseNumber": {
                    "id": "address_number",
                    "label": "Housenumber",
                    "default": ""
                  },
                  "addition": {
                    "id": "address_addition",
                    "label": "Addition",
                    "default": ""
                  },
                  "box": {
                    "id": "address_bus",
                    "label": "Box number",
                    "default": ""
                  },
                  "postalCode": {
                    "id": "address_postalcode",
                    "label": "Postalcode",
                    "default": ""
                  },
                  "city": {
                    "id": "address_city",
                    "label": "City",
                    "default": ""
                  },
                  "country": {
                    "id": "address_country",
                    "label": "Country",
                    "type": "enum",
                    "default": "BE",
                    "generateByServer": true,
                    "module": "Addresses",
                    "moduleField": "address_country"
                  }
                }
              }, {
                "id": "aos_products_quotes|ean_c",
                "label": "EAN",
                "type": "TextField"
              }, {
                "id": "aos_products_quotes|meter_no_c",
                "label": "Meter number",
                "type": "TextField"
              }, {
                "id": "aos_products_quotes|meter_open_c",
                "label": "Meter open?",
                "default": "true",
                "type": "toggle"
              }, {
                "id": "aos_products_quotes|move_in_c",
                "label": "Move of customer?",
                "default": "false",
                "type": "toggle",
                "hideExpression": "model[\"aos_products_quotes|meter_open_c\"] !== true"
              }, {
                "id": "aos_products_quotes|switchtype_c",
                "label": "Switch type",
                "type": "LabelAndText",
                "fieldExpression": "{%aos_products_quotes_I_switchtype_c%}"
              }]
            }
          },
          "model": {
            "number": "43"
          },
          "parentModel": {
            "name": "Ken Block",
            "country": "USA",
            "car": {
              "mark": "Ford",
              "model": "Focus RSRX"
            }
          },
          "progress": {
            "steps": [{
              "type_c": "DEFAULT",
              "id": "58c0593f-59c4-ee62-2cf3-579b04f8f308",
              "key_c": "CQFA_PRICE",
              "name": "Determine quote price",
              "active": false,
              "canBeActivated": true,
              "disabled": false,
              "progressPercentage": 50,
              "valid": {
                "result": true,
                "errors": []
              }
            }, {
              "type_c": "DEFAULT",
              "id": "dfedb225-30b6-877e-c87e-579b050fe905",
              "key_c": "CQFA_CONNECTION",
              "name": "Connection details",
              "active": true,
              "canBeActivated": true,
              "disabled": false,
              "progressPercentage": 50,
              "valid": {
                "result": true,
                "errors": []
              }
            }, {
              "type_c": "DEFAULT",
              "id": "43df03f5-5524-d161-b02c-579b057516a0",
              "key_c": "CQFA_BILLING",
              "name": "Billing details",
              "active": false,
              "canBeActivated": true,
              "disabled": false,
              "progressPercentage": 50,
              "valid": {
                "result": true,
                "errors": []
              }
            }]
          },
          "step": {
            "willSave": null,
            "done": null,
            "next": {
              "nextStep": null,
              "actionId": null,
              "recordId": null,
              "lastStep": null,
              "mainMenuKey": null,
              "dashId": null
            }
          }
        },
        "message": "Success"
      };

      const payload = {
        "model": {
          "number": ""
        },
        "parentModel": {
          "name": "Ken Block",
          "country": "",
          "car": {
            "mark": "Ford",
            "model": ""
          }
        }
      };

      const expected = angular.copy(mockResponse.data);
      _.unset(expected, 'parentModel.name'); //the name field is same as payload
      _.unset(expected, 'parentModel.car.mark'); //the mark field is same as payload

      $httpBackend.expectPOST(API_URL + 'Flow/CQFA/42', payload, function (headers) {
        return headers[LOG_HEADERS_KEYS.DESCRIPTION] === 'Guidance: CQFA | step | recordId: 42';
      }).respond(mockResponse);

      let promiseResolved = false;
      guidanceModeDatasource.step({ recordId: "42", flowId: "CQFA" }, payload).then(function (data) {
        expect(data).toEqual(expected);
        promiseResolved = true;
      });

      $httpBackend.flush();

      expect(promiseResolved).toBe(true);
      $httpBackend.verifyNoOutstandingExpectation();
      $httpBackend.verifyNoOutstandingRequest();
    });

    it('should create a URL based on available parameters', function () {
      //We're not interesting in returning anything in this test, just checking the parameters.
      spyOn($http, 'post').and.returnValue($q.defer().promise);

      //Two filled in variables
      checkInputParametersCreateUrl(['a', 'b'], API_URL + "Flow/a/b");

      //One filled in variable
      checkInputParametersCreateUrl(['a', ''], API_URL + "Flow/a");
      checkInputParametersCreateUrl(['', 'b'], API_URL + "Flow/b");

      //No filled in variable
      checkInputParametersCreateUrl(['', ''], API_URL + "Flow");

      //There are 2^2=4 options (2 variables that can either be empty or non-empty) so we should in total have that many calls.
      expect($http.post).toHaveBeenCalledTimes(4);
    });

    //Utility function to test that a given ste of input parameters (delivered in an array for legibility) produce a given url.
    function checkInputParametersCreateUrl(inputParameters, expectedUrl) {
      guidanceModeDatasource.step({
        flowId: inputParameters[0],
        recordId: inputParameters[1]
      }, {});
      expect($http.post.calls.mostRecent().args[0]).toBe(expectedUrl);
    }
  });
});
