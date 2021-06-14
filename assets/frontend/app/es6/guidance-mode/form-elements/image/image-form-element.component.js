"use strict";

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.component:imageFormElement
 * @description
 * # imageFormElement
 * Component of the digitalWorkplaceApp
 *
 * This component creates displays an  image with a text and a button.
 *
 * Example usage:
 *
 * <image-form-element
 *   ng-model
 *   id="img-id"
 *   key="img-id"
 *   text="Simple text here."
 *   image-url="https://exesscms.local/imageurl.png"
 *   action="action-id"
 *   action-text="GO!"
 *   action-model="{"name": "Ken Block"}"
 *   action-params="{"id":"43"}">
 * </image-form-element>
 */
angular.module('digitalWorkplaceApp')
  .component('imageFormElement', {
    templateUrl: 'es6/guidance-mode/form-elements/image/image-form-element.component.html',
    require: {
      ngModel: 'ngModel',
      guidanceObserversAccessor: '^guidanceObserversAccessor'
    },
    bindings: {
      id: "@",
      key: "@",
      imageUrl: "@",
      text: "@",
      action: "@",
      actionText: "@",
      actionModel: "<",
      actionParams: "<"
    },
    controllerAs: 'imageFormElementController',
    controller: function (actionDatasource) {
      const imageFormElementController = this;

      imageFormElementController.fullModel = {};

      imageFormElementController.$onInit = function () {
        const guidanceFormObserver = imageFormElementController.guidanceObserversAccessor.getGuidanceFormObserver();
        imageFormElementController.fullModel = guidanceFormObserver.getFullModel();
      };

      imageFormElementController.callAction = function () {
        actionDatasource.performAndHandle({
          "id": imageFormElementController.action,
          "params": _.merge(
            {},
            _.mapValues(imageFormElementController.actionParams, (key) => {
              return _.get(imageFormElementController.fullModel, key, null);
            }),
            {
              model: _.mapValues(imageFormElementController.actionModel, (key) => {
                return _.get(imageFormElementController.fullModel, key, null);
              })
            }
          )
        });
      };
    }
  });
