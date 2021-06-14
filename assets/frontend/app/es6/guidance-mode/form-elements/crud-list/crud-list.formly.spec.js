'use strict';

/**
 * This test also tests the underlying crudListFormElementDirective.
 * We just have this to get access to the HTML element and do DOM-manipulations.
 * To keep in line with the other form elements, that directive is tested in terms of this form element.
 */
describe('Form element: crud-list', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let scope;
  let element;

  let $rootScope;
  let $compile;
  let $q;

  let actionDatasource;
  let guidanceModalObserver;
  let guidanceFormObserver;
  let CONFIRM_ACTION;

  const template = '<formly-form model="model" fields="fields"/>';

  const henkPerson = {
    name: "Henk",
    age: 42
  };

  const truusPerson = {
    name: "Truus",
    age: 54
  };

  beforeEach(inject(function (_$rootScope_, _$compile_, _$q_, $state,
                              _actionDatasource_, _guidanceModalObserver_,
                              _CONFIRM_ACTION_, GuidanceFormObserver) {
    $rootScope = _$rootScope_;
    $compile = _$compile_;
    $q = _$q_;
    actionDatasource = _actionDatasource_;
    guidanceModalObserver = _guidanceModalObserver_;
    CONFIRM_ACTION = _CONFIRM_ACTION_;

    guidanceFormObserver = new GuidanceFormObserver();

    spyOn(guidanceFormObserver, 'getFullModel').and.returnValue({ companyName: "wky" });

    mockHelpers.blockUIRouter($state);
  }));

  function compile(personList = []) {
    scope = $rootScope.$new();
    scope.fields = [
      {
        key: "personList",
        type: "crud-list",
        templateOptions: {
          label: "People",
          headers: [{
            label: "NAME",
            cellType: "list-simple-single-line-cell",
            cellClass: "cell__text",
            cellOptions: {
              text: "{% name %}"
            }
          }, {
            label: "AGE",
            cellType: "list-simple-single-line-cell",
            cellClass: "cell__text",
            cellOptions: {
              text: "{% age %}"
            }
          }],
          createUpdateActionId: "createPerson",
          disabled: false,
          readonly: false
        }
      }
    ];
    scope.model = {
      personList
    };

    const guidanceFormObserverAccessorElement = mockHelpers.createGuidanceFormObserverAccessor({
      $compile,
      $rootScope,
      guidanceFormObserver
    });

    element = angular.element(template);
    element = $compile(element)(scope);
    guidanceFormObserverAccessorElement.append(element);
    $rootScope.$apply();
  }

  describe('an initially empty table', function () {
    beforeEach(function () {
      compile();
    });

    it('should contain zero rows', function () {
      expect(element.find("#rows").html()).toBe("");
    });

    it('should redraw the table when the model changes', function () {
      scope.model = {
        personList: [henkPerson]
      };
      $rootScope.$apply();
      const children = $(element.find("#rows tr"));
      expect(children.length).toBe(1);

      const firstRowCells = $(children[0]).find("td");
      expect($(firstRowCells[0]).text().trim()).toBe("Henk");
      expect($(firstRowCells[1]).text().trim()).toBe("42");
    });
  });

  describe('a non-empty table', function () {
    beforeEach(function () {
      compile([henkPerson, truusPerson]);
    });

    it('should render a row element for each row', function () {
      const children = $(element.find("#rows tr"));
      expect(children.length).toBe(2);

      const firstRowCells = $(children[0]).find("td");
      expect($(firstRowCells[0]).text().trim()).toBe("Henk");
      expect($(firstRowCells[1]).text().trim()).toBe("42");

      const secondRowCells = $(children[1]).find("td");
      expect($(secondRowCells[0]).text().trim()).toBe("Truus");
      expect($(secondRowCells[1]).text().trim()).toBe("54");
    });
  });

  describe('create button', function () {
    beforeEach(function () {
      compile([henkPerson]);

      spyOn(actionDatasource, 'perform').and.callFake(mockHelpers.resolvedPromise($q, {
        arguments: {
          propX: "fake"
        }
      }));
      spyOn(guidanceModalObserver, 'openModal').and.callFake(mockHelpers.resolvedPromise($q, {
        model: truusPerson
      }));

      element.find("a > span.icon-plus").click();

      expect(actionDatasource.perform).toHaveBeenCalledTimes(1);
      expect(actionDatasource.perform).toHaveBeenCalledWith({
        id: "createPerson",
        model: {
          companyName: 'wky'
        }
      });

      expect(guidanceModalObserver.openModal).toHaveBeenCalledTimes(1);
      expect(guidanceModalObserver.openModal).toHaveBeenCalledWith({
        propX: "fake"
      }, CONFIRM_ACTION.CONFIRM_CREATE_LIST_ROW);
    });

    it('should add a row to the table', function () {
      const children = $(element.find("#rows tr"));
      expect(children.length).toBe(2);

      const firstRowCells = $(children[0]).find("td");
      expect($(firstRowCells[0]).text().trim()).toBe("Henk");
      expect($(firstRowCells[1]).text().trim()).toBe("42");

      const secondRowCells = $(children[1]).find("td");
      expect($(secondRowCells[0]).text().trim()).toBe("Truus");
      expect($(secondRowCells[1]).text().trim()).toBe("54");
    });

    it('should update the model', function () {
      expect(scope.model).toEqual({
        personList: [henkPerson, truusPerson]
      });
    });
  });

  describe('delete row button', function () {
    beforeEach(function () {
      compile([henkPerson]);

      const rows = $(element.find("#rows tr"));
      expect(rows.length).toBe(1);

      const row = $(rows[0]);
      row.find("crud-list-form-element-button a.icon-remove").click();
    });

    it('should remove the element from the model', function () {
      expect(scope.model.personList).toEqual([]);
    });

    it('should remove the row from the table', function () {
      expect(element.find("#rows tr").length).toBe(0);
    });
  });

  describe('update row button', function () {
    let updateButton;

    beforeEach(function () {
      compile([henkPerson]);

      const rows = $(element.find("#rows tr"));
      expect(rows.length).toBe(1);
      const row = $(rows[0]);
      updateButton = row.find("crud-list-form-element-button a.icon-edit");

      spyOn(actionDatasource, 'perform').and.callFake(mockHelpers.resolvedPromise($q, {
        arguments: {
          propX: "fake"
        }
      }));
    });

    describe('upon confirm', function () {
      beforeEach(function () {
        spyOn(guidanceModalObserver, 'openModal').and.callFake(function ({ model }) {
          //It's Henk's birthday! We adapt the model here to check that when we cancel the model isn't changed because it points to the same object.
          model.age = 43;
          return mockHelpers.resolvedPromise($q, { model })();
        });

        updateButton.click();

        expect(actionDatasource.perform).toHaveBeenCalledTimes(1);
        expect(actionDatasource.perform).toHaveBeenCalledWith({
          id: "createPerson",
          model: {
            companyName: 'wky'
          }
        });
      });

      it('should update the element in the model', function () {
        expect(scope.model.personList).toEqual([{
          name: "Henk",
          age: 43
        }]);
      });

      it('should redraw the row in the table', function () {
        const row = element.find("#rows tr");
        expect(row.length).toBe(1);

        const rowCells = row.find("td");
        expect($(rowCells[0]).text().trim()).toBe("Henk");
        expect($(rowCells[1]).text().trim()).toBe("43");
      });
    });

    describe('upon cancel', function () {
      beforeEach(function () {
        spyOn(guidanceModalObserver, 'openModal').and.callFake(function ({ model }) {
          //It's Henk's birthday! Isn't it? We adapt the model here to check that when we cancel the model isn't changed because it points to the same object.
          model.age = 43;
          //No wait, Henk's birthday is next week. Cancel the modal.
          return mockHelpers.rejectedPromise($q)();
        });

        updateButton.click();
      });

      it('should not update the element in the model', function () {
        expect(scope.model.personList).toEqual([henkPerson]);
      });

      it('should redraw the row in the table', function () {
        const row = element.find("#rows tr");
        expect(row.length).toBe(1);

        const rowCells = row.find("td");
        expect($(rowCells[0]).text().trim()).toBe("Henk");
        expect($(rowCells[1]).text().trim()).toBe("42");
      });
    });

    describe("the 'disabled' functionality", function () {
      it('should have create, edit and delete buttons if the field is not disabled and otherwise remove them', function () {
        expect(element.find('.action-buttons:has(span.icon-plus)').length).toBe(1);
        let row = element.find("#rows tr");
        let rowCells = row.find("td");
        expect($(rowCells[2]).find("crud-list-form-element-button").length).toBe(1);
        expect($(rowCells[3]).find("crud-list-form-element-button").length).toBe(1);

        scope.fields[0].templateOptions.disabled = true;
        $rootScope.$apply();

        expect(element.find('.action-buttons:has(span.icon-plus)').length).toBe(0);
        row = element.find("#rows tr");
        rowCells = row.find("td");
        expect($(rowCells[2]).find("crud-list-form-element-button").length).toBe(0);
        expect($(rowCells[3]).find("crud-list-form-element-button").length).toBe(0);
      });
    });

    describe("the 'readonly' functionality", function () {
      it('should have create, edit and delete buttons if the field is not readonly and otherwise remove them', function () {
        expect(element.find('.action-buttons:has(span.icon-plus)').length).toBe(1);
        let row = element.find("#rows tr");
        let rowCells = row.find("td");
        expect($(rowCells[2]).find("crud-list-form-element-button").length).toBe(1);
        expect($(rowCells[3]).find("crud-list-form-element-button").length).toBe(1);

        scope.fields[0].templateOptions.readonly = true;
        $rootScope.$apply();

        expect(element.find('.action-buttons:has(span.icon-plus)').length).toBe(0);
        row = element.find("#rows tr");
        rowCells = row.find("td");
        expect($(rowCells[2]).find("crud-list-form-element-button").length).toBe(0);
        expect($(rowCells[3]).find("crud-list-form-element-button").length).toBe(0);
      });
    });
  });

  it("should destroy the list row cell's scopes when destroying the list scope", function () {
    compile([henkPerson]);
    const cellContentElements = element.find("table tbody tr:first-of-type td").children();
    const scopes = _.map(cellContentElements, (element) => $(element).scope());

    // At first we expect none of the cell scopes to be destroyed.
    expect(_.some(scopes, '$$destroyed')).toBe(false);

    // Now we destroy the list's scope
    scope.$destroy();

    // And we expect all the cell scopes to be destroyed.
    expect(_.every(scopes, '$$destroyed')).toBe(true);
  });
});
