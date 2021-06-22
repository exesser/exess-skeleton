'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp:kitchenSink component
 * @description
 * # kitchenSink
 *
 * The kitchenSink component is a mock page where you can test form elements.
 *
 * Example usage:
 *
 * <kitchen-sink></kitchen-sink>
 *
 * Component of the digitalWorkplaceApp
 */
/*
 The line below excludes this file from testing,
 this file is a 'dev' tool.
 */
/* istanbul ignore next */
angular.module('digitalWorkplaceApp')
  .component('kitchenSink', {
    templateUrl: 'es6/guidance-mode/kitchen-sink/kitchen-sink.component.html',
    controllerAs: 'kitchenSinkController',
    controller: function (progressBarObserver, guidanceFormObserverFactory,
                          validationObserverFactory, suggestionsObserverFactory, $timeout, $state,
                          translateFilter, $log, previousState, $stateParams, $location,
                          guidanceModeBackendState, ACTION_EVENT) {
      const kitchenSinkController = this;

      guidanceModeBackendState.setBackendIsBusy(false, { event: ACTION_EVENT.CHANGED, focus: 'bla' });

      kitchenSinkController.guidanceFormObserver = guidanceFormObserverFactory.createGuidanceFormObserver();

      //Create the validationObserver and suggestionsObserver for the initial step.
      kitchenSinkController.validationObserver = validationObserverFactory.createValidationObserver();
      kitchenSinkController.suggestionsObserver = suggestionsObserverFactory.createSuggestionsObserver();

      kitchenSinkController.forms = []; // All the FormControllers that make up this 'Guidance step'.

      // Loading message state
      kitchenSinkController.loading = false;
      kitchenSinkController.loadingMessage = '';
      kitchenSinkController.valid = true;

      // Hide or show the borders of all applicable fields
      const hasBorder = true;

      // The orientation direction of all applicable fields, empty string uses the defaults.
      const orientation = ''; // 'label-top' | 'header-top' | 'label-left' | ''

      // Whether or not the form should be shown in readonly mode.
      const readonly = false;

      // The model for all steps
      const model = {
        "formEnabled": true,
        "company_name_c": "",
        "description": "",
        "case_c": {
          "text": "Hi do you guy's support other countries such as the Netherlands",
          "tags": [{ hashtag: "NL", id: "12415151-7c30-a1b7-3e0a-564efd6363ef" }]
        },
        "nace": [
          {
            "key": "0111",
            "label": "0111 - Teelt van granen (m.u.v. rijst), peulgewassen en oliehoudende zaden"
          }
        ],
        "bla": "data:image/png;base64,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Gky6ISuggAIKKJBdwAIne4aNTwEFFFBAgQYFLHAaTLohK6CAAgookF3AAid7ho1PAQUUUECBBgUscBpMuiEroIACCiiQXcACJ3uGjU8BBRRQQIEGBSxwGky6ISuggAIKKJBdwAIne4aNTwEFFFBAgQYFLHAaTLohK6CAAgookF3AAid7ho1PAQUUUECBBgUscBpMuiEroIACCiiQXcACJ3uGjU8BBRRQQIEGBSxwGky6ISuggAIKKJBdwAIne4aNTwEFFFBAgQYFLHAaTLohK6CAAgookF3AAid7ho1PAQUUUECBBgUscBpMuiEroIACCiiQXcACJ3uGjU8BBRRQQIEGBSxwGky6ISuggAIKKJBdwAIne4aNTwEFFFBAgQYFLHAaTLohK6CAAgookF3AAid7ho1PAQUUUECBBgUscBpMuiEroIACCiiQXcACJ3uGjU8BBRRQQIEGBSxwGky6ISuggAIKKJBdwAIne4aNTwEFFFBAgQYFLHAaTLohK6CAAgookF3AAid7ho1PAQUUUECBBgUscBpMuiEroIACCiiQXcACJ3uGjU8BBRRQQIEGBSxwGky6ISuggAIKKJBdwAIne4aNTwEFFFBAgQYFLHAaTLohK6CAAgookF3AAid7ho1PAQUUUECBBgUscBpMuiEroIACCiiQXcACJ3uGjU8BBRRQQIEGBSxwGky6ISuggAIKKJBdwAIne4aNTwEFFFBAgQYFLHAaTLohK6CAAgookF3AAid7ho1PAQUUUECBBgUscBpMuiEroIACCiiQXcACJ3uGjU8BBRRQQIEGBSxwGky6ISuggAIKKJBdwAIne4aNTwEFFFBAgQYFLHAaTLohK6CAAgookF3AAid7ho1PAQUUUECBBgUscBpMuiEroIACCiiQXcACJ3uGjU8BBRRQQIEGBSxwGky6ISuggAIKKJBdwAIne4aNTwEFFFBAgQYFLHAaTLohK6CAAgookF3AAid7ho1PAQUUUECBBgUscBpMuiEroIACCiiQXcACJ3uGjU8BBRRQQIEGBSxwGky6ISuggAIKKJBdwAIne4aNTwEFFFBAgQYFLHAaTLohK6CAAgookF3AAid7ho1PAQUUUECBBgUscBpMuiEroIACCiiQXcACJ3uGjU8BBRRQQIEGBSxwGky6ISuggAIKKJBdwAIne4aNTwEFFFBAgQYFLHAaTLohK6CAAgookF3AAid7ho1PAQUUUECBBgUscBpMuiEroIACCiiQXcACJ3uGjU8BBRRQQIEGBSxwGky6ISuggAIKKJBdwAIne4aNTwEFFFBAgQYFLHAaTLohK6CAAgookF3AAid7ho1PAQUUUECBBgUscBpMuiEroIACCiiQXcACJ3uGjU8BBRRQQIEGBf4PsRT1+0H6fqIAAAAASUVORK5CYII=",
        "addresses_leads": {
          "address_type": "Lead Address",
          "address_street": "",
          "address_number": "",
          "address_addition": "",
          "address_bus": "",
          "address_postalcode": "",
          "address_city": "",
          "address_country": ""
        },
        "future": {
          "contact_date_c": "",
          "contact_datetime_c": ""
        },
        "wattage": 15,
        "lead": {
          "has": {
            gas: false,
            elec: true
          }
        },
        "contracts": [{
          name: "Already uploaded file",
          url: "https://exess/uploads/Already uploaded file.pdf",
          id: "2e4516c9-7289-4745-92b0-a36cbb80c422"
        }, {
          name: "Already uploaded file number 2",
          url: "https://exess/uploads/Already uploaded file number 2.pdf",
          id: "bdb20fba-fae1-475e-9b69-18a7dce61dbd"
        }],
        "files": [],
        "record_type": "B2B",
        "status": "OPEN",
        "active_lead_c": "true",
        "first_name": "",
        "last_name": "",
        "lead_has_gas": false,
        "lead_has_electricity": false,
        "company_number_c": "",
        "legal_form_c": "",
        "function_c": "",
        "gender_c": "",
        "language": {
          "native": []
        },
        "leads_contact_details": {
          "phone": "",
          "mobile": "",
          "email": ""
        },
        "preferred_contact_hour_c": "UNKNOWN",
        "hasFrontDoor": true,
        "hasFrontWindow": true,
        "company_watt": 50,
        "company_status": "CLOSED",
        "connection_type": {
          GAS: false,
          ELEC: false
        }
      };

      const textFieldsStep = createStep({
        "grid": {
          "columns": [{
            "size": "1-4",
            "hasMargin": false,
            "cssClasses": ["progressbar"],
            "rows": [{
              "size": "1-1",
              "type": "progressBar",
              "options": {
                "title": "Kitchen Sink"
              }
            }]
          }, {
            "size": "3-4",
            "hasMargin": false,
            "rows": [{
              size: "1-1",
              "grid": {
                "columns": [{
                  "size": "1-2",
                  "hasMargin": false,
                  "rows": [{
                    "size": "1-3",
                    "type": "basicFormlyForm",
                    "cssClasses": ["card", "blue"],
                    "options": {
                      "formKey": "a"
                    }
                  }, {
                    "size": "2-3",
                    "type": "titleContainingGrid",
                    "cssClasses": ["card"],
                    "options": {
                      "defaultTitle": "Company name",
                      "titleExpression": "{%company_name_c%}",
                      "grid": {
                        "columns": [{
                          "size": "1-1",
                          "rows": [{
                            "size": "1-1",
                            "type": "basicFormlyForm",
                            "options": {
                              "formKey": "b"
                            }
                          }]
                        }]
                      }
                    }
                  }]
                }]
              }
            }]
          }]
        },
        "form": {
          "a": {
            "type_c": "DEFAULT",
            "id": "8b42beb4-5ab2-018f-bb1f-56a8afa918ba",
            "key_c": "8b42beb4-5ab2-018f-bb1f-56a8afa918ba",
            "name": "",
            "fields": [{
              "id": "company_name_c",
              "label": "Company name",
              "default": "",
              "type": "LargeTextField",
              hasBorder: false,
              orientation,
              readonly,
              "validation": {
                required: false,
                minlength: 3,
                maxlength: 13,
                pattern: ".*(bv|BV)"
              },
              "expressionProperties": {
                "templateOptions.disabled": "model.formEnabled === false"
              }
            }, {
              "label": "Contact person",
              "type": "InputFieldGroup",
              "id": "name",
              orientation,
              readonly,
              "fields": [{
                "id": "first_name",
                "label": "First name",
                "type": "resizing-input",
                hasBorder: false,
                "validation": {
                  pattern: "[A-Z]{1}.+",
                  patternValidationMessage: "First name must start with a capital letter and contain at least 1 other character."
                }
              }, {
                "id": "last_name",
                "label": "Last Name",
                hasBorder: false,
                "type": "resizing-input",
                "validation": {
                  pattern: "[A-Z]{1}.+",
                  patternValidationMessage: "Last name must start with a capital letter and contain at least 1 other character."
                }
              }],
              "expressionProperties": {
                "templateOptions.disabled": "model.formEnabled === false"
              }
            }]
          },
          "b": {
            "type_c": "DEFAULT",
            "id": "8b42beb4-5ab2-018f-bb1f-56a8afa918ba",
            "key_c": "8b42beb4-5ab2-018f-bb1f-56a8afa918ba",
            "name": "",
            "fields": [{
              "id": "case_c",
              "label": "Explain your case",
              "type": "hashtagText",
              orientation,
              readonly,
              "datasourceName": "blaat",
              "expressionProperties": {
                "templateOptions.disabled": "model.formEnabled === false"
              },
              "validation": {
                required: true
              }
            }, {
              "id": "case_c",
              "label": "Explain your case 2",
              "type": "hashtagText",
              orientation,
              readonly,
              "datasourceName": "blaat",
              "displayWysiwyg": true,
              "expressionProperties": {
                "templateOptions.disabled": "model.formEnabled === false"
              },
              "validation": {
                required: true
              }
            }, {
              "id": "description",
              "label": "Description",
              "type": "textarea",
              orientation,
              readonly,
              "expressionProperties": {
                "templateOptions.disabled": "model.formEnabled === false"
              },
              validation: {
                required: true,
                minlength: 3,
                maxlength: 15,
                pattern: "[A-Z]{1}.+",
                patternValidationMessage: "First name must start with a capital letter and contain at least 1 other character."
              }
            }]
          }
        },
        errors: {
          case_c: ["This case is no fun at all"]
        },
        suggestions: {
          "future.contact_datetime_c": [{
            "label": "EOM",
            "model": {
              "future": {
                "contact_datetime_c": "2018-12-31 12:00:00"
              }
            }
          }, {
            "label": "+1 Y",
            "model": {
              "future": {
                "contact_datetime_c": "2019-12-31 12:00:00"
              }
            }
          }, {
            "label": "+2 Y",
            "model": {
              "future": {
                "contact_datetime_c": "2020-12-31 12:00:00"
              }
            }
          }],
          "future.contact_date_c": [{
            "label": "EOM",
            "model": {
              "future": {
                "contact_date_c": "2018-12-31"
              }
            }
          }, {
            "label": "+1 Y",
            "model": {
              "future": {
                "contact_date_c": "2019-12-31"
              }
            }
          }, {
            "label": "+2 Y",
            "model": {
              "future": {
                "contact_date_c": "2020-12-31"
              }
            }
          }],
          "company_name_c": [{
            "label": "Billing House",
            "labelAddition": "",
            "model": {
              "company_name_c": "Billing"
            }
          }, {
            "label": "Exesser BV",
            "labelAddition": "",
            "model": {
              "company_name_c": "Exesser"
            }
          }, {
            "label": "42",
            "labelAddition": "",
            "model": {
              "company_name_c": "42"
            }
          }],
          "addresses_leads": [
            {
              "label": "Veldkant 7",
              "labelAddition": "Kontich",
              "model": {
                "addresses_leads": {
                  "address_street": "Veldkant",
                  "address_number": "7",
                  "address_addition": "A",
                  "address_bus": "12345",
                  "address_postalcode": "2000",
                  "address_city": "Kontich",
                  "address_country": "Belgium"
                }
              }
            }, {
              "label": "Koraalrood 33",
              "labelAddition": "Zoetermeer",
              "model": {
                "addresses_leads": {
                  "address_street": "Koraalrood",
                  "address_number": "33",
                  "address_addition": "B",
                  "address_bus": "54321",
                  "address_postalcode": "2718 SB",
                  "address_city": "Zoetermeer",
                  "address_country": "the Netherlands"
                }
              }
            }
          ],
          last_name: [{
            "label": "Hus",
            "labelAddition": "Boy wonder",
            "model": {
              "last_name": "Hus"
            }
          }, {
            "label": "Terzea",
            "labelAddition": "Captain Commander",
            "model": {
              "last_name": "Terzea"
            }
          }],
          "company_number_c": [{
            "label": "BE0444444449",
            "labelAddition": "",
            "model": {
              "company_number_c": "BE0444444449"
            }
          }, {
            "label": " BE0555555559",
            "labelAddition": "",
            "model": {
              "company_number_c": "BE0555555559"
            }
          }, {
            "label": "BE0333333339",
            "labelAddition": "",
            "model": {
              "company_number_c": " BE0333333339"
            }
          }, {
            "label": "BE0444444449",
            "labelAddition": "",
            "model": {
              "company_number_c": "BE0444444449"
            }
          }, {
            "label": " BE0555555559",
            "labelAddition": "",
            "model": {
              "company_number_c": "BE0555555559"
            }
          }]
        }
      });

      const checksAndTogglesStep = createStep({
        "form": {
          "a": {
            "type_c": "DEFAULT",
            "id": "8b42beb4-5ab2-018f-bb1f-56a8afa918ba",
            "key_c": "8b42beb4-5ab2-018f-bb1f-56a8afa918ba",
            "name": "",
            "fields": [{
              "id": "gasElec",
              "type": "IconCheckboxGroup",
              orientation,
              readonly,
              "fields": [{
                "id": "lead.has.gas",
                "iconClass": "icon-elektriciteit"
              }, {
                "id": "lead.has.elec",
                "iconClass": "icon-aardgas"
              }],
              "expressionProperties": {
                "templateOptions.disabled": "model.formEnabled === false"
              }
            }, {
              "id": "formEnabled",
              "label": "Do you want to enable the form?",
              "type": "bool",
              orientation,
              readonly
            }, {
              "id": "company_status",
              "label": "Company status",
              hasBorder,
              orientation,
              readonly,
              "type": "radioGroup",
              "enumValues": [{
                "key": "OPEN",
                "value": "Open"
              }, {
                "key": "CLOSED",
                "value": "Closed"
              }],
              "expressionProperties": {
                "templateOptions.disabled": "model.formEnabled === false"
              }
            }, {
              "id": "lead.has",
              "label": "Check what is applicable",
              orientation,
              readonly,
              "type": "checkboxGroup",
              "mode": "CHECKBOX",
              "module": "leads",
              "moduleField": "connection_type",
              "enumValues": [{
                "key": "gas",
                "value": "Gas"
              }, {
                "key": "elec",
                "value": "Elec"
              }],
              "expressionProperties": {
                "templateOptions.disabled": "model.formEnabled === false"
              }
            }, {
              "id": "lead.has",
              "label": "Tick what is applicable",
              hasBorder,
              orientation,
              readonly,
              "type": "toggleGroup",
              "mode": "TOGGLE",
              "module": "leads",
              "moduleField": "connection_type",
              "enumValues": [{
                "key": "gas",
                "value": "Gas"
              }, {
                "key": "elec",
                "value": "Elec"
              }],
              "expressionProperties": {
                "templateOptions.disabled": "model.formEnabled === false"
              }
            }, {
              "id": "wattage",
              "type": "range",
              "label": "Wattage",
              orientation,
              readonly,
              "stepBy": 1,
              "min": 0,
              "max": 100,
              "expressionProperties": {
                "templateOptions.disabled": "model.formEnabled === false"
              }
            }, {
              "id": "gender_c",
              "label": "Gender",
              "default": "UNKNOWN",
              "type": "enum",
              "module": "leads",
              "moduleField": "gender_c",
              hasBorder,
              orientation,
              readonly,
              "enumValues": [{
                "key": "UNKNOWN",
                "value": "Unknown"
              }, {
                "key": "FEMALE",
                "value": "FEMALE"
              }, {
                "key": "MALE",
                "value": "MALE"
              }],
              "expressionProperties": {
                "templateOptions.disabled": "model.formEnabled === false"
              },
              validation: {
                required: true
              }
            }, {
              "id": "language.native",
              "label": "Language",
              "default": "EN",
              "type": "enum",
              "multiple": true,
              orientation,
              readonly,
              "enumValues": [{
                "key": "UNKNOWN",
                "value": "Unknown"
              }, {
                "key": "EN",
                "value": "English"
              }, {
                "key": "FR",
                "value": "French"
              }, {
                "key": "NL",
                "value": "Dutch"
              }],
              "expressionProperties": {
                "templateOptions.disabled": "model.formEnabled === false"
              },
              validation: {
                required: true
              }
            }, {
              "id": "language.native",
              "label": "Language",
              "default": "EN",
              "type": "enum",
              "multiple": true,
              "checkboxes": true,
              orientation,
              readonly,
              "enumValues": [{
                "key": "EN",
                "value": "English",
                "disabled": true
              }, {
                "key": "FR",
                "value": "French"
              }, {
                "key": "NL",
                "value": "Dutch"
              }],
              "expressionProperties": {
                "templateOptions.disabled": "model.formEnabled === false"
              },
              validation: {
                required: true
              }
            }, {
              "id": "hasFrontWindow",
              "label": "Do you have a front window?",
              "type": "toggle",
              orientation,
              readonly,
              "expressionProperties": {
                "templateOptions.disabled": "model.formEnabled === false"
              }
            }, {
              "id": "hasFrontDoor",
              "label": "Do you have a front door?",
              "type": "bool",
              orientation,
              readonly,
              "expressionProperties": {
                "templateOptions.disabled": "model.formEnabled === false"
              }
            }]
          }
        }
      });

      const otherStep = createStep({
          "form": {
            "a": {
              "type_c": "DEFAULT",
              "id": "8b42beb4-5ab2-018f-bb1f-56a8afa918ba",
              "key_c": "8b42beb4-5ab2-018f-bb1f-56a8afa918ba",
              "name": "",
              "fields": [{
                "id": "formEnabled",
                "label": "Do you want to enable the form?",
                "type": "bool",
                orientation,
                readonly
              }, {
                "id": "files",
                "label": "Upload files",
                orientation,
                readonly,
                "type": "upload",
                "validation": {
                  "accept": ".png",
                  "required": true,
                  "requiredValidationMessage": "Upload at least one file"
                },
                "expressionProperties": {
                  "templateOptions.disabled": "model.formEnabled === false"
                }
              }, {
                "id": "contracts",
                "label": "Upload contracts",
                orientation,
                readonly,
                "type": "upload",
                "validation": {
                  "accept": ".pdf",
                  "required": true
                },
                "expressionProperties": {
                  "templateOptions.disabled": "model.formEnabled === false"
                }
              }, {
                "id": "nace",
                "label": "Nace",
                "type": "selectWithSearch",
                orientation,
                readonly,
                "plusButtonTitle": "Select a NACE code",
                "modalTitle": "Select a NACE code",
                "selectedResultsTitle": "Selected NACE code",
                "multiple": false,
                "datasourceName": "Nace",
                "expressionProperties": {
                  "templateOptions.disabled": "model.formEnabled === false"
                },
                "validation": {
                  "required": true
                }
              }, {
                "id": "bla",
                "label": "Sign",
                "type": "drawPad",
                orientation,
                readonly,
                "expressionProperties": {
                  "templateOptions.disabled": "model.formEnabled === false"
                },
                "validation": {
                  "required": true
                }
              }]
            }
          }
        })
        ;

      const steps = [textFieldsStep, checksAndTogglesStep, otherStep];

      const initialIndex = _.get($stateParams, 'page', 0);
      kitchenSinkController.guidanceMode = angular.copy(steps[initialIndex]);
      notifiyObserversOfStepChange();

      $timeout(function () {
        kitchenSinkController.suggestionsObserver.setSuggestions(kitchenSinkController.guidanceMode.suggestions);
      }, 1000);

      kitchenSinkController.guidanceFormObserver.setFormControllerCreatedCallback(function (formController) {
        kitchenSinkController.forms.push(formController);
      });

      kitchenSinkController.guidanceFormObserver.setFormValueChangedCallback(function () {
        kitchenSinkController.validationObserver.setErrors(kitchenSinkController.guidanceMode.errors);
        kitchenSinkController.valid = _.every(kitchenSinkController.forms, '$valid');

        kitchenSinkController.suggestionsObserver.setSuggestions(kitchenSinkController.guidanceMode.suggestions);
      });

      kitchenSinkController.backArrowClicked = function () {
        $timeout(function () {
          previousState.navigateTo();
        }, 500);
      };

      progressBarObserver.registerClickCallback(function (stepId) {
        kitchenSinkController.validationObserver = validationObserverFactory.createValidationObserver();
        kitchenSinkController.suggestionsObserver = suggestionsObserverFactory.createSuggestionsObserver();

        const index = _.parseInt(stepId);

        kitchenSinkController.guidanceMode = angular.copy(steps[index]);
        $location.search('page', index);

        kitchenSinkController.forms = [];
        notifiyObserversOfStepChange();
      });

      function notifiyObserversOfStepChange() {
        /*
         Because the grid is redrawn give the progress indicator and the guidance-form sometime
         to set-up the observers once more.
         */
        $timeout(function () {
          kitchenSinkController.guidanceFormObserver.stepChangeOccurred(kitchenSinkController.guidanceMode);
          progressBarObserver.setProgressMetadata(kitchenSinkController.guidanceMode.progress);
        }, 200);
      }

      function createStep({ form, errors = {}, suggestions = {}, grid = false }) {
        if (grid === false) {
          grid = {
            "columns": [{
              "size": "1-4",
              "hasMargin": false,
              "cssClasses": ["progressbar"],
              "rows": [{
                "size": "1-1",
                "type": "progressBar",
                "options": {
                  "title": "Kitchen Sink"
                }
              }]
            }, {
              "size": "3-4",
              "hasMargin": false,
              "rows": [{
                size: "1-1",
                "type": "titleContainingGrid",
                "cssClasses": ["card"],
                "options": {
                  "defaultTitle": "Company name",
                  "titleExpression": "{%company_name_c%}",
                  "grid": {
                    "columns": [{
                      "size": "1-1",
                      "rows": [{
                        "size": "1-1",
                        "type": "basicFormlyForm",
                        "options": {
                          "formKey": "a"
                        }
                      }]
                    }]
                  }
                }
              }]
            }]
          };
        }

        return {
          model,
          grid,
          form,
          "guidance": {
            title: "Create Lead",
            loadingMessage: "Loading Lead"
          },
          "step": {
            "willSave": false,
            "done": false
          },
          errors,
          suggestions,
          progress: {
            "steps": [{
              "key_c": "0",
              "name": "Text fields",
              "active": false,
              "canBeActivated": true,
              "disabled": false,
              "progressPercentage": 100,
              "substeps": []
            }, {
              "key_c": "1",
              "name": "Checks and toggles",
              "active": false,
              "canBeActivated": true,
              "disabled": true,
              "progressPercentage": 0,
              "substeps": []
            }, {
              "key_c": "2",
              "name": "Other",
              "active": false,
              "canBeActivated": true,
              "disabled": true,
              "progressPercentage": 0,
              "substeps": []
            }]
          }
        };
      }
    }
  });
