'use strict';

/**
 * The checkable-icon form element is a type that renders an icon
 * which toggles a boolean value when you click on it.
 *
 * It has the following behavioral properties:
 *
 * +------------------------+------------------------------------+
 * |        Behavior        |             Supported              |
 * +------------------------+------------------------------------+
 * | Border                 | false                              |
 * | Disabled               | true, no visual indication however |
 * | Readonly               | true, no visual indication however |
 * | Min length             | false                              |
 * | Max length             | false                              |
 * | Required               | false                              |
 * | Pattern                | false                              |
 * | FormValueChanged event | modelChangedMixin                  |
 * | noBackendInteraction   | true                               |
 * | Suggestions            | false                              |
 * | Validations            | false                              |
 * | Orientation            | true                               |
 * +------------------------+------------------------------------+
 *
 */
angular.module('digitalWorkplaceApp')
  .config(function (formlyConfigProvider) {

    formlyConfigProvider.setType({
      name: 'checkable-icon',
      templateUrl: 'es6/guidance-mode/form-elements/checkable-icon/checkable-icon.formly.html',
      defaultOptions: {
        modelOptions: {
          getterSetter: true,
          allowInvalid: true
        }
      }
    });
  });
