'use strict';

/**
 * The image form element is a type that renders a image with a text and a button.
 *
 * It has the following behavioral properties:
 *
 * +------------------------+-------------------+
 * |        Behavior        | Supported         |
 * +------------------------+-------------------+
 * | Border                 | false             |
 * | Disabled               | false             |
 * | Readonly               | false             |
 * | Min length             | false             |
 * | Max length             | false             |
 * | Required               | false             |
 * | Pattern                | false             |
 * | FormValueChanged event | false             |
 * | noBackendInteraction   | false             |
 * | Suggestions            | false             |
 * | Validations            | false             |
 * | Orientation            | false             |
 * +------------------------+-------------------+
 *
 */
angular.module('digitalWorkplaceApp')
  .config(function (formlyConfigProvider) {

    formlyConfigProvider.setType({
      name: 'image',
      templateUrl: 'es6/guidance-mode/form-elements/image/image.formly.html',
      defaultOptions: {
        modelOptions: {
          getterSetter: true,
          allowInvalid: true
        }
      }
    });
  });
