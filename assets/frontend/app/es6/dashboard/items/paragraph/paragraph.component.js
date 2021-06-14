"use strict";

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.paragraph component
 * @description
 * # paragraph
 *
 * The paragraph component is a very simple component that places some text in a paragraph HTML element.
 *
 * Example usage:
 * <paragraph text="Hello World"></paragraph>
 *
 * Will result in the following HTML:
 *
 * <paragraph text="Hello World">
 *   <p>Hello World</p>
 * </paragraph>
 *
 * Component of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .component('paragraph', {
    templateUrl: 'es6/dashboard/items/paragraph/paragraph.component.html',
    bindings: {
      text: '@'
    },
    controllerAs: 'paragraphController',
    controller: _.noop
  });
