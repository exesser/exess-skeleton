'use strict';

describe('Factory: listObserver', function () {

  beforeEach(module('digitalWorkplaceApp'));

  let listObserver;
  const listKey = 'accounts';

  beforeEach(inject(function (_listObserver_) {
    listObserver = _listObserver_;
  }));

  it('should register toggleListRowSelection callbacks.', function () {
    const observer = jasmine.createSpy('observer');

    listObserver.registerToggleListRowSelectionCallback(listKey, observer);
    listObserver.toggleListRowSelection('unknown-list', "itemId", true);
    listObserver.toggleListRowSelection(listKey, "itemId", true);

    expect(observer).toHaveBeenCalledTimes(1);
    expect(observer).toHaveBeenCalledWith("itemId", true);
  });

  it('should add toggleAllListRowsSelections callbacks that can be deregistered by calling the result.', function () {
    const observer = jasmine.createSpy('observer');
    const deregisterFunction = listObserver.registerToggleAllListRowsSelectionsCallback(listKey, observer);

    //First invocation
    listObserver.toggleAllListRowsSelections(listKey, "itemId");
    expect(observer).toHaveBeenCalledTimes(1);
    expect(observer).toHaveBeenCalledWith("itemId");

    //Deregister the callback
    deregisterFunction();

    //Second invocation after deregistering, count is not changed.
    listObserver.toggleAllListRowsSelections(listKey, "itemId");
    expect(observer).toHaveBeenCalledTimes(1);
  });

  it('should register toggleExtraRowContentPlaceholder callbacks.', function () {
    const observer = jasmine.createSpy('observer');
    const actionData = { parentId: "mockId" };

    listObserver.registerToggleExtraRowContentPlaceholderCallback(listKey, observer);
    listObserver.toggleExtraRowContentPlaceholder('unknown-list', "accounts-action-bar", "itemId", actionData);
    listObserver.toggleExtraRowContentPlaceholder(listKey, "accounts-action-bar", "itemId", actionData);

    expect(observer).toHaveBeenCalledTimes(1);
    expect(observer).toHaveBeenCalledWith("accounts-action-bar", "itemId", actionData);
  });

  it('should register reloadListCallback callbacks.', function () {
    const observer = jasmine.createSpy('observer');

    listObserver.registerReloadListCallback(listKey, observer);
    listObserver.reloadList('unknown-list');
    listObserver.reloadList(listKey);

    expect(observer).toHaveBeenCalledTimes(1);
  });
});
