'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.flow:progressBar component
 * @description
 * # progressBar
 *
 * Component to show the progress indicator.
 * The data needed to render the progress bar is delivered from the progressBarObserver.
 *
 * <progress-bar></progress-bar>
 *
 * Component of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .component('progressBar', {
    templateUrl: 'es6/guidance-mode/progress-bar/progress-bar.component.html',
    controllerAs: 'progressBarController',
    bindings: {
      'params': '<'
    },
    controller: function (progressBarObserver) {
      const progressBarController = this;

      progressBarController.image = _.get(progressBarController, 'params.image', null);

      progressBarObserver.progressMetadata = {};
      progressBarObserver.registerProgressMetadataCallback(function (progressMetadata) {
        progressBarController.progressMetadata = progressMetadata;
      });

      progressBarController.click = (step) => {
        if (step.canBeActivated && step.active === false) {
          progressBarObserver.clicked(step.key_c);
        }
      };
    }
  });
