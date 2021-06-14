'use strict';

/**
 * @ngdoc component
 * @name digitalWorkplaceApp.component focusMode
 * @description
 * # focusMode
 *
 * Creates a focus mode which is a view which renders on top of the
 * normal view. A focus mode has a back-arrow, title, top-actions
 * and a top-search, it also knows where to render flash messages.
 *
 * For example:
 *
 * <focus-mode-content
 *    back-arrow-clicked="createLeadController.backArrowClicked()"
 *    show-top-arrow="true"
 *    top-arrow-clicked="createLeadController.topArrowClicked()">
 *    <h1>This is the content of the controller</h1>
 *    <p>The content is transcluded in the correct position</p>
 * </focus-mode-content>
 *
 * Component of the digital workplace.
 */
angular.module('digitalWorkplaceApp')
  .component('focusModeContent', {
    transclude: true,
    templateUrl: 'es6/core/focus-mode-content/focus-mode-content.component.html',
    bindings: {
      title: '@',
      backArrowClicked: '&',
      showTopArrow: '@',
      topArrowClicked: '&'
    },
    controllerAs: 'focusModeContentController',
    controller: function (slideAnimation, $rootScope) {
      const focusModeContentController = this;

      focusModeContentController.$onInit = function () {
        slideAnimation.open();
      };

      focusModeContentController.onBackArrowClicked = function () {
        $rootScope.$on('$stateChangeSuccess', function () { //eslint-disable-line angular/on-watch
          slideAnimation.close();
        });

        focusModeContentController.backArrowClicked();
      };

      focusModeContentController.onTopArrowClicked = function () {
        $rootScope.$on('$stateChangeSuccess', function () { //eslint-disable-line angular/on-watch
          slideAnimation.close();
        });

        focusModeContentController.topArrowClicked();
      };
    }
  });
