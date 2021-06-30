'use strict';

angular.module('digitalWorkplaceApp')
  .component('guidanceModal', {
    templateUrl: 'es6/guidance-mode/guidance-component/presenters/modal/guidance-modal.component.html',
    controllerAs: 'guidanceModalController',
    controller: function (guidanceModalObserver, guidanceFormObserverFactory, $q, CONFIRM_ACTION, commandHandler,
                          $timeout, guidanceGuardian, guidanceModeBackendState, actionDatasource, $log, hotkeys) {

      const guidanceModalController = this;

      guidanceModalController.isOpen = false;
      guidanceModalController.modalData = {};
      guidanceModalController.originalModalData = {};
      guidanceModalController.confirmAction = null;
      guidanceModalController.valid = true;
      guidanceModalController.loading = false;

      hotkeys.add({
        combo: 'esc',
        description: 'Close the modal.',
        allowIn: ['INPUT', 'SELECT', 'TEXTAREA'],
        callback: function () {
          guidanceModalController.close();
        }
      });

      //Deferred to confirm or reject when the user presses the respective buttons.
      let modalDeferred;

      /**
       * Calls the guidanceModalObserver's registerOpenModalCallback function.
       * The callback function sets the isOpen property to true so the modal shows up and sets the modalData on the controller.
       * @returns {Promise} promise that can be resolved or rejected when the modal lifecycle is over.
       */
      guidanceModalObserver.registerOpenModalCallback(function (modalData, confirmAction = CONFIRM_ACTION.CONFIRM) {
        guidanceModalController.guidanceFormObserver = guidanceFormObserverFactory.createGuidanceFormObserver();
        guidanceModalController.guidanceFormObserver.setFormValidityUpdateCallback(function (valid) {
          guidanceModalController.valid = valid;
        });

        guidanceModalController.isOpen = true;
        guidanceModalController.modalData = modalData;
        guidanceModalController.originalModalData = angular.copy(modalData);
        guidanceModalController.confirmAction = confirmAction;

        modalDeferred = $q.defer();
        return modalDeferred.promise;
      });

      /**
       * Calls the guidanceModalObserver's registerResetModalCallback function.
       * The callback function reset the modal properties (isOpen, modalData,
       * confirmAction, valid and guidanceFormObserver).
       */
      guidanceModalObserver.registerResetModalCallback(resetModal);

      /**
       * Function that is called when the confirm button is clicked.
       * Resolves the promise with the model as argument.
       * Afterwards resets the modal.
       */
      guidanceModalController.confirm = function () {
        if (guidanceModalController.confirmIsDisabled()) {
          return;
        }

        guidanceModalController.loading = true;

        guidanceModalController.guidanceFormObserver.confirmGuidance(guidanceModalController.confirmAction).then(function (command) {
          resetModal();

          /*
           When the command is another 'openModal' they will interfere
           with each other. Therefore we handle the command on the
           next event loop. This gives the old modal time to unregister
           itself.
           */
          $timeout(function () {
            modalDeferred.resolve(command);
          }, 1);
        }).catch(function () {
          guidanceModalController.loading = false;
        });

      };

      /**
       * Function that is called when the cancel button is clicked.
       */
      guidanceModalController.cancel = function () {
        if (_.has(guidanceModalController.originalModalData, 'cancelCommandKey') === false) {
          $log.error('Action is not configured correctly! When you have a `cancelLabel` you also need a `cancelCommandKey`.');
          return;
        }

        actionDatasource.performAndHandle({ "id": guidanceModalController.originalModalData.cancelCommandKey });
        guidanceModalController.close();
      };

      /**
       * Function that is called when the x button is clicked.
       */
      guidanceModalController.close = function () {
        if (_.has(guidanceModalController.originalModalData, 'closeCommandKey')) {
          actionDatasource.performAndHandle({ "id": guidanceModalController.originalModalData.closeCommandKey });
        }
        /*
         Close any guard caused by this modals 'guidanceFormObserver'.
         Otherwise the user will get pop-ups if he tries to navigate
         somewhere.

         Note that this must happen now before the 'resetmodal'.
         Otherwise the 'guidanceFormObserver' will be null.
         */
        guidanceGuardian.endGuard(guidanceModalController.guidanceFormObserver);

        /*
         Reject the promise that started this modal, so the creator
         knows this modal was did not provide a value.
         */
        modalDeferred.reject();

        // Reset the modal now so it can be reused.
        resetModal();
      };

      /**
       * Function that returns true if there is a warning in the modal.
       * @returns {Boolean} whether or not there is a warning.
       */
      guidanceModalController.hasWarning = function () {
        return _.isEmpty(guidanceModalController.originalModalData.warningText) === false;
      };

      guidanceModalController.getExtraAction = function () {
        return _.get(guidanceModalController, "originalModalData.extraActions", []);
      };

      guidanceModalController.performAction = function (command) {
        commandHandler.handle(command);
      };

      function resetModal() {
        guidanceModalController.isOpen = false;
        guidanceModalController.modalData = {};
        guidanceModalController.originalModalData = {};
        guidanceModalController.confirmAction = null;
        guidanceModalController.valid = true;
        guidanceModalController.guidanceFormObserver = null;
        guidanceModalController.loading = false;
      }

      guidanceModalController.confirmIsDisabled = function () {
        return !guidanceModalController.valid || guidanceModeBackendState.getBackendIsBusy();
      };

      guidanceModalController.hideConfirmButton = function () {
        return _.isEmpty(guidanceModalController.originalModalData.confirmLabel) || guidanceModalController.loading;
      };

      guidanceModalController.hideCancelButton = function () {
        return _.has(guidanceModalController.originalModalData, 'cancelLabel') === false || guidanceModalController.loading;
      };
    }
  });
