'use strict';

angular.module('digitalWorkplaceApp')
  .config(function ($stateProvider) {
    $stateProvider.state('focus-mode', {
      parent: 'base',
      url: '/:mainMenuKey/focus-mode/:focusModeId/:recordId?query&modelKey',
      views: {
        'focus-mode@': {
          template: '<focus-mode></focus-mode>'
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
