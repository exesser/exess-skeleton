'use strict';

/**
 * Creates some text that can take a expression.
 *
 * This type is unique because it doesn't really allow for any
 * user input. It is used to display some helpful string to the user.
 *
 * It has the following behavioral properties:
 *
 * +------------------------+-----------+
 * |        Behavior        | Supported |
 * +------------------------+-----------+
 * | Border                 | false     |
 * | Disabled               | false     |
 * | Readonly               | false     |
 * | Min length             | false     |
 * | Max length             | false     |
 * | Required               | false     |
 * | Pattern                | false     |
 * | FormValueChanged event | false     |
 * | noBackendInteraction   | false     |
 * | Suggestions            | false     |
 * | Validations            | false     |
 * | Orientation            | true      |
 * +------------------------+-----------+
 *
 */
angular.module('digitalWorkplaceApp')
  .config(function (formlyConfigProvider) {
    formlyConfigProvider.setType({
      name: 'dynamic-text',
      templateUrl: 'es6/guidance-mode/form-elements/dynamic-text/dynamic-text.formly.html',
      controller: 'DynamicTextController as dynamicTextController'
    });
  });

angular.module('digitalWorkplaceApp')
  .filter('fixDecimal', function() {
    function fixDecimalFilter(input) {
      if (_.isUndefined(input) || _.isNull(input)) {
        return input;
      }

      // If you change this please also change the php DataDecoder
      let matches = input.toString().match(/[\d]+[.]{1}[\d]+[.]*/);
      if (_.isEmpty(matches)) {
        return input;
      } else {
        matches.forEach(function (match) {
          if (_.isString(match.substr(match.length - 1)) && match.substr(match.length - 1) !== '.') {
            input = _.replace(input, match, _.replace(match, '.', ','));
          }
        });

        return input;
      }
    }

    return fixDecimalFilter;
  })
  .controller('DynamicTextController', function($scope) {
    const dynamicTextController = this;

    dynamicTextController.model = $scope.model;
    dynamicTextController.unparsedFieldExpression = $scope.options.templateOptions.unparsedFieldExpression;
  });
