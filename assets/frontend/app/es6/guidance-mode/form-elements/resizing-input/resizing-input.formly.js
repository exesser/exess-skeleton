'use strict';

/**
 *
 * The resizing-input form element is a form element which grows
 * in width as you type along.
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

    //An input element that grows/shrinks when necessary.
    formlyConfigProvider.setType({
      name: 'resizing-input',
      templateUrl: 'es6/guidance-mode/form-elements/resizing-input/resizing-input.formly.html',
      defaultOptions: {
        modelOptions: {
          getterSetter: true,
          allowInvalid: true
        }
      }
    });
  });
