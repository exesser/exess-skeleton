'use strict';

/**
 * The wysiwyg-editor form element renders a textAngular WYSIWYG text editor
 * where PDF templates can be edited and modified.
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
 * | Required               | true              |
 * | Pattern                | false             |
 * | FormValueChanged event | modelChangedMixin |
 * | noBackendInteraction   | true              |
 * | Suggestions            | false             |
 * | Validations            | false             |
 * | Orientation            | false             |
 * +------------------------+-------------------+
 *
 */
angular.module('digitalWorkplaceApp')
  .config(function (formlyConfigProvider) {
    formlyConfigProvider.setType({
      name: 'wysiwyg-editor',
      templateUrl: 'es6/guidance-mode/form-elements/wysiwyg-editor/wysiwyg-editor.formly.html',
      defaultOptions: {
        modelOptions: {
          getterSetter: true,
          allowInvalid: true
        }
      }
    });
  });