'use strict';

/**
 *
 * The textarea form element is a form element in which the user
 * can enter a large text.
 *
 * It has the following behavioral properties:
 *
 * +------------------------+-------------------+
 * |        Behavior        | Supported         |
 * +------------------------+-------------------+
 * | Border                 | false             |
 * | Disabled               | true              |
 * | Readonly               | true              |
 * | Min length             | true              |
 * | Max length             | true              |
 * | Required               | true              |
 * | Pattern                | true              |
 * | FormValueChanged event | modelChangedMixin |
 * | noBackendInteraction   | true              |
 * | Suggestions            | false             |
 * | Validations            | true              |
 * | Orientation            | true              |
 * +------------------------+-------------------+
 *
 * The suggestions are not provided in the usual manner and instead are
 * done via a custom datasource. The reason for this is because we need
 * to send the last three words to the back-end. Also the back-end will
 * suggest both text and a new hashtag which need to be handled differently
 * than usual.
 */
angular.module('digitalWorkplaceApp')
  .config(function (formlyConfigProvider) {

    // A textarea.
    formlyConfigProvider.setType({
      name: 'textarea',
      templateUrl: 'es6/guidance-mode/form-elements/textarea/textarea.formly.html',
      defaultOptions: {
        modelOptions: {
          getterSetter: true,
          allowInvalid: true
        }
      }
    });
  });
