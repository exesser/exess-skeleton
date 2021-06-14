'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp:largeGuidanceMode component
 * @description
 * # largeGuidanceMode
 *
 * The largeGuidanceMode component is responsible for rendering the large (full screen) guidance modes.
 *
 * Example usage:
 *
 * <large-guidance-mode
 *   flow-id="CreateLead"
 *   record-id="1337"
 *   guidance-mode="guidanceMode">
 * </large-guidance-mode>
 *
 * Component of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .component('largeGuidanceMode', {
    templateUrl: 'es6/guidance-mode/guidance-component/presenters/large-guidance-mode/large-guidance-mode.component.html',
    controllerAs: 'largeGuidanceModeController',
    controller: function(guidanceFormObserverFactory, $timeout, $stateParams, $scope, guidanceModeDatasource,
                         translateFilter, commandHandler, previousState, primaryButtonObserver, CONFIRM_ACTION) {
      const largeGuidanceModeController = this;

      largeGuidanceModeController.$onInit = function() {
        largeGuidanceModeController.guidanceFormObserver = guidanceFormObserverFactory.createGuidanceFormObserver();

        largeGuidanceModeController.loading = true;
        largeGuidanceModeController.valid = true;

        $timeout(function() {
          largeGuidanceModeController.flowId = $stateParams.flowId;
          largeGuidanceModeController.recordId = $stateParams.recordId;

          guidanceModeDatasource.get($stateParams).then((guidanceMode) => {
            largeGuidanceModeController.loading = false;

            largeGuidanceModeController.guidanceMode = guidanceMode;

            setPrimaryButtonData();
          });
        }, 100);

        const deregisterStepChangeOccurredCallback = largeGuidanceModeController.guidanceFormObserver.addStepChangeOccurredCallback(function() {
          setPrimaryButtonData();
        });

        //When the large-guidance mode controller is destroyed, deregister the step change watch
        $scope.$on("$destroy", function() {
          deregisterStepChangeOccurredCallback();
        });

        largeGuidanceModeController.guidanceFormObserver.setFormValidityUpdateCallback(function (valid) {
          largeGuidanceModeController.valid = valid;

          setPrimaryButtonData();
        });

        primaryButtonObserver.setPrimaryButtonClickedCallback(function() {
          if (largeGuidanceModeController.valid) {
            if (largeGuidanceModeController.hasNextStep()) {
              largeGuidanceModeController.guidanceFormObserver.requestNextStep();
            } else {
              largeGuidanceModeController.loading = true;

              largeGuidanceModeController.guidanceFormObserver.confirmGuidance(CONFIRM_ACTION.CONFIRM).then(function(command) {
                commandHandler.handle(command);
                largeGuidanceModeController.loading = false;
              }).catch(function() {
                largeGuidanceModeController.loading = false;
              });
            }
          }
        });
      };

      largeGuidanceModeController.backArrowClicked = function () {
        primaryButtonObserver.resetPrimaryButtonData();
        $timeout(function () {
          previousState.navigateTo();
        }, 500);
      };

      largeGuidanceModeController.hasNextStep = function () {
        return !largeGuidanceModeController.guidanceMode.step.willSave;
      };

      largeGuidanceModeController.primaryButtonTitle = function () {
        const buttonTitle = largeGuidanceModeController.hasNextStep() ? 'NEXT' : 'CONFIRM';
        return translateFilter(buttonTitle);
      };

      function setPrimaryButtonData() {
        primaryButtonObserver.setPrimaryButtonData({
          title: largeGuidanceModeController.primaryButtonTitle(),
          disabled: !largeGuidanceModeController.valid
        });
      }
    }
  });
