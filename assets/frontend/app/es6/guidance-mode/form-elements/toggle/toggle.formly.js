'use strict';

/**
 * The toggle form element is a form element which renders a toggle
 * that can take either a true or a false value.
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
      name: 'toggle',
      templateUrl: 'es6/guidance-mode/form-elements/toggle/toggle.formly.html',
      defaultOptions: {
        modelOptions: {
          getterSetter: true,
          allowInvalid: true
        }
      }
    });
  });
