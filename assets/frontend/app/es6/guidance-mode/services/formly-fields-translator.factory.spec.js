'use strict';

describe('Factory: formlyFieldsTranslatorFactory', function () {

  // load the controller's module
  beforeEach(module('digitalWorkplaceApp'));

  let formlyFieldsTranslator;
  let $log;

  // Initialize the controller and a mock scope
  beforeEach(inject(function (_formlyFieldsTranslator_, _$log_) {
    formlyFieldsTranslator = _formlyFieldsTranslator_;
    $log = _$log_;
  }));

  it('should translate varchars to input field definitions', function () {
    const input = [
      {
        id: "person_firstname",
        label: "First name",
        type: "varchar",
        uom: "uom",
        hideExpression: "model.showField",
        expressionProperties: {
          "templateOptions.disabled": "false"
        },
        validation: {
          required: true
        }
      }
    ];
    const expectedOutput = [
      {
        wrapper: "label-left-fields-right-wrapper",
        templateOptions: {
          label: "First name"
        },
        hideExpression: "model.showField",
        fieldGroup: [
          {
            id: "person_firstname",
            key: "person_firstname",
            type: "input",
            templateOptions: {
              hasBorder: true,
              readonly: false,
              noBackendInteraction: false,
              uom: "uom",
              required: true,
              label: "First name"
            },
            expressionProperties: {
              "templateOptions.disabled": "false"
            }
          }
        ]
      }
    ];
    expect(formlyFieldsTranslator.translate(input)).toEqual(expectedOutput);
  });

  it('should translate upload to upload field definitions', function () {
    const input = [
      {
        id: "upload",
        guid: 'fake-guid',
        label: "Upload file",
        type: "upload",
        validation: {
          accept: ".pdf"
        },
        hideExpression: "model.showField",
        expressionProperties: {
          "templateOptions.disabled": "false"
        }
      }
    ];
    const expectedOutput = [
      {
        wrapper: 'label-left-fields-right-wrapper',
        templateOptions: {
          label: "Upload file"
        },
        hideExpression: "model.showField",
        fieldGroup: [
          {
            id: "upload",
            key: "upload",
            type: "upload",
            templateOptions: {
              accept: ".pdf",
              hasBorder: true,
              readonly: false,
              noBackendInteraction: false,
              maxFileSizeMB: 10,
              label: "Upload file",
              guid: 'fake-guid'
            },
            expressionProperties: {
              "templateOptions.disabled": "false"
            }
          }
        ]
      }
    ];
    expect(formlyFieldsTranslator.translate(input)).toEqual(expectedOutput);
  });

  it('should translate TextFields to input field definitions', function () {
    const input = [
      {
        id: "person_firstname",
        label: "First name",
        type: "TextField",
        uom: "uom",
        hideExpression: "model.showField",
        expressionProperties: {
          "templateOptions.disabled": "false"
        },
        validation: {
          required: true
        }
      }
    ];
    const expectedOutput = [
      {
        wrapper: "label-left-fields-right-wrapper",
        templateOptions: {
          label: "First name"
        },
        hideExpression: "model.showField",
        fieldGroup: [
          {
            id: "person_firstname",
            key: "person_firstname",
            type: "input",
            templateOptions: {
              hasBorder: true,
              readonly: false,
              noBackendInteraction: false,
              uom: "uom",
              required: true,
              label: "First name"
            },
            expressionProperties: {
              "templateOptions.disabled": "false"
            }
          }
        ]
      }
    ];
    expect(formlyFieldsTranslator.translate(input)).toEqual(expectedOutput);
  });

  it('should translate LargeTextFields to large-input field definitions', function () {
    const input = [
      {
        id: "person_firstname",
        label: "First name",
        type: "LargeTextField",
        uom: "uom",
        hideExpression: "model.showField",
        expressionProperties: {
          "templateOptions.disabled": "false"
        },
        validation: {
          required: true
        }
      }
    ];

    const expectedOutput = [
      {
        wrapper: "header-top-fields-bottom-wrapper",
        templateOptions: {
          label: "First name"
        },
        hideExpression: "model.showField",
        fieldGroup: [
          {
            id: "person_firstname",
            key: "person_firstname",
            type: "large-input",
            expressionProperties: {
              "templateOptions.disabled": "false"
            },
            templateOptions: {
              hasBorder: true,
              readonly: false,
              noBackendInteraction: false,
              uom: "uom",
              required: true,
              label: "First name"
            }
          }
        ]
      }
    ];
    expect(formlyFieldsTranslator.translate(input)).toEqual(expectedOutput);
  });

  it('should translate email fields to input field definitions', function () {
    const input = [
      {
        id: "email",
        label: "Email address",
        type: "email",
        uom: "uom",
        hideExpression: "model.showField",
        expressionProperties: {
          "templateOptions.disabled": "false"
        },
        validation: {
          required: true
        }
      }
    ];
    const expectedOutput = [
      {
        wrapper: "label-left-fields-right-wrapper",
        templateOptions: {
          label: "Email address"
        },
        hideExpression: "model.showField",
        fieldGroup: [
          {
            id: "email",
            key: "email",
            type: "input",
            templateOptions: {
              hasBorder: true,
              readonly: false,
              noBackendInteraction: false,
              uom: "uom",
              required: true,
              label: "Email address"
            },
            expressionProperties: {
              "templateOptions.disabled": "false"
            }
          }
        ]
      }
    ];
    expect(formlyFieldsTranslator.translate(input)).toEqual(expectedOutput);
  });

  it('should translate bool fields to checkbox field definitions', function () {
    const input = [
      {
        id: "bool",
        label: "Boolean",
        type: "bool",
        hideExpression: "model.showField",
        expressionProperties: {
          "templateOptions.disabled": "false"
        },
        validation: {
          required: true
        }
      }
    ];
    const expectedOutput = [
      {
        wrapper: "label-left-fields-right-wrapper",
        templateOptions: {
          label: "Boolean"
        },
        hideExpression: "model.showField",
        fieldGroup: [
          {
            id: "bool",
            key: "bool",
            type: "checkbox",
            templateOptions: {
              hasBorder: true,
              readonly: false,
              noBackendInteraction: false,
              required: true
            },
            expressionProperties: {
              "templateOptions.disabled": "false"
            }
          }
        ]
      }
    ];
    expect(formlyFieldsTranslator.translate(input)).toEqual(expectedOutput);
  });

  it('should translate toggle fields to toggle field definitions', function () {
    const input = [
      {
        id: "toggle",
        label: "Toggle",
        type: "toggle",
        hideExpression: "model.showField",
        expressionProperties: {
          "templateOptions.disabled": "false"
        },
        validation: {
          required: true
        }
      }
    ];
    const expectedOutput = [
      {
        wrapper: "label-left-fields-right-wrapper",
        templateOptions: {
          label: "Toggle"
        },
        hideExpression: "model.showField",
        fieldGroup: [
          {
            id: "toggle",
            key: "toggle",
            type: "toggle",
            templateOptions: {
              hasBorder: true,
              readonly: false,
              noBackendInteraction: false,
              required: true
            },
            expressionProperties: {
              "templateOptions.disabled": "false"
            }
          }
        ]
      }
    ];
    expect(formlyFieldsTranslator.translate(input)).toEqual(expectedOutput);
  });

  it('should translate date fields to datepicker field definitions', function () {
    const input = [
      {
        id: "date",
        label: "Date",
        type: "date",
        hideExpression: "model.showField",
        expressionProperties: {
          "templateOptions.disabled": "false"
        },
        validation: {
          required: true
        }
      }
    ];
    const expectedOutput = [
      {
        wrapper: 'label-left-fields-right-wrapper',
        templateOptions: {
          label: 'Date'
        },
        hideExpression: "model.showField",
        fieldGroup: [
          {
            id: "date",
            key: "date",
            type: "datepicker",
            templateOptions: {
              hasBorder: true,
              readonly: false,
              noBackendInteraction: false,
              required: true,
              label: "Date",
              hasTime: false
            },
            expressionProperties: {
              "templateOptions.disabled": "false"
            }
          }
        ]
      }
    ];
    expect(formlyFieldsTranslator.translate(input)).toEqual(expectedOutput);
  });

  it('should translate datetime fields to datepicker field definitions', function () {
    const input = [
      {
        id: "datetime",
        label: "Date",
        type: "datetime",
        hideExpression: "model.showField",
        expressionProperties: {
          "templateOptions.disabled": "false"
        },
        validation: {
          required: true
        }
      }
    ];
    const expectedOutput = [
      {
        wrapper: 'label-left-fields-right-wrapper',
        templateOptions: {
          label: 'Date'
        },
        hideExpression: "model.showField",
        fieldGroup: [
          {
            id: "datetime",
            key: "datetime",
            type: "datepicker",
            templateOptions: {
              hasBorder: true,
              readonly: false,
              noBackendInteraction: false,
              required: true,
              label: "Date",
              hasTime: true
            },
            expressionProperties: {
              "templateOptions.disabled": "false"
            }
          }
        ]
      }
    ];
    expect(formlyFieldsTranslator.translate(input)).toEqual(expectedOutput);
  });

  it('should translate phone fields to input field definitions', function () {
    const input = [
      {
        id: "phone",
        label: "Phone number",
        type: "phone",
        uom: "uom",
        hideExpression: "model.showField",
        expressionProperties: {
          "templateOptions.disabled": "false"
        },
        validation: {
          required: true
        }
      }
    ];
    const expectedOutput = [
      {
        wrapper: "label-left-fields-right-wrapper",
        templateOptions: {
          label: "Phone number"
        },
        hideExpression: "model.showField",
        fieldGroup: [
          {
            id: "phone",
            key: "phone",
            type: "input",
            templateOptions: {
              hasBorder: true,
              readonly: false,
              noBackendInteraction: false,
              uom: "uom",
              required: true,
              label: "Phone number"
            },
            expressionProperties: {
              "templateOptions.disabled": "false"
            }
          }
        ]
      }
    ];
    expect(formlyFieldsTranslator.translate(input)).toEqual(expectedOutput);
  });

  it('should consider hidden fields removed', function () {
    const input = [
      {
        id: "hiddenId",
        label: "This will not be shown.",
        type: "hidden"
      }
    ];
    const expectedOutput = [];
    expect(formlyFieldsTranslator.translate(input)).toEqual(expectedOutput);
    expect($log.warn.logs[0][0]).toBe('Warning the type: "hidden" has been removed. Field id: "hiddenId"');
  });

  it('should translate enum fields to select field definitions', function () {
    const input = [
      {
        id: "enum",
        label: "Enumeration",
        type: "enum",
        multiple: false,
        enumValues: [
          {
            key: "option-1",
            value: 42,
            disabled: true
          }, {
            key: "option-2",
            value: 43
          }
        ],
        hideExpression: "model.showField",
        expressionProperties: {
          "templateOptions.disabled": "false"
        },
        validation: {
          required: true
        }
      }
    ];
    const expectedOutput = [
      {
        wrapper: "label-left-fields-right-wrapper",
        templateOptions: {
          label: "Enumeration"
        },
        hideExpression: "model.showField",
        fieldGroup: [
          {
            id: "enum",
            key: "enum",
            type: "select",
            templateOptions: {
              label: "Enumeration",
              multipleSelect: false,
              checkboxes: false,
              hasBorder: true,
              readonly: false,
              sortEnums: true,
              noBackendInteraction: false,
              required: true,
              options: [
                {
                  name: 42,
                  value: "option-1"
                }, {
                  name: 43,
                  value: "option-2"
                }
              ]
            },
            expressionProperties: {
              "templateOptions.disabled": "false"
            }
          }
        ]
      }
    ];
    expect(formlyFieldsTranslator.translate(input)).toEqual(expectedOutput);
  });

  it('should translate enum fields to select field definitions when it has checkboxes: true', function () {
    const input = [
      {
        id: "enum",
        label: "Enumeration",
        type: "enum",
        multiple: false,
        checkboxes: true,
        sortEnums: false,
        enumValues: [
          {
            key: "option-1",
            value: 42,
            disabled: true
          }, {
            key: "option-2",
            value: 43
          }
        ],
        hideExpression: "model.showField",
        expressionProperties: {
          "templateOptions.disabled": "false"
        },
        validation: {
          required: true
        }
      }
    ];
    const expectedOutput = [
      {
        wrapper: "label-left-fields-right-wrapper",
        templateOptions: {
          label: "Enumeration"
        },
        hideExpression: "model.showField",
        fieldGroup: [
          {
            id: "enum",
            key: "enum",
            type: "select",
            templateOptions: {
              label: "Enumeration",
              multipleSelect: false,
              checkboxes: true,
              hasBorder: true,
              readonly: false,
              sortEnums: false,
              noBackendInteraction: false,
              required: true,
              options: [
                {
                  name: 42,
                  value: "option-1",
                  disabled: true
                }, {
                  name: 43,
                  value: "option-2",
                  disabled: false
                }
              ]
            },
            expressionProperties: {
              "templateOptions.disabled": "false"
            }
          }
        ]
      }
    ];
    expect(formlyFieldsTranslator.translate(input)).toEqual(expectedOutput);
  });

  it('should consider dynamic enum to be removed', function () {
    const input = [
      {
        id: "dynEnum",
        label: "Dynamic enumeration",
        type: "dynamicEnum",
        getEnumValuesMethod: "http://fakeAddress.com/getEnumValues"
      }
    ];
    const expectedOutput = [];
    expect(formlyFieldsTranslator.translate(input)).toEqual(expectedOutput);
    expect($log.warn.logs[0][0]).toBe('Warning the type: "dynamicEnum" has been removed. Field id: "dynEnum"');
  });

  it('should consider quoteComponents fields to be removed', function () {
    const input = [
      {
        id: "quoteComponents",
        type: "quoteComponents"
      }
    ];
    const expectedOutput = [];
    expect(formlyFieldsTranslator.translate(input)).toEqual(expectedOutput);
    expect($log.warn.logs[0][0]).toBe('Warning the type: "quoteComponents" has been removed. Field id: "quoteComponents"');
  });

  it('should translate address fields to address field definitions', function () {
    const input = [
      {
        id: "address",
        label: "Address",
        type: "address",
        fields: {
          street: {
            id: "address_street",
            label: "Street",
            type: "address_street"
          },
          country: {
            id: "address_country",
            label: "Street",
            type: "address_country",
            enumValues: [{ 'key': 'AT', 'value': 'AT' }, { 'key': 'BE', 'value': 'BE' }]
          },
          city: {
            id: "address_street",
            label: "Street",
            type: "address_street",
            display: false
          }
        },
        hideExpression: "model.showField",
        expressionProperties: {
          "templateOptions.disabled": "false"
        },
        validation: {
          required: true
        }
      }
    ];
    const expectedOutput = [
      {
        wrapper: "label-left-fields-right-wrapper",
        templateOptions: {
          label: "Address"
        },
        hideExpression: "model.showField",
        fieldGroup: [
          {
            id: "address",
            key: "address",
            type: "address",
            templateOptions: {
              label: "Address",
              hasBorder: true,
              readonly: false,
              noBackendInteraction: false,
              required: true,
              fields: {
                street: {
                  key: "address_street",
                  label: "Street",
                  type: "address_street",
                  display: true
                },
                country: {
                  key: "address_country",
                  label: "Street",
                  type: "address_country",
                  enumValues: [{ 'name': 'AT', 'value': 'AT' }, { 'name': 'BE', 'value': 'BE' }],
                  display: true
                },
                city: {
                  key: "address_street",
                  label: "Street",
                  type: "address_street",
                  display: false
                }
              }
            },
            expressionProperties: {
              "templateOptions.disabled": "false"
            }
          }
        ]
      }
    ];
    expect(formlyFieldsTranslator.translate(input)).toEqual(expectedOutput);
  });

  it('should translate InputFieldGroup fields to header-top-fields-bottom-wrapper definitions', function () {
    const input = [
      {
        "label": "Contact person",
        "type": "InputFieldGroup",
        "fields": [
          {
            "id": "first_name",
            "label": "First name",
            "type": "resizing-input",
            "hasBorder": true,
            validation: {
              required: true
            }
          },
          {
            "id": "last_name",
            "label": "Last Name",
            "type": "resizing-input",
            "hasBorder": true,
            validation: {
              required: true
            }
          }
        ],
        hideExpression: "model.showField",
        expressionProperties: {
          "templateOptions.disabled": "model['accounts|aos_quotes|multiean_overall_price_c']"
        }
      }
    ];
    const expectedOutput = [
      {
        wrapper: "header-top-fields-bottom-wrapper",
        templateOptions: {
          label: "Contact person"
        },
        hideExpression: "model.showField",
        fieldGroup: [
          {
            id: "first_name",
            key: "first_name",
            type: "resizing-input",
            className: 'inline-block',
            templateOptions: {
              label: "First name",
              hasBorder: true,
              required: true,
              readonly: false,
              noBackendInteraction: false
            },
            expressionProperties: {
              "templateOptions.disabled": "model['accounts|aos_quotes|multiean_overall_price_c']"
            }
          },
          {
            id: "last_name",
            key: "last_name",
            type: "resizing-input",
            className: 'inline-block',
            templateOptions: {
              label: "Last Name",
              hasBorder: true,
              required: true,
              readonly: false,
              noBackendInteraction: false
            },
            expressionProperties: {
              "templateOptions.disabled": "model['accounts|aos_quotes|multiean_overall_price_c']"
            }
          }
        ]
      }
    ];
    expect(formlyFieldsTranslator.translate(input)).toEqual(expectedOutput);
  });

  it('should translate iconCheckboxGroup fields to checkable-icons-group definitions', function () {
    const input = [
      {
        type: "IconCheckboxGroup",
        fields: [
          {
            id: "lead_has_gas",
            iconClass: "icon-aardgas"
          },
          {
            id: "lead_has_electricity",
            iconClass: "icon-elektriciteit"
          }
        ],
        hideExpression: "model.showField",
        expressionProperties: {
          "templateOptions.disabled": "false"
        }
      }
    ];

    const expectedOutput = [
      {
        wrapper: "checkable-icons-group-wrapper",
        templateOptions: {
          label: undefined
        },
        hideExpression: "model.showField",
        fieldGroup: [
          {
            id: "lead_has_gas",
            key: "lead_has_gas",
            type: "checkable-icon",
            className: 'inline-block',
            templateOptions: {
              iconClass: "icon-aardgas",
              readonly: false,
              noBackendInteraction: false
            },
            expressionProperties: {
              "templateOptions.disabled": "false"
            }
          },
          {
            id: "lead_has_electricity",
            key: "lead_has_electricity",
            type: "checkable-icon",
            className: 'inline-block',
            templateOptions: {
              iconClass: "icon-elektriciteit",
              readonly: false,
              noBackendInteraction: false
            },
            expressionProperties: {
              "templateOptions.disabled": "false"
            }
          }
        ]
      }
    ];
    expect(formlyFieldsTranslator.translate(input)).toEqual(expectedOutput);
  });

  it('should translate rangeType fields to range definitions', function () {
    const input = [
      {
        "id": "wattage",
        "type": "range",
        "label": "Wattage",
        "stepBy": 42,
        "min": 0,
        "max": 84,
        hideExpression: "model.showField",
        expressionProperties: {
          "templateOptions.disabled": "false"
        },
        validation: {
          required: true
        }
      }
    ];
    const expectedOutput = [
      {
        wrapper: "label-left-fields-right-wrapper",
        templateOptions: {
          label: 'Wattage'
        },
        hideExpression: "model.showField",
        fieldGroup: [
          {
            id: "wattage",
            key: "wattage",
            type: "range",
            templateOptions: {
              stepBy: 42,
              min: 0,
              max: 84,
              required: true,
              readonly: false,
              noBackendInteraction: false,
              label: "Wattage"
            },
            expressionProperties: {
              "templateOptions.disabled": "false"
            }
          }
        ]
      }
    ];
    expect(formlyFieldsTranslator.translate(input)).toEqual(expectedOutput);
  });

  it('should translate radioGroup types to select field definitions', function () {
    const input = [
      {
        id: "status",
        label: "Status",
        type: "radioGroup",
        enumValues: [
          {
            key: "OPEN",
            value: "Open"
          }, {
            key: "CLOSED",
            value: "Closed"
          }
        ],
        hideExpression: "model.showField",
        expressionProperties: {
          "templateOptions.disabled": "false"
        }
      }
    ];

    const expectedOutput = [
      {
        wrapper: "label-left-fields-right-wrapper",
        templateOptions: {
          label: "Status"
        },
        hideExpression: "model.showField",
        fieldGroup: [
          {
            id: "status_OPEN",
            key: "status",
            type: "radio",
            className: 'inline-block',
            templateOptions: {
              label: "Open",
              value: "OPEN",
              readonly: false,
              noBackendInteraction: false
            },
            expressionProperties: {
              "templateOptions.disabled": "false"
            }
          },
          {
            id: "status_CLOSED",
            key: "status",
            type: "radio",
            className: 'inline-block',
            templateOptions: {
              label: "Closed",
              value: "CLOSED",
              readonly: false,
              noBackendInteraction: false
            },
            expressionProperties: {
              "templateOptions.disabled": "false"
            }
          }
        ]
      }
    ];
    expect(formlyFieldsTranslator.translate(input)).toEqual(expectedOutput);
  });

  it('should translate checkboxGroup types to checkboxes wrapped in an label-left-fields-right-wrapper with a joint key', function () {
    const input = [
      {
        id: "commodity",
        label: "Select your type",
        type: "checkboxGroup",
        enumValues: [
          {
            key: "gas",
            value: "Gas"
          }, {
            key: "elec",
            value: "Elec"
          }
        ],
        hideExpression: "model.showField",
        expressionProperties: {
          "templateOptions.disabled": "false"
        }
      }
    ];

    const expectedOutput = [
      {
        wrapper: "label-left-fields-right-wrapper",
        templateOptions: {
          label: "Select your type"
        },
        hideExpression: "model.showField",
        fieldGroup: [
          {
            id: "commodity.gas",
            key: "commodity.gas",
            type: "checkbox",
            className: 'inline-block',
            templateOptions: {
              label: "Gas",
              readonly: false,
              noBackendInteraction: false
            },
            expressionProperties: {
              "templateOptions.disabled": "false"
            }
          },
          {
            id: "commodity.elec",
            key: "commodity.elec",
            type: "checkbox",
            className: 'inline-block',
            templateOptions: {
              label: "Elec",
              readonly: false,
              noBackendInteraction: false
            },
            expressionProperties: {
              "templateOptions.disabled": "false"
            }
          }
        ]
      }
    ];
    expect(formlyFieldsTranslator.translate(input)).toEqual(expectedOutput);
  });

  it('should translate checkboxGroup types to checkboxes wrapped in an label-left-fields-right-wrapper with a single key', function () {
    const input = [
      {
        id: undefined, //Here the parent level id is empty so the keys are just the keys in the enumValues.
        label: "Select your type",
        type: "checkboxGroup",
        readonly: true,
        enumValues: [
          {
            key: "gas",
            value: "Gas"
          }, {
            key: "elec",
            value: "Elec"
          }
        ],
        hideExpression: "model.showField",
        expressionProperties: {
          "templateOptions.disabled": "false"
        }
      }
    ];
    const expectedOutput = [
      {
        wrapper: "label-left-fields-right-wrapper",
        hideExpression: "model.showField",
        templateOptions: {
          label: "Select your type"
        },
        fieldGroup: [
          {
            id: "gas",
            key: "gas",
            type: "checkbox",
            className: 'inline-block',
            templateOptions: {
              label: "Gas",
              readonly: true,
              noBackendInteraction: false
            },
            expressionProperties: {
              "templateOptions.disabled": "false"
            }
          },
          {
            id: "elec",
            key: "elec",
            type: "checkbox",
            className: 'inline-block',
            templateOptions: {
              label: "Elec",
              readonly: true,
              noBackendInteraction: false
            },
            expressionProperties: {
              "templateOptions.disabled": "false"
            }
          }
        ]
      }
    ];
    expect(formlyFieldsTranslator.translate(input)).toEqual(expectedOutput);
  });

  it('should translate toggleGroup types to toggles wrapped in an label-left-fields-right-wrapper with a joint key', function () {
    const input = [
      {
        id: "commodity",
        label: "Select your type",
        type: "toggleGroup",
        enumValues: [
          {
            key: "gas",
            value: "Gas"
          }, {
            key: "elec",
            value: "Elec"
          }
        ],
        hideExpression: "model.showField",
        expressionProperties: {
          "templateOptions.disabled": "false"
        }
      }
    ];

    const expectedOutput = [
      {
        wrapper: "label-left-fields-right-wrapper",
        templateOptions: {
          label: "Select your type"
        },
        hideExpression: "model.showField",
        fieldGroup: [
          {
            id: "commodity.gas",
            key: "commodity.gas",
            type: "toggle",
            className: 'inline-block',
            templateOptions: {
              label: "Gas",
              readonly: false,
              noBackendInteraction: false
            },
            expressionProperties: {
              "templateOptions.disabled": "false"
            }
          },
          {
            id: "commodity.elec",
            key: "commodity.elec",
            type: "toggle",
            className: 'inline-block',
            templateOptions: {
              label: "Elec",
              readonly: false,
              noBackendInteraction: false
            },
            expressionProperties: {
              "templateOptions.disabled": "false"
            }
          }
        ]
      }
    ];
    expect(formlyFieldsTranslator.translate(input)).toEqual(expectedOutput);
  });

  it('should translate toggleGroup types to toggles wrapped in an label-left-fields-right-wrapper with a single key', function () {
    const input = [
      {
        id: undefined, //Here the parent level id is empty so the keys are just the keys in the enumValues.
        label: "Select your type",
        type: "toggleGroup",
        readonly: true,
        enumValues: [
          {
            key: "gas",
            value: "Gas"
          }, {
            key: "elec",
            value: "Elec"
          }
        ],
        hideExpression: "model.showField",
        expressionProperties: {
          "templateOptions.disabled": "false"
        }
      }
    ];

    const expectedOutput = [
      {
        wrapper: "label-left-fields-right-wrapper",
        templateOptions: {
          label: "Select your type"
        },
        hideExpression: "model.showField",
        fieldGroup: [
          {
            id: "gas",
            key: "gas",
            type: "toggle",
            className: 'inline-block',
            templateOptions: {
              label: "Gas",
              readonly: true,
              noBackendInteraction: false
            },
            expressionProperties: {
              "templateOptions.disabled": "false"
            }
          },
          {
            id: "elec",
            key: "elec",
            type: "toggle",
            className: 'inline-block',
            templateOptions: {
              label: "Elec",
              readonly: true,
              noBackendInteraction: false
            },
            expressionProperties: {
              "templateOptions.disabled": "false"
            }
          }
        ]
      }
    ];
    expect(formlyFieldsTranslator.translate(input)).toEqual(expectedOutput);
  });

  it('should translate LabelAndText types to a dynamic-text field wrapped in an label-left-fields-right-wrapper.', function () {
    const input = [
      {
        id: "age",
        label: "What is your age?",
        type: "LabelAndText",
        fieldExpression: "true || false",
        hideExpression: "model.showField"
      }
    ];
    const expectedOutput = [
      {
        wrapper: "label-left-fields-right-wrapper",
        templateOptions: {
          label: 'What is your age?'
        },
        hideExpression: "model.showField",
        fieldGroup: [
          {
            id: 'age',
            key: 'age',
            type: 'dynamic-text',
            templateOptions: {
              label: "What is your age?",
              unparsedFieldExpression: "true || false"
            }
          }
        ]
      }
    ];
    expect(formlyFieldsTranslator.translate(input)).toEqual(expectedOutput);
  });

  it('should translate LabelAndAction types to a dynamic-text-with-action field wrapped in an label-left-fields-right-wrapper.', function () {
    const input = [
      {
        id: "account-id",
        label: "Account",
        type: "LabelAndAction",
        fieldExpression: "true || false",
        hideExpression: "model.showField",
        action: {
          id: 'action-id'
        }
      }
    ];
    const expectedOutput = [
      {
        wrapper: "label-left-fields-right-wrapper",
        templateOptions: {
          label: 'Account'
        },
        hideExpression: "model.showField",
        fieldGroup: [
          {
            id: 'account-id',
            key: 'account-id',
            type: 'dynamic-text-with-action',
            templateOptions: {
              label: "Account",
              unparsedFieldExpression: "true || false",
              action: {
                id: 'action-id'
              }
            }
          }
        ]
      }
    ];
    expect(formlyFieldsTranslator.translate(input)).toEqual(expectedOutput);
  });

  it('should translate crud-list types to a crud-list form element type', function () {
    const input = [
      {
        id: "crud-list",
        type: "crud-list",
        createUpdateActionId: "createListEntry",
        label: "CRUD List",
        headers: [{
          label: "COLUMN",
          cellClass: "cell__text",
          cellType: "list-simple-single-line-cell",
          cellOptions: {
            text: "{% column_property %}"
          }
        }],
        hideExpression: "model.showField",
        expressionProperties: {
          "templateOptions.disabled": "false"
        }
      }
    ];

    const expectedOutput = [
      {
        id: "crud-list",
        key: "crud-list",
        type: "crud-list",
        hideExpression: "model.showField",
        templateOptions: {
          label: "CRUD List",
          headers: [{
            label: "COLUMN",
            cellClass: "cell__text",
            cellType: "list-simple-single-line-cell",
            cellOptions: {
              text: "{% column_property %}"
            }
          }],
          createUpdateActionId: "createListEntry",
          readonly: false
        },
        expressionProperties: {
          "templateOptions.disabled": "false"
        }
      }
    ];

    expect(formlyFieldsTranslator.translate(input)).toEqual(expectedOutput);
  });

  it('should translate selectWithSearch types to select-with-search form element type', function () {
    const input = [
      {
        id: "id",
        label: "Users",
        type: "selectWithSearch",
        multiple: true,
        plusButtonTitle: "Select one or more Users",
        modalTitle: "Select one or more Users",
        selectedResultsTitle: "Selected Users",
        datasourceName: "Users",
        hideExpression: "model.showField",
        validation: {
          required: true
        },
        expressionProperties: {
          "templateOptions.disabled": "false"
        }
      }
    ];

    const expectedOutput = [
      {
        wrapper: "label-top-fields-bottom-wrapper",
        templateOptions: {
          label: 'Users'
        },
        hideExpression: "model.showField",
        fieldGroup: [
          {
            id: 'id',
            key: 'id',
            type: 'select-with-search',
            templateOptions: {
              label: 'Users',
              modalTitle: 'Select one or more Users',
              plusButtonTitle: 'Select one or more Users',
              selectedResultsTitle: 'Selected Users',
              multipleSelect: true,
              readonly: false,
              readonlyJoin: ", ",
              noBackendInteraction: false,
              required: true,
              datasourceName: 'Users'
            },
            expressionProperties: {
              "templateOptions.disabled": "false"
            }
          }
        ]
      }
    ];

    expect(formlyFieldsTranslator.translate(input)).toEqual(expectedOutput);
  });

  it('should translate "tariffCalculation" types to a unwrapped type', function () {
    const input = [
      {
        id: "tariffCalculation",
        type: "tariffCalculation",
        hideExpression: "model.showField",
        expressionProperties: {
          "templateOptions.disabled": "false"
        },
        editable: true
      }
    ];

    const expectedOutput = [
      {
        id: "tariffCalculation",
        key: "tariffCalculation",
        type: "tariff-calculation",
        hideExpression: "model.showField",
        templateOptions: {
          hasBorder: true,
          hideButtonsConditions: {},
          editable: true
        },
        expressionProperties: {
          "templateOptions.disabled": "false"
        }
      }
    ];

    expect(formlyFieldsTranslator.translate(input)).toEqual(expectedOutput);
  });

  it('should consider "tariffOverview" types to be removed', function () {
    const input = [
      {
        id: "tariffOverview",
        type: "tariffOverview"
      }
    ];

    const expectedOutput = [];
    expect(formlyFieldsTranslator.translate(input)).toEqual(expectedOutput);
    expect($log.warn.logs[0][0]).toBe('Warning the type: "tariffOverview" has been removed. Field id: "tariffOverview"');
  });

  it('should translate hashtagText types to hashtag-text form element type', function () {
    const input = [
      {
        id: "id",
        label: "Case",
        type: "hashtagText",
        datasourceName: "Incoming",
        hideExpression: "model.showField",
        validation: {
          required: true
        },
        expressionProperties: {
          "templateOptions.disabled": "false"
        }
      }
    ];

    const expectedOutput = [
      {
        wrapper: 'label-top-fields-bottom-wrapper',
        templateOptions: {
          label: 'Case'
        },
        hideExpression: 'model.showField',
        fieldGroup: [{
          id: 'id',
          key: 'id',
          type: 'hashtagText',
          expressionProperties: {
            "templateOptions.disabled": 'false'
          },
          templateOptions: {
            datasourceName: 'Incoming',
            required: true,
            readonly: false,
            noBackendInteraction: false,
            label: 'Case',
            displayWysiwyg: false
          }
        }]
      }
    ];

    expect(formlyFieldsTranslator.translate(input)).toEqual(expectedOutput);
  });

  it('should translate hashtagText types to hashtag-text with WYSIWYG', function () {
    const input = [
      {
        id: "id",
        label: "Case",
        type: "hashtagText",
        datasourceName: "Incoming",
        displayWysiwyg: true,
        hideExpression: "model.showField",
        validation: {
          required: true
        },
        expressionProperties: {
          "templateOptions.disabled": "false"
        }
      }
    ];

    const expectedOutput = [
      {
        wrapper: 'label-top-fields-bottom-wrapper',
        templateOptions: {
          label: 'Case'
        },
        hideExpression: 'model.showField',
        fieldGroup: [{
          id: 'id',
          key: 'id',
          type: 'hashtagText',
          expressionProperties: {
            "templateOptions.disabled": 'false'
          },
          templateOptions: {
            datasourceName: 'Incoming',
            required: true,
            readonly: false,
            noBackendInteraction: false,
            label: 'Case',
            displayWysiwyg: true
          }
        }]
      }
    ];

    expect(formlyFieldsTranslator.translate(input)).toEqual(expectedOutput);
  });

  it('should translate textarea to textarea field definitions', function () {
    const input = [
      {
        id: "person_firstname",
        label: "First name",
        type: "textarea",
        hideExpression: "model.showField",
        expressionProperties: {
          "templateOptions.disabled": "false"
        },
        validation: {
          required: true
        }
      }
    ];

    const expectedOutput = [
      {
        wrapper: "label-top-fields-bottom-wrapper",
        templateOptions: {
          label: "First name"
        },
        hideExpression: "model.showField",
        fieldGroup: [
          {
            id: "person_firstname",
            key: "person_firstname",
            type: "textarea",
            expressionProperties: {
              "templateOptions.disabled": "false"
            },
            templateOptions: {
              hasBorder: true,
              readonly: false,
              noBackendInteraction: false,
              required: true,
              label: "First name"
            }
          }
        ]
      }
    ];
    expect(formlyFieldsTranslator.translate(input)).toEqual(expectedOutput);
  });

  it('should translate json-editor to json-editor field definitions', function () {
    const input = [
      {
        id: "person_firstname",
        label: "First name",
        type: "json-editor",
        hideExpression: "model.showField",
        expressionProperties: {
          "templateOptions.disabled": "false"
        },
        validation: {
          required: true
        }
      }
    ];

    const expectedOutput = [
      {
        wrapper: "label-top-fields-bottom-wrapper",
        templateOptions: {
          label: "First name"
        },
        hideExpression: "model.showField",
        fieldGroup: [
          {
            id: "person_firstname",
            key: "person_firstname",
            type: "json-editor",
            expressionProperties: {
              "templateOptions.disabled": "false"
            },
            templateOptions: {
              hasBorder: true,
              readonly: false,
              noBackendInteraction: false,
              required: true,
              label: "First name"
            }
          }
        ]
      }
    ];
    expect(formlyFieldsTranslator.translate(input)).toEqual(expectedOutput);
  });

  it('should translate wysiwyg type to wysiwyg-editor form element', function () {
    const input = [
      {
        id: "message",
        label: "Message",
        type: "wysiwyg",
        hideExpression: "model.showField",
        expressionProperties: {
          "templateOptions.disabled": "false"
        },
        validation: {
          required: false
        }
      }
    ];
    const expectedOutput = [
      {
        wrapper: "label-top-fields-bottom-wrapper",
        templateOptions: {
          label: "Message"
        },
        hideExpression: "model.showField",
        fieldGroup: [
          {
            id: "message",
            key: "message",
            type: "wysiwyg-editor",
            templateOptions: {
              required: false,
              readonly: false,
              noBackendInteraction: false,
              label: "Message"
            },
            expressionProperties: {
              "templateOptions.disabled": "false"
            }
          }
        ]
      }
    ];
    expect(formlyFieldsTranslator.translate(input)).toEqual(expectedOutput);
  });

  it('should translate draw-pad type to draw-pad form element', function () {
    const input = [
      {
        id: "signature",
        label: "Signature",
        type: "drawPad",
        hideExpression: "model.showField",
        expressionProperties: {
          "templateOptions.disabled": "false"
        },
        validation: {
          required: false
        }
      }
    ];
    const expectedOutput = [
      {
        wrapper: 'label-top-fields-bottom-wrapper',
        templateOptions: {
          label: 'Signature'
        },
        hideExpression: 'model.showField',
        fieldGroup: [{
          id: 'signature',
          key: 'signature',
          type: 'draw-pad',
          templateOptions: {
            readonly: false,
            noBackendInteraction: false,
            width: 400,
            height: 280,
            label: 'Signature',
            required: false
          },
          expressionProperties: {
            "templateOptions.disabled": "false"
          }
        }]
      }
    ];

    expect(formlyFieldsTranslator.translate(input)).toEqual(expectedOutput);
  });

  it('should throw an error when an unspecified type is attempted to be translated', function () {
    const input = [
      {
        id: "unsupported",
        label: "Unsupported type",
        type: "unsupportedType"
      }
    ];
    expect(function () {
      formlyFieldsTranslator.translate(input);
    }).toThrow(new Error("Error during conversion to formly types, unsupported type 'unsupportedType'. Field id: 'unsupported'"));
  });
})
;
