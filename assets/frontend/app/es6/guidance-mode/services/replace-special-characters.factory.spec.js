'use strict';

describe('Factory: replaceSpecialCharacters', function () {

  // load the controller's module
  beforeEach(module('digitalWorkplaceApp'));

  let replaceSpecialCharacters;

  // Initialize the controller and a mock scope
  beforeEach(inject(function (_replaceSpecialCharacters_) {
    replaceSpecialCharacters = _replaceSpecialCharacters_;
  }));

  it('should replace [] with --theArray and backwards', function () {
    const originalObject = {
      "cars[]|type": ["Ford", "Audi", "Mercedes"],
      "cars[]|model": ["F150", "A1", "Viano"]
    };

    const formattedObject = {
      "cars--theArray|type": ["Ford", "Audi", "Mercedes"],
      "cars--theArray|model": ["F150", "A1", "Viano"]
    };
    expect(replaceSpecialCharacters.replaceArraySign(originalObject, true)).toEqual(formattedObject);
    expect(replaceSpecialCharacters.replaceArraySign(formattedObject, false)).toEqual(originalObject);
  });
});
