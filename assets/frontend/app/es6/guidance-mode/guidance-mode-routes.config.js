'use strict';

/**
 *  The kitchen sink is a playground to test the guidance-flows in.
 *  Here you can easily mock server responses by just hand copy
 *  pasting them in.
 */
angular.module('digitalWorkplaceApp')
  .config(function ($stateProvider) {
    $stateProvider.state('kitchen-sink', {
      parent: 'base',
      url: '/kitchen-sink?page',
      views: {
        'focus-mode@': {
          template: '<kitchen-sink></kitchen-sink>'
        }
      },
      onExit: function(primaryButtonObserver) {
        //Remove the primary button data if this isn't already done. Could happen when you click on a link from a guidance mode.
        primaryButtonObserver.resetPrimaryButtonData();
      }
    });

    $stateProvider.state('guidance-mode', {
      parent: 'base',
      url: '/guidance-mode/:flowId/:recordId?recordType&flowAction&modelKey',
      views: {
        'focus-mode@': {
          template: '<large-guidance-mode></large-guidance-mode>'
        }
      },
      onExit: function(primaryButtonObserver) {
        //Remove the primary button data if this isn't already done. Could happen when you click on a link from a guidance mode.
        primaryButtonObserver.resetPrimaryButtonData();
      }
    });
  });
