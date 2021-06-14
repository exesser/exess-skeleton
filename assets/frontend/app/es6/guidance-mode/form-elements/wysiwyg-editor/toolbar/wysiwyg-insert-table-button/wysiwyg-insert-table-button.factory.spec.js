'use strict';

describe('Factory: wysiwygInsertTableButtonFactory', function () {
  beforeEach(module('digitalWorkplaceApp'));

  let wysiwygInsertTableButtonFactory;
  let taTools;

  beforeEach(inject(function (_wysiwygInsertTableButtonFactory_, _taRegisterTool_, _taTools_) {
    wysiwygInsertTableButtonFactory = _wysiwygInsertTableButtonFactory_;
    taTools = _taTools_;
  }));

  it('wysiwygInsertTableButtonFactory setInsertTable method should exist', function () {
    expect(wysiwygInsertTableButtonFactory.setInsertTable).toBeDefined();
    expect(taTools.insertTableTest).toBeUndefined();
    wysiwygInsertTableButtonFactory.setInsertTable('insertTableTest');
    expect(taTools.insertTableTest.action).toBeDefined();
    taTools.insertTableTest.action();
  });
});
