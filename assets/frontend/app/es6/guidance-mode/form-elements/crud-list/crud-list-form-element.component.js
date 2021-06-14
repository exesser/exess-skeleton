"use strict";

/**
  * @ngdoc function
  * @name digitalWorkplaceApp.component:crudListFormElement
  * @description
  * # crudListFormElement
  * Component of the digitalWorkplaceApp
  *
  * This component renders a light version of the dynamic-list.
  * What actions it contains is hard-coded.
  *
  * Its supported operations are:
  *
  * - Create
  *   The resulting command is expected to be an 'openModal' command
  *   that we handle locally instead of delegating the logic to the commandHandler.
  *
  * - Update
  *   The resulting command is expected to be an 'openModal' command
  *   that we handle locally instead of delegating the logic to the commandHandler.
  *   We copy the model of the specific row and deliver it to the modalController so we see the data entered.
  *
  * - Delete
  *   Removes the row from the model without confirmation.
  *
  * The create operation for example will trigger a modal that contains a Guidance.
  * It renders a form where you can fill in the required data. It is posted to the backend with the '' event name.
  * It will then respond with the JSON we need to render a new row.
  *
  * The actual data we are keeping track of will be put in a 'model' property. This is how we are able to separate the data we want
  * to show and the data we want to keep track of.
  *
  * Use in the following way:
  *
  * <crud-list-form-element
  *   ng-model <!-- The ng-model attribute to bind to. Formly handles this for us if we just put 'ng-model' in there. -->
  *   id='tableX' <!-- The form id of the table -->
  *   key='tableX' <!-- The form key of the table -->
  *   title='Table X' <!-- The title of the table -->
  *   create-update-action-id='createTableXRow' <!-- The id of the action to trigger when the user presses 'create' -->
  *   is-disabled="otherField > 2" <!-- Expression that disables this field when it evaluates to true -->
  *   is-readonly="false" <!-- Expression that makes the field readonly when it evaluates to true -->
  *   headers='[{
  *     "label": "Column 1",
  *     "cellClass": "cell__text",
  *     "cellType": "list-simple-single-line-cell",
  *     "cellOptions": {
  *       "text": "{% column_1 %}"
  *     }
  *   }]'> <!-- The headers of the table -->
  * </crud-list-form-element>
  *
*/
angular.module('digitalWorkplaceApp')
  .component('crudListFormElement', {
    templateUrl: 'es6/guidance-mode/form-elements/crud-list/crud-list-form-element.component.html',
    require: {
      'ngModel': 'ngModel',
      guidanceObserversAccessor: '^guidanceObserversAccessor'
    },
    controllerAs: 'crudListFormElementController',
    bindings: {
      id: "@",
      key: "@",
      title: "@",
      createUpdateActionId: "@",
      headersJson: "@headers",
      isDisabled: "<",
      isReadonly: "<"
    },
    controller: function($scope, $element, $compile, $rootScope, $timeout, actionDatasource, guidanceModalObserver,
                         interpolateFilter, CONFIRM_ACTION) {
      const crudListFormElementController = this;

      crudListFormElementController.headers = angular.fromJson(crudListFormElementController.headersJson);
      crudListFormElementController.rows = [];
      crudListFormElementController.fullModel = {};

      crudListFormElementController.$onInit = function() {
        $scope.$watch('crudListFormElementController.ngModel.$viewValue', function(newValue) {
          //Initially the newValue is undefined, when we receive the array populate the table.
          if (_.isArray(newValue)) {
            crudListFormElementController.rows = newValue;
            createRowsInView();
          }
        }, true);

        const guidanceFormObserver = crudListFormElementController.guidanceObserversAccessor.getGuidanceFormObserver();
        crudListFormElementController.fullModel = guidanceFormObserver.getFullModel();
      };

      //When the isDisabled property changes, redraw the table.
      $scope.$watch('crudListFormElementController.isDisabled', function(newValue, oldValue) {
        if (_.isEqual(newValue, oldValue) === false) {
          createRowsInView();
        }
      });

      //When the isReadonly property changes, redraw the table.
      $scope.$watch('crudListFormElementController.isReadonly', function(newValue, oldValue) {
        if (_.isEqual(newValue, oldValue) === false) {
          createRowsInView();
        }
      });

      /**
       * Opens a modal to create a new list row entry. When confirming the modal,
       * the data is sent to the backend which responds with a model for that row.
       * This data is then added to the row collection and the table is redrawn.
       */
      crudListFormElementController.create = function() {
        actionDatasource.perform({
          id: crudListFormElementController.createUpdateActionId,
          model: crudListFormElementController.fullModel
        }).then(function(data) {
          const modalData = data.arguments;
          guidanceModalObserver.openModal(modalData, CONFIRM_ACTION.CONFIRM_CREATE_LIST_ROW).then(function(row) {
            crudListFormElementController.rows.push(row.model);
            crudListFormElementController.ngModel.$setViewValue(crudListFormElementController.rows);
            createRowsInView();
          });
        });
      };

      /**
       * Opens a modal to update an existing list row entry. When confirming the modal,
       * the data is sent to the backend which responds with a model for that row.
       * This data then replaced the data at the given row index of the row collection and the table is redrawn.
       * @param index the row index
       */
      crudListFormElementController.updateRow = function(index) {
        actionDatasource.perform({
          id: crudListFormElementController.createUpdateActionId,
          model: crudListFormElementController.fullModel
        }).then(function(data) {
          const modalData = data.arguments;
          // Creating a copy of the row's model so we don't change anything on cancel.
          modalData.model = angular.copy(crudListFormElementController.rows[index]);
          guidanceModalObserver.openModal(modalData, CONFIRM_ACTION.CONFIRM_CREATE_LIST_ROW).then(function(row) {
            crudListFormElementController.rows[index] = row.model;
            crudListFormElementController.ngModel.$setViewValue(crudListFormElementController.rows);
            createRowsInView();
          });
        });
      };

      /**
       * Removes a given row by row index from the row collection and redraws the table.
       * Currently does not trigger a warning.
       * @param index the row index
       */
      crudListFormElementController.removeRow = function(index) {
        crudListFormElementController.rows.splice(index, 1);
        createRowsInView();
      };

      function createRowsInView() {
        const rowsElement = $element.find('#rows')[0];
        rowsElement.innerHTML = '';

        _.forEach(crudListFormElementController.rows, function(row, index) {
          const rowElement = createElement('tr');
          rowElement.className = "list__row";
          rowsElement.appendChild(rowElement);

          _.forEach(crudListFormElementController.headers, function(header) {
            const cellElement = createElement('td');
            cellElement.className = "list__cell " + header.cellClass;
            rowElement.appendChild(cellElement);

            appendComponentElement(cellElement, {
              type: header.cellType,
              options: _.mapValues(header.cellOptions, function(cellOption) {
                return interpolateFilter(cellOption, row);
              })
            });
          });

          if (!crudListFormElementController.isDisabled && !crudListFormElementController.isReadonly) {
            appendActionCell(rowElement, {
              type: "crudListFormElementButton",
              options: {
                iconClass: "icon-edit",
                rowIndex: index,
                buttonClicked: "crudListFormElementController.updateRow(index)"
              }
            });
            appendActionCell(rowElement, {
              type: "crudListFormElementButton",
              options: {
                iconClass: "icon-remove",
                rowIndex: index,
                buttonClicked: "crudListFormElementController.removeRow(index)"
              }
            });
          }
        });
      }

      function appendActionCell(rowElement, elementDetails) {
        const cellElement = createElement('td');
        cellElement.className = "list__cell cell__action";
        rowElement.appendChild(cellElement);

        return appendComponentElement(cellElement, elementDetails);
      }

      function appendComponentElement(parentElement, elementDetails) {
        const componentElement = createElement(_.kebabCase(elementDetails.type));
        _.forOwn(elementDetails.options, function(value, key) {
          componentElement.setAttribute(_.kebabCase(key), value);
        });
        parentElement.appendChild(componentElement);

        /*
         Intentionally creating a child scope from our current scope rather than $rootScope because we want
         to be able to pass a callback function.

         Also this means that the element's scope is destroyed when the surrounding crud-list's scope is destroyed.
         See the 'gridlr' component for more information.
         */
        $compile(componentElement)($scope.$new());

        return componentElement;
      }

      // Quick function so the eslint-disable doesn't have to appear multiple times.
      function createElement(tagName) {
        return document.createElement(tagName); //eslint-disable-line angular/document-service
      }
    }
  });
