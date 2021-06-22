'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.list component
 * @description
 * # list
 *
 * <list list-key="accounts-big" params="{"recordId":"5e5e87d6-e427-df1a-c59f-579612e0a44f"}"></list>
 *
 * Component of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .component('list', {
    templateUrl: 'es6/list/list.component.html',
    controllerAs: 'listController',
    bindings: {
      listKey: "@",
      paramsJson: "@params"
    },
    controller: function (listDatasource, $element, $compile, $scope, listObserver, filtersObserver, listStatus,
                          sidebarObserver, SIDEBAR_ELEMENT, actionDatasource, promiseUtils, commandHandler,
                          expressionTransformer, $interpolate, $window, $timeout, DEBOUNCE_TIME) {
      const listController = this;

      // full data received from backend
      listController.data = {};

      // custom params on list (recordId, etc.)
      listController.params = angular.fromJson(listController.paramsJson);

      // an array with the Ids of selected items
      listController.selectList = [];
      listController.selectAllCheckbox = false;

      //an array with all the responsive open rows
      listController.openRows = [];

      // an object that indicates if the item extra row is open or not
      // structure :  {'action_bar-123-456-111': true, 'action_bar-123-456-222: false}
      listController.openGridsStore = {};

      listController.loading = true;

      listController.page = listStatus.getPage(getListKeyForSession());
      listController.sortBy = listStatus.getSort(getListKeyForSession());
      listController.filters = listStatus.getFilters(getListKeyForSession());
      listController.quickSearch = listStatus.getQuickSearch(getListKeyForSession());
      listController.quickSearchField = angular.copy(listController.quickSearch);
      listController.displayFooter = false;
      listController.loadedAsResponsive = false;

      /*
       Wrap 'listDatasource.getList' so only the response or the latest request is used when two or
       more request are pending at the same time.
       We should not use listDatasource.getList directly anymore.
      */
      const latestListDatasourceGetList = promiseUtils.useLatest(listDatasource.getList);

      listController.$onInit = function () {
        if (!_.isEmpty(_.get(listController, 'params.filters'))) {
          listController.filters = _.merge(_.get(listController, 'filters', {}), _.get(listController, 'params.filters', {}));
          _.unset(listController, 'params.filters');
        }

        displayFilters();
        updateList();

        angular.element($window).bind('resize', function () {
          if (listController.loadedAsResponsive !== listController.showResponsiveList()) {
            appendRecordsToView();
          }
        });

        $scope.$watch('listController.quickSearchField', _.debounce(function(newValue, oldValue) {
          if (_.isEqual(oldValue, newValue) === false) {
            listController.setPage(1);
            listController.setQuickSearch(newValue);
            updateList();
          }
        }, DEBOUNCE_TIME), true);
      };

      listController.getRows = function () {
        return _.get(listController, 'data.rows', []);
      };

      listController.getPagination = function () {
        return _.get(listController, 'data.pagination', null);
      };

      listController.getTopBar = function () {
        return _.get(listController, 'data.topBar', null);
      };

      listController.getHeaders = function () {
        return _.get(listController, 'data.headers', []);
      };

      listController.setPage = function (page, reloadList = false) {
        listController.page = page;
        listStatus.setPage(getListKeyForSession(), page);

        if (reloadList) {
          updateList();
        }
      };

      listController.setQuickSearch = function (value) {
        listController.quickSearch = value;
        listStatus.setQuickSearch(getListKeyForSession(), value);
      };

      listController.showResponsiveList = function () {
        return _.get(listController, 'data.settings.responsive', false) && $window.innerWidth < 960;
      };

      listController.showFooter = function () {
        return _.get(listController, 'data.settings.displayFooter', false);
      };

      listController.showQuickSearch = function () {
        return _.get(listController, 'data.settings.quickSearch', false);
      };

      listController.showHeaders = function () {
        return _.isEmpty(_.filter(listController.getHeaders(), (header) => {
          return _.isEmpty(header.label) === false;
        })) === false;
      };

      listController.showSortingOptions = function () {
        return _.isEmpty(listController.getTopBar()) === false
          && _.isEmpty(listController.getTopBar().sortingOptions) === false;
      };

      listController.sortHasChanged = function () {
        listStatus.setSort(getListKeyForSession(), listController.sortBy);
        listController.setPage(1);
        updateList();
      };

      listController.topBarActionClicked = function (button) {
        if (button.enabled === false) {
          return;
        }

        let { action } = button;
        let newAction = angular.copy(action);

        /**
         * List top actions have a custom behavior. You can have two actions on a list, one will send the main recordId
         * of the page when the action is called and the other will send the ids of the selected items in the list.
         *
         * To be able to configure this from the backend we choose to add the recordId as a custom parameter in the
         * action configuration like '{"recordId": "%recordId%"}'. This will automatic add the main recordId on the
         * action parameters. If we don't set any recordId on the action then we will automatic add the ids of
         * selected records.
         *
         * We have a third option now, if the newAction contains a `extraParamsFromRow`, we will merge all of those params
         * from the selected records and attached them to the newAction.
         */
        if (_.has(newAction, 'extraParamsFromRow')) {
          const extraParamsTemplate = expressionTransformer(angular.toJson(newAction.extraParamsFromRow));
          const extraParams = listController.selectList.map((rowId) => {
            const rowData = _.get(_.find(listController.getRows(), function (row) {
              return _.toString(row.id) === _.toString(rowId);
            }), 'rowData', []);
            const rowDataTransformed = angular.fromJson(expressionTransformer(angular.toJson(rowData)));
            return angular.fromJson($interpolate(extraParamsTemplate)(rowDataTransformed));
          });

          _.unset(newAction, 'extraParamsFromRow');
          newAction = _.mergeWith({}, ...extraParams, newAction, customizerMerge);
        }

        if (_.get(newAction, 'mandatorySelectRecord', false) && _.isEmpty(listController.selectList)) {
          if (_.has(newAction, 'mandatorySelectRecordMessage')) {
            commandHandler.handle({
               "command": "popUpMessage",
               "arguments": {
                 "message": newAction.mandatorySelectRecordMessage,
                 "title": _.get(newAction, 'mandatorySelectRecordTitle', 'INFO')
               }
            });
          }

          return;
        }

        if (_.has(newAction, 'recordId') === false && _.has(newAction, 'recordIds') === false) {
          newAction = _.merge({}, newAction, { recordIds: listController.selectList });
        }

        _.unset(newAction, 'confirmAction');
        _.unset(newAction, 'mandatorySelectRecord');
        _.unset(newAction, 'mandatorySelectRecordMessage');
        _.unset(newAction, 'mandatorySelectRecordTitle');
        actionDatasource.performAndHandle(newAction);
      };

      listController.getConfirmMessageForButton = function(button) {
        return _.get(button, "action.confirmAction", "");
      };

      listController.emptyButtonClicked = function () {
        actionDatasource.performAndHandle(listController.data.emptyButton.action);
      };

      listController.exportToCSVButtonClicked = function () {
        const params = _.merge({}, createListParams(), {
          recordIds: listController.selectList
        });

        /*
          Do not send the page when exporting because they are not
          relevant. Exporting always goes beyond one page.
        */
        delete params.page;

        listDatasource.exportToCSV({
          listKey: listController.listKey,
          params: params
        }).then(commandHandler.handle);
      };

      listController.addAllToSelectList = function () {
        listController.selectList = [];
        listObserver.toggleAllListRowsSelections(getListKeyForObservers(), false);

        if (listController.selectAllCheckbox) {
          listController.selectList = _.map(listController.getRows(), 'id');
          listObserver.toggleAllListRowsSelections(getListKeyForObservers(), true);
        }
      };

      listController.rowIsOpen = function (rowId) {
        return _.includes(listController.openRows, rowId);
      };

      listController.toggleRow = function (rowId) {
        if (listController.rowIsOpen(rowId)) {
          _.remove(listController.openRows, (row) => row === rowId);
          return;
        }

        listController.openRows.push(rowId);
      };

      listObserver.registerToggleListRowSelectionCallback(getListKeyForObservers(), function (itemId, itemSelected) {

        if (itemSelected) {
          listController.selectList.push(itemId);
        } else {
          _.remove(listController.selectList, (selectItem) => selectItem === itemId);
        }
      });

      listObserver.registerToggleExtraRowContentPlaceholderCallback(getListKeyForObservers(), function (gridKey, itemId) {
        const storeKey = `${gridKey}-${itemId}`;
        const isOpen = _.result(listController.openGridsStore, storeKey, false);

        const placeHolder = $element.find('#placeholder-row-' + itemId.replace(/(:|\.|\[|\]|,|=|@|~| |\+)/g, "\\$1"))[0];
        let placeHolderCell = placeHolder;
        if (placeHolder.children.length) {
          placeHolderCell = placeHolder.children[0];
          placeHolderCell.colSpan = 999;
          placeHolderCell.style.margin = "0";
          placeHolderCell.style.padding = "0";
        }

        placeHolder.className = _.replace(placeHolder.className, ' is-open', '');
        placeHolderCell.innerHTML = '';

        if (!isOpen) {
          listDatasource.getExtraRowContent({
            gridKey: gridKey,
            listKey: listController.listKey,
            itemId: encodeURIComponent(itemId),
            actionData: _.get(listController.data, 'settings.actionData', {})
          }).then(function (data) {
            appendComponentElement(placeHolderCell, {
              type: 'gridlr',
              options: {
                definition: data.grid
              }
            });

            $timeout(function () {
              placeHolder.className += " is-open";
            }, 150);
          });
        }

        listController.openGridsStore[storeKey] = !isOpen;
      });

      listObserver.registerReloadListCallback(getListKeyForObservers(), function () {
        updateList();
      });

      filtersObserver.registerFiltersHaveChangedCallback(getListKeyForObservers(), function (filters) {
        listController.filters = filters;
        listController.setPage(1);
        listController.quickSearch = '';
        updateList();
      });

      // if we have filters stored in sessionStorage we will automatically 'open' the filter Sidebar
      function displayFilters() {
        const showFilters = _(listController.filters)
          .flatMap(_.values)
          .map('value')
          .some(_.negate(_.isEmpty));

        if (showFilters) {
          sidebarObserver.toggleSidebarElement(SIDEBAR_ELEMENT.FILTERS);
        }
      }

      function updateList() {
        const rowsElement = $element.find('#rows')[0];
        const rowsElementResponsive = $element.find('#rows-responsive')[0];
        rowsElement.innerHTML = '';
        rowsElementResponsive.innerHTML = '';
        listController.loading = true;

        latestListDatasourceGetList({
          listKey: listController.listKey,
          params: createListParams()
        }).then(function (data) {
          if (_.isEmpty(data.rows) && listController.page > 1) {
            listController.setPage(1, true);
            return;
          }

          listController.data = data;
          listController.loading = false;
          listController.setPage(data.pagination.page);

          $timeout(function () {
            appendRecordsToView();
          }, 2);
        });
      }

      function appendRecordsToView() {

        listController.openGridsStore = {};
        listController.selectList = [];
        listController.selectAllCheckbox = false;
        listController.openRows = [];

        listController.loadedAsResponsive = listController.showResponsiveList();

        const rowsElement = $element.find('#rows')[0];
        const rowsElementResponsive = $element.find('#rows-responsive')[0];
        rowsElement.innerHTML = '';
        rowsElementResponsive.innerHTML = '';

        if (listController.loadedAsResponsive) {
          appendResponsiveRowsToView(rowsElementResponsive);
          return;
        }

        appendNonResponsiveRowsToView(rowsElement);
      }

      function appendNonResponsiveRowsToView(rowsElement) {
        _.forEach(listController.getRows(), function (row) {
          const rowElement = createElement('tr');
          rowElement.className = "list__row";

          _.forEach(row.cells, function (cellComponent) {
            const cellElement = createElement('td');
            cellElement.className = "list__cell " + cellComponent.class;

            appendComponentElement(cellElement, cellComponent);

            rowElement.appendChild(cellElement);
          });

          const placeholderRow = createElement('tr');
          placeholderRow.className = "row__extra_content";
          placeholderRow.id = 'placeholder-row-' + row.id;
          placeholderRow.appendChild(createElement('td'));

          rowsElement.appendChild(rowElement);
          rowsElement.appendChild(placeholderRow);

          const scopeRow = $scope.$new();
          $compile(rowElement)(scopeRow);

          //When the element is destroyed, destroy the scope as well.
          angular.element(rowElement).on("$destroy", function () {
            scopeRow.$destroy();
          });
        });
      }

      function appendResponsiveRowsToView(rowsElement) {
        _.forEach(listController.getRows(), function (givenRow) {
          const row = angular.copy(givenRow);
          checkResponsiveRowCells(row);

          const rowElement = createElement('div');
          rowElement.className = "m-table-list__item";
          rowElement.setAttribute('ng-class', "{'is-open': listController.rowIsOpen('" + row.id + "')}");

          const arrow = createElement('span');
          arrow.className = "icon-arrow-down icon-arrow-down-expend";
          arrow.setAttribute('ng-click', "listController.toggleRow('" + row.id + "')");
          rowElement.appendChild(arrow);

          _.forEach(row.cells, function (cellComponent, key, cells) {
            const cellElement = createElement('div');
            cellElement.className = cellComponent.class;

            if (
              (cellComponent.type === 'list_plus_cell' && key === cells.length - 1)
              || (cellComponent.type === 'list_checkbox_cell' && key === 0)
            ) {
              cellElement.className += " m-table-list__item__abstract";
            } else {
              cellElement.className += " m-table-list__item__section";

              const header = createElement('span');
              header.className = "m-table-list__item__label";
              header.innerHTML = listController.getHeaders()[key].label;
              cellElement.appendChild(header);
            }

            if (_.get(cellComponent, "responsive-header", false)) {
              cellElement.className += " m-table-list__item__header";
            }

            appendComponentElement(cellElement, cellComponent);

            rowElement.appendChild(cellElement);
          });

          const placeholderRow = createElement('div');
          placeholderRow.className = "row__extra_content";
          placeholderRow.id = 'placeholder-row-' + row.id;

          rowsElement.appendChild(rowElement);
          rowsElement.appendChild(placeholderRow);

          const scopeRow = $scope.$new();
          $compile(rowElement)(scopeRow);

          //When the element is destroyed, destroy the scope as well.
          angular.element(rowElement).on("$destroy", function () {
            scopeRow.$destroy();
          });
        });
      }

      function checkResponsiveRowCells(row) {
        if (!_(row.cells).map((cell) => _.get(cell, 'responsive-header', false)).some()) {
          if (_.has(row.cells, 2)) {
            row.cells[2]['responsive-header'] = true;
          } else if (_.has(row.cells, 1)) {
            row.cells[1]['responsive-header'] = true;
          }

        }
      }

      function createListParams() {
        return _.merge({}, listController.params, {
          page: listController.page,
          sortBy: listController.sortBy,
          filters: listController.filters,
          quickSearch: listController.quickSearch
        });
      }

      function appendComponentElement(cellElement, { type, options }) {
        const componentElement = createElement(_.kebabCase(type));

        _.forOwn(options, function (value, key) {
          if (key === 'listKey' && value === listController.listKey) {
            value = getListKeyForObservers();
          }
          componentElement.setAttribute(_.kebabCase(key), _.isObject(value) ? angular.toJson(value) : value);
        });

        cellElement.appendChild(componentElement);

        /*
         Intentionally creating a child scope from our current scope rather than $rootScope so the element's scope is destroyed when the
         containing crud-list's scope is destroyed.
         See the 'gridlr' component for more information.
         */
        const scope = $scope.$new();
        $compile(componentElement)(scope);

        //When the element is destroyed, destroy the scope as well.
        angular.element(componentElement).on("$destroy", function () {
          scope.$destroy();
        });
      }

      function customizerMerge(objValue, srcValue) {
        if (_.isArray(objValue)) {
          return objValue.concat(srcValue);
        }
      }

      function getListKeyForSession() {
        return listController.listKey
          + _.get(listController, 'params.recordId', '')
          + _.get(listController, 'params.uniqueListKey', '');
      }

      function getListKeyForObservers() {
        return _.get(listController, 'params.uniqueListKey', listController.listKey);
      }

      // Quick function so the eslint-disable doesn't have to appear multiple times.
      function createElement(tagName) {
        return document.createElement(tagName); //eslint-disable-line angular/document-service
      }
    }
  });
