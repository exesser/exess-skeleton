'use strict';

/**
 * Creates some text with link that can take a expression.
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
 * | Suggestions            | false     |
 * | Validations            | false     |
 * | Orientation            | true      |
 * +------------------------+-----------+
 *
 */
angular.module('digitalWorkplaceApp')
  .config(function (formlyConfigProvider) {
    formlyConfigProvider.setType({
      name: 'dynamic-text-with-action',
      templateUrl: 'es6/guidance-mode/form-elements/dynamic-text-with-action/dynamic-text-with-action.formly.html',
      controller: 'DynamicTextWithActionController as dynamicTextWithActionController'
    });
  });

angular.module('digitalWorkplaceApp')
  .controller('DynamicTextWithActionController', function($scope, validationMixin, actionDatasource) {
    const dynamicTextWithActionController = this;

    dynamicTextWithActionController.model = $scope.model;
    dynamicTextWithActionController.unparsedFieldExpression = $scope.options.templateOptions.unparsedFieldExpression;

      dynamicTextWithActionController.actionClicked = function (newWindow) {
      actionDatasource.performAndHandle($scope.options.templateOptions.action, newWindow);
    };
  });
