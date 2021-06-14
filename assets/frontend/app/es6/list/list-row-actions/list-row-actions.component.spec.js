'use strict';

describe('Component: listRowActions', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let $rootScope;
  let $compile;
  let $q;
  let listDatasource;
  let listObserver;

  let element;

  let createQuoteElement;
  let createOpportunityElement;
  let logCaseElement;
  let closeActionsElement;

  const actionData = { parentId: "mockId" };
  const template = `
    <list-row-actions
      record-type="lead"
      record-id="1337"
      id="123-123-345"
      grid-key="action-bar"
      action-data='{parentId:"mockId"}'>
    </list-row-actions>
  `;

  beforeEach(inject(function (_$rootScope_, _$compile_, $state, _$q_, _listDatasource_, _listObserver_) {
    $rootScope = _$rootScope_;
    $compile = _$compile_;
    $q = _$q_;
    listDatasource = _listDatasource_;
    listObserver = _listObserver_;

    mockHelpers.blockUIRouter($state);

    spyOn(listDatasource, 'getActionButtons').and.callFake(mockHelpers.resolvedPromise($q, [
      {
        enabled: true,
        label: "create quote",
        icon: "icon-quote",
        action: {
          id: "1"
        }
      },
      {
        enabled: false,
        label: "create opportunity",
        icon: "icon-opportunity",
        action: {
          id: "2"
        }
      },
      {
        id: "3",
        enabled: true,
        label: "log case",
        icon: "icon-log",
        action: {
          id: 3
        }
      }
    ]));

    const scope = $rootScope.$new(true);
    element = angular.element(template);
    element = $compile(element)(scope);
    $rootScope.$apply();

    expect(listDatasource.getActionButtons).toHaveBeenCalledTimes(1);
    expect(listDatasource.getActionButtons).toHaveBeenCalledWith({ recordType: "lead", recordId: "1337", actionData });

    const listRowActions = element.find('list-row-action');
    const otherActions = element.find(':not(list-row-action) > a');
    // 3 actions 1 close button
    expect(listRowActions.length).toBe(3);
    expect(otherActions.length).toBe(1);

    createQuoteElement = $(listRowActions[0]);
    createOpportunityElement = $(listRowActions[1]);
    logCaseElement = $(listRowActions[2]);
    closeActionsElement = $(otherActions[0]);
  }));

  it('should compile down to a black bar with actions.', function () {
    //List row actions
    expect(createQuoteElement).not.toBe(undefined);
    expect(createOpportunityElement).not.toBe(undefined);
    expect(logCaseElement).not.toBe(undefined);

    //Overall close action
    expect(closeActionsElement).not.toBe(undefined);
    expect(closeActionsElement.attr('class')).toBe('close-actions icon-close');
  });

  it('should let the observer know that the row should be closed.', function () {
    spyOn(listObserver, 'toggleExtraRowContentPlaceholder');

    closeActionsElement.click();

    expect(listObserver.toggleExtraRowContentPlaceholder).toHaveBeenCalledTimes(1);
    expect(listObserver.toggleExtraRowContentPlaceholder).toHaveBeenCalledWith('lead', 'action-bar', '123-123-345');
  });
});
