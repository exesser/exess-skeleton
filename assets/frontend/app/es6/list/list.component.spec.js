'use strict';

describe('Component: list', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let $rootScope;
  let $timeout;
  let $compile;
  let $state;
  let $q;
  let $window;
  let scope;

  let actionDatasource;
  let listDatasource;
  let listObserver;
  let listStatus;
  let filtersObserver;
  let sidebarObserver;
  let commandHandler;

  let spyOnGetList;
  let spyOnListStatusGetSort;
  let spyOnListStatusGetFilters;
  let mockResponseList;
  let promiseUtils;

  let template = '<list list-key="{{ listKey }}" params="{{ params }}"> </list>';
  let element;
  let mainDivContent;
  let childrenOfMainDivContent;

  let DEBOUNCE_TIME;

  // Keeps track of lodashes original debounce function
  let lodashDebounce;

  // Keeps track of all the functions that went through the debounce.
  let debouncedFunctions;

  beforeEach(inject(function (_$rootScope_, _$compile_, _$state_, _$q_, _listDatasource_, _$timeout_, _$window_,
                              _listObserver_, _filtersObserver_, _sidebarObserver_, _actionDatasource_, _listStatus_,
                              _promiseUtils_, _commandHandler_, _DEBOUNCE_TIME_) {
    $state = _$state_;
    $rootScope = _$rootScope_;
    $timeout = _$timeout_;
    $compile = _$compile_;
    $q = _$q_;
    $window = _$window_;
    DEBOUNCE_TIME = _DEBOUNCE_TIME_;

    actionDatasource = _actionDatasource_;
    listStatus = _listStatus_;
    listDatasource = _listDatasource_;
    listObserver = _listObserver_;
    sidebarObserver = _sidebarObserver_;
    filtersObserver = _filtersObserver_;
    promiseUtils = _promiseUtils_;
    commandHandler = _commandHandler_;

    spyOnListStatusGetSort = spyOn(listStatus, 'getSort').and.returnValue(undefined);
    spyOnListStatusGetFilters = spyOn(listStatus, 'getFilters').and.returnValue(undefined);
    spyOn(listStatus, 'getPage').and.returnValue(1);

    spyOn($state, 'transitionTo');
    spyOn(sidebarObserver, 'toggleSidebarElement');
    spyOn(listStatus, 'setSort');
    spyOn(listStatus, 'setPage');

    // Mock lodash debounce so we can test the key up events more easily.
    lodashDebounce = _.debounce;

    // Clear the debouncedFunctions
    debouncedFunctions = [];
    /*
     Mock the debounce so it immediately executes the function.
     Remember we are not testing 'lodash' it has plenty of tests itself.
     */
    _.debounce = function (fn, time) {
      debouncedFunctions.push(fn.name);

      expect(time).toBe(DEBOUNCE_TIME);
      return fn;
    };
  }));

  // Reset the debounce to its original lodash function.
  afterEach(function () {
    _.debounce = lodashDebounce;
  });

  function compile(canExportToCSV = true, params = {}, page = 1, rows = false, emptyButton = undefined, responsive = false) {
    const defaultRows = [
      {
        id: "123123345",
        class: "test",
        cells: [
          {
            type: "list_checkbox_cell",
            options: {
              id: "123123345",
              listKey: "accounts-big"
            },
            class: "cell__checkbox"
          },
          {
            type: "list-icon-text-cell",
            options: {
              iconType: "bedrijf",
              iconStatus: "prospect",
              text: "prospect"
            },
            class: "cell__text"
          },
          {
            type: "list-link-bold-top-two-liner-cell",
            options: {
              line1: "WKY 2",
              line2: "BE012345678",
              linkTo: "dashboard",
              params: {
                mainMenuKey: "sales-marketing",
                dashboardId: "account",
                recordId: "a8d6fbfd-58ce-4f9c-0afe-5735d87a01eb"
              }
            },
            class: "cell__text"
          },
          {
            type: "list-link-pink-down-two-liner-cell",
            options: {
              line1: "Ken Block",
              line2: "ken@bblock.com",
              link: "mailto:ken@bblock.com"
            },
            class: "cell__text"
          },
          {
            type: "list-simple-two-liner-cell",
            options: {
              line1: "Vredestraat 22",
              line2: "2220 Heist op den berg"
            },
            class: "cell__text"
          },
          {
            type: "list_plus_cell",
            options: {
              id: "123123345",
              listKey: "accounts-big",
              gridKey: "more-info"
            },
            class: "cell__action"
          }
        ],
        rowData: {
          "id": "123123345",
          "name": "WKY 2",
          "contact_person|name": "Ken Block",
          "contact_person|mail": "ken@bblock.com"
        }
      },
      {
        id: "1234567-1234-23,4+5 5~3",
        class: "test",
        cells: [
          {
            type: "list_checkbox_cell",
            options: {
              id: "1234567-1234-23,4+5 5~3",
              listKey: "accounts-big"
            },
            class: "cell__checkbox"
          },
          {
            type: "list-icon-text-cell",
            options: {
              iconType: "particulier",
              iconStatus: "old-customer",
              text: "old"
            },
            class: "cell__text"
          },
          {
            type: "list-link-bold-top-two-liner-cell",
            options: {
              line1: "Hansen RX",
              line2: "BE04781234",
              linkTo: "dashboard",
              params: {
                mainMenuKey: "sales-marketing",
                dashboardId: "account",
                recordId: "a8d6fbfd-58ce-4f9c-0afe-5735d87a01eb"
              }
            },
            class: "cell__text"
          },
          {
            type: "list-link-pink-down-two-liner-cell",
            options: {
              line1: "Timmy Hansen",
              line2: "timmy@hansen.com",
              link: "mailto:timmy@hansen.com"
            },
            class: "cell__text"
          },
          {
            type: "list-simple-two-liner-cell",
            options: {
              line1: "Street 22",
              line2: "3456vv City"
            },
            class: "cell__text"
          },
          {
            type: "list_plus_cell",
            options: {
              id: "1234567-1234-23,4+5 5~3",
              listKey: "accounts-big",
              gridKey: "action-bar"
            },
            class: "cell__action"
          }
        ],
        rowData: {
          "id": "1234567-1234-23,4+5 5~3",
          "name": "Hansen RX",
          "contact_person|name": "Timmy Hansen",
          "contact_person|mail": "timmy@hansen.com"
        }
      }
    ];

    mockResponseList = {
      settings: {
        title: "Accounts",
        responsive,
        displayFooter: true,
        actionData: {
          "parentId": "mockId"
        }
      },
      topBar: {
        selectAll: true,
        canExportToCSV,
        buttons: [
          {
            label: "delete",
            type: "CALLBACK",
            icon: "icon-remove",
            action: {
              id: "backend-action-delete"
            },
            enabled: true
          },
          {
            label: "merge",
            icon: "icon-merge",
            type: "CALLBACK",
            action: {
              id: "backend-action-merge",
              recordId: 42
            },
            enabled: true
          }, {
            label: "merge-all",
            icon: "icon-merge",
            type: "CALLBACK",
            action: {
              id: "backend-action-merge-all",
              recordIds: [42, 1337, 666]
            },
            enabled: true
          }, {
            label: "nuke-planet",
            icon: "icon-nuke",
            type: "CALLBACK",
            action: {
              id: "backend-action-nukify-planet",
              recordId: 666
            },
            enabled: false
          }, {
            label: "action-extra-params",
            icon: "icon-merge",
            type: "CALLBACK",
            action: {
              id: "backend-action-extra-params",
              extraParamsFromRow: {
                recordIds: ["{%id%}"],
                "{%id%}": {
                  "company": "{%name%}",
                  "name": "{%contact_person|name%}",
                  "mail": "{%contact_person|mail%}"
                }
              }
            },
            enabled: true
          }, {
            label: "with-confirm",
            icon: "icon",
            type: "CALLBACK",
            action: {
              id: "backend-action-with-confirm",
              confirmAction: "Are you sure ?",
              recordId: 666
            },
            enabled: true
          }, {
            label: "with-mandatory-records",
            icon: "icon",
            type: "CALLBACK",
            action: {
              id: "backend-action-with-mandatory-records",
              mandatorySelectRecord: true,
              mandatorySelectRecordMessage: "You must select at least one record"
            },
            enabled: true
          }
        ],
        filters: [
          {
            label: "Only Active",
            key: "ACTIVE"
          },
          {
            label: "Only New",
            key: "NEW"
          }
        ],
        sortingOptions: [
          {
            label: "company name",
            key: "COMPANY_NAME"
          },
          {
            label: "firstname,lastname",
            key: "NAME"
          }
        ]
      },
      headers: [
        {
          label: ""
        },
        {
          label: "Status & type",
          colSize: "1-4"
        },
        {
          label: "company & vat",
          colSize: "1-4"
        },
        {
          label: "Contact",
          colSize: "1-4"
        },
        {
          label: "billing address",
          colSize: "1-4"
        },
        {
          label: ""
        }
      ],
      rows: rows === false ? defaultRows : rows,
      pagination: {
        page: page,
        pages: 10,
        sortBy: "NAME",
        size: 10
      },
      emptyButton
    };

    const mockResponseListExtraRowContent = {
      grid: {
        cssClasses: ["cols"],
        columns: [
          {
            size: "1-1",
            hasMargin: false,
            rows: [
              {
                type: "paragraph",
                size: "1-1",
                options: {
                  text: "paragraph text"
                }
              }
            ]
          }
        ]
      }
    };

    spyOnGetList = spyOn(listDatasource, 'getList').and.callFake(mockHelpers.resolvedPromise($q, mockResponseList));
    spyOn(listDatasource, 'getExtraRowContent').and.callFake(mockHelpers.resolvedPromise($q, mockResponseListExtraRowContent));

    scope = $rootScope.$new();
    scope.listKey = "accounts-big";
    scope.params = params;

    element = angular.element(template);
    element = $compile(element)(scope);
    $rootScope.$apply();
    $timeout.flush();

    mainDivContent = $(element.find('div')[0]);
    childrenOfMainDivContent = mainDivContent.children();
  }

  describe('initialisation function', function () {
    it('should call listDatasource.getList with only the page, sortBy and filters as arguments if no parameters are specified in the component', function () {
      compile();

      expect(listDatasource.getList).toHaveBeenCalledTimes(1);
      expect(listDatasource.getList).toHaveBeenCalledWith({
        listKey: "accounts-big",
        params: {
          page: 1
        }
      });
    });

    it('should call listDatasource.getList with the page, sortBy, filters and custom arguments set in the component', function () {
      const params = {
        recordId: 42,
        query: "Awesome things"
      };

      compile(true, params);

      expect(sidebarObserver.toggleSidebarElement).not.toHaveBeenCalled();

      expect(listDatasource.getList).toHaveBeenCalledTimes(1);
      expect(listDatasource.getList).toHaveBeenCalledWith({
        listKey: "accounts-big",
        params: {
          page: 1,
          recordId: 42,
          query: "Awesome things"
        }
      });
    });

    it('should change the default value for filters and sortBy if we have data in sessionStorage and in params', function () {
      spyOnListStatusGetSort.and.returnValue('123-234');
      spyOnListStatusGetFilters.and.returnValue({ "company": { "default": { "value": "WKY", "operator": "=" } } });

      compile(true, {filters: { "company": { "default": { "value": "Block" } } }});

      expect(sidebarObserver.toggleSidebarElement).toHaveBeenCalledTimes(1);

      expect(listDatasource.getList).toHaveBeenCalledTimes(1);
      expect(listDatasource.getList).toHaveBeenCalledWith({
        listKey: "accounts-big",
        params: {
          page: 1,
          sortBy: '123-234',
          filters: { "company": { "default": { "value": "Block", "operator": "=" } } }
        }
      });
    });

    it('should wrap "listDatasource.getList" in a "promiseUtils.useLatest"', function () {
      spyOn(promiseUtils, 'useLatest').and.callThrough();

      compile();

      expect(promiseUtils.useLatest).toHaveBeenCalledTimes(1);
      expect(promiseUtils.useLatest).toHaveBeenCalledWith(listDatasource.getList);
    });
  });

  describe('after initialisation', function () {
    beforeEach(function () {
      compile(false);
    });

    describe('main div', function () {
      it('should have some specific classes', function () {
        expect(mainDivContent.hasClass('view__list')).toBe(true);
        expect(mainDivContent.hasClass('show-options')).toBe(true);
        expect(mainDivContent.hasClass('is-compact')).toBe(true);
      });

      it('should have five children (four divs and one table) with some specific classes', function () {

        expect(childrenOfMainDivContent.length).toBe(5);

        const listHeaderElement = $(childrenOfMainDivContent[0]);
        expect(listHeaderElement.is('div')).toBe(true);
        expect(listHeaderElement.hasClass('list__header')).toBe(true);

        const listActionsElement = $(childrenOfMainDivContent[1]);
        expect(listActionsElement.is('div')).toBe(true);
        expect(listActionsElement.hasClass('list__actions')).toBe(true);

        const listContentElement = $(childrenOfMainDivContent[2]);
        expect(listContentElement.is('table')).toBe(true);
        expect(listContentElement.hasClass('list__content')).toBe(true);

        const listContentResponsiveElement = $(childrenOfMainDivContent[3]);
        expect(listContentResponsiveElement.is('div')).toBe(true);
        expect(listContentResponsiveElement.hasClass('m-table-list')).toBe(true);

        const listFooterElement = $(childrenOfMainDivContent[4]);
        expect(listFooterElement.is('div')).toBe(true);
        expect(listFooterElement.hasClass('list__footer')).toBe(true);

        // Check that the '.list__empty' is not visible
        expect(element.find('.list__empty').length).toBe(0);
      });

      describe('list__header', function () {
        it('should have a h2 inside with a specific value', function () {
          expect(element.find('div.list__header > h2').text()).toBe('Accounts');
        });
      });

      describe('list__actions', function () {
        let childrenOfListActionsElement;

        beforeEach(function () {
          childrenOfListActionsElement = $(childrenOfMainDivContent[1]).children();
        });

        it('should have three children (all divs) with specific classes', function () {

          expect($(childrenOfListActionsElement[0]).is('div')).toBe(true);
          expect($(childrenOfListActionsElement[0]).hasClass('input')).toBe(true);

          expect($(childrenOfListActionsElement[1]).is('div')).toBe(true);
          expect($(childrenOfListActionsElement[1]).hasClass('button-group')).toBe(true);
          expect($(childrenOfListActionsElement[1]).hasClass('action-buttons')).toBe(true);

          expect($(childrenOfListActionsElement[2]).is('div')).toBe(true);
          expect($(childrenOfListActionsElement[2]).hasClass('sort-options')).toBe(true);
        });

        it('should have three children with specific content', function () {
          const div0Label = $($(childrenOfListActionsElement[0]).children('label')[0]);
          expect(div0Label.length).toBe(1);
          expect(div0Label.hasClass('input__checkbox')).toBe(true);
          expect($(div0Label.children('input')[0]).attr('type')).toBe('checkbox');
          expect($(div0Label.children('span')[0]).hasClass('icon-checkmark')).toBe(true);

          const actionLinkElements = $(childrenOfListActionsElement[1]).children('a');
          expect(actionLinkElements.length).toBe(1);

          const exportSpanElements = $(actionLinkElements[0]).children('span');
          expect(exportSpanElements.length).toBe(2);
          expect($(exportSpanElements[0]).hasClass('icon-wijzigen')).toBe(true);
          expect($(exportSpanElements[1]).text()).toBe('EXPORT TO CSV');
          expect($(actionLinkElements[0]).hasClass('ng-hide')).toBe(true);

          const confirmActionElements = $(childrenOfListActionsElement[1]).children('confirm-action');
          expect(confirmActionElements.length).toBe(7);

          const deleteAction = $(confirmActionElements[0]);
          expect(deleteAction.attr('button-icon')).toBe('icon-remove');
          expect(deleteAction.attr('button-label')).toBe('DELETE');
          expect(deleteAction.attr('confirm-message')).toBe('');
          expect(deleteAction.attr('action')).toBe('listController.topBarActionClicked(button)');

          const mergeAction = $(confirmActionElements[1]);
          expect(mergeAction.attr('button-icon')).toBe('icon-merge');
          expect(mergeAction.attr('button-label')).toBe('MERGE');
          expect(mergeAction.attr('confirm-message')).toBe('');
          expect(mergeAction.attr('action')).toBe('listController.topBarActionClicked(button)');

          const mergeAllAction = $(confirmActionElements[2]);
          expect(mergeAllAction.attr('button-icon')).toBe('icon-merge');
          expect(mergeAllAction.attr('button-label')).toBe('MERGE-ALL');
          expect(mergeAllAction.attr('confirm-message')).toBe('');
          expect(mergeAllAction.attr('action')).toBe('listController.topBarActionClicked(button)');

          const nukePlanetAction = $(confirmActionElements[3]);
          expect(nukePlanetAction.attr('button-icon')).toBe('icon-nuke');
          expect(nukePlanetAction.attr('button-label')).toBe('NUKE-PLANET');
          expect(nukePlanetAction.attr('confirm-message')).toBe('');
          expect(nukePlanetAction.attr('action')).toBe('listController.topBarActionClicked(button)');

          const extraParamsAction = $(confirmActionElements[4]);
          expect(extraParamsAction.attr('button-icon')).toBe('icon-merge');
          expect(extraParamsAction.attr('button-label')).toBe('ACTION-EXTRA-PARAMS');
          expect(extraParamsAction.attr('confirm-message')).toBe('');
          expect(extraParamsAction.attr('action')).toBe('listController.topBarActionClicked(button)');

          const withConfirmAction = $(confirmActionElements[5]);
          expect(withConfirmAction.attr('button-icon')).toBe('icon');
          expect(withConfirmAction.attr('button-label')).toBe('WITH-CONFIRM');
          expect(withConfirmAction.attr('confirm-message')).toBe('Are you sure ?');
          expect(withConfirmAction.attr('action')).toBe('listController.topBarActionClicked(button)');

          const div2Div = $(childrenOfListActionsElement[2]).children('div');
          expect(div2Div.length).toBe(1);
          expect($(div2Div[0]).hasClass('input')).toBe(true);

          const div2DivDivInput = $(div2Div[0]).children('div').children('input');
          expect(div2DivDivInput.length).toBe(1);
          expect($(div2DivDivInput[0]).attr('name')).toBe('quick-search');

          const div3Div = $(childrenOfListActionsElement[3]).children('div');
          expect(div3Div.length).toBe(1);
          expect($(div3Div[0]).hasClass('input')).toBe(true);

          const div3DivDivSelect = $(div3Div[0]).children('div').children('select');
          expect(div3DivDivSelect.length).toBe(1);
          expect($(div3DivDivSelect[0]).attr('name')).toBe('input-selected');

          const div3DivDivSelectOptions = div3DivDivSelect.children("option");
          expect(div3DivDivSelectOptions.size()).toBe(3);
          expect($(div3DivDivSelectOptions[0]).val()).toBe('');
          expect($(div3DivDivSelectOptions[0]).text()).toBe('Sort by');
          expect($(div3DivDivSelectOptions[1]).val()).toBe('string:COMPANY_NAME');
          expect($(div3DivDivSelectOptions[1]).text()).toBe('company name');
          expect($(div3DivDivSelectOptions[2]).val()).toBe('string:NAME');
          expect($(div3DivDivSelectOptions[2]).text()).toBe('firstname,lastname');
        });
      });

    });
  });

  describe('table list', function () {
    beforeEach(function () {
      compile(false);
    });
    it('should have a header row', function () {
      const childrenOfHeaderRow = $(element.find('table.list__content thead tr')[0]).children();

      expect(childrenOfHeaderRow.length).toBe(6);

      expect($(childrenOfHeaderRow[0]).text()).toBe('');
      expect($(childrenOfHeaderRow[0]).hasClass('list__cell')).toBe(true);
      expect($(childrenOfHeaderRow[0]).is('th')).toBe(true);

      expect($(childrenOfHeaderRow[1]).text()).toBe('Status & type');
      expect($(childrenOfHeaderRow[1]).hasClass('list__cell')).toBe(true);
      expect($(childrenOfHeaderRow[1]).is('th')).toBe(true);

      expect($(childrenOfHeaderRow[2]).text()).toBe('company & vat');
      expect($(childrenOfHeaderRow[2]).hasClass('list__cell')).toBe(true);
      expect($(childrenOfHeaderRow[2]).is('th')).toBe(true);

      expect($(childrenOfHeaderRow[3]).text()).toBe('Contact');
      expect($(childrenOfHeaderRow[3]).hasClass('list__cell')).toBe(true);
      expect($(childrenOfHeaderRow[3]).is('th')).toBe(true);

      expect($(childrenOfHeaderRow[4]).text()).toBe('billing address');
      expect($(childrenOfHeaderRow[4]).hasClass('list__cell')).toBe(true);
      expect($(childrenOfHeaderRow[4]).is('th')).toBe(true);

      expect($(childrenOfHeaderRow[5]).text()).toBe('');
      expect($(childrenOfHeaderRow[5]).hasClass('list__cell')).toBe(true);
      expect($(childrenOfHeaderRow[5]).is('th')).toBe(true);
    });

    it('should render rows based on received information', function () {
      const rowsOfTable = element.find('table.list__content tbody tr');

      expect(rowsOfTable.length).toBe(4);
      expect($(rowsOfTable[0]).hasClass('list__row')).toBe(true);
      expect($(rowsOfTable[1]).hasClass('row__extra_content')).toBe(true);
      expect($(rowsOfTable[1]).attr('id')).toBe('placeholder-row-123123345');
      expect($(rowsOfTable[2]).hasClass('list__row')).toBe(true);
      expect($(rowsOfTable[3]).hasClass('row__extra_content')).toBe(true);
      expect($(rowsOfTable[3]).attr('id')).toBe('placeholder-row-1234567-1234-23,4+5 5~3');

      const childrenOfFirstRow = $(rowsOfTable[0]).children();
      let child;

      expect(childrenOfFirstRow.length).toBe(6);

      child = $(childrenOfFirstRow[0]).children('list-checkbox-cell');
      expect($(childrenOfFirstRow[0]).is('td')).toBe(true);
      expect($(childrenOfFirstRow[0]).hasClass('list__cell')).toBe(true);
      expect($(childrenOfFirstRow[0]).hasClass('cell__checkbox')).toBe(true);
      expect(child.length).toBe(1);
      expect($(child).attr('id')).toBe('123123345');

      child = $(childrenOfFirstRow[1]).children('list-icon-text-cell');
      expect($(childrenOfFirstRow[1]).is('td')).toBe(true);
      expect($(childrenOfFirstRow[1]).hasClass('list__cell')).toBe(true);
      expect($(childrenOfFirstRow[1]).hasClass('cell__text')).toBe(true);
      expect(child.length).toBe(1);
      expect($(child).attr('icon-type')).toBe('bedrijf');
      expect($(child).attr('icon-status')).toBe('prospect');
      expect($(child).attr('text')).toBe('prospect');

      child = $(childrenOfFirstRow[2]).children('list-link-bold-top-two-liner-cell');
      expect($(childrenOfFirstRow[2]).is('td')).toBe(true);
      expect($(childrenOfFirstRow[2]).hasClass('list__cell')).toBe(true);
      expect($(childrenOfFirstRow[2]).hasClass('cell__text')).toBe(true);
      expect(child.length).toBe(1);
      expect($(child).attr('line-1')).toBe('WKY 2');
      expect($(child).attr('line-2')).toBe('BE012345678');
      expect($(child).attr('link-to')).toBe('dashboard');
      expect($(child).attr('params')).toBe('{"mainMenuKey":"sales-marketing","dashboardId":"account","recordId":"a8d6fbfd-58ce-4f9c-0afe-5735d87a01eb"}');

      child = $(childrenOfFirstRow[3]).children('list-link-pink-down-two-liner-cell');
      expect($(childrenOfFirstRow[3]).is('td')).toBe(true);
      expect($(childrenOfFirstRow[3]).hasClass('list__cell')).toBe(true);
      expect($(childrenOfFirstRow[3]).hasClass('cell__text')).toBe(true);
      expect(child.length).toBe(1);
      expect($(child).attr('line-1')).toBe('Ken Block');
      expect($(child).attr('line-2')).toBe('ken@bblock.com');
      expect($(child).attr('link')).toBe('mailto:ken@bblock.com');

      child = $(childrenOfFirstRow[4]).children('list-simple-two-liner-cell');
      expect($(childrenOfFirstRow[4]).is('td')).toBe(true);
      expect($(childrenOfFirstRow[4]).hasClass('list__cell')).toBe(true);
      expect($(childrenOfFirstRow[4]).hasClass('cell__text')).toBe(true);
      expect(child.length).toBe(1);
      expect($(child).attr('line-1')).toBe('Vredestraat 22');
      expect($(child).attr('line-2')).toBe('2220 Heist op den berg');

      child = $(childrenOfFirstRow[5]).children('list-plus-cell');
      expect($(childrenOfFirstRow[5]).is('td')).toBe(true);
      expect($(childrenOfFirstRow[5]).hasClass('list__cell')).toBe(true);
      expect($(childrenOfFirstRow[5]).hasClass('cell__action')).toBe(true);
      expect(child.length).toBe(1);
      expect($(child).attr('id')).toBe('123123345');
      expect($(child).attr('grid-key')).toBe('more-info');
    });

    it('should add an action bar when the + button is clicked', function () {
      const itemId = '1234567-1234-23,4+5 5~3';
      const placeHolder = $(element.find('tr#placeholder-row-' + itemId.replace(/(:|\.|\[|\]|,|=|@|~| |\+)/g, "\\$1"))[0]);
      const placeHolderCell = $(placeHolder.children()[0]);

      expect(placeHolder.hasClass('is-open')).toBe(false);
      expect(placeHolderCell.text()).toBe('');

      spyOn($rootScope, '$new').and.callThrough();

      const actionData = { parentId: "mockId" };
      listObserver.toggleExtraRowContentPlaceholder('accounts-big', 'gridKey', itemId, actionData);
      $rootScope.$apply();
      $timeout.flush();

      expect(listDatasource.getExtraRowContent).toHaveBeenCalledTimes(1);
      expect(listDatasource.getExtraRowContent).toHaveBeenCalledWith({
        gridKey: 'gridKey',
        listKey: 'accounts-big',
        itemId: '1234567-1234-23%2C4%2B5%205~3',
        actionData: actionData
      });

      expect(placeHolder.hasClass('is-open')).toBe(true);
      expect(placeHolderCell.find('paragraph').length).toBe(1);
      expect($(placeHolderCell.find('paragraph')[0]).attr('text')).toBe('paragraph text');

      listObserver.toggleExtraRowContentPlaceholder('accounts-big', 'gridKey', itemId);
      $rootScope.$apply();

      expect(placeHolder.hasClass('is-open')).toBe(false);
    });

    it('should destroy the scope of a rendered row when the element is destroyed', function () {

      const listIconTextCell = element.find('table.list__content tbody tr');
      const listIconTextCellScope = $(listIconTextCell).scope();
      expect(listIconTextCellScope.$$destroyed).toBe(false);

      //Remove the element and check that the scope is destroyed.
      listIconTextCell.remove();
      expect(listIconTextCellScope.$$destroyed).toBe(true);
    });
  });

  describe('responsive list', function () {
    beforeEach(function () {
      const defaultRows = [
        {
          id: "123123345",
          class: "test",
          cells: [
            {
              type: "list_checkbox_cell",
              options: {
                id: "123123345",
                listKey: "accounts-big"
              },
              class: "cell__checkbox"
            },
            {
              type: "list-icon-text-cell",
              options: {
                iconType: "bedrijf",
                iconStatus: "prospect",
                text: "prospect"
              },
              class: "cell__text"
            },
            {
              type: "list-link-bold-top-two-liner-cell",
              "responsive-header": true,
              options: {
                line1: "WKY 2",
                line2: "BE012345678",
                linkTo: "dashboard",
                params: {
                  mainMenuKey: "sales-marketing",
                  dashboardId: "account",
                  recordId: "a8d6fbfd-58ce-4f9c-0afe-5735d87a01eb"
                }
              },
              class: "cell__text"
            },
            {
              type: "list-link-pink-down-two-liner-cell",
              options: {
                line1: "Ken Block",
                line2: "ken@bblock.com",
                link: "mailto:ken@bblock.com"
              },
              class: "cell__text"
            },
            {
              type: "list-simple-two-liner-cell",
              options: {
                line1: "Vredestraat 22",
                line2: "2220 Heist op den berg"
              },
              class: "cell__text"
            },
            {
              type: "list_plus_cell",
              options: {
                id: "123123345",
                listKey: "accounts-big",
                gridKey: "more-info"
              },
              class: "cell__action"
            }
          ],
          rowData: {
            "id": "123123345",
            "name": "WKY 2",
            "contact_person|name": "Ken Block",
            "contact_person|mail": "ken@bblock.com"
          }
        },
        {
          id: "1234567-1234-23,4+5 5~3",
          class: "test",
          cells: [
            {
              type: "list_checkbox_cell",
              options: {
                id: "1234567-1234-23,4+5 5~3",
                listKey: "accounts-big"
              },
              class: "cell__checkbox"
            },
            {
              type: "list-icon-text-cell",
              options: {
                iconType: "particulier",
                iconStatus: "old-customer",
                text: "old"
              },
              class: "cell__text"
            },
            {
              type: "list-link-bold-top-two-liner-cell",
              options: {
                line1: "Hansen RX",
                line2: "BE04781234",
                linkTo: "dashboard",
                params: {
                  mainMenuKey: "sales-marketing",
                  dashboardId: "account",
                  recordId: "a8d6fbfd-58ce-4f9c-0afe-5735d87a01eb"
                }
              },
              class: "cell__text"
            },
            {
              type: "list-link-pink-down-two-liner-cell",
              options: {
                line1: "Timmy Hansen",
                line2: "timmy@hansen.com",
                link: "mailto:timmy@hansen.com"
              },
              class: "cell__text"
            },
            {
              type: "list-simple-two-liner-cell",
              options: {
                line1: "Street 22",
                line2: "3456vv City"
              },
              class: "cell__text"
            },
            {
              type: "list_plus_cell",
              options: {
                id: "1234567-1234-23,4+5 5~3",
                listKey: "accounts-big",
                gridKey: "action-bar"
              },
              class: "cell__action"
            }
          ],
          rowData: {
            "id": "1234567-1234-23,4+5 5~3",
            "name": "Hansen RX",
            "contact_person|name": "Timmy Hansen",
            "contact_person|mail": "timmy@hansen.com"
          }
        },
        {
          id: "123123345-4",
          class: "test",
          cells: [
            {
              type: "list_checkbox_cell",
              options: {
                id: "123123345-4",
                listKey: "accounts-big"
              },
              class: "cell__checkbox"
            }
          ],
          rowData: {
            "id": "123123345-4",
            "name": "WKY 2",
            "contact_person|name": "Ken Block",
            "contact_person|mail": "ken@bblock.com"
          }
        },
        {
          id: "123123345-4",
          class: "test",
          cells: [
            {
              type: "list_checkbox_cell",
              options: {
                id: "123123345-4",
                listKey: "accounts-big"
              },
              class: "cell__checkbox"
            },
            {
              type: "list-icon-text-cell",
              options: {
                iconType: "bedrijf",
                iconStatus: "prospect",
                text: "prospect"
              },
              class: "cell__text"
            }
          ],
          rowData: {
            "id": "123123345-4",
            "name": "WKY 2",
            "contact_person|name": "Ken Block",
            "contact_person|mail": "ken@bblock.com"
          }
        }
      ];
      compile(true, {}, 1, defaultRows, undefined, true);
    });

    it('should render rows based on received information', function () {
      const rowsOfTable = $(element.find('.m-table-list')[0]).children();
      expect(rowsOfTable.length).toBe(8);

      expect($(rowsOfTable[0]).hasClass('m-table-list__item')).toBe(true);
      expect($(rowsOfTable[1]).hasClass('row__extra_content')).toBe(true);
      expect($(rowsOfTable[1]).attr('id')).toBe('placeholder-row-123123345');

      expect($(rowsOfTable[2]).hasClass('m-table-list__item')).toBe(true);
      expect($(rowsOfTable[3]).hasClass('row__extra_content')).toBe(true);
      expect($(rowsOfTable[3]).attr('id')).toBe('placeholder-row-1234567-1234-23,4+5 5~3');

      const childrenOfFirstRow = $(rowsOfTable[0]).children('div');
      expect(childrenOfFirstRow.length).toBe(6);

      let child;

      child = $(childrenOfFirstRow[0]).children('list-checkbox-cell');
      expect($(childrenOfFirstRow[0]).hasClass('cell__checkbox')).toBe(true);
      expect($(childrenOfFirstRow[0]).hasClass('m-table-list__item__abstract')).toBe(true);
      expect(child.length).toBe(1);
      expect($(child).attr('id')).toBe('123123345');

      child = $(childrenOfFirstRow[1]).children('list-icon-text-cell');
      expect($(childrenOfFirstRow[1]).hasClass('cell__text')).toBe(true);
      expect(child.length).toBe(1);
      expect($(child).attr('icon-type')).toBe('bedrijf');
      expect($(child).attr('icon-status')).toBe('prospect');
      expect($(child).attr('text')).toBe('prospect');

      child = $(childrenOfFirstRow[2]).children('list-link-bold-top-two-liner-cell');
      expect($(childrenOfFirstRow[2]).hasClass('cell__text')).toBe(true);
      expect(child.length).toBe(1);
      expect($(child).attr('line-1')).toBe('WKY 2');
      expect($(child).attr('line-2')).toBe('BE012345678');
      expect($(child).attr('link-to')).toBe('dashboard');
      expect($(child).attr('params')).toBe('{"mainMenuKey":"sales-marketing","dashboardId":"account","recordId":"a8d6fbfd-58ce-4f9c-0afe-5735d87a01eb"}');

      child = $(childrenOfFirstRow[3]).children('list-link-pink-down-two-liner-cell');
      expect($(childrenOfFirstRow[3]).hasClass('cell__text')).toBe(true);
      expect(child.length).toBe(1);
      expect($(child).attr('line-1')).toBe('Ken Block');
      expect($(child).attr('line-2')).toBe('ken@bblock.com');
      expect($(child).attr('link')).toBe('mailto:ken@bblock.com');

      child = $(childrenOfFirstRow[4]).children('list-simple-two-liner-cell');
      expect($(childrenOfFirstRow[4]).hasClass('cell__text')).toBe(true);
      expect(child.length).toBe(1);
      expect($(child).attr('line-1')).toBe('Vredestraat 22');
      expect($(child).attr('line-2')).toBe('2220 Heist op den berg');

      child = $(childrenOfFirstRow[5]).children('list-plus-cell');
      expect($(childrenOfFirstRow[5]).hasClass('cell__action')).toBe(true);
      expect($(childrenOfFirstRow[5]).hasClass('m-table-list__item__abstract')).toBe(true);
      expect(child.length).toBe(1);
      expect($(child).attr('id')).toBe('123123345');
      expect($(child).attr('grid-key')).toBe('more-info');
    });

    it('should toggle a responsive row', function () {
      const rowsOfTable = $(element.find('.m-table-list')[0]).children();
      expect(rowsOfTable.length).toBe(8);

      const row1Data = $(rowsOfTable[0]);
      const row1Arrow = row1Data.children('span');

      expect(row1Data.hasClass('is-open')).toBe(false);

      row1Arrow.click();

      expect(row1Data.hasClass('is-open')).toBe(true);
      row1Arrow.click();
      expect(row1Data.hasClass('is-open')).toBe(false);
    });

    it('should add an action bar when the + button is clicked', function () {
      const itemId = '123123345';
      const placeHolder = $(element.find('div#placeholder-row-' + itemId)[0]);

      expect(placeHolder.hasClass('is-open')).toBe(false);
      expect(placeHolder.text()).toBe('');

      spyOn($rootScope, '$new').and.callThrough();

      const actionData = { parentId: "mockId" };
      listObserver.toggleExtraRowContentPlaceholder('accounts-big', 'gridKey', itemId, actionData);
      $rootScope.$apply();
      $timeout.flush();

      expect(listDatasource.getExtraRowContent).toHaveBeenCalledTimes(1);
      expect(listDatasource.getExtraRowContent).toHaveBeenCalledWith({
        gridKey: 'gridKey',
        listKey: 'accounts-big',
        itemId: '123123345',
        actionData: actionData
      });

      expect(placeHolder.hasClass('is-open')).toBe(true);
      expect(placeHolder.find('paragraph').length).toBe(1);
      expect($(placeHolder.find('paragraph')[0]).attr('text')).toBe('paragraph text');

      listObserver.toggleExtraRowContentPlaceholder('accounts-big', 'gridKey', itemId);
      $rootScope.$apply();

      expect(placeHolder.hasClass('is-open')).toBe(false);
    });

    it('should change to a normal list on resize', function () {

      expect(element.find('.m-table-list div.m-table-list__item').length).toBe(4);
      expect(element.find('table.list__content tbody tr').length).toBe(0);

      //resize to a ig screen
      $window.innerWidth = 1026;
      angular.element($window).triggerHandler('resize');
      $rootScope.$apply();
      scope.$destroy();

      expect(element.find('.m-table-list div.m-table-list__item').length).toBe(0);
      expect(element.find('table.list__content tbody tr').length).toBe(8);

      $window.innerWidth = 959;
      angular.element($window).triggerHandler('resize');
      $rootScope.$apply();
      scope.$destroy();

      expect(element.find('.m-table-list div.m-table-list__item').length).toBe(4);
      expect(element.find('table.list__content tbody tr').length).toBe(0);
    });

    it('should destroy the scope of a rendered component when the element is destroyed', function () {

      const listIconTextCell = element.find('div.m-table-list__item');
      const listIconTextCellScope = $(listIconTextCell).scope();
      expect(listIconTextCellScope.$$destroyed).toBe(false);

      //Remove the element and check that the scope is destroyed.
      listIconTextCell.remove();
      expect(listIconTextCellScope.$$destroyed).toBe(true);
    });
  });

  describe('empty list', function () {
    it('should render the "empty" messages when there is no row content', function () {
      compile(false, {}, 1, []);

      const emptyHeaderElement = $(element.find('.list__empty > h2'));
      expect(emptyHeaderElement.text()).toBe('no entries in this list');

      const emptyMessageElement = $(element.find('.list__empty > h4'));
      expect(emptyMessageElement.text()).toBe("It seems like this list is still empty.");

      // Check that the button is not rendered when emptyButton is empty
      expect(element.find('.list__empty > a.button').length).toBe(0);
    });

    it('should render a working "emptyButton" when it is defined', function () {
      spyOn(actionDatasource, 'performAndHandle');

      const emptyButton = {
        label: "Add company",
        action: {
          id: "backend-action-delete"
        }
      };

      compile(false, {}, 1, [], emptyButton);

      const emptyButtonElement = $(element.find('.list__empty > a.button'));
      expect(emptyButtonElement.text()).toContain('Add company');

      emptyButtonElement.click();
      $rootScope.$apply();

      expect(actionDatasource.performAndHandle).toHaveBeenCalledTimes(1);
      expect(actionDatasource.performAndHandle).toHaveBeenCalledWith({
        id: 'backend-action-delete'
      });
    });
  });

  it('should show a loading indicator when the list is loading', function () {
    compile();

    const listPromise = $q.defer();

    spyOnGetList.and.returnValue(listPromise.promise);

    const nextPageButton = $(element.find('#next-page-1 > a')[0]);

    nextPageButton.click();
    $rootScope.$apply();

    const emptyHeaderElement = $(element.find('.list__empty > h4'));
    expect(emptyHeaderElement.text()).toBe('Loading list...');

    listPromise.resolve(mockResponseList);

    $rootScope.$apply();

    // Check that the '.list__empty' is not visible
    expect(element.find('.list__empty').length).toBe(0);
  });

  it('should reload the page when pagination is clicked', function () {
    compile();
    expect(listStatus.setPage).toHaveBeenCalledTimes(1);

    spyOnGetList.and.callFake(mockHelpers.resolvedPromise($q, mockResponseList));

    const nextPageButton = $(element.find('#next-page-1 > a')[0]);

    let rowsOfTable = element.find('table tbody tr');
    expect(rowsOfTable.length).toBe(4);

    expect(listDatasource.getList).toHaveBeenCalledTimes(1);
    expect(listDatasource.getList.calls.argsFor(0)[0]).toEqual({
      listKey: 'accounts-big',
      params: { page: 1 }
    });

    nextPageButton.click();
    $rootScope.$apply();
    $timeout.flush();

    expect(listStatus.setPage).toHaveBeenCalledTimes(3); //one first load, second from button, 3rd from second load
    expect(listStatus.setPage).toHaveBeenCalledWith('accounts-big', 2);
    rowsOfTable = element.find('table tbody tr');
    expect(rowsOfTable.length).toBe(4);

    expect(listDatasource.getList).toHaveBeenCalledTimes(2);
    expect(listDatasource.getList.calls.argsFor(1)[0]).toEqual({
      listKey: 'accounts-big',
      params: { page: 2 }
    });

  });

  it('should make a second call when the page is > 1 and the response is empty', function () {
    compile();
    expect(listDatasource.getList).toHaveBeenCalledTimes(1);
    expect(listDatasource.getList.calls.argsFor(0)[0]).toEqual({
      listKey: 'accounts-big',
      params: { page: 1 }
    });

    const mockResponseListEmpty = angular.copy(mockResponseList);
    mockResponseListEmpty.rows = [];
    spyOnGetList.and.callFake(mockHelpers.resolvedPromise($q, mockResponseListEmpty));
    $(element.find('#next-page-2 > a')[0]).click();
    $rootScope.$apply();

    expect(listDatasource.getList).toHaveBeenCalledTimes(3);
    expect(listDatasource.getList.calls.argsFor(1)[0]).toEqual({
      listKey: 'accounts-big',
      params: { page: 3 }
    });

    expect(listDatasource.getList.calls.argsFor(2)[0]).toEqual({
      listKey: 'accounts-big',
      params: { page: 1 }
    });
  });

  it('should reload the page when sort changes', function () {
    compile(true, {}, 4);

    spyOnGetList.and.callFake(mockHelpers.resolvedPromise($q, mockResponseList));
    const sortBySelect = $(element.find('div.sort-options select')[0]);

    let rowsOfTable = element.find('table tbody tr');
    expect(rowsOfTable.length).toBe(4);

    expect(listDatasource.getList).toHaveBeenCalledTimes(1);
    expect(listDatasource.getList.calls.argsFor(0)[0]).toEqual({
      listKey: 'accounts-big',
      params: { page: 1 }
    });

    rowsOfTable = element.find('table tbody tr');
    expect(rowsOfTable.length).toBe(4);

    const options = sortBySelect.children("option");
    expect(options.size()).toBe(3);

    $(options[2]).prop('selected', true).change();
    $rootScope.$apply();
    $timeout.flush();

    expect(listDatasource.getList).toHaveBeenCalledTimes(2);
    expect(listDatasource.getList.calls.argsFor(1)[0]).toEqual({
      listKey: 'accounts-big',
      params: { page: 1, sortBy: 'NAME' }
    });

    // the rows of table are generated by component controller and appended to the table element
    // this why I have to select them again
    rowsOfTable = element.find('table tbody tr');
    expect(rowsOfTable.length).toBe(4);

    expect(listStatus.setSort).toHaveBeenCalledTimes(1);
    expect(listStatus.setSort).toHaveBeenCalledWith('accounts-big', 'NAME');
  });

  it('should reload the page when filter changes', function () {
    compile(true, {}, 4);

    spyOnGetList.and.callFake(mockHelpers.resolvedPromise($q, mockResponseList));

    let rowsOfTable = element.find('table tbody tr');
    expect(rowsOfTable.length).toBe(4);

    expect(listDatasource.getList).toHaveBeenCalledTimes(1);
    expect(listDatasource.getList.calls.argsFor(0)[0]).toEqual({
      listKey: 'accounts-big',
      params: { page: 1 }
    });

    rowsOfTable = element.find('table tbody tr');
    expect(rowsOfTable.length).toBe(4);

    filtersObserver.filtersHaveChanged('accounts-big', {
      first_name: "Ken",
      last_name: "Block"
    });

    $rootScope.$apply();
    $timeout.flush();

    rowsOfTable = element.find('table tbody tr');
    expect(rowsOfTable.length).toBe(4);

    expect(listDatasource.getList).toHaveBeenCalledTimes(2);
    expect(listDatasource.getList.calls.argsFor(1)[0]).toEqual({
      listKey: 'accounts-big',
      params: {
        page: 1,
        filters: { first_name: 'Ken', last_name: 'Block' },
        quickSearch: ''
      }
    });
  });

  it('should reload the page when quickSearch changes', function () {
    compile();

    spyOnGetList.and.callFake(mockHelpers.resolvedPromise($q, mockResponseList));

    let rowsOfTable = element.find('table tbody tr');
    expect(rowsOfTable.length).toBe(4);

    expect(listDatasource.getList).toHaveBeenCalledTimes(1);
    expect(listDatasource.getList.calls.argsFor(0)[0]).toEqual({
      listKey: 'accounts-big',
      params: { page: 1 }
    });

    const quickSearchField = element.find('[name="quick-search"]');
    quickSearchField.val('Ken').change();
    $rootScope.$apply();
    $timeout.flush();

    rowsOfTable = element.find('table tbody tr');
    expect(rowsOfTable.length).toBe(4);

    expect(listDatasource.getList).toHaveBeenCalledTimes(2);
    expect(listDatasource.getList.calls.argsFor(1)[0]).toEqual({
      listKey: 'accounts-big',
      params: {
        page: 1,
        quickSearch: 'Ken'
      }
    });

    quickSearchField.val('').change();
    $rootScope.$apply();
    $timeout.flush();
  });

  it('should select all items checkboxes when the "Select All" checkbox is ticked', function () {
    compile();

    const selectAllCheckbox = $(element.find('div.list__actions div.input label.input__checkbox input')[0]);
    const checkboxes = element.find('list-checkbox-cell input');

    expect($(checkboxes[0]).is(':checked')).toBe(false);
    expect($(checkboxes[1]).is(':checked')).toBe(false);

    selectAllCheckbox.click();
    expect($(checkboxes[0]).is(':checked')).toBe(true);
    expect($(checkboxes[1]).is(':checked')).toBe(true);

    selectAllCheckbox.click();

    expect($(checkboxes[0]).is(':checked')).toBe(false);
    expect($(checkboxes[1]).is(':checked')).toBe(false);
  });

  describe('Top button actions', function () {
    it('should make a actionDatasource call when the top buttons are clicked', function () {
      compile();

      spyOn(actionDatasource, 'performAndHandle').and.callFake(mockHelpers.resolvedPromise($q, "refresh"));

      const deleteButton = $(element.find('div.action-buttons a')[0]);

      listObserver.toggleListRowSelection('accounts-big', '123123345', true);

      deleteButton.click();

      expect(actionDatasource.performAndHandle).toHaveBeenCalledTimes(1);
      expect(actionDatasource.performAndHandle).toHaveBeenCalledWith({
        id: "backend-action-delete",
        recordIds: ["123123345"]
      });

      listObserver.toggleListRowSelection('accounts-big', '1234567-1234-23,4+5 5~3', true);
      deleteButton.click();

      expect(actionDatasource.performAndHandle).toHaveBeenCalledTimes(2);
      expect(actionDatasource.performAndHandle).toHaveBeenCalledWith({
        id: "backend-action-delete",
        recordIds: ["123123345", "1234567-1234-23,4+5 5~3"]
      });

      listObserver.toggleListRowSelection('accounts-big', '123123345', false);
      deleteButton.click();

      expect(actionDatasource.performAndHandle).toHaveBeenCalledTimes(3);
      expect(actionDatasource.performAndHandle).toHaveBeenCalledWith({
        id: "backend-action-delete",
        recordIds: ["1234567-1234-23,4+5 5~3"]
      });
    });

    it('should not set recordIds when they are already set in the action object', function () {
      compile();

      spyOn(actionDatasource, 'performAndHandle').and.callFake(mockHelpers.resolvedPromise($q, "refresh"));
      const mergeButton = $(element.find('div.action-buttons a[name="MERGE"]')[0]);
      mergeButton.click();

      expect(actionDatasource.performAndHandle).toHaveBeenCalledTimes(1);
      expect(actionDatasource.performAndHandle).toHaveBeenCalledWith({ id: "backend-action-merge", recordId: 42 });

      const mergeAllButton = $(element.find('div.action-buttons a[name="MERGE-ALL"]')[0]);
      mergeAllButton.click();

      expect(actionDatasource.performAndHandle).toHaveBeenCalledTimes(2);
      expect(actionDatasource.performAndHandle).toHaveBeenCalledWith({
        id: "backend-action-merge-all",
        recordIds: [42, 1337, 666]
      });
    });

    it('should do anything when the button is disabled', function () {
      compile();

      spyOn(actionDatasource, 'performAndHandle').and.callFake(mockHelpers.resolvedPromise($q, "refresh"));

      const nukeButton = $(element.find('div.action-buttons a[name="NUKE-PLANET"]')[0]);
      nukeButton.click();

      expect(actionDatasource.performAndHandle).not.toHaveBeenCalled();
    });

    it('should merge the data from row when we have `extraParamsFromRow`', function () {
      compile();

      spyOn(actionDatasource, 'performAndHandle').and.callFake(mockHelpers.resolvedPromise($q, "refresh"));

      listObserver.toggleListRowSelection('accounts-big', 123123345, true);

      const extraActionParamButton = $(element.find('div.action-buttons a[name="ACTION-EXTRA-PARAMS"]')[0]);
      extraActionParamButton.click();

      expect(actionDatasource.performAndHandle).toHaveBeenCalledTimes(1);
      expect(actionDatasource.performAndHandle).toHaveBeenCalledWith({
        "id": "backend-action-extra-params",
        "recordIds": ["123123345"],
        "123123345": { company: "WKY 2", name: "Ken Block", mail: "ken@bblock.com" }
      });

      listObserver.toggleListRowSelection('accounts-big', '1234567-1234-23,4+5 5~3', true);

      extraActionParamButton.click();
      expect(actionDatasource.performAndHandle).toHaveBeenCalledTimes(2);
      expect(actionDatasource.performAndHandle).toHaveBeenCalledWith({
        "id": "backend-action-extra-params",
        "recordIds": ["123123345", "1234567-1234-23,4+5 5~3"],
        "123123345": { company: "WKY 2", name: "Ken Block", mail: "ken@bblock.com" },
        "1234567-1234-23,4+5 5~3": { company: "Hansen RX", name: "Timmy Hansen", mail: "timmy@hansen.com" }
      });

    });

    it('should check if we have selected records when the action has `mandatorySelectRecord`', function () {
      compile();

      spyOn(actionDatasource, 'performAndHandle').and.callFake(mockHelpers.resolvedPromise($q, "refresh"));
      spyOn(commandHandler, 'handle');


      const extraActionParamButton = $(element.find('div.action-buttons a[name="WITH-MANDATORY-RECORDS"]')[0]);
      extraActionParamButton.click();
      expect(actionDatasource.performAndHandle).not.toHaveBeenCalled();
      expect(commandHandler.handle).toHaveBeenCalledTimes(1);
      expect(commandHandler.handle).toHaveBeenCalledWith({
        command: 'popUpMessage',
        arguments: { message: 'You must select at least one record', title: 'INFO' }
      });

      listObserver.toggleListRowSelection('accounts-big', 123123345, true);
      extraActionParamButton.click();

      expect(actionDatasource.performAndHandle).toHaveBeenCalledTimes(1);
      expect(actionDatasource.performAndHandle).toHaveBeenCalledWith({
        "id": "backend-action-with-mandatory-records",
        "recordIds": [123123345]
      });
    });

  });

  it('should reload the page when "registerReloadListCallback" triggers', function () {
    compile();

    listObserver.reloadList('accounts-big');

    $rootScope.$apply();

    // The first listDatasource.getList will be done on initialization.
    expect(listDatasource.getList).toHaveBeenCalledTimes(2);
    expect(listDatasource.getList).toHaveBeenCalledWith({
      listKey: "accounts-big",
      params: {
        page: 1,
        quickSearch: ''
      }
    });
  });

  it("should destroy the list row cell's scopes when destroying the list scope", function () {
    compile();

    const cellContentElements = element.find("table tbody tr:first-of-type td").children();
    const scopes = _.map(cellContentElements, (element) => $(element).scope());

    // At first we expect none of the cell scopes to be destroyed.
    expect(_.some(scopes, '$$destroyed')).toBe(false);

    // Now we destroy the list's scope
    scope.$destroy();

    // And we expect all the cell scopes to be destroyed.
    expect(_.every(scopes, '$$destroyed')).toBe(true);
  });

  it("should destroy the extra row content's scope when destroying the list scope", function () {
    compile();

    const itemId = '123123345';
    const actionData = { parentId: "mockId" };
    const placeHolder = $(element.find('tr#placeholder-row-' + itemId)[0]);

    // Trigger the extra row to be opened
    listObserver.toggleExtraRowContentPlaceholder('accounts-big', 'gridKey', itemId, actionData);
    $rootScope.$apply();

    // Check that it was indeed requested
    expect(listDatasource.getExtraRowContent).toHaveBeenCalledTimes(1);
    expect(listDatasource.getExtraRowContent).toHaveBeenCalledWith({
      gridKey: 'gridKey',
      listKey: 'accounts-big',
      itemId: '123123345',
      actionData: actionData
    });

    // Check that we have a paragraph and its scope has not been destroyed.
    const paragraph = placeHolder.find("paragraph");
    const paragraphScope = paragraph.scope();
    expect(paragraph.length).toBe(1);
    expect(paragraphScope.$$destroyed).toBe(false);

    // Now we destroy the list's scope
    scope.$destroy();

    // And we expect the paragraph's scope to be destroyed
    expect(paragraphScope.$$destroyed).toBe(true);
  });

  it('should export to CSV when export to CSV button is clicked', function () {
    const promise = mockHelpers.resolvedPromise($q)();

    spyOnListStatusGetSort.and.returnValue('123-234');
    spyOnListStatusGetFilters.and.returnValue({ "company": { "default": { "value": "WKY" } } });

    spyOn(commandHandler, 'handle');
    spyOn(listDatasource, 'exportToCSV').and.returnValue(promise);

    compile(true);

    const selectAllCheckbox = $(element.find('div.list__actions div.input label.input__checkbox input')[0]);
    selectAllCheckbox.click();

    $rootScope.$apply();

    const exportToCSVLinkElement = element.find('#export-to-CSV');
    exportToCSVLinkElement.click();

    $rootScope.$apply();

    expect(listDatasource.exportToCSV).toHaveBeenCalledTimes(1);
    expect(listDatasource.exportToCSV).toHaveBeenCalledWith({
      listKey: "accounts-big",
      params: {
        sortBy: '123-234',
        filters: { "company": { "default": { "value": "WKY" } } },
        recordIds: ['123123345', '1234567-1234-23,4+5 5~3'],
        quickSearch: ''
      }
    });

    expect(commandHandler.handle).toHaveBeenCalledTimes(1);
  });
});
