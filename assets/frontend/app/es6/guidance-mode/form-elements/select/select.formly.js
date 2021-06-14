'use strict';

/**
 *
 * The select form element is a form element in which either one or
 * multiple options can be chosen from a list.
 *
 * The options can be overwritten using the suggestions.
 *
 * It has the following behavioral properties:
 *
 * +------------------------+------------------------------------+
 * |        Behavior        |             Supported              |
 * +------------------------+------------------------------------+
 * | Border                 | true                               |
 * | Disabled               | true                               |
 * | Readonly               | true                               |
 * | Min length             | false                              |
 * | Max length             | false                              |
 * | Required               | true                               |
 * | Pattern                | false                              |
 * | FormValueChanged event | manual                             |
 * | noBackendInteraction   | true                               |
 * | Suggestions            | used to overwrite existing options |
 * | Validations            | true                               |
 * | Orientation            | true                               |
 * +------------------------+------------------------------------+
 *
 */
angular.module('digitalWorkplaceApp')
  .config(function (formlyConfigProvider) {
    //A select input field in the styling of the style guide.
    formlyConfigProvider.setType({
      name: 'select',
      templateUrl: 'es6/guidance-mode/form-elements/select/select.formly.html',
      defaultOptions: {
        modelOptions: {
          getterSetter: true,
          allowInvalid: true
        }
      }
    });
  });
