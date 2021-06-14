'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.dwpCtrlClick directive
 * @description
 * # dwpCtrlClick
 *
 * Directive of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .directive('dwpCtrlClick', function ($parse) {
    return function (scope, element, attrs) {
      let fn = $parse(attrs.dwpCtrlClick);
      element.bind('contextmenu', function (event) {
        if (event.ctrlKey) {
          scope.$apply(function () {
            event.preventDefault();
            fn(scope, { $event: event });
          });
        }
      });
    };
  });
