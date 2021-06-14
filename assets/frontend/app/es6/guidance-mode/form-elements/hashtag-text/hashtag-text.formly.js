'use strict';

/**
 *
 * The hashtagText form element is a form element in which the user
 * can enter a large text and add hashtags to describe the large
 * text.
 *
 * It has the following behavioral properties:
 *
 * +------------------------+----------------------------------------------+
 * |        Behavior        | Supported                                    |
 * +------------------------+----------------------------------------------+
 * | Border                 | false                                        |
 * | Disabled               | true                                         |
 * | Readonly               | true                                         |
 * | Min length             | false                                        |
 * | Max length             | false                                        |
 * | Required               | true                                         |
 * | Pattern                | false                                        |
 * | FormValueChanged event | manual                                       |
 * | noBackendInteraction   | true                                         |
 * | Suggestions (custom)   | used to provide hashtags and autocompletions |
 * | Validations            | true                                         |
 * | Orientation            | true                                         |
 * +------------------------+----------------------------------------------+
 *
 * The suggestions are not provided in the usual manner and instead are
 * done via a custom datasource. The reason for this is because we need
 * to send the last three words to the back-end. Also the back-end will
 * suggest both text and a new hashtag which need to be handled differently
 * than usual.
 */
angular.module('digitalWorkplaceApp')
  .config(function (formlyConfigProvider) {

    // A textarea, a list of hashtags and an input to add hashtags.
    formlyConfigProvider.setType({
      name: 'hashtagText',
      templateUrl: 'es6/guidance-mode/form-elements/hashtag-text/hashtag-text.formly.html',
      defaultOptions: {
        modelOptions: {
          getterSetter: true,
          allowInvalid: true
        }
      }
    });
  });
