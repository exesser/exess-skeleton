'use strict';

describe('Form element: tariff calculation', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let scope;
  let element;

  let $rootScope;
  let $compile;

  let guidanceFormObserver;
  let validationObserver;
  let suggestionsObserver;
  let PRICE_EVENT_MODEL_KEY;

  let guidanceModeBackendState;
  let elementIdGenerator;

  let lodashDebounce;

  // Keeps track of all the functions that went through the debounce.
  let debouncedFunctions;

  const template = '<formly-form model="model" fields="fields"/>';

  beforeEach(inject(function (_$rootScope_, _$compile_, $state, GuidanceFormObserver, ValidationObserver,
                              SuggestionsObserver, _guidanceModeBackendState_, DEBOUNCE_TIME_TARIFF_CALCULATION,
                              _elementIdGenerator_, _PRICE_EVENT_MODEL_KEY_) {
    $rootScope = _$rootScope_;
    $compile = _$compile_;
    guidanceModeBackendState = _guidanceModeBackendState_;
    elementIdGenerator = _elementIdGenerator_;
    PRICE_EVENT_MODEL_KEY = _PRICE_EVENT_MODEL_KEY_;

    mockHelpers.blockUIRouter($state);

    guidanceFormObserver = new GuidanceFormObserver();
    validationObserver = new ValidationObserver();
    suggestionsObserver = new SuggestionsObserver();

    spyOn(guidanceFormObserver, 'formValueChanged');
    spyOn(elementIdGenerator, 'generateId').and.returnValue('field-fake-id');
    spyOn(guidanceModeBackendState, 'setBackendIsBusy');
    spyOn(guidanceModeBackendState, 'getBackendIsBusy').and.returnValue(true);

    // Mock lodash debounce so we can test the key up events more easily.
    lodashDebounce = _.debounce;

    // Clear the debouncedFunctions
    debouncedFunctions = [];

    /*
     Mock the debounce so it immediately executes the function.
     Remember we are not testing 'lodash' it has plenty of tests itself.
     */
    _.debounce = function (fn, time) {
      debouncedFunctions.push(fn.name);

      expect(time).toBe(DEBOUNCE_TIME_TARIFF_CALCULATION);
      return fn;
    };

    scope = $rootScope.$new();
  }));

// Reset the debounce to its original lodash function.
  afterEach(function () {
    _.debounce = lodashDebounce;
  });

  function compile(model = {}, disabled = false) {
    const fields = [
      {
        id: "calculations",
        key: "calculations",
        type: "tariff-calculation",
        templateOptions: {
          disabled,
          hideButtonsConditions: {
            "ADD-YEAR": "model[\"companyName\"] === 'wky'",
            "CALCULATE": "model[\"companyName\"] !== 'wky'"
          }
        }
      }
    ];

    scope = $rootScope.$new();
    scope.model = model;
    scope.fields = fields;

    const guidanceFormObserverAccessorElement = mockHelpers.createGuidanceFormObserverAccessor({
      $compile,
      $rootScope,
      guidanceFormObserver,
      validationObserver,
      suggestionsObserver
    });

    element = angular.element(template);
    element = $compile(element)(scope);

    guidanceFormObserverAccessorElement.append(element);

    $rootScope.$apply();
  }

  it('should have the correct element id', function () {
    compile();

    expect(elementIdGenerator.generateId).toHaveBeenCalledTimes(1);
    expect(elementIdGenerator.generateId).toHaveBeenCalledWith('calculations', guidanceFormObserver);
    expect($(element.find("div")[1]).attr('id')).toBe('field-fake-id');
  });

  it('should hide a button if the backend condition is true', function () {
    compile({ companyName: "wky" });

    const buttons = element.find("button");
    const calculateButton = $(buttons[0]);
    const addYearButton = $(buttons[1]);
    const resetButton = $(buttons[2]);

    expect(calculateButton.hasClass('ng-hide')).toBe(false);
    expect(addYearButton.hasClass('ng-hide')).toBe(true);
    expect(resetButton.hasClass('ng-hide')).toBe(false);
  });

  it('should send a "calculate" request when the calculate link is clicked', function () {
    compile();
    expect(guidanceFormObserver.formValueChanged).not.toHaveBeenCalled();
    expect(guidanceModeBackendState.setBackendIsBusy).not.toHaveBeenCalled();

    const link = $(element.find("button")[0]);
    expect(link.text()).toBe('Calculate');
    link.click();

    expect(guidanceModeBackendState.setBackendIsBusy).toHaveBeenCalledTimes(1);
    expect(guidanceModeBackendState.setBackendIsBusy).toHaveBeenCalledWith(true);

    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(1);
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
      focus: 'calculations',
      value: undefined
    }, false);

    expect(_.get(scope.model, PRICE_EVENT_MODEL_KEY)).toEqual('CHANGE');
  });

  it('should send an "add year" request when the add year link is clicked', function () {
    expect(guidanceFormObserver.formValueChanged).not.toHaveBeenCalled();
    compile();

    const link = $(element.find("button")[1]);
    expect(link.text()).toBe('Add year');
    link.click();

    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(1);
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
      focus: 'calculations',
      value: undefined
    }, false);

    expect(_.get(scope.model, PRICE_EVENT_MODEL_KEY)).toEqual('ADD-YEAR');
  });

  it('should send a "reset" request when the reset link is clicked', function () {
    compile();

    expect(guidanceFormObserver.formValueChanged).not.toHaveBeenCalled();

    const link = $(element.find("button")[2]);
    expect(link.text()).toBe('Reset');
    link.click();

    $rootScope.$apply();

    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(1);
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
      focus: 'calculations',
      value: undefined
    }, false);

    expect(_.get(scope.model, PRICE_EVENT_MODEL_KEY)).toEqual('RESET');
  });

  it('should re-calculate when the input values have changed', function () {
    const model = {
      calculations: [
        [
          [
            {
              label: "High",
              uom: "MWH",
              value: "0.00000000",
              title: "MARGIN",
              key: "dyn_cadc3567-1303-effd-8c6a-567007d67618",
              disabled: false
            },
            {
              label: "Low",
              uom: "MWH",
              value: "0.00000000",
              title: "",
              key: "dyn_ea3c2954-42d5-d724-2119-567007516b9f",
              disabled: false
            },
            {
              label: "Fixed fee",
              uom: "Year",
              value: "0.00000000",
              title: "",
              key: "dyn_8c71daa1-9c1e-0182-b222-56700baf6795",
              disabled: false
            }
          ],
          [
            {
              title: 12,
              label: "High",
              uom: "MWH",
              value: 65.0326,
              disabled: true
            },
            {
              title: 12,
              label: "Low",
              uom: "MWH",
              value: 46.64915,
              disabled: true
            },
            {
              title: 12,
              label: "Fixed fee",
              uom: "Year",
              value: 69,
              disabled: true
            }
          ]
        ]
      ],
      company: 42
    };

    compile(model);

    $(element.find('input')[0]).val('4141').change();

    expect(angular.copy(scope.model.calculations)).toEqual([
      [
        [
          {
            label: "High",
            uom: "MWH",
            value: "4141",
            title: "MARGIN",
            key: "dyn_cadc3567-1303-effd-8c6a-567007d67618",
            disabled: false
          },
          {
            label: "Low",
            uom: "MWH",
            value: "0.00000000",
            title: "",
            key: "dyn_ea3c2954-42d5-d724-2119-567007516b9f",
            disabled: false
          },
          {
            label: "Fixed fee",
            uom: "Year",
            value: "0.00000000",
            title: "",
            key: "dyn_8c71daa1-9c1e-0182-b222-56700baf6795",
            disabled: false
          }
        ],
        [
          {
            title: 12,
            label: "High",
            uom: "MWH",
            value: 65.0326,
            disabled: true
          },
          {
            title: 12,
            label: "Low",
            uom: "MWH",
            value: 46.64915,
            disabled: true
          },
          {
            title: 12,
            label: "Fixed fee",
            uom: "Year",
            value: 69,
            disabled: true
          }
        ]
      ]
    ]);
  });

  it('should redraw the table when the model changes externally', function () {
    const model = {
      calculations: []
    };

    compile(model);

    expect(element.find('input').length).toBe(0);

    model.calculations = [
      [
        [
          {
            "value": 0,
            "key": "dyn_cadc3567-1303-effd-8c6a-567007d67618",
            "disabled": false,
            "label": "High",
            "uom": "€/MWh",
            "title": "Margin"
          },
          {
            "value": 0,
            "key": "dyn_ea3c2954-42d5-d724-2119-567007516b9f",
            "disabled": false,
            "label": "Low",
            "uom": "€/MWh",
            "title": "Margin"
          },
          {
            "value": "0.00000000",
            "key": "dyn_8c71daa1-9c1e-0182-b222-56700baf6795",
            "disabled": false,
            "label": "Fixed Fee",
            "uom": "€/Year",
            "title": "Margin"
          }
        ],
        [
          {
            "value": 50.8638,
            "key": "dyn_8c71daa1-9c1e-0182-b222-56700baf6796",
            "disabled": false,
            "label": "High",
            "uom": "€/MWh",
            "title": "Array months"
          },
          {
            "value": 36.1238,
            "key": "dyn_8c71daa1-9c1e-0182-b222-56700baf679b",
            "disabled": false,
            "label": "Low",
            "uom": "€/MWh",
            "title": "Array months"
          },
          {
            "value": 69,
            "key": "dyn_8c71daa1-9c1e-0182-b222-56700baf679d",
            "disabled": false,
            "label": "Fixed Fee",
            "uom": "€/Year",
            "title": "Array months"
          }
        ]
      ]
    ];

    $rootScope.$apply();

    // There should be two <th>
    const thElements = element.find('th');
    expect(thElements.length).toBe(2);
    expect($(thElements[0]).text()).toBe('Margin');
    expect($(thElements[1]).text()).toBe('Array months');

    // There should be six <label>
    const labelElements = element.find('label');
    expect(labelElements.length).toBe(6);
    expect($(labelElements[0]).text()).toBe('High');
    expect($(labelElements[1]).text()).toBe('High');
    expect($(labelElements[2]).text()).toBe('Low');
    expect($(labelElements[3]).text()).toBe('Low');
    expect($(labelElements[4]).text()).toBe('Fixed Fee');
    expect($(labelElements[5]).text()).toBe('Fixed Fee');

    // There should be six <input> fields
    const inputElements = element.find('input');
    expect(inputElements.length).toBe(6);
    expect($(inputElements[0]).val()).toBe('0');
    expect($(inputElements[1]).val()).toBe('50.8638');
    expect($(inputElements[2]).val()).toBe('0');
    expect($(inputElements[3]).val()).toBe('36.1238');
    expect($(inputElements[4]).val()).toBe('0.00000000');
    expect($(inputElements[5]).val()).toBe('69');

    // There should be six <span>'s
    const spanElements = element.find('span.unit');
    expect(spanElements.length).toBe(6);
    expect($(spanElements[0]).text()).toBe(' €/MWh');
    expect($(spanElements[1]).text()).toBe(' €/MWh');
    expect($(spanElements[2]).text()).toBe(' €/MWh');
    expect($(spanElements[3]).text()).toBe(' €/MWh');
    expect($(spanElements[4]).text()).toBe(' €/Year');
    expect($(spanElements[5]).text()).toBe(' €/Year');

    // There are three buttons to click
    expect(element.find("div button").length).toBe(3);
  });

  it('should know how to render disabled rows', function () {
    const model = {
      calculations: []
    };

    compile(model);

    expect(element.find('input').length).toBe(0);

    model.calculations = [
      [
        [
          {
            "value": 0,
            "key": "dyn_cadc3567-1303-effd-8c6a-567007d67618",
            "disabled": true,
            "label": "High",
            "uom": "€/MWh",
            "title": "Margin"
          }
        ],
        [
          {
            "value": 50.8638,
            "key": "dyn_8c71daa1-9c1e-0182-b222-56700baf6796",
            "disabled": true,
            "label": "High",
            "uom": "€/MWh",
            "title": "Array months"
          }
        ]
      ]
    ];

    $rootScope.$apply();

    // There should be two <th>
    const thElements = element.find('th');
    expect(thElements.length).toBe(2);
    expect($(thElements[0]).text()).toBe('Margin');
    expect($(thElements[1]).text()).toBe('Array months');

    // There should be 2 <divs>
    const divElements = element.find('td > div');
    expect(divElements.length).toBe(2);
    expect($(divElements[0]).text()).toContain('High 0 €/MWh');
    expect($(divElements[1]).text()).toContain('High 50.8638 €/MWh');

    // There are three buttons to click
    expect(element.find("div button").length).toBe(3);
  });

  it('should make the entire table non-editable if the global disabled property is set to true', function () {
    // The global 'editable' property is set to false.
    compile({}, true);

    // There are no input fields
    expect(element.find('input').length).toBe(0);

    // There are no buttons to click
    expect(element.find("div button").length).toBe(0);
  });
});
