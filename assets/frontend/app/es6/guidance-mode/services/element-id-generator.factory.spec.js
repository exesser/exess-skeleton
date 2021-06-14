'use strict';

describe('Factory: elementIdGenerator', function () {

  // load the controller's module
  beforeEach(module('digitalWorkplaceApp'));

  let elementIdGenerator;
  let guidanceFormObserver;

  // Initialize the controller and a mock scope
  beforeEach(inject(function (_elementIdGenerator_, GuidanceFormObserver) {
    elementIdGenerator = _elementIdGenerator_;
    guidanceFormObserver = new GuidanceFormObserver();
  }));

  it('should generate the field id correctly when the block key is empty', function () {
    spyOn(guidanceFormObserver, 'getRepeatableBlockKey').and.returnValue('');
    expect(elementIdGenerator.generateId('some-id', guidanceFormObserver)).toEqual('some-id-field');
  });

  it('should generate the field id correctly when the block key is a simple string', function () {
    spyOn(guidanceFormObserver, 'getRepeatableBlockKey').and.returnValue('simpleString');
    expect(elementIdGenerator.generateId('some-id', guidanceFormObserver)).toEqual('some-id-simple-string-field');
  });

  it('should generate the field id correctly when the block key is a mix string', function () {
    spyOn(guidanceFormObserver, 'getRepeatableBlockKey').and.returnValue('aaa-bbbCcc.dDDD-123abc456def');
    expect(elementIdGenerator.generateId('some-id', guidanceFormObserver)).toEqual('some-id-aaa-bbb-ccc-d-ddd-123-abc-456-def-field');
  });
});
