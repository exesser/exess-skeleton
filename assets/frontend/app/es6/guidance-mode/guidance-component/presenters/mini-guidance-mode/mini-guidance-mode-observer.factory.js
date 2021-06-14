'use strict';

angular.module('digitalWorkplaceApp')
  .factory('miniGuidanceModeObserver', function() {

    let openMiniGuidanceCallback = _.noop;

    return {
      openMiniGuidance,
      registerOpenMiniGuidanceCallback
    };

    /**
     * Opens a miniGuidance from the given guidanceData.
     * @returns {Promise} a promise that can be resolved or rejected.
     */
    function openMiniGuidance(guidanceData) {
      return openMiniGuidanceCallback(guidanceData);
    }

    /**
     * Register a callback that is invoked when the openMiniGuidance function is called.
     * @param callback function
     */
    function registerOpenMiniGuidanceCallback(callback) {
      openMiniGuidanceCallback = callback;
    }
  });
