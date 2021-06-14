'use strict';

/**
 * The address form element is a form element that contains multiple
 * subfields for the different address parts.
 *
 * It has the following behavioral properties:
 *
 * +------------------------+-------------------------------------------------+
 * |        Behavior        | Supported                                       |
 * +------------------------+-------------------------------------------------+
 * | Border                 | true                                            |
 * | Disabled               | true                                            |
 * | Readonly               | true                                            |
 * | Min length             | false                                           |
 * | Max length             | false                                           |
 * | Required               | true (street, houseNumber, postalCode and city) |
 * | Pattern                | false                                           |
 * | FormValueChanged event | manual                                          |
 * | noBackendInteraction   | true                                            |
 * | Suggestions            | true                                            |
 * | Validations            | true                                            |
 * | Orientation            | true                                            |
 * +------------------------+-------------------------------------------------+
 *
 */
angular.module('digitalWorkplaceApp')
  .config(function (formlyConfigProvider) {

    formlyConfigProvider.setType({
      name: 'address',
      templateUrl: 'es6/guidance-mode/form-elements/address/address.formly.html',
      defaultOptions: {
        modelOptions: {
          getterSetter: true,
          allowInvalid: true
        }
      }
    });
  });
