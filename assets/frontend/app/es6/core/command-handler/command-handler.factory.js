'use strict';

/**
 * @ngdoc service
 * @name digitalWorkplaceApp.commandHandler
 * @description Factory which handles commands coming back from the backend.
 *
 * The command structure consists of the following form:
 *
 * {
 *   "command": "navigate", // The name
 *   "arguments": {}
 * }
 *
 * The 'arguments' shape is specific for each type of command.
 *
 * Factory in the digitalWorkplaceApp.
 */
angular.module('digitalWorkplaceApp')
  .factory('commandHandler', function (guidanceModalObserver, miniGuidanceModeObserver, $state, listObserver, $log,
                                       $timeout, previousState, $window, progressBarObserver, modelSession,
                                       replaceSpecialCharacters, navigateAwayWarning, guidanceGuardian) {
    const commands = {
      openModal,
      openMiniGuidance,
      reloadPage,
      navigate,
      reloadList,
      nothing,
      previousPage,
      openLink,
      changeStep,
      popUpMessage
    };

    return { handle };

    /**
     * Handle the provided command.
     *
     * @throws Unsupported command error when command is not recognized.
     * @param {Object: {command: String, arguments: Object} commandObject
     */
    function handle(commandObject) {
      if (_.isFunction(commands[commandObject.command]) === false) {
        throw new Error(`Unsupported command '${commandObject.command}'.`);
      }

      const command = commands[commandObject.command];

      /**
       * Sometimes the command is triggered from a Modal so first
       * we have to reset the Modal and then call the command.
       * Therefore we handle the command on the next event loop. This gives the modal time to unregister.
       */
      guidanceModalObserver.resetModal();

      if (!_.isEmpty(_.get(commandObject, 'confirmMessage', null))) {
        openConfirmModal(commandObject);
      } else {
        $timeout(function () {
          command(commandObject.arguments);
        }, 1);
      }
    }

    /**
     * Opens a modal with the modalData.
     *
     * When the modal is 'finished' it is expected to resolve with another
     * command which the commandHandler will handle again.
     *
     * Example:
     *
     * {
     *   "command": "openModal",
     *   "arguments": {}
     * }
     *
     * @param modalData data required to open a modal
     */
    function openModal(modalData) {
      modalData = replaceSpecialCharacters.replaceArraySign(modalData);
      guidanceModalObserver.openModal(modalData).then(handle);
    }

    /**
     * Opens a mini guidance with the miniGuidanceData.
     *
     * When the mini guidance is 'finished' it is expected to resolve
     * with another command which the commandHandle will handle again.
     *
     * Example:
     *
     * {
     *   "command": "minidGuidanceData",
     *   "arguments": {}
     * }
     *
     * @param miniGuidanceData data required to open a mini-guidance
     */
    function openMiniGuidance(miniGuidanceData) {
      miniGuidanceModeObserver.openMiniGuidance(miniGuidanceData).then(handle);
    }

    /**
     * Reloads the currently active state with the same state parameters.
     *
     * Example:
     *
     * {
     *   "command": "reloadPage",
     *   "arguments": {}
     * }
     */
    function reloadPage() {
      $state.go($state.current, $state.params, { reload: true });
    }

    /**
     * Navigates to another state.
     *
     * Example:
     *
     * {
     *   "command": "navigate",
     *   "arguments": {
     *     "linkTo": "dashboard",
     *     "params": {
     *       "mainMenuKey": "sales-marketing",
     *       "dashboardId": "leads"
     *     }
     *   }
     * }
     *
     * @param {Object{linkTo: String, params: Object}} navigationData containing a linkTo and optionally params.
     */
    function navigate(navigationData) {
      if (_.isEmpty(navigationData.params.model) === false) {
        const modelKey = generateKey(10);
        let fullModelKey = modelKey;

        if (navigationData.linkTo !== 'guidance-mode' && _.has(navigationData.params.model, 'dwp|guidanceFlowId')) {
          fullModelKey = `${modelKey}-${_.get(navigationData.params.model, 'dwp|guidanceFlowId')}`;
        }

        modelSession.setModel(fullModelKey, navigationData.params.model);

        _.unset(navigationData, 'params.model');
        navigationData.params.modelKey = modelKey;
      }

      if (_.get(navigationData, 'force', false)) {
        navigateAwayWarning.disable();
        guidanceGuardian.resetGuardian();
      }

      if (_.get(navigationData, 'newWindow', false)) {
        $window.open($state.href(navigationData.linkTo, navigationData.params), '_blank');
        return;
      }

      // Reload true so navigating to the same state twice is not a problem.
      $state.go(navigationData.linkTo, navigationData.params, { reload: true });
    }

    /**
     * Reloads the list with the listKey.
     *
     * Example:
     *
     * {
     *   "command": "reloadList",
     *   "arguments": {
     *     "listKey": "AwesomeList",
     *   }
     * }
     *
     * @param  {Object{listKey: String}} reloadListData Object containing the listKey to reload.
     */
    function reloadList(reloadListData) {
      listObserver.reloadList(reloadListData.listKey);
    }

    /**
     * Does nothing when handling the response.
     *
     * Example:
     *
     * {
     *   "command": "nothing",
     *   "arguments": {}
     * }
     *
     * It does log the fact that it did nothing, not as an warning or
     * error but just as a heads up.
     */
    function nothing() {
      $log.log('commandHandler: was told to do nothing.');
    }

    /**
     * Goes to the previous page. Acts in the same way as clicking
     * the "black back arrow button" programmatically.
     */
    function previousPage() {
      previousState.navigateTo();
    }

    /**
     * Opens a link directly in a new browser tab or window depending
     * on the settings of the user.
     *
     * Example:
     *
     * {
     *   "command": "openLink",
     *   "arguments": {
     *     "link": "http://www.sample-videos.com/csv/Sample-Spreadsheet-10-rows.csv",
     *   }
     * }
     *
     * @param {Object{link: String, newTab: Bool}} openLinkData Object containing the link to open
     */
    function openLink({ link, newTab }) {
      if (newTab === true) {
        $window.open(link, '_blank');
      } else {
        navigateAwayWarning.disable();
        $window.location.assign(link);
      }
    }

    /**
     * Inform the progressBarObserver that we want to change the step.
     *
     * Example:
     *
     * {
     *   "command": "changeStep",
     *   "arguments": {
     *      "stepId": "CQFA_BILLING"
     *   }
     * }
     *
     * @param {Object{stepId: String}} changeStepData Object containing the next step id
     */
    function changeStep({ stepId }) {
      progressBarObserver.clicked(stepId);
    }

    function popUpMessage(commandData) {
      openModal({
          "title": _.get(commandData, 'title', ''),
          "grid": {
            "columns": [
              {
                "rows": [
                  {
                    "type": "paragraph",
                    "options": {
                      "text": _.get(commandData, 'message', '')
                    }
                  }
                ]
              }
            ]
          }
        }
      );
    }

    function openConfirmModal(commandObject) {
      let newCommand = angular.copy(commandObject);
      _.unset(newCommand, 'confirmMessage');

      openModal({
        "title": _.get(commandObject, 'confirmTitle', ''),
        "extraActions": [
          {
            "label": "Yes",
            "command": newCommand,
          }
        ],
        "grid": {
          "columns": [
            {
              "rows": [
                {
                  "type": "paragraph",
                  "options": {
                    "text": commandObject.confirmMessage
                  }
                }
              ]
            }
          ]
        }
      });
    }

    function generateKey(idLength) {
      let text = "";
      const possible = _.words("A B C D E F G H I J K L M N O P Q R S T U V W X Y Z a b c d e f g h i j k l m n o p q r s t u v w x y z 0 1 2 3 4 5 6 7 8 9 -");

      _.each(_.range(0, idLength), () => {
        text += _.nth(possible, _.random(0, possible.length - 1));
      });

      return text;
    }
  });
