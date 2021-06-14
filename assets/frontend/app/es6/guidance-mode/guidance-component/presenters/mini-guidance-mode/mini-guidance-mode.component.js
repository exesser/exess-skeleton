'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp:miniGuidanceMode component
 * @description
 * # miniGuidanceMode
 *
 * Creates a mini-guidance wrapper in which a guidance is loaded when
 * the miniGuidanceModeObserver's openMiniGuidanceCallback function is
 * invoked.
 *
 * <mini-guidance-mode></mini-guidance-mode>
 *
 * Component of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .component('miniGuidanceMode', {
    templateUrl: 'es6/guidance-mode/guidance-component/presenters/mini-guidance-mode/mini-guidance-mode.component.html',
    controllerAs: 'miniGuidanceModeController',
    controller: function (miniGuidanceModeObserver, sidebarObserver, SIDEBAR_ELEMENT, topActionState,
                          guidanceFormObserverFactory, $q, CONFIRM_ACTION, guidanceModeBackendState,
                          miniGuidanceModeStatus, $timeout, $scope, DEBOUNCE_TIME) {
      const miniGuidanceModeController = this;

      topActionState.setMiniGuidanceCanBeOpened(false);
      miniGuidanceModeController.guidanceData = {};
      miniGuidanceModeController.valid = true;
      miniGuidanceModeController.loading = false;

      //Deferred to confirm or reject when the user presses the respective buttons.
      let miniGuidanceDeferred;

      miniGuidanceModeController.$onInit = function () {
        const sessionStorageGuidanceData = miniGuidanceModeStatus.getGuidanceData();
        if (_.isEmpty(sessionStorageGuidanceData) === false) {
          miniGuidanceModeController.registerOpenMiniGuidance(sessionStorageGuidanceData);
        }

        miniGuidanceModeObserver.registerOpenMiniGuidanceCallback(function (guidanceData) {
          if (topActionState.miniGuidanceCanBeOpened()) {
            resetMiniGuidance();
          }

          return $timeout(function () {
            return miniGuidanceModeController.registerOpenMiniGuidance(guidanceData);
          }, 1);
        });
      };

      miniGuidanceModeController.registerOpenMiniGuidance = function (guidanceData) {
        // save the new guidance in session
        miniGuidanceModeStatus.setGuidanceData(angular.copy(guidanceData));

        miniGuidanceModeController.guidanceFormObserver = guidanceFormObserverFactory.createGuidanceFormObserver();
        miniGuidanceModeController.guidanceFormObserver.setFormValidityUpdateCallback(function (valid) {
          miniGuidanceModeController.valid = valid;
        });

        topActionState.setMiniGuidanceCanBeOpened(true);
        miniGuidanceModeController.guidanceData = guidanceData;

        //update the model saved in session
        $scope.$watch('miniGuidanceModeController.guidanceData.model', _.debounce(function (newValue, oldValue) {
          if (_.isEqual(oldValue, newValue) === false) {
            miniGuidanceModeStatus.updateModel(newValue);
          }
        }, DEBOUNCE_TIME), true);

        sidebarObserver.openSidebarElement(SIDEBAR_ELEMENT.MINI_GUIDANCE);

        miniGuidanceDeferred = $q.defer();
        return miniGuidanceDeferred.promise;
      };

      /**
       * Function that is called when the confirm button is clicked.
       * Resolves the promise with the model as argument.
       * Afterwards resets the miniGuidance.
       */
      miniGuidanceModeController.confirm = function () {
        if (miniGuidanceModeController.confirmIsDisabled()) {
          return;
        }

        miniGuidanceModeController.loading = true;

        miniGuidanceModeController.guidanceFormObserver.confirmGuidance(CONFIRM_ACTION.CONFIRM).then(function (command) {
          miniGuidanceModeController.loading = false;
          miniGuidanceDeferred.resolve(command);
          resetMiniGuidance();
        }).catch(function () {
          miniGuidanceModeController.loading = false;
        });
      };

      /**
       * Function that is called when the cancel button is clicked.
       * Rejects the promise.
       * Afterwards resets the miniGuidance.
       */
      miniGuidanceModeController.cancel = function () {
        miniGuidanceDeferred.reject();
        resetMiniGuidance();
      };

      /**
       * Function that returns true if guidanceData has been set and the mini-guidance can be opened.
       * @returns {Boolean} whether or not the mini-guidance can be opened
       */
      miniGuidanceModeController.miniGuidanceCanBeOpened = function () {
        return topActionState.miniGuidanceCanBeOpened();
      };

      function resetMiniGuidance() {
        sidebarObserver.closeAllSidebarElements();

        miniGuidanceModeStatus.setGuidanceData({});

        topActionState.setMiniGuidanceCanBeOpened(false);
        miniGuidanceModeController.guidanceData = {};
        miniGuidanceModeController.valid = true;
        miniGuidanceModeController.guidanceFormObserver = null;
      }

      miniGuidanceModeController.confirmIsDisabled = function () {
        return !miniGuidanceModeController.valid || guidanceModeBackendState.getBackendIsBusy();
      };
    }
  });
