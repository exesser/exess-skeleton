'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp:embeddedGuidance component
 * @description
 * # embeddedGuidance
 *
 * Creates an embedded guidance, an embedded guidance is a guidance which
 * renders in a grid. It has its own 'Primary button' in the top corner
 * of the component itself.
 *
 * The embedded guidance will use its parameters to request the guidance
 * data from the back-end. It will then render the 'grid' and 'form' it
 * receives.
 *
 * A couple of caveats:
 *
 * The rendered guidance is expected to be part of a one step guidance.
 * The embedded-guidance does not handle next steps, it always assumes that
 * the primary button is the 'save' button.
 *
 * The 'grid' which is returned by the back-end, after a 'guidanceModeDatasource.get',
 * cannot contain grid-wrappers, if you try to do so the 'content' will
 * be a blank page. The reason for this is because the embedded-guidance
 * itself is a variant of the 'title-containing-grid' which renders a
 * primary button next to it. Nesting 'grid-wrappers' is not something
 * that the styleguide supports.
 *
 * The embedded guidance is expected to render inside of a centeredGuidanceGrid.
 * Otherwise the appearance will look strange, the reason for this is
 * because the embedded guidance needs a .guidance CSS class on a parent
 * <div> in order to function the centeredGuidanceGrid provides a .guidance
 * CSS class.
 *
 * Example usage:
 *
 * <embedded-guidance
 *   record-type="Quote"
 *   flow-action="ReadOnly"
 *   flow-id="QuoteDeletion"
 *   record-id="12"
 *   guidance-params="controller.guidanceParams"
 *   show-primary-button="true"
 *   primary-button-title="CONFIRM"
 *   default-Title="Read Quote"
 *   title-expression="{% firstname %}"
 *   model-key="connections"
 *   model-id="123-456-789">
 * </embedded-guidance>
 *
 * Component of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .constant('PARENT_MODEL_KEY', 'dwp|parentModel')
  .component('embeddedGuidance', {
    templateUrl: 'es6/guidance-mode/guidance-component/presenters/embedded-guidance/embedded-guidance.component.html',
    require: {
      guidanceObserversAccessor: "?^guidanceObserversAccessor"
    },
    bindings: {
      recordType: "@",
      flowId: "@",
      flowAction: "@",
      recordId: "@",
      guidanceParams: "<",    // Object containing other parameters for the 'guidanceModeDatasource.get' request.
      showPrimaryButton: "<",
      primaryButtonTitle: "@",
      defaultTitle: '@',
      titleExpression: '@',
      bigTitle: '@',
      modelKey: '@',
      modelId: '@'
    },
    controllerAs: 'embeddedGuidanceController',
    controller: function (guidanceModeDatasource, guidanceFormObserverFactory, CONFIRM_ACTION,
                          commandHandler, guidanceModeBackendState, $scope, PARENT_MODEL_KEY, $stateParams) {
      const embeddedGuidanceController = this;

      embeddedGuidanceController.guidanceFormObserver = guidanceFormObserverFactory.createGuidanceFormObserver();
      embeddedGuidanceController.enableNavigateAwayGuard = true;

      embeddedGuidanceController.loading = true;
      embeddedGuidanceController.valid = false;
      embeddedGuidanceController.guidanceMode = { model: {} };
      embeddedGuidanceController.parentModel = {};
      embeddedGuidanceController.guidanceHasBeenLoaded = false;
      embeddedGuidanceController.errorsFromParent = {};
      embeddedGuidanceController.suggestionsFromParent = {};
      embeddedGuidanceController.guidanceRandomKey = _.random(0, 9999);

      embeddedGuidanceController.$onInit = function () {
        if (_.isNull(embeddedGuidanceController.guidanceObserversAccessor)) {
          // in this case is a normal embedded guidance (normal = not embedded in another guidance)
          guidanceModeDatasourceGet();
        } else {
          // this guidance is a repeatable block
          guidanceModeBackendState.addBackendIsBusyFor(embeddedGuidanceController.guidanceRandomKey);
          embeddedGuidanceController.enableNavigateAwayGuard = false;
          registerParentErrorsChangedCallback();
          registerParentSuggestionsChangedCallback();
          registerStepChangeOccurredCallback();
          addRepeatableKeyOnGuidanceFormObserver();
        }
      };

      embeddedGuidanceController.guidanceFormObserver.setFormValidityUpdateCallback(function (valid) {
        embeddedGuidanceController.valid = valid;
      });

      embeddedGuidanceController.primaryButtonClicked = function () {
        if (embeddedGuidanceController.primaryButtonIsDisabled()) {
          return;
        }

        embeddedGuidanceController.loading = true;

        embeddedGuidanceController.guidanceFormObserver.confirmGuidance(CONFIRM_ACTION.CONFIRM).then((data) => {
          embeddedGuidanceController.loading = false;
          commandHandler.handle(data);
        }).catch(() => {
          embeddedGuidanceController.loading = false;
        });
      };

      embeddedGuidanceController.primaryButtonIsDisabled = function () {
        return !embeddedGuidanceController.valid || guidanceModeBackendState.getBackendIsBusy();
      };

      function getChildFromModel(model) {
        return _.get(model, `${embeddedGuidanceController.modelKey}.${embeddedGuidanceController.modelId}`, {});
      }

      function registerStepChangeOccurredCallback() {
        const parentGuidanceFormObserver = embeddedGuidanceController.guidanceObserversAccessor.getGuidanceFormObserver();
        const stepChangeCallbackDeregister = parentGuidanceFormObserver.addStepChangeOccurredCallback(function ({ model }) {
          embeddedGuidanceController.parentModel = model;
          embeddedGuidanceController.guidanceMode.parentModel = embeddedGuidanceController.parentModel;
          embeddedGuidanceController.guidanceParams.parentModel = embeddedGuidanceController.parentModel;

          if (embeddedGuidanceController.guidanceHasBeenLoaded === false) {
            addParentModelOnGuidanceParams();
            guidanceModeDatasourceGet();
          }
        });

        embeddedGuidanceController.$onDestroy = function () {
          stepChangeCallbackDeregister();
        };
      }

      function addParentModelOnGuidanceParams() {
        const originalModelFromParent = _.merge({}, getChildFromModel(embeddedGuidanceController.parentModel));

        embeddedGuidanceController.guidanceMode.model = _.merge(
          _.get(embeddedGuidanceController.guidanceParams, `model`, {}),
          embeddedGuidanceController.guidanceMode.model,
          originalModelFromParent
        );

        _.merge(embeddedGuidanceController.guidanceParams, { model: embeddedGuidanceController.guidanceMode.model });
      }

      function guidanceModeDatasourceGet() {
        const options = {
          recordType: embeddedGuidanceController.recordType,
          flowId: embeddedGuidanceController.flowId,
          flowAction: embeddedGuidanceController.flowAction,
          recordId: embeddedGuidanceController.recordId
        };

        /**
         * When we have a model key in URL we are concatenating with flowId and send it to the data-source.
         * Data-source will get the model from session add add it to the backend request.
         *
         * We are concatenating the model key with the flow key because on a dashboard we can have multiple
         * embedded guidance and we what to apply the model only to one of them.
         */
        if (_.has($stateParams, 'modelKey')) {
          options.modelKey = `${$stateParams.modelKey}-${embeddedGuidanceController.flowId}`;
        }

        guidanceModeDatasource.get(options, angular.copy(embeddedGuidanceController.guidanceParams)).then(function (guidanceMode) {
          embeddedGuidanceController.guidanceHasBeenLoaded = true;
          embeddedGuidanceController.loading = false;
          embeddedGuidanceController.guidanceMode = guidanceMode;

          if (_.has(guidanceMode, 'parentModel')) {
            _.merge(embeddedGuidanceController.parentModel, guidanceMode.parentModel);
            embeddedGuidanceController.guidanceMode.parentModel = embeddedGuidanceController.parentModel;
            embeddedGuidanceController.guidanceMode.modelKey = embeddedGuidanceController.modelKey;
            embeddedGuidanceController.guidanceMode.modelId = embeddedGuidanceController.modelId;

            if (_.has(embeddedGuidanceController.parentModel, embeddedGuidanceController.modelKey) === false) {
              embeddedGuidanceController.parentModel[embeddedGuidanceController.modelKey] = {};
            }

            embeddedGuidanceController.parentModel[embeddedGuidanceController.modelKey][embeddedGuidanceController.modelId] = embeddedGuidanceController.guidanceMode.model;
          }

          guidanceModeBackendState.removeBackendIsBusyFor(embeddedGuidanceController.guidanceRandomKey);
        });
      }

      function registerParentErrorsChangedCallback() {
        const validationObserver = embeddedGuidanceController.guidanceObserversAccessor.getValidationObserver();
        validationObserver.registerErrorsChangedCallback(function () {
          const errors = validationObserver.getErrorsForKey(`${embeddedGuidanceController.modelKey}.${embeddedGuidanceController.modelId}`);
          if (errors !== [] && errors !== embeddedGuidanceController.errorsFromParent) {
            embeddedGuidanceController.errorsFromParent = errors;
            embeddedGuidanceController.guidanceMode.errors = embeddedGuidanceController.errorsFromParent;
          }
        });
      }

      function registerParentSuggestionsChangedCallback() {
        const suggestionsObserver = embeddedGuidanceController.guidanceObserversAccessor.getSuggestionsObserver();
        suggestionsObserver.registerSuggestionsChangedCallback(function () {
          const suggestions = suggestionsObserver.getSuggestionsForKey(`${embeddedGuidanceController.modelKey}.${embeddedGuidanceController.modelId}`);
          if (_.isEmpty(suggestions) === false && suggestions !== embeddedGuidanceController.suggestionsFromParent) {
            embeddedGuidanceController.suggestionsFromParent = suggestions;
            embeddedGuidanceController.guidanceMode.suggestions = embeddedGuidanceController.suggestionsFromParent;
          }
        });
      }

      function addRepeatableKeyOnGuidanceFormObserver() {
        embeddedGuidanceController.guidanceFormObserver.setRepeatableBlockKey(`${embeddedGuidanceController.modelKey}.${embeddedGuidanceController.modelId}`);
      }
    }
  });
