'use strict';

// Routes for the login functionality.
angular.module('digitalWorkplaceApp')
  .config(function ($stateProvider, $urlRouterProvider) {
    $urlRouterProvider.otherwise('/start/dashboard/home/');

    $stateProvider.state('base', {
      abstract: true,
      params: {
        query: ""
      },
      views: {
        '@': {
          template: '<div ui-view></div>'
        },
        modal: {
          template: '<guidance-modal></guidance-modal>'
        },
        "mini-guidance": {
          template: '<mini-guidance-mode></mini-guidance-mode>'
        },
        logout: {
          template: '<logout location="menu"></logout>'
        }
      }
    });
  });
