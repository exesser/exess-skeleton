'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.factory:miniGuidanceModeStatus factory
 * @description
 * # miniGuidanceModeStatus
 *
 * The miniGuidanceModeStatus factory is responsible for saving in sessionStorage the last open mini guidance
 */
angular.module('digitalWorkplaceApp')
  .constant('MINI_GUIDANCE_SESSION_KEY', 'MINI_GUIDANCE_SESSION_KEY')
  .factory('miniGuidanceModeStatus', function ($window, MINI_GUIDANCE_SESSION_KEY) {

    let guidanceData = $window.sessionStorage.getItem(MINI_GUIDANCE_SESSION_KEY);
    if (_.isNull(guidanceData)) {
      guidanceData = {};
    } else {
      guidanceData = angular.fromJson(guidanceData);
    }

    return {
      getGuidanceData,
      setGuidanceData,
      updateModel
    };

    /**
     * Save the guidance data.
     * @param newGuidanceData
     */
    function setGuidanceData(newGuidanceData) {
      guidanceData = newGuidanceData;
      saveGuidanceData();
    }

    /**
     * Update the model.
     * @param model
     */
    function updateModel(model) {
      if (_.isEmpty(guidanceData)) {
        return;
      }

      guidanceData.model = model;
      saveGuidanceData();
    }

    /**
     * Get the stored guidance data
     */
    function getGuidanceData() {
      return guidanceData;
    }

    /**
     * Save the guidance data in session
     */
    function saveGuidanceData() {
      $window.sessionStorage.setItem(MINI_GUIDANCE_SESSION_KEY, angular.toJson(guidanceData));
    }
  });