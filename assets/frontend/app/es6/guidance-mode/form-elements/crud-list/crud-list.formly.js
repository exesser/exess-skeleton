'use strict';

/**
 * The crud-list form element is a type where the user can build a table of data.
 * New rows can be added and existing rows can be edited or deleted.
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
 * | Required               | false     |
 * | Pattern                | false     |
 * | FormValueChanged event | false     |
 * | noBackendInteraction   | false     |
 * | Suggestions            | false     |
 * | Validations            | false     |
 * | Orientation            | false     |
 * +------------------------+-----------+
 *
 */
angular.module('digitalWorkplaceApp')
  .config(function (formlyConfigProvider) {
    formlyConfigProvider.setType({
      name: 'crud-list',
      templateUrl: 'es6/guidance-mode/form-elements/crud-list/crud-list.formly.html',
      defaultOptions: {
        modelOptions: {
          getterSetter: true,
          allowInvalid: true
        }
      }
    });
  });
