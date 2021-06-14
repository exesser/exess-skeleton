'use strict';

/**
 *
 * The range-input form element is a form element which renders a
 * slider for a numeric value.
 *
 * It has the following behavioral properties:
 *
 * +------------------------+-------------------+
 * |        Behavior        | Supported         |
 * +------------------------+-------------------+
 * | Border                 | false             |
 * | Disabled               | true              |
 * | Readonly               | true              |
 * | Min length             | false             |
 * | Max length             | false             |
 * | Required               | false             |
 * | Pattern                | false             |
 * | FormValueChanged event | modelChangedMixin |
 * | noBackendInteraction   | true              |
 * | Suggestions            | false             |
 * | Validations            | true              |
 * | Orientation            | true              |
 * +------------------------+-------------------+
 *
 */
angular.module('digitalWorkplaceApp')
  .config(function (formlyConfigProvider) {

    formlyConfigProvider.setType({
      name: 'range',
      templateUrl: 'es6/guidance-mode/form-elements/range-input/range-input.formly.html',
      defaultOptions: {
        modelOptions: {
          getterSetter: true,
          allowInvalid: true
        }
      }
    });
  });
