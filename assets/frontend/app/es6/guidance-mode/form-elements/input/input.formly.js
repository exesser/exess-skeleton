'use strict';

/**
 *
 * The input form element is a form element in which a simple
 * textual value can be entered.
 *
 * It has the following behavioral properties:
 *
 * +------------------------+-------------------+
 * |        Behavior        | Supported         |
 * +------------------------+-------------------+
 * | Border                 | true              |
 * | Disabled               | true              |
 * | Readonly               | true              |
 * | Min length             | true              |
 * | Max length             | true              |
 * | Required               | true              |
 * | Pattern                | true              |
 * | FormValueChanged event | modelChangedMixin |
 * | noBackendInteraction   | true              |
 * | Suggestions            | true              |
 * | Validations            | true              |
 * | Orientation            | true              |
 * +------------------------+-------------------+
 *
 */
angular.module('digitalWorkplaceApp')
  .config(function (formlyConfigProvider) {

    //A simple input field in the styling of the style guide.
    formlyConfigProvider.setType({
      name: 'input',
      templateUrl: 'es6/guidance-mode/form-elements/input/input.formly.html',
      defaultOptions: {
        modelOptions: {
          getterSetter: true,
          allowInvalid: true
        }
      }
    });
  });
