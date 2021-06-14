'use strict';

/**
 *
 * The radio form element is a form element which represents one
 * single option in a radio group.
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
      name: 'radio',
      templateUrl: 'es6/guidance-mode/form-elements/radio/radio.formly.html',
      defaultOptions: {
        modelOptions: {
          getterSetter: true,
          allowInvalid: true
        }
      }
    });
  });
