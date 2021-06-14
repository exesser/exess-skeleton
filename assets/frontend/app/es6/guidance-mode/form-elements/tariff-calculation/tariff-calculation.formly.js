'use strict';

/**
 * The tariff calculation form element is a form element that allows you to calculate a price by setting the price margins.
 *
 * It has the following behavioral properties:
 *
 * +------------------------+-----------+
 * |        Behavior        | Supported |
 * +------------------------+-----------+
 * | Border                 | false     |
 * | Disabled               | true      |
 * | Min length             | false     |
 * | Max length             | false     |
 * | Required               | false     |
 * | Pattern                | false     |
 * | FormValueChanged event | false     |
 * | noBackendInteraction   | false     |
 * | Suggestions            | false     |
 * | Validations            | false     |
 * | Orientation            | false     |
 * +------------------------+-----------+
 *
 */
angular.module('digitalWorkplaceApp')
  .config(function (formlyConfigProvider) {

    //A price calculation directive. Generates a table where some settings can be made (green electricity, etc.) and margins can be set.
    formlyConfigProvider.setType({
      name: 'tariff-calculation',
      templateUrl: 'es6/guidance-mode/form-elements/tariff-calculation/tariff-calculation.formly.html',
      defaultOptions: {
        modelOptions: {
          getterSetter: true,
          allowInvalid: true
        }
      }
    });
  });
