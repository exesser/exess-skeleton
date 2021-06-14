'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.factory:topActionState factory
 * @description
 * # topActionState
 *
 * in this factory we are keeping the state(canBeOpened: true|false)
 * of the top-action modules (plus-menu, filters and guidance-mode)
 */
angular.module('digitalWorkplaceApp')
  .factory('topActionState', function() {

    let filtersCanBeOpenedState = false;
    let plusMenuCanBeOpenedState = false;
    let miniGuidanceCanBeOpenedState = false;
    let primaryButtonData = null;

    return {
      filtersCanBeOpened,
      setFiltersCanBeOpened,

      plusMenuCanBeOpened,
      setPlusMenuCanBeOpened,

      miniGuidanceCanBeOpened,
      setMiniGuidanceCanBeOpened,

      getPrimaryButtonData,
      setPrimaryButtonData,
      resetPrimaryButtonData
    };

    function filtersCanBeOpened() {
      return filtersCanBeOpenedState;
    }

    function setFiltersCanBeOpened(canBeOpened) {
      filtersCanBeOpenedState = canBeOpened;
    }

    function plusMenuCanBeOpened() {
      return plusMenuCanBeOpenedState;
    }

    function setPlusMenuCanBeOpened(canBeOpened) {
      plusMenuCanBeOpenedState = canBeOpened;
    }

    function miniGuidanceCanBeOpened() {
      return miniGuidanceCanBeOpenedState;
    }

    function setMiniGuidanceCanBeOpened(canBeOpened) {
      miniGuidanceCanBeOpenedState = canBeOpened;
    }

    function setPrimaryButtonData(data) {
      primaryButtonData = data;
    }

    function resetPrimaryButtonData() {
      primaryButtonData = null;
    }

    function getPrimaryButtonData() {
      return primaryButtonData;
    }
  });
