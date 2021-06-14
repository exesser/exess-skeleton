'use strict';

/**
 * The datepicker form element renders a pikaday datepicker in which
 * you can choose a date or manually type it.
 *
 * It has the following behavioral properties:
 *
 * +------------------------+-----------+
 * |        Behavior        | Supported |
 * +------------------------+-----------+
 * | Border                 | true      |
 * | Disabled               | true      |
 * | Readonly               | true      |
 * | Min length             | false     |
 * | Max length             | false     |
 * | Required               | true      |
 * | Pattern                | false     |
 * | FormValueChanged event | manual    |
 * | noBackendInteraction   | true      |
 * | Suggestions            | true      |
 * | Validations            | true      |
 * | Orientation            | true      |
 * +------------------------+-----------+
 *
 */
angular.module('digitalWorkplaceApp')
  .config(function (formlyConfigProvider) {

    //A directive that creates a pikaday datepicker as specified in the style guide.
    formlyConfigProvider.setType({
      name: 'datepicker',
      templateUrl: 'es6/guidance-mode/form-elements/datepicker/datepicker.formly.html',
      defaultOptions: {
        modelOptions: {
          getterSetter: true,
          allowInvalid: true
        }
      }
    });
  });
