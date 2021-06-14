'use strict';

/**
 * The upload form element is a form element in which a file of a
 * specified type can be uploaded.
 *
 * It has the following behavioral properties:
 *
 * +------------------------+-----------+
 * |        Behavior        | Supported |
 * +------------------------+-----------+
 * | Border                 | false     |
 * | Disabled               | true      |
 * | Readonly               | true      |
 * | Min length             | false     |
 * | Max length             | false     |
 * | Required               | true      |
 * | Pattern                | false     |
 * | FormValueChanged event | false     |
 * | noBackendInteraction   | true      |
 * | Suggestions            | false     |
 * | Validations            | true      |
 * | Orientation            | true      |
 * +------------------------+-----------+
 *
 */
angular.module('digitalWorkplaceApp')
  .config(function (formlyConfigProvider) {
    formlyConfigProvider.setType({
      name: 'upload',
      templateUrl: 'es6/guidance-mode/form-elements/upload/upload.formly.html',
      defaultOptions: {
        modelOptions: {
          getterSetter: true,
          allowInvalid: true
        }
      }
    });
  });
