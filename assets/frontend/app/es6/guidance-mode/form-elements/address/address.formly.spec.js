'use strict';

describe('Form element: address', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let API_URL;
  let scope;
  let element;
  let $rootScope;
  let $compile;
  let $httpBackend;

  let guidanceFormObserver;
  let validationObserver;
  let suggestionsObserver;

  let guidanceModeBackendState;
  let elementIdGenerator;
  let ACTION_EVENT;

  let street;
  let number;
  let box;
  let addition;
  let postalCode;
  let city;
  let country;
  let addressContainer;

  const template = '<formly-form form="form" model="model" fields="fields"/>';

  beforeEach(inject(function (_API_URL_, _$rootScope_, _$compile_, _$httpBackend_, $state, GuidanceFormObserver,
                              ValidationObserver, SuggestionsObserver, _guidanceModeBackendState_, _ACTION_EVENT_,
                              _elementIdGenerator_) {
    API_URL = _API_URL_;
    $rootScope = _$rootScope_;
    $compile = _$compile_;
    $httpBackend = _$httpBackend_;

    guidanceModeBackendState = _guidanceModeBackendState_;
    elementIdGenerator = _elementIdGenerator_;
    ACTION_EVENT = _ACTION_EVENT_;

    validationObserver = new ValidationObserver();
    suggestionsObserver = new SuggestionsObserver();
    guidanceFormObserver = new GuidanceFormObserver();

    spyOn(guidanceFormObserver, 'formValueChanged');
    spyOn(elementIdGenerator, 'generateId').and.callFake(function (id) {
      return id + "-fake-id";
    });

    mockHelpers.blockUIRouter($state);
  }));

  function compile(hasBorder = false, required = false, displayCountry = true) {
    scope = $rootScope.$new();

    scope.model = {
      fieldEnabled: true,
      headquarters: {
        address: {
          street: "",
          houseNumber: "",
          box: "",
          addition: "",
          postalCode: "",
          city: ""
        }
      }
    };

    scope.fields = [
      {
        id: 'headquarters.address',
        key: 'headquarters.address',
        type: 'address',
        templateOptions: {
          label: 'Give an address please',
          noBackendInteraction: false,
          disabled: false,
          hasBorder,
          required,
          readonly: false,
          fields: {
            "street": {
              "key": "street",
              "label": "Street",
              "type": "address_street",
              "display": true
            },
            "houseNumber": {
              "key": "houseNumber",
              "label": "Number",
              "type": "address_number",
              "display": true
            },
            "box": {
              "key": "box",
              "label": "Box",
              "type": "address_bus",
              "display": true
            },
            "addition": {
              "key": "addition",
              "label": "Addition",
              "type": "address_addition",
              "display": true
            },
            "postalCode": {
              "key": "postalCode",
              "label": "Postalcode",
              "type": "address_postalcode",
              "display": true
            },
            "city": {
              "key": "city",
              "label": "City",
              "type": "address_city",
              "display": true
            },
            "country": {
              "key": "country",
              "label": "Country",
              "type": "address_country",
              "display": displayCountry,
              "enumValues": [{ "name": "BE", "value": "BE" }, { "name": "NL", "value": "NL" }]
            }
          }
        }
      }
    ];

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

    const inputElements = element.find("input");
    street = $(inputElements[0]);
    number = $(inputElements[1]);
    addition = $(inputElements[2]);
    box = $(inputElements[3]);
    postalCode = $(inputElements[4]);
    city = $(inputElements[5]);
    country = $(element.find("select")[0]);
    addressContainer = $(element.find("div")[1]);
  }

  it('should populate its child elements with the values from the model and have the correct ids', function () {
    compile();

    expect(elementIdGenerator.generateId).toHaveBeenCalledWith('headquarters.address', guidanceFormObserver);
    expect(elementIdGenerator.generateId).toHaveBeenCalledWith('street', guidanceFormObserver);
    expect(elementIdGenerator.generateId).toHaveBeenCalledWith('houseNumber', guidanceFormObserver);
    expect(elementIdGenerator.generateId).toHaveBeenCalledWith('box', guidanceFormObserver);
    expect(elementIdGenerator.generateId).toHaveBeenCalledWith('addition', guidanceFormObserver);
    expect(elementIdGenerator.generateId).toHaveBeenCalledWith('postalCode', guidanceFormObserver);
    expect(elementIdGenerator.generateId).toHaveBeenCalledWith('city', guidanceFormObserver);
    expect(elementIdGenerator.generateId).toHaveBeenCalledWith('country', guidanceFormObserver);

    scope.model.headquarters.address = {
      street: 'Straatweg',
      houseNumber: '42',
      box: '12345',
      addition: 'A',
      postalCode: '2018',
      city: 'Antwerpen'
    };

    $rootScope.$apply();

    expect(street.val()).toBe('Straatweg');
    expect(number.val()).toBe('42');
    expect(box.val()).toBe('12345');
    expect(addition.val()).toBe('A');
    expect(postalCode.val()).toBe('2018');
    expect(city.val()).toBe('Antwerpen');

    expect(street.attr('id')).toBe('street-fake-id');
    expect(number.attr('id')).toBe('houseNumber-fake-id');
    expect(box.attr('id')).toBe('box-fake-id');
    expect(addition.attr('id')).toBe('addition-fake-id');
    expect(postalCode.attr('id')).toBe('postalCode-fake-id');
    expect(city.attr('id')).toBe('city-fake-id');
    expect(addressContainer.attr('id')).toBe('headquarters.address-fake-id-container');
  });

  it('should set the ngModel value when the fields change', function () {
    compile();

    $httpBackend.when('GET', API_URL + "Road65/Address?street=Koraalrood").respond([]);

    expect(street.attr('placeholder')).toBe('Street');
    expect(number.attr('placeholder')).toBe('Number');
    expect(box.attr('placeholder')).toBe('Box');
    expect(addition.attr('placeholder')).toBe('Addition');
    expect(postalCode.attr('placeholder')).toBe('Postalcode');
    expect(city.attr('placeholder')).toBe('City');

    street.val('Koraalrood').change();
    number.val('33').change();
    box.val('9128').change();
    addition.val('B').change();
    postalCode.val('2718SB').change();
    city.val('Zoetermeer').change();

    $rootScope.$apply();

    expect(scope.model.headquarters.address).toEqual({
      street: "Koraalrood",
      houseNumber: "33",
      box: "9128",
      addition: "B",
      postalCode: "2718SB",
      city: "Zoetermeer"
    });
  });

  it('should let the "guidanceFormObserver" know values have changed', function () {
    compile(false, false, true);

    expect(guidanceFormObserver.formValueChanged).not.toHaveBeenCalled();

    scope.model.headquarters.address = {
      street: "",
      houseNumber: "",
      box: "",
      addition: "",
      postalCode: "",
      city: "",
      country: ""
    };

    $rootScope.$apply();

    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(1);
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
      focus: 'headquarters.address',
      value: {
        street: "",
        houseNumber: "",
        box: "",
        addition: "",
        postalCode: "",
        city: "",
        country: ""
      }
    }, false);

    //Set the street name
    street.val('Koraalrood').change();
    $rootScope.$apply();
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(2);
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
      focus: 'headquarters.address',
      value: {
        street: "Koraalrood",
        houseNumber: "",
        box: "",
        addition: "",
        postalCode: "",
        city: "",
        country: ""
      }
    }, false);

    //Set the house number
    number.val('33').change();
    $rootScope.$apply();
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(3);
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
      focus: 'headquarters.address',
      value: {
        street: "Koraalrood",
        houseNumber: "33",
        box: "",
        addition: "",
        postalCode: "",
        city: "",
        country: ""
      }
    }, false);

    //Set the box number
    box.val('9128').change();
    $rootScope.$apply();
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(4);
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
      focus: 'headquarters.address',
      value: {
        street: "Koraalrood",
        houseNumber: "33",
        box: "9128",
        addition: "",
        postalCode: "",
        city: "",
        country: ""
      }
    }, false);

    //Set the addition
    addition.val('B').change();
    $rootScope.$apply();
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(5);
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
      focus: 'headquarters.address',
      value: {
        street: "Koraalrood",
        houseNumber: "33",
        box: "9128",
        addition: "B",
        postalCode: "",
        city: "",
        country: ""
      }
    }, false);

    //Set the postal code
    postalCode.val('2718SB').change();
    $rootScope.$apply();
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(6);
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
      focus: 'headquarters.address',
      value: {
        street: "Koraalrood",
        houseNumber: "33",
        box: "9128",
        addition: "B",
        postalCode: "2718SB",
        city: "",
        country: ""
      }
    }, false);

    //Set the city
    city.val('Zoetermeer').change();
    $rootScope.$apply();
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(7);
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
      focus: 'headquarters.address',
      value: {
        street: "Koraalrood",
        houseNumber: "33",
        box: "9128",
        addition: "B",
        postalCode: "2718SB",
        city: "Zoetermeer",
        country: ""
      }
    }, false);

    //Set the country
    const options = country.find("option");
    $(options[2]).prop('selected', true).change();

    $rootScope.$apply();
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(8);
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
      focus: 'headquarters.address',
      value: {
        street: "Koraalrood",
        houseNumber: "33",
        box: "9128",
        addition: "B",
        postalCode: "2718SB",
        city: "Zoetermeer",
        country: "NL"
      }
    }, false);
  });

  it('should let the "guidanceFormObserver" know that the values have changed when noBackendInteraction is true', function () {
    compile();

    expect(guidanceFormObserver.formValueChanged).not.toHaveBeenCalled();
    expect(street.val()).toBe("");

    scope.fields[0].templateOptions.noBackendInteraction = true;
    $rootScope.$apply();

    street.val('Koraalrood').change();
    $rootScope.$apply();

    expect(street.val()).toBe("Koraalrood");
    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(1);

    expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
      focus: 'headquarters.address',
      value: {
        street: "Koraalrood",
        houseNumber: "",
        box: "",
        addition: "",
        postalCode: "",
        city: ""
      }
    }, true);
  });

  describe('the validation functionality', function () {
    it('should set the field to erroneous if field errors occur', function () {
      compile();
      expect(scope.form.$valid).toBe(true);

      validationObserver.setErrors({
        "headquarters.address": ["That is not a correct address."]
      });
      $rootScope.$apply();
      expect(scope.form.$valid).toBe(false);
      expect(scope.form['headquarters.address'].$error.BACK_END_ERROR).toBe(true);
    });
  });

  describe('the suggestion functionality', function () {
    it('should present options and choose a value when you select a suggestion', function () {
      compile();

      suggestionsObserver.setSuggestions({
        "headquarters.address": [{
          label: "Veldkant 7",
          labelAddition: "Kontich",
          model: {
            headquarters: {
              address: {
                street: "Veldkant",
                houseNumber: "7",
                addition: "A",
                box: "12345",
                postalCode: "2550",
                city: "Kontich"
              }
            }
          }
        }]
      });
      $rootScope.$apply();

      // check if for-elements are set correctly
      const autocomplete = $(element.find('autocomplete'));
      const autoCompleteController = autocomplete.controller('autocomplete');
      expect(autoCompleteController.forElements).toEqual(['street-fake-id', 'houseNumber-fake-id', 'box-fake-id', 'addition-fake-id', 'postalCode-fake-id', 'city-fake-id', 'country-fake-id']);

      // Check if the left and right text are properly set
      expect(autocomplete.attr('suggestion-left-text-property')).toBe('label');
      expect(autocomplete.attr('suggestion-right-text-property')).toBe('labelAddition');

      const suggestions = autocomplete.find("ul li");
      expect(suggestions.length).toBe(1);

      //Choose the suggestion and check that the model has changed
      $(suggestions[0]).click();
      $rootScope.$apply();

      expect(scope.model.headquarters.address).toEqual({
        street: "Veldkant",
        houseNumber: "7",
        addition: "A",
        box: "12345",
        postalCode: "2550",
        city: "Kontich"
      });

      /*
       A $watch on 'addressFormElementController.ngModel.$viewValue',
       also triggers 'formValueChanged' changes when a suggestion is
       clicked.
       */
      expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(1);
      expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
        focus: 'headquarters.address',
        value: {
          street: "Veldkant",
          houseNumber: "7",
          addition: "A",
          box: "12345",
          postalCode: "2550",
          city: "Kontich"
        }
      }, false);
    });
  });

  describe('the border functionality', function () {
    it('should show a border when the hasBorder templateOption is true.', function () {
      compile(true);

      expect(element.find('.editable-group').length).toBe(0);
    });

    it('should not show a border when the hasBorder templateOption is false.', function () {
      compile(false);

      expect(element.find('.editable-group').length).toBe(1);
    });
  });

  describe("the 'disabled' functionality", function () {
    it('should made fields read-only if the read-only item is changed in the fieldlist', function () {
      compile();

      expect(number.prop('readonly')).toBe(false);
      expect(street.prop('readonly')).toBe(false);
      expect(number.prop('readonly')).toBe(false);
      expect(box.prop('readonly')).toBe(false);
      expect(addition.prop('readonly')).toBe(false);
      expect(postalCode.prop('readonly')).toBe(false);
      expect(city.prop('readonly')).toBe(false);

      scope.fields[0].templateOptions.disabled = true;
      $rootScope.$apply();

      expect(number.prop('readonly')).toBe(true);
      expect(street.prop('readonly')).toBe(true);
      expect(number.prop('readonly')).toBe(true);
      expect(box.prop('readonly')).toBe(true);
      expect(addition.prop('readonly')).toBe(true);
      expect(postalCode.prop('readonly')).toBe(true);
      expect(city.prop('readonly')).toBe(true);
    });

    it('should make the field disabled if the backend is busy', function () {
      spyOn(guidanceModeBackendState, 'getBackendIsBusy').and.returnValue(true);
      spyOn(guidanceModeBackendState, 'getPerformedAction').and.returnValue({
        event: ACTION_EVENT.CHANGED,
        focus: 'field'
      });
      compile();

      expect(number.prop('readonly')).toBe(true);
      expect(street.prop('readonly')).toBe(true);
      expect(number.prop('readonly')).toBe(true);
      expect(box.prop('readonly')).toBe(true);
      expect(addition.prop('readonly')).toBe(true);
      expect(postalCode.prop('readonly')).toBe(true);
      expect(city.prop('readonly')).toBe(true);
    });
  });

  describe("the 'readonly' functionality", function () {
    it('should made the field readonly if the templateOptions.readonly property evaluates to true', function () {
      compile(false, false, true);

      expect(element.find('resizing-input').length).toBe(6);
      expect(element.find('select').length).toBe(1);
      expect(element.find('strong').length).toBe(0);

      scope.model.headquarters.address = {
        street: "Veldkant",
        houseNumber: 7,
        addition: "A",
        box: "12345",
        postalCode: 2550,
        city: "Kontich",
        country: "BE"
      };
      scope.fields[0].templateOptions.readonly = true;

      $rootScope.$apply();

      expect(element.find('resizing-input').length).toBe(0);

      let strong = $(element.find('strong')[0]);
      expect(strong.attr('id')).toBe('headquarters.address-fake-id');
      expect(strong.text()).toContain('Veldkant 7 A 12345');
      expect(strong.text()).toContain('2550 Kontich BE');

      // Now leave some gaps in the address
      scope.model.headquarters.address = {
        street: "Veldkant",
        houseNumber: "7",
        addition: "",
        box: "",
        postalCode: "2550",
        city: "Kontich",
        country: ""
      };
      $rootScope.$apply();

      strong = $(element.find('strong')[0]);
      expect(strong.attr('id')).toBe('headquarters.address-fake-id');
      expect(strong.text()).toContain('Veldkant 7');
      expect(strong.text()).toContain('2550 Kontich');

      // Now invert the gaps
      scope.model.headquarters.address = {
        street: "",
        houseNumber: "",
        addition: "1324",
        box: "BA",
        postalCode: "",
        city: "",
        country: ""
      };
      $rootScope.$apply();

      strong = $(element.find('strong')[0]);
      expect(strong.attr('id')).toBe('headquarters.address-fake-id');
      expect(strong.text()).toContain('1324 BA');
    });
  });

  describe("the 'required' functionality", function () {
    it('should not set any errors when the field is not required', function () {
      compile();
      expect(scope.form.$valid).toBe(true);
    });

    it('should set errors when the field is required and the object is empty', function () {
      compile(false, true);
      expectAddressToBeInvalid();
    });

    it('should remove errors when the street, houseNumber, postalCode and city are set', function () {
      compile(false, true);

      scope.model.headquarters.address.street = 'Veldkant';
      $rootScope.$apply();
      expectAddressToBeInvalid();

      scope.model.headquarters.address.houseNumber = '7';
      $rootScope.$apply();
      expectAddressToBeInvalid();

      scope.model.headquarters.address.postalCode = '2550';
      $rootScope.$apply();
      expectAddressToBeInvalid();

      scope.model.headquarters.address.city = 'Kontich';
      $rootScope.$apply();
      expectAddressToBeInvalid();

      scope.model.headquarters.address.country = 'BE';
      $rootScope.$apply();
      expect(scope.form.$valid).toBe(true);
    });
  });

  it('should ignore the country when not displayed', function () {
    compile(false, true, false);

    expect(country.length).toBe(0);

    scope.model.headquarters.address.street = 'Veldkant';
    $rootScope.$apply();
    expectAddressToBeInvalid();

    scope.model.headquarters.address.houseNumber = '7';
    $rootScope.$apply();
    expectAddressToBeInvalid();

    scope.model.headquarters.address.postalCode = '2550';
    $rootScope.$apply();
    expectAddressToBeInvalid();

    scope.model.headquarters.address.city = 'Kontich';
    $rootScope.$apply();
    expect(scope.form.$valid).toBe(true);
  });

  describe("the 'reset' functionality", function () {
    it('should clear the fields and suggestion when the reset button is clicked', function () {
      compile();
      scope.model.headquarters.address = {
        street: 'Straatweg',
        houseNumber: '42',
        box: '12345',
        addition: 'A',
        postalCode: '2018',
        city: 'Antwerpen',
        address_type: "Lead address"
      };

      suggestionsObserver.setSuggestions({
        "headquarters.address": [{
          label: "Veldkant 7",
          labelAddition: "Kontich",
          model: {
            headquarters: {
              address: {
                street: "Veldkant",
                houseNumber: "7",
                addition: "A",
                box: "12345",
                postalCode: "2550",
                city: "Kontich"
              }
            }
          }
        }]
      });
      $rootScope.$apply();

      expect(street.val()).toBe('Straatweg');
      expect(number.val()).toBe('42');
      expect(box.val()).toBe('12345');
      expect(addition.val()).toBe('A');
      expect(postalCode.val()).toBe('2018');
      expect(city.val()).toBe('Antwerpen');
      expect(scope.model.headquarters.address.address_type).toBe('Lead address');

      // check if for-elements are set correctly
      const autocomplete = $(element.find('autocomplete'));
      let suggestions = autocomplete.find("ul li");
      expect(suggestions.length).toBe(1);

      const resetLink = $(element.find("a")[1]);
      expect(resetLink.text().trim()).toBe("reset");
      expect(resetLink.hasClass('ng-hide')).toBe(false);

      scope.fields[0].templateOptions.disabled = true;
      $rootScope.$apply();

      expect(resetLink.hasClass('ng-hide')).toBe(true);

      scope.fields[0].templateOptions.disabled = false;
      $rootScope.$apply();

      resetLink.click();

      expect(street.val()).toBe('');
      expect(number.val()).toBe('');
      expect(box.val()).toBe('');
      expect(addition.val()).toBe('');
      expect(postalCode.val()).toBe('');
      expect(city.val()).toBe('');
      expect(resetLink.hasClass('ng-hide')).toBe(true);

      //the fields that are not displayed should not be reseated
      expect(scope.model.headquarters.address.address_type).toBe('Lead address');

      suggestions = autocomplete.find("ul li");
      expect(suggestions.length).toBe(0);
    });
  });

  function expectAddressToBeInvalid() {
    expect(scope.form.$valid).toBe(false);
    expect(scope.form['headquarters.address'].$error.required).toBe(true);
  }
});
