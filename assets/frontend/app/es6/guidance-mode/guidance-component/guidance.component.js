'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.guidance component
 * @description
 * # guidance
 *
 * The guidance component retrieves a guidance from the back-end and renders
 * the grid it specifies.
 *
 * It interacts with the outside world using the guidanceFormObserver.
 *
 * The purpose of these guidances is to render some forms, let the user
 * edit the fields inside it, browse through the steps of the guidance
 * if applicable and eventually confirm the guidance.
 *
 * For example:
 *
 * <guidance
 *   guidance-data="controller.modalData"
 *   guidance-form-observer="controller.guidanceFormObserver"
 *   flow-id="{{ controller.modalData.flowId }}"
 *   record-id="{{ controller.modalData.recordId }}"
 *   inform-progress-bar-observer="true"
 *   enable-navigate-away-guard="true">
 *  </guidance>
 *
 * Component of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
/*
 Confirm actions are posted to the backend when confirming a
 guidance mode. The backend then has an idea how to handle the
 subsequent action.
 */
  .constant('CONFIRM_ACTION', {
    'CONFIRM': 'CONFIRM', //For large guidance modes
    'CONFIRM_CREATE_LIST_ROW': 'CONFIRM-CREATE-LIST-ROW' //For create operations in crud lists, this should not save any data on the backend
  })
  /*
   Action events are posted to the backend when we change a field or step.
   */
  .constant('ACTION_EVENT', {
    'CHANGED': 'CHANGED',
    'NEXT_STEP': 'NEXT-STEP',
    'NEXT_STEP_FORCED': 'NEXT-STEP-FORCED'
  })
  .component('guidance', {
    templateUrl: 'es6/guidance-mode/guidance-component/guidance.component.html',
    bindings: {
      //The guidanceFormObserver bound to the lifecycle of this guidance component.
      guidanceFormObserver: "<",

      // The current's step guidance data. Two-way binding so we always have access to the current step in the components that use the guidance component.
      guidanceData: "=",

      //The id of the flow
      flowId: "@",

      //An optional id that refers to a specific object in the backend.
      recordId: "@",

      /*
       We only trigger setProgressMetadata on the progressBarObserver
       if informProgressBarObserver is set to true.This is to prevent
       interference between normal guidances and modals/mini-guidances
       that we open inside it. The latter two are limited to one single
       step per guidance so a progress bar makes no sense there anyway.
       */
      informProgressBarObserver: "<",

      /*
       Whether or not the backend saves the current progress of the guidance.
       */
      enableGuidanceRecovery: "<",

      /*
       Whether or not ui.router state changes should trigger a pop-up
       asking if the user is sure he wants to navigate way. This pop-up
       is only shown when the user has entered data.
       */
      enableNavigateAwayGuard: "<"
    },
    controllerAs: 'guidanceController',
    controller: function (guidanceModalObserver, guidanceModeDatasource, $q, $timeout, DEBOUNCE_TIME, promiseUtils,
                          validationObserverFactory, suggestionsObserverFactory, progressBarObserver, commandHandler,
                          guidanceGuardian, guidanceModeBackendState, ACTION_EVENT, $scope, PARENT_MODEL_KEY, $state) {
      const guidanceController = this;

      guidanceController.$onInit = function () {
        saveModelForDiff();
        setModelAndParentModelOnGuidanceFormObserver();
        addWatchOnGuidanceDataErrorsToNotifyTheObservers();
        addWatchOnGuidanceDataSuggestionsToNotifyTheObservers();
      };

      // Create the validationObserver and suggestionsObserver for the initial step.
      guidanceController.validationObserver = validationObserverFactory.createValidationObserver();
      guidanceController.suggestionsObserver = suggestionsObserverFactory.createSuggestionsObserver();
      guidanceController.originalModel = {};
      guidanceController.lastPostModel = {};

      /*
       Wrap 'guidanceModeDatasource.step' so only the response or the last
       request is used when two or more request are pending at the
       same time.

       We should not use guidanceModeDatasource.step directly anymore.
       */
      const latestGuidanceModeDatasourceStep = promiseUtils.useLatest(guidanceModeDatasource.step);

      // All the FormControllers that make up this 'Guidance step'.
      guidanceController.forms = [];

      notifyObserversOfStepChange();

      /*
       Timeout is done so we can immediately set errors and suggestions
       for the initial step before you type something.
       This way the product suggestion list is filled.
       */
      $timeout(function () {
        handleValidationErrors(guidanceController.guidanceData.errors);
        handleSuggestions(guidanceController.guidanceData.suggestions);
      }, 500);

      guidanceController.guidanceFormObserver.setFormControllerCreatedCallback(function (formController) {
        guidanceController.forms.push(formController);
      });

      /*
       Observe the form action changes and perform validation/binding
       suggestions when this occurs. Wrapped inside a DEBOUNCE_TIME
       debounce so we limit the number of calls going to the backend.
       */
      guidanceController.guidanceFormObserver.setFormValueChangedCallback(function (guidanceAction, noBackendInteraction) {
        if (noBackendInteraction) {
          $timeout(function () {
            guidanceController.guidanceFormObserver.formValidityUpdate(_.every(guidanceController.forms, "$valid"));
          }, 1);

          return;
        }

        guidanceModeBackendState.setBackendIsBusy(true);
        guidanceController.formValueChanged(guidanceAction);
      });

      guidanceController.formValueChanged = _.debounce(function (guidanceAction) {
        if (guidanceController.enableNavigateAwayGuard) {
          guidanceGuardian.startGuard(guidanceController.guidanceFormObserver);
        }

        const actionObject = {
          event: ACTION_EVENT.CHANGED,
          focus: guidanceAction.focus,
          previousValue: _.get(guidanceController.originalModel, guidanceAction.focus, null)
        };
        requestStep(actionObject).then(handleValidationResponse);
      }, DEBOUNCE_TIME);


      guidanceController.guidanceFormObserver.setConfirmGuidanceCallback(function (confirmAction) {
        return requestStep({ event: confirmAction }).then(function (command) {
          if (_.isUndefined(command.command)) {
            handleRequestStepResponse(command);
            return $q.reject('The response is NOT a command.');
          }

          // Disable any guard we might have triggered.
          guidanceGuardian.endGuard(guidanceController.guidanceFormObserver);

          /*
           Return the command to the next listener of the promise chain,
           so it may preform actions on them like usual.
           */
          return command;
        });
      });

      guidanceController.guidanceFormObserver.setRequestNextStepCallback(function () {
        requestStep({ event: ACTION_EVENT.NEXT_STEP }).then(handleRequestStepResponse);
      });

      if (guidanceController.informProgressBarObserver) {
        progressBarObserver.registerClickCallback(function (stepId) {
          requestStep({ event: ACTION_EVENT.NEXT_STEP_FORCED, nextStep: stepId }).then(handleRequestStepResponse);
        });
      }

      function requestStep(actionObject) {
        const steps = _.get(guidanceController.guidanceData, 'progress.steps');
        if (_.isEmpty(steps) === false) {
          const currentStep = _.find(steps, function (step) {
            return step.active;
          });

          actionObject.currentStep = currentStep.key_c;
        }

        const postBody = {
          model: angular.copy(guidanceController.guidanceData.model),
          progress: guidanceController.guidanceData.progress,
          action: actionObject
        };

        if (_.has(guidanceController.guidanceData, 'parentModel')) {
          postBody.parentModel = angular.copy(guidanceController.guidanceData.parentModel);
        }

        if (guidanceController.enableGuidanceRecovery === true) {
          postBody.route = {
            linkTo: $state.current.name,
            params: $state.params
          };
        }

        const changedFields = compare(guidanceController.originalModel, postBody.model);
        if (_.isEmpty(changedFields) === false) {
          postBody.action.changedFields = changedFields;
        }

        if (
          _.isEmpty(guidanceController.lastPostModel) === false
          && _.isEqual(postBody.action.event, ACTION_EVENT.CHANGED)
          && _.isEqual(guidanceController.lastPostModel, postBody.model)
        ) {
          guidanceModeBackendState.setBackendIsBusy(false);
          return $q.reject('There is already a call with this model in progress.');
        }

        if (
          _.isEmpty(guidanceController.originalModel) === false
          && _.isEqual(postBody.action.event, ACTION_EVENT.CHANGED)
          && _.isEqual(guidanceController.originalModel, postBody.model)
        ) {
          guidanceModeBackendState.setBackendIsBusy(false);
          return $q.reject('The model was not change from last response.');
        }

        guidanceController.lastPostModel = angular.copy(postBody.model);
        guidanceModeBackendState.setBackendIsBusy(true, actionObject);

        return latestGuidanceModeDatasourceStep({
          flowId: guidanceController.flowId,
          recordId: guidanceController.recordId
        }, postBody).then(function (data) {
          guidanceModeBackendState.setBackendIsBusy(false);
          return data;
        }).finally(function () {
          guidanceModeBackendState.setBackendIsBusy(false);
        });
      }

      function handleValidationResponse(data) {
        if (!_.isUndefined(data.form)) {
          handleRequestStepResponse(data);
          return;
        }

        guidanceController.originalModel = _.merge({}, guidanceController.lastPostModel, data.model);
        guidanceController.lastPostModel = {};

        _.merge(guidanceController.guidanceData.model, data.model);
        if (_.has(data, 'parentModel')) {
          _.merge(guidanceController.guidanceData.parentModel, data.parentModel);
        }

        /*
         Because the grid is redrawn give the progress indicator
         and the guidance-form some time to set-up the observers
         once more.
         */
        $timeout(function () {
          handleValidationErrors(data.errors);
          handleSuggestions(data.suggestions);

          /*
           * A possible result of a validation request is a command given from the backend.
           * If this is the case, process it. This could mean navigating away from the current guidance
           * or opening a modal for example.
           */
          if (data.processCommand === true) {
            commandHandler.handle(data.command);
          }
        }, 200);
      }

      function handleRequestStepResponse(data) {
        // If we don't have data.form it means that have and error in the current step
        // and we received back only suggestions and errors. In this case we will only call
        // the handleValidationResponse method.
        if (_.isUndefined(data.form)) {
          handleValidationResponse(data);
          return;
        }
        //Create a new validationObserver and suggestionsObserver
        guidanceController.validationObserver = validationObserverFactory.createValidationObserver();
        guidanceController.suggestionsObserver = suggestionsObserverFactory.createSuggestionsObserver();

        //We are switching steps, so we discard the currently available forms.
        guidanceController.forms = [];

        //if the guidanceData contains parentModel - save it
        let savedParentModel = null;
        let modelKey = null;
        let modelId = null;
        if (_.has(guidanceController.guidanceData, 'parentModel')) {
          savedParentModel = guidanceController.guidanceData.parentModel;
          modelKey = guidanceController.guidanceData.modelKey;
          modelId = guidanceController.guidanceData.modelId;
        }

        // make sure the grid is reloaded
        const newGrid = angular.copy(data.grid);
        data.grid = {};
        guidanceController.guidanceData = data;
        $timeout(function () {
          guidanceController.guidanceData.grid = newGrid;
        }, 1);

        // if we have a saved parentModel apply it to the guidanceData
        if (_.isNull(savedParentModel) === false) {
          _.merge(savedParentModel, _.get(guidanceController, 'guidanceData.parentModel', {}));
          guidanceController.guidanceData.parentModel = savedParentModel;
          guidanceController.guidanceData.modelKey = modelKey;
          guidanceController.guidanceData.modelId = modelId;
          guidanceController.guidanceData.parentModel[modelKey][modelId] = guidanceController.guidanceData.model;
        }
        guidanceController.lastPostModel = {};
        saveModelForDiff();
        setModelAndParentModelOnGuidanceFormObserver();
        notifyObserversOfStepChange();

        /*
         Because the grid is redrawn give the progress indicator
         and the guidance-form some time to set-up the observers
         once more.
         */
        $timeout(function () {
          handleValidationErrors(data.errors);
          handleSuggestions(data.suggestions);
        }, 200);
      }

      function notifyObserversOfStepChange() {
        /*
         Because the grid is redrawn give the progress indicator
         and the guidance-form some time to set-up the observers
         once more.
         */
        $timeout(function () {
          guidanceController.guidanceFormObserver.stepChangeOccurred(guidanceController.guidanceData);
          if (guidanceController.informProgressBarObserver) {
            progressBarObserver.setProgressMetadata(guidanceController.guidanceData.progress);
          }
        }, 200);
      }

      function handleValidationErrors(errors) {
        guidanceController.guidanceData.errors = errors;
        guidanceController.validationObserver.setErrors(_.get(guidanceController, "guidanceData.errors", {}));
        guidanceController.guidanceFormObserver.formValidityUpdate(_.every(guidanceController.forms, "$valid"));
      }

      function handleSuggestions(suggestions) {
        guidanceController.guidanceData.suggestions = suggestions;
        guidanceController.suggestionsObserver.setSuggestions(guidanceController.guidanceData.suggestions);
      }

      function setModelAndParentModelOnGuidanceFormObserver() {
        guidanceController.guidanceFormObserver.setFullModel(guidanceController.guidanceData.model);
        if (_.has(guidanceController.guidanceData, 'parentModel')) {
          guidanceController.guidanceFormObserver.setParentModel(guidanceController.guidanceData.parentModel);
        }
      }

      function addWatchOnGuidanceDataErrorsToNotifyTheObservers() {
        $scope.$watch('guidanceController.guidanceData.errors', function (newValue, oldValue) {
          if (_.isEqual(oldValue, newValue) === false) {
            handleValidationErrors(guidanceController.guidanceData.errors);
          }
        }, true);
      }

      function addWatchOnGuidanceDataSuggestionsToNotifyTheObservers() {
        $scope.$watch('guidanceController.guidanceData.suggestions', function (newValue, oldValue) {
          if (_.isEqual(oldValue, newValue) === false) {
            handleSuggestions(guidanceController.guidanceData.suggestions);
          }
        }, true);
      }

      function compare(prevObject, newObject) {
        const difference = {};

        const prevObjectKeys = _.keys(prevObject);
        const newObjectKeys = _.keys(newObject);

        _.forEach(_.difference(prevObjectKeys, newObjectKeys), (prevKey) => {
          difference[prevKey] = prevObject[prevKey];
        });

        _.forEach(_.difference(newObjectKeys, prevObjectKeys), (newKey) => {
          difference[newKey] = null;
        });

        _.forEach(_.intersection(newObjectKeys, prevObjectKeys), (key) => {
            if (key === PARENT_MODEL_KEY || _.isEqual(newObject[key], prevObject[key])) {
              return;
            }

            difference[key] = prevObject[key];
          }
        );

        return difference;
      }

      function saveModelForDiff() {
        if (_.has(guidanceController, 'guidanceData.model') === false) {
          return;
        }

        //sort the model by key
        _(guidanceController.guidanceData.model).toPairs().sortBy(0).fromPairs().value();

        //save a copy of the model for later use
        guidanceController.originalModel = angular.copy(guidanceController.guidanceData.model);
      }
    }
  });
