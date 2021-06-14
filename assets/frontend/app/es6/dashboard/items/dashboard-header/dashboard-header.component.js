'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp:dashboardHeader component
 * @description
 * # dashboardHeader
 *
 * Creates a dashboard header block which has a solid background color
 * and icon and a label. The background color is based on which row
 * the dashboard header is rendered.
 *
 * Example usage:
 *
 * <dashboard-header label="Sales" icon="icon-wijzigen"></dashboard-header>
 *
 * Component of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .component('dashboardHeader', {
    templateUrl: 'es6/dashboard/items/dashboard-header/dashboard-header.component.html',
    bindings: {
      icon: '@',
      label: '@'
    },
    controllerAs: 'dashboardHeaderController',
    controller: _.noop
  });
