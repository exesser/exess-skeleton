'use strict';

/**
 *
 * The large-input form element is a form element in which a simple
 * textual value can be entered.
 *
 * The input is displayed in a larger textfield than a regular input,
 * and it grows in width as you type along.
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
      name: 'large-input',
      templateUrl: 'es6/guidance-mode/form-elements/large-input/large-input.formly.html',
      defaultOptions: {
        modelOptions: {
          getterSetter: true,
          allowInvalid: true
        }
      }
    });
  });
