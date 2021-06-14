'use strict';

/**
 * The select-with-search form element is a type where the
 * can select values from a modal window.
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
 * | Orientation            | true  (does not look OK in 'label-left' mode) |
 * +------------------------+-----------------------------------------------+
 *
 */
angular.module('digitalWorkplaceApp')
  .config(function (formlyConfigProvider) {

    formlyConfigProvider.setType({
      name: 'select-with-search',
      templateUrl: 'es6/guidance-mode/form-elements/select-with-search/select-with-search.formly.html',
      defaultOptions: {
        modelOptions: {
          getterSetter: true,
          allowInvalid: true
        }
      }
    });
  });
