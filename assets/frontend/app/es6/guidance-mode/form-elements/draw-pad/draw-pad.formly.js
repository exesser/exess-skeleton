'use strict';

/**
 * The draw-pad form element is a type where we can create draw.
 *
 * It has the following behavioral properties:
 *
 * +------------------------+-----------------------------------------------+
 * |        Behavior        | Supported                                     |
 * +------------------------+-----------------------------------------------+
 * | Border                 | false                                         |
 * | Disabled               | true                                          |
 * | Readonly               | true                                          |
 * | Min length             | false                                         |
 * | Max length             | false                                         |
 * | Required               | true                                          |
 * | Pattern                | false                                         |
 * | FormValueChanged event | true                                          |
 * | noBackendInteraction   | true                                          |
 * | Suggestions            | false                                         |
 * | Validations            | false                                         |
 * | Orientation            | true                                          |
 * +------------------------+-----------------------------------------------+
 *
 */
angular.module('digitalWorkplaceApp')
  .config(function (formlyConfigProvider) {

    formlyConfigProvider.setType({
      name: 'draw-pad',
      templateUrl: 'es6/guidance-mode/form-elements/draw-pad/draw-pad.formly.html',
      defaultOptions: {
        modelOptions: {
          getterSetter: true,
          allowInvalid: true
        }
      }
    });
  });
