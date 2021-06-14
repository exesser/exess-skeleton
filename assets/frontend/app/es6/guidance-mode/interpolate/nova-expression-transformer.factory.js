'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.factory:novaExpressionTransformer factory
 * @description
 *
 * Factory that converts a nova expression to an Angular expression.
 * This is necessary because we sometimes want to place expressions in directives without evaluating them,
 * for example when providing a dynamic title based on user input. The directive is filled with the expression and is responsible for interpolating this
 * expression. If we put in a regular Angular expression here it is evaluated too early and results in an empty string.
 *
 * This factory returns a function that takes a Nova-expression as input:
 *
 * {% first_name %} {% last_name %}
 *
 * And returns an angular expression as output:
 *
 * {{ first_name }} {{ last_name }}
 *
 * Factory of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .factory('novaExpressionTransformer', function() {
    return function (novaExpression) {
      novaExpression = _.replace(novaExpression, /{%/g, '{{');
      novaExpression = _.replace(novaExpression, /%}/g, '}}');
      return _.replace(novaExpression, /\|/g, "_I_");
    };
  });
