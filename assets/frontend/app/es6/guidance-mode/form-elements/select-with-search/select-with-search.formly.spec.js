'use strict';

describe('Form type: select-with-search', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let scope;
  let element;
  let selectWithSearchDatasource;

  let $rootScope;
  let $compile;
  let $q;

  let guidanceFormObserver;
  let validationObserver;
  let suggestionsObserver;

  let guidanceModeBackendState;
  let elementIdGenerator;
  let ACTION_EVENT;

  const template = '<formly-form form="form" model="model" fields="fields"/>';

  beforeEach(inject(function (_$rootScope_, _$compile_, $state, _$q_, _selectWithSearchDatasource_,
                              GuidanceFormObserver, ValidationObserver, SuggestionsObserver,
                              _guidanceModeBackendState_, _ACTION_EVENT_, _elementIdGenerator_) {
    $rootScope = _$rootScope_;
    $compile = _$compile_;

    guidanceModeBackendState = _guidanceModeBackendState_;
    ACTION_EVENT = _ACTION_EVENT_;

    selectWithSearchDatasource = _selectWithSearchDatasource_;
    elementIdGenerator = _elementIdGenerator_;
    $q = _$q_;

    guidanceFormObserver = new GuidanceFormObserver();
    validationObserver = new ValidationObserver();
    suggestionsObserver = new SuggestionsObserver();

    spyOn(guidanceFormObserver, 'getFullModel').and.returnValue({ companyName: "wky" });
    spyOn(elementIdGenerator, 'generateId').and.returnValue('field-fake-id');

    mockHelpers.blockUIRouter($state);
  }));

  function compile(nace = [], multiple = true, disabled = false, required = false) {
    scope = $rootScope.$new();
    scope.model = { nace };

    scope.fields = [
      {
        id: "nace",
        key: "nace",
        type: "select-with-search",
        templateOptions: {
          label: "Nace",
          plusButtonTitle: "Add one or more NACE code(s)",
          modalTitle: "Select one or more NACE code(s)",
          selectedResultsTitle: "Selected NACE code(s)",
          multipleSelect: multiple,
          params: {property: "value"},
          datasourceName: "NACE",
          noBackendInteraction: false,
          disabled,
          required,
          readonly: false,
          readonlyJoin: ", "
        }
      }
    ];

    const guidanceFormObserverAccessorElement = mockHelpers.createGuidanceFormObserverAccessor({
      $compile,
      $rootScope,
      guidanceFormObserver,
      validationObserver,
      suggestionsObserver
    });

    element = angular.element(template);
    element = $compile(element)(scope);

    guidanceFormObserverAccessorElement.append(element);

    $rootScope.$apply();
  }

  it('should rendered the form element correctly', function () {
    compile();
    const formElement = $(element.find("select-with-search-form-element")[0]);
    const addButton = $(formElement.find('button')[0]);

    expect(elementIdGenerator.generateId).toHaveBeenCalledTimes(1);
    expect(elementIdGenerator.generateId).toHaveBeenCalledWith('nace', guidanceFormObserver);
    expect($(formElement.find('div')[0]).attr('id')).toBe('field-fake-id');

    expect(formElement.attr('id')).toEqual('nace');
    expect(formElement.attr('plus-button-title')).toEqual("Add one or more NACE code(s)");
    expect(formElement.attr('modal-title')).toEqual("Select one or more NACE code(s)");
    expect(formElement.attr('selected-results-title')).toEqual("Selected NACE code(s)");
    expect(formElement.attr('datasource-name')).toEqual("NACE");

    expect(addButton.hasClass('button-placeholder')).toBe(true);
    expect(addButton.find('span').length).toEqual(2);
    expect($(addButton.find('span')[0]).hasClass('icon-plus')).toBe(true);
    expect($(addButton.find('span')[1]).text()).toEqual('Add one or more NACE code(s)');

    expect(formElement.find('.action-list ul li').length).toEqual(0);

  });

  it('should not have rendered the modal before opening', function () {
    compile();
    expect(angular.element('select-with-search-modal').length).toBe(0);
  });

  it('should remove an item from model when the close icon is clicked', function () {
    compile([
      {
        key: "A",
        label: "A - Agriculture, forestry and fishing"
      },
      {
        key: "A1",
        label: "A1 - Crop and animal production, hunting"
      }
    ]);

    expect(scope.model.nace).toEqual([
      {
        key: "A",
        label: "A - Agriculture, forestry and fishing"
      },
      {
        key: "A1",
        label: "A1 - Crop and animal production, hunting"
      }
    ]);

    $(element.find('.action-list ul li .icon-close')[1]).click();

    expect(angular.copy(scope.model.nace)).toEqual([
      {
        key: "A",
        label: "A - Agriculture, forestry and fishing"
      }
    ]);
  });

  describe('disabled behavior', function () {
    it('should not rendered the modal when the disabled flag is true', function () {
      compile([], true, true);

      $(element.find('button')[0]).click();
      $rootScope.$apply();

      expect(angular.element('select-with-search-modal').length).toBe(0);
    });

    it('should NOT remove an item from model when the close icon is clicked and the disabled flag is true', function () {
      const model = [
        {
          key: "A",
          label: "A - Agriculture, forestry and fishing"
        },
        {
          key: "A1",
          label: "A1 - Crop and animal production, hunting"
        }
      ];
      compile(model, true, true);

      expect(scope.model.nace).toEqual(model);

      $(element.find('.action-list ul li .icon-close')[1]).click();

      expect(angular.copy(scope.model.nace)).toEqual(model);
    });

    it('should not rendered the modal when the backend is busy', function () {
      spyOn(guidanceModeBackendState, 'getBackendIsBusy').and.returnValue(true);
      spyOn(guidanceModeBackendState, 'getPerformedAction').and.returnValue({
        event: ACTION_EVENT.CHANGED,
        focus: 'field'
      });

      compile([], true, false);
      $(element.find('button')[0]).click();

      $rootScope.$apply();

      expect(angular.element('select-with-search-modal').length).toBe(0);
    });

    it('should NOT remove an item from model when the close icon is clicked and the backend is busy', function () {
      spyOn(guidanceModeBackendState, 'getBackendIsBusy').and.returnValue(true);
      spyOn(guidanceModeBackendState, 'getPerformedAction').and.returnValue({
        event: ACTION_EVENT.CHANGED,
        focus: 'field'
      });

      const model = [
        {
          key: "A",
          label: "A - Agriculture, forestry and fishing"
        },
        {
          key: "A1",
          label: "A1 - Crop and animal production, hunting"
        }
      ];
      compile(model, true, false);

      expect(scope.model.nace).toEqual(model);

      $(element.find('.action-list ul li .icon-close')[1]).click();

      expect(angular.copy(scope.model.nace)).toEqual(model);
    });
  });

  describe('readonly behavior', function () {
    it('should made the field readonly if the templateOptions.readonly property evaluates to true', function () {
      compile([], true);

      expect(element.find('button').length).toBe(1);
      expect(element.find('strong').length).toBe(0);

      scope.fields[0].templateOptions.readonly = true;
      scope.model.nace = [
        {
          key: "A",
          label: "A - Agriculture, forestry and fishing"
        },
        {
          key: "A1",
          label: "A1 - Crop and animal production, hunting"
        }
      ];
      $rootScope.$apply();

      expect(element.find('button').length).toBe(0);

      const strong = $(element.find('strong')[0]);
      expect(strong.attr('id')).toBe('field-fake-id');
      expect(strong.text()).toBe('A - Agriculture, forestry and fishing, A1 - Crop and animal production, hunting');

      // Expect no 'and x more' here because the length is 2
      expect(element.find('em').length).toBe(0);
    });

    it('should render a "... show x more" when there are more than 2 selected items', function () {
      compile([], true);

      expect(element.find('button').length).toBe(1);
      expect(element.find('strong').length).toBe(0);

      scope.fields[0].templateOptions.readonly = true;
      scope.model.nace = [
        { key: "A", label: "A" },
        { key: "B", label: "B" },
        { key: "C", label: "C" },
        { key: "D", label: "D" },
        { key: "E", label: "E" },
        { key: "F", label: "F" },
        { key: "G", label: "G" }
      ];
      $rootScope.$apply();

      expect(element.find('button').length).toBe(0);

      const strong = $(element.find('strong')[0]);
      expect(strong.attr('id')).toBe('field-fake-id');
      expect(strong.text()).toBe('A, B');

      let showMore = $(element.find('a')[0]);
      expect(showMore.text()).toBe('... show 5 more');

      showMore.click();

      const showLess = $(element.find('a')[0]);
      expect(showLess.text()).toBe('show less');

      showLess.click();

      showMore = $(element.find('a')[0]);
      expect(showMore.text()).toBe('... show 5 more');
    });
  });

  describe("the 'required' functionality", function () {
    it('should not set any errors when the field is not required', function () {
      compile([], false, false, false);
      expect(scope.form.$valid).toBe(true);
    });

    it('should set errors when the field is required and the value is empty', function () {
      compile([], false, false, true);

      expect(scope.form.$valid).toBe(false);
      expect(scope.form.nace.$error.required).toBe(true);
    });

    it('should remove errors when a value is chosen', function () {
      compile([], true, false, true);
      scope.model.nace = [
        {
          key: "A",
          label: "A - Agriculture, forestry and fishing"
        },
        {
          key: "A1",
          label: "A1 - Crop and animal production, hunting"
        }
      ];
      $rootScope.$apply();

      expect(scope.form.$valid).toBe(true);
    });

    it('should remove errors when value is changed from backend', function () {
      compile([], true, false, true);
      scope.model.nace = [
        {
          key: "A",
          label: "A - Agriculture, forestry and fishing"
        }
      ];

      validationObserver.setErrors([]);
      $rootScope.$apply();

      expect(scope.form.$valid).toBe(true);
    });
  });

  describe('after opening', function () {

    let selectWithSearchModal;
    let selectedResults;
    let nonSelectedResults;
    let spyOnSelectWithSearchDatasource;

    beforeEach(function () {
      //The initial selected elements
      compile([
        {
          key: "A",
          label: "A - Agriculture, forestry and fishing"
        },
        {
          key: "A1",
          label: "A1 - Crop and animal production, hunting"
        }
      ]);

      //The result of the first search operation
      spyOnSelectWithSearchDatasource = spyOn(selectWithSearchDatasource, 'getSelectOptions').and.callFake(mockHelpers.resolvedPromise($q, {
        rows: [{
          key: "A",
          label: "A - Agriculture, forestry and fishing"
        }, {
          key: "A1",
          label: "A1 - Crop and animal production, hunting"
        }, {
          key: "B",
          label: "B - Mining and quarrying"
        }],
        pagination: {
          page: 1,
          pages: 2,
          pageSize: 2,
          total: 3
        }
      }));

      $(element.find('button')[0]).click();
      $rootScope.$apply();

      expect(selectWithSearchDatasource.getSelectOptions).toHaveBeenCalledWith('NACE', {
        page: 1,
        query: '',
        params: {property: "value"},
        fullModel: { companyName: "wky" }
      });

      selectWithSearchModal = $(angular.element('select-with-search-modal')[0]);
      selectedResults = selectWithSearchModal.find(".jq-selected-results li");
      nonSelectedResults = selectWithSearchModal.find(".jq-non-selected-results li");
    });

    it('should render the modal', function () {
      expect(selectWithSearchModal.length).toBe(1);
    });

    it('should render the initial model value as selected rows', function () {
      expect(selectedResults.length).toBe(2);
      expect($(selectedResults[0]).find("label").text().trim()).toBe("A - Agriculture, forestry and fishing");
      expect($(selectedResults[1]).find("label").text().trim()).toBe("A1 - Crop and animal production, hunting");
    });

    it('should render the rows that are not selected in the unselected rows', function () {
      expect(nonSelectedResults.length).toBe(1);
      expect(nonSelectedResults.find("label").text().trim()).toBe("B - Mining and quarrying");
    });

    it("should move an item from 'unselected' to 'selected' when clicking it", function () {
      $(nonSelectedResults[0]).find("input").click();

      selectedResults = selectWithSearchModal.find(".jq-selected-results li");
      nonSelectedResults = selectWithSearchModal.find(".jq-non-selected-results li");

      expect(selectedResults.length).toBe(3);
      expect($(selectedResults[0]).find("label").text().trim()).toBe("A - Agriculture, forestry and fishing");
      expect($(selectedResults[1]).find("label").text().trim()).toBe("A1 - Crop and animal production, hunting");
      expect($(selectedResults[2]).find("label").text().trim()).toBe("B - Mining and quarrying");

      expect(nonSelectedResults.length).toBe(0);
    });

    it("should move an item from 'selected' to 'unselected' when clicking it if the search results contain the record", function () {
      $(selectedResults[0]).find("input").click();

      selectedResults = selectWithSearchModal.find(".jq-selected-results li");
      nonSelectedResults = selectWithSearchModal.find(".jq-non-selected-results li");

      expect(selectedResults.length).toBe(1);
      expect($(selectedResults[0]).find("label").text().trim()).toBe("A1 - Crop and animal production, hunting");

      expect(nonSelectedResults.length).toBe(2);
      expect($(nonSelectedResults[0]).find("label").text().trim()).toBe("A - Agriculture, forestry and fishing");
      expect($(nonSelectedResults[1]).find("label").text().trim()).toBe("B - Mining and quarrying");
    });

    describe('and the user performs another query that returns different results', function () {
      beforeEach(function () {
        //The result of the second search operation
        spyOnSelectWithSearchDatasource.and.callFake(mockHelpers.resolvedPromise($q, {
          rows: [
            {
              key: "C",
              label: "C - Manufacturing"
            }
          ],
          pagination: {
            page: 1,
            pages: 2,
            pageSize: 2,
            total: 3
          }
        }));

        $(selectWithSearchModal.find(".input-holder input")).val("C").change();
        $rootScope.$apply();

        selectWithSearchModal.find(".input__with-button input").click();
        expect(selectWithSearchDatasource.getSelectOptions).toHaveBeenCalledTimes(2);
        expect(selectWithSearchDatasource.getSelectOptions).toHaveBeenCalledWith('NACE', {
          page: 1,
          query: 'C',
          params: {property: "value"},
          fullModel: { companyName: "wky" }
        });
      });

      it('should retain the previous selected results', function () {
        expect(selectedResults.length).toBe(2);
        expect($(selectedResults[0]).find("label").text().trim()).toBe("A - Agriculture, forestry and fishing");
        expect($(selectedResults[1]).find("label").text().trim()).toBe("A1 - Crop and animal production, hunting");
      });

      it('should show the new non selected results', function () {
        nonSelectedResults = selectWithSearchModal.find(".jq-non-selected-results li");
        expect(nonSelectedResults.length).toBe(1);
        expect($(nonSelectedResults[0]).find("label").text().trim()).toBe("C - Manufacturing");
      });

      it("should not move an item from 'selected' to 'unselected' if the search result does not contain the record", function () {
        $(selectedResults[0]).find("input").click();

        selectedResults = selectWithSearchModal.find(".jq-selected-results li");
        nonSelectedResults = selectWithSearchModal.find(".jq-non-selected-results li");

        expect(selectedResults.length).toBe(1);
        expect($(selectedResults[0]).find("label").text().trim()).toBe("A1 - Crop and animal production, hunting");

        expect(nonSelectedResults.length).toBe(1);
        expect($(nonSelectedResults[0]).find("label").text().trim()).toBe("C - Manufacturing");
      });
    });

    it("should make a new call when you change the query and press enter", function () {

      $(selectWithSearchModal.find(".input-holder input")).val("WKY").change();
      $(selectWithSearchModal.find(".input-holder input")).triggerHandler({ type: 'keyup', keyCode: 12 });
      $(selectWithSearchModal.find(".input-holder input")).triggerHandler({ type: 'keyup', keyCode: 13 });
      $rootScope.$apply();

      expect(selectWithSearchDatasource.getSelectOptions).toHaveBeenCalledTimes(2);
      expect(selectWithSearchDatasource.getSelectOptions).toHaveBeenCalledWith('NACE', {
        page: 1,
        query: 'WKY',
        params: {property: "value"},
        fullModel: { companyName: "wky" }
      });
    });

    it("should clear the search query you've typed in when you press the clear button and make a new request", function () {
      //Set the query value to 'C'
      $(selectWithSearchModal.find(".input-holder input")).val("C").change();
      $rootScope.$apply();

      //Check that setting the query was successful
      expect($(selectWithSearchModal.find(".input-holder input")).val()).toBe("C");

      //Click the clear query button
      $(selectWithSearchModal.find(".multi-select__search a.icon-close")).click();

      //Check that clearing the query was successful
      expect($(selectWithSearchModal.find(".input-holder input")).val()).toBe("");

      expect(selectWithSearchDatasource.getSelectOptions).toHaveBeenCalledTimes(2);
      expect(selectWithSearchDatasource.getSelectOptions).toHaveBeenCalledWith('NACE', {
        page: 1,
        query: '',
        params: {property: "value"},
        fullModel: { companyName: "wky" }
      });
    });

    it("should should make a new request if the page is changed", function () {
      expect($(selectWithSearchModal.find('.pagination ul li')[0]).hasClass('is-active')).toBe(true);
      expect($(selectWithSearchModal.find('.pagination ul li')[1]).hasClass('is-active')).toBe(false);

      spyOnSelectWithSearchDatasource.and.callFake(mockHelpers.resolvedPromise($q, {
        rows: [
          {
            key: "C",
            label: "C - Manufacturing"
          }
        ],
        pagination: {
          page: 2,
          pages: 2,
          pageSize: 2,
          total: 3
        }
      }));

      //Click on page 2
      $(selectWithSearchModal.find('.pagination ul li a')[1]).click();

      expect($(selectWithSearchModal.find('.pagination ul li')[1]).hasClass('is-active')).toBe(false);
      expect($(selectWithSearchModal.find('.pagination ul li')[2]).hasClass('is-active')).toBe(true);

      expect(selectWithSearchDatasource.getSelectOptions).toHaveBeenCalledTimes(2);
      expect(selectWithSearchDatasource.getSelectOptions).toHaveBeenCalledWith('NACE', {
        page: 2,
        query: '',
        params: {property: "value"},
        fullModel: { companyName: "wky" }
      });
    });

    describe('after clicking confirm', function () {

      beforeEach(function () {
        spyOn(guidanceFormObserver, 'formValueChanged');

        // Check the first elected result and the second result element (because the first selected becomes the first unselected result)
        // so we get another ngModel value than we previously did.
        $(selectedResults[0]).find("input").click();
        $(nonSelectedResults[0]).find("input").click();

        selectedResults = selectWithSearchModal.find(".jq-selected-results li");
        expect(selectedResults.length).toBe(2);
        expect($(selectedResults[0]).find("label").text().trim()).toBe("A1 - Crop and animal production, hunting");
        expect($(selectedResults[1]).find("label").text().trim()).toBe("B - Mining and quarrying");
      });

      it('should set the model value to the selected results and let the "guidanceFormObserver" know that the values have changed', function () {
        expect(guidanceFormObserver.formValueChanged).not.toHaveBeenCalled();
        expect(angular.copy(scope.model.nace)).toEqual([
          {
            key: "A",
            label: "A - Agriculture, forestry and fishing"
          },
          {
            key: "A1",
            label: "A1 - Crop and animal production, hunting"
          }
        ]);

        selectWithSearchModal.find(".modal__header a").click();
        $rootScope.$apply();

        //Performing angular.copy to strip out angular internal properties
        expect(angular.copy(scope.model.nace)).toEqual([
          {
            key: "A1",
            label: "A1 - Crop and animal production, hunting"
          },
          {
            key: "B",
            label: "B - Mining and quarrying"
          }
        ]);

        expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(1);
        expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
          focus: 'nace',
          value: scope.model.nace
        }, false);
      });

      it('should let the "guidanceFormObserver" know that the values have changed when noBackendInteraction is true', function () {
        expect(guidanceFormObserver.formValueChanged).not.toHaveBeenCalled();
        expect(angular.copy(scope.model.nace)).toEqual([
          {
            key: "A",
            label: "A - Agriculture, forestry and fishing"
          },
          {
            key: "A1",
            label: "A1 - Crop and animal production, hunting"
          }
        ]);

        scope.fields[0].templateOptions.noBackendInteraction = true;
        $rootScope.$apply();

        selectWithSearchModal.find(".modal__header a").click();
        $rootScope.$apply();

        expect(angular.copy(scope.model.nace)).toEqual([
          {
            key: "A1",
            label: "A1 - Crop and animal production, hunting"
          },
          {
            key: "B",
            label: "B - Mining and quarrying"
          }
        ]);
        expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
          focus: 'nace',
          value: scope.model.nace
        }, true);
      });

      it('should destroy the modal', function () {
        selectWithSearchModal.find(".modal__header a").click();
        $rootScope.$apply();

        expect(angular.element('select-with-search-modal').length).toBe(0);
      });
    });

    afterEach(function () {
      angular.element('select-with-search-modal').remove();
    });
  });

  describe('after opening with multiple false', function () {

    let selectWithSearchModal;
    let selectedResults;
    let nonSelectedResults;

    beforeEach(function () {
      //The initial selected elements
      compile([
        {
          key: "A",
          label: "A - Agriculture, forestry and fishing"
        }
      ], false);

      //The result of the first search operation
      spyOn(selectWithSearchDatasource, 'getSelectOptions').and.callFake(mockHelpers.resolvedPromise($q, {
        rows: [{
          key: "A",
          label: "A - Agriculture, forestry and fishing"
        }, {
          key: "A1",
          label: "A1 - Crop and animal production, hunting"
        }, {
          key: "B",
          label: "B - Mining and quarrying"
        }],
        pagination: {
          page: 1,
          pages: 2,
          pageSize: 2,
          total: 3
        }
      }));

      $(element.find('button')[0]).click();
      $rootScope.$apply();

      expect(selectWithSearchDatasource.getSelectOptions).toHaveBeenCalledWith('NACE', {
        page: 1,
        query: '',
        params: {property: "value"},
        fullModel: { companyName: "wky" }
      });

      selectWithSearchModal = $(angular.element('select-with-search-modal')[0]);
      selectedResults = selectWithSearchModal.find(".jq-selected-results li");
      nonSelectedResults = selectWithSearchModal.find(".jq-non-selected-results li");
    });

    it("should replace the selected item with the one you click if multiple is set to false", function () {
      expect(selectedResults.length).toBe(1);
      expect($(selectedResults[0]).find("label").text().trim()).toBe("A - Agriculture, forestry and fishing");

      expect(nonSelectedResults.length).toBe(2);
      expect($(nonSelectedResults[0]).find("label").text().trim()).toBe("A1 - Crop and animal production, hunting");
      expect($(nonSelectedResults[1]).find("label").text().trim()).toBe("B - Mining and quarrying");

      $(nonSelectedResults[0]).find("input").click();

      selectedResults = selectWithSearchModal.find(".jq-selected-results li");
      nonSelectedResults = selectWithSearchModal.find(".jq-non-selected-results li");

      expect(selectedResults.length).toBe(1);
      expect($(selectedResults[0]).find("label").text().trim()).toBe("A1 - Crop and animal production, hunting");

      expect(nonSelectedResults.length).toBe(2);
      expect($(nonSelectedResults[0]).find("label").text().trim()).toBe("A - Agriculture, forestry and fishing");
      expect($(nonSelectedResults[1]).find("label").text().trim()).toBe("B - Mining and quarrying");
    });
  });
});
