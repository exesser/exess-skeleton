'use strict';

describe('Component: wysiwyg-table-button', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let $rootScope;
  let spyEditorWrapSelection = jasmine.createSpy();
  let element;

  let CellR1C1;
  let CellR1C2;
  let CellR1C3;
  let CellR1C4;
  let CellR1C5;
  let CellR1C6;

  let CellR2C1;
  let CellR2C2;
  let CellR2C3;
  let CellR2C4;
  let CellR2C5;
  let CellR2C6;

  let CellR3C1;
  let CellR3C2;
  let CellR3C3;
  let CellR3C4;
  let CellR3C5;
  let CellR3C6;

  let ulElement;
  let buttonElement;

  const template = `<wysiwyg-insert-table-button is-disabled="isDisabled()" editor="editor"></wysiwyg-insert-table-button>`;

  beforeEach(inject(function (_$rootScope_, $compile) {
    $rootScope = _$rootScope_;

    const scope = $rootScope.$new(true);
    scope.editor = {
      wrapSelection: spyEditorWrapSelection
    };

    scope.isDisabled = function () {
      return false;
    };

    element = angular.element(template);
    element = $compile(element)(scope);

    $rootScope.$apply();

    ulElement = $(element.find("ul")[0]);
    buttonElement = $(element.find("button")[0]);

    const tdElements = element.find("td");
    expect(tdElements.length).toBe(48);

    CellR1C1 = $(tdElements[0]);
    CellR1C2 = $(tdElements[1]);
    CellR1C3 = $(tdElements[2]);
    CellR1C4 = $(tdElements[3]);
    CellR1C5 = $(tdElements[4]);
    CellR1C6 = $(tdElements[5]);

    CellR2C1 = $(tdElements[6]);
    CellR2C2 = $(tdElements[7]);
    CellR2C3 = $(tdElements[8]);
    CellR2C4 = $(tdElements[9]);
    CellR2C5 = $(tdElements[10]);
    CellR2C6 = $(tdElements[11]);

    CellR3C1 = $(tdElements[12]);
    CellR3C2 = $(tdElements[13]);
    CellR3C3 = $(tdElements[14]);
    CellR3C4 = $(tdElements[15]);
    CellR3C5 = $(tdElements[16]);
    CellR3C6 = $(tdElements[17]);
  }));

  it('should show/hide the table when the button is clicked', function () {
    expect(ulElement.hasClass('ng-hide')).toBe(true);
    buttonElement.click();
    expect(ulElement.hasClass('ng-hide')).toBe(false);
    buttonElement.click();
    expect(ulElement.hasClass('ng-hide')).toBe(true);
  });

  it('should add the pink class to the cells that are "selected"', function () {
    // first none of the cells has the pink class
    expect(CellR1C1.hasClass('pink')).toBe(false);
    expect(CellR1C2.hasClass('pink')).toBe(false);
    expect(CellR1C3.hasClass('pink')).toBe(false);
    expect(CellR1C4.hasClass('pink')).toBe(false);
    expect(CellR1C5.hasClass('pink')).toBe(false);
    expect(CellR1C6.hasClass('pink')).toBe(false);
    expect(CellR2C1.hasClass('pink')).toBe(false);
    expect(CellR2C2.hasClass('pink')).toBe(false);
    expect(CellR2C3.hasClass('pink')).toBe(false);
    expect(CellR2C4.hasClass('pink')).toBe(false);
    expect(CellR2C5.hasClass('pink')).toBe(false);
    expect(CellR2C6.hasClass('pink')).toBe(false);
    expect(CellR3C1.hasClass('pink')).toBe(false);
    expect(CellR3C2.hasClass('pink')).toBe(false);
    expect(CellR3C3.hasClass('pink')).toBe(false);
    expect(CellR3C4.hasClass('pink')).toBe(false);
    expect(CellR3C5.hasClass('pink')).toBe(false);
    expect(CellR3C6.hasClass('pink')).toBe(false);

    CellR2C3.trigger('mouseover');

    // only 6 cells should have the pink class
    expect(CellR1C1.hasClass('pink')).toBe(true);
    expect(CellR1C2.hasClass('pink')).toBe(true);
    expect(CellR1C3.hasClass('pink')).toBe(true);
    expect(CellR1C4.hasClass('pink')).toBe(false);
    expect(CellR1C5.hasClass('pink')).toBe(false);
    expect(CellR1C6.hasClass('pink')).toBe(false);
    expect(CellR2C1.hasClass('pink')).toBe(true);
    expect(CellR2C2.hasClass('pink')).toBe(true);
    expect(CellR2C3.hasClass('pink')).toBe(true);
    expect(CellR2C4.hasClass('pink')).toBe(false);
    expect(CellR2C5.hasClass('pink')).toBe(false);
    expect(CellR2C6.hasClass('pink')).toBe(false);
    expect(CellR3C1.hasClass('pink')).toBe(false);
    expect(CellR3C2.hasClass('pink')).toBe(false);
    expect(CellR3C3.hasClass('pink')).toBe(false);
    expect(CellR3C4.hasClass('pink')).toBe(false);
    expect(CellR3C5.hasClass('pink')).toBe(false);
    expect(CellR3C6.hasClass('pink')).toBe(false);

    CellR2C3.trigger('mouseleave');

    // none of the cells should have the pink class
    expect(CellR1C1.hasClass('pink')).toBe(false);
    expect(CellR1C2.hasClass('pink')).toBe(false);
    expect(CellR1C3.hasClass('pink')).toBe(false);
    expect(CellR1C4.hasClass('pink')).toBe(false);
    expect(CellR1C5.hasClass('pink')).toBe(false);
    expect(CellR1C6.hasClass('pink')).toBe(false);
    expect(CellR2C1.hasClass('pink')).toBe(false);
    expect(CellR2C2.hasClass('pink')).toBe(false);
    expect(CellR2C3.hasClass('pink')).toBe(false);
    expect(CellR2C4.hasClass('pink')).toBe(false);
    expect(CellR2C5.hasClass('pink')).toBe(false);
    expect(CellR2C6.hasClass('pink')).toBe(false);
    expect(CellR3C1.hasClass('pink')).toBe(false);
    expect(CellR3C2.hasClass('pink')).toBe(false);
    expect(CellR3C3.hasClass('pink')).toBe(false);
    expect(CellR3C4.hasClass('pink')).toBe(false);
    expect(CellR3C5.hasClass('pink')).toBe(false);
    expect(CellR3C6.hasClass('pink')).toBe(false);
  });

  it('should notify the editor to add a table when you click on a cell', function () {
    expect(spyEditorWrapSelection).not.toHaveBeenCalled();

    // the "select" table is not displayed
    expect(ulElement.hasClass('ng-hide')).toBe(true);
    buttonElement.click();

    // the "select" table should be displayed
    expect(ulElement.hasClass('ng-hide')).toBe(false);

    CellR2C3.click();

    // the "select" table should not be displayed anymore
    expect(ulElement.hasClass('ng-hide')).toBe(true);

    // the editor should be notify to add a table with 2 rows and 3 columns
    expect(spyEditorWrapSelection).toHaveBeenCalled();
    expect(spyEditorWrapSelection).toHaveBeenCalledWith('insertHTML', '<table><tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr><tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr></table><br>');

    CellR1C2.click();
    // the editor should be notify to add a table with 1 row and 2 columns
    expect(spyEditorWrapSelection).toHaveBeenCalledWith('insertHTML', '<table><tr><td>&nbsp;</td><td>&nbsp;</td></tr></table><br>');
  });
});