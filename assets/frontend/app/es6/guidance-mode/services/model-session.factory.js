'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.factory:modelSession factory
 * @description
 * # modelSession
 *
 * The modelSession factory is responsible for saving in sessionStorage the model that is sent to guidance.
 */
angular.module('digitalWorkplaceApp')
  .factory('modelSession', function($window) {

    const sessionKey = "MODEL_KEY";

    let modelStore = $window.sessionStorage.getItem(sessionKey);

    if (_.isNull(modelStore)) {
      modelStore = {};
    } else {
      modelStore = angular.fromJson(modelStore);
    }

    return {
      setModel,
      getModel
    };

    /**
     * Save the model.
     * @param modelKey
     * @param model
     */
    function setModel(modelKey, model) {
      modelStore[modelKey] = model;
      $window.sessionStorage.setItem(sessionKey, angular.toJson(modelStore));
    }

    /**
     * Get the model
     * @param modelKey
     */
    function getModel(modelKey) {
      return _.get(modelStore, modelKey, {});
    }
  });
