'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.factory:expressionTransformer factory
 * @description
 *
 * Factory that converts a exess expression to an Angular expression.
 * This is necessary because we sometimes want to place expressions in directives without evaluating them,
 * for example when providing a dynamic title based on user input. The directive is filled with the expression and is responsible for interpolating this
 * expression. If we put in a regular Angular expression here it is evaluated too early and results in an empty string.
 *
 * This factory returns a function that takes an exess expression as input:
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
  .factory('expressionTransformer', function() {
    return function (expression) {
      expression = _.replace(expression, /{%/g, '{{');
      expression = _.replace(expression, /%}/g, '}}');
      return _.replace(expression, /\|/g, "_I_");
    };
  });
