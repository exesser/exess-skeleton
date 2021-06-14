'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.dwpApp component
 * @description
 * # dwpApp
 *
 * This component represents the Digital Workplace application.
 * It boots up the application and takes 'options' to configure
 * the application.
 *
 * Example usage:
 *
 * <dwp-app
 *   options="{
 *     initialPage: {       // The initial page show, defaults to #/start/dashboard/home
 *       linkTo: 'guidance-mode',
 *       params: {
 *         flowId: 'CUPQ'
 *       }
 *     }
 *   }">
 * </dwp-app>
 *
 * Component of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .component('dwpApp', {
    templateUrl: 'es6/base/dwp-app.component.html',
    bindings: {
      options: '<?'
    },
    controllerAs: 'dwpAppController',
    controller: function($state, sidebarState, GTM_ENABLED) {
      const dwpAppController = this;

      const defaultOptions = {
        initialPage: undefined
      };

      dwpAppController.$onInit = function() {
        dwpAppController.options = _.merge({}, defaultOptions, dwpAppController.options);

        if (_.isUndefined(dwpAppController.options.initialPage) === false) {
          const initialPage = dwpAppController.options.initialPage;

          // Reload true so navigating to the same state twice is not a problem.
          $state.go(initialPage.linkTo, initialPage.params, { reload: true });
        }
      };

      dwpAppController.showSidebar = function() {
        return sidebarState.getShowSidebar();
      };

      dwpAppController.pushGTM = function() {
        return GTM_ENABLED;
      };
    }
  });
