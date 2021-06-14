'use strict';

// Routes for the dashboards functionality.
angular.module('digitalWorkplaceApp')
  .config(function ($stateProvider) {
    $stateProvider.state('dashboard', {
      parent: 'base',
      url: '/:mainMenuKey/dashboard/:dashboardId/:recordId?query&modelKey&recordType',
      views: {
        'main-content@': {
          template: '<dashboard></dashboard>'
        },
        "plus-menu@": {
          templateUrl: 'es6/sidebar/plus-menu/plus-menu.controller.html',
          controller: 'PlusMenuController as plusMenuController'
        },
        "filters@": {
          templateUrl: 'es6/sidebar/filters/filters.controller.html',
          controller: 'FiltersController as filtersController'
        }
      }
    });
  });


