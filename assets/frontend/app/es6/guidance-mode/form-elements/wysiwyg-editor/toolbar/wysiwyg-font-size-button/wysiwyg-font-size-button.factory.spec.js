'use strict';

describe('Factory: wysiwygFontSizeButtonFactory', function () {
  beforeEach(module('digitalWorkplaceApp'));

  let wysiwygFontSizeButtonFactory;
  let taTools;
  let event;

  beforeEach(inject(function (_wysiwygFontSizeButtonFactory_, _taTools_) {
    wysiwygFontSizeButtonFactory = _wysiwygFontSizeButtonFactory_;
    taTools = _taTools_;
    event = new Event();
  }));

  it('wysiwygFontSizeButtonFactory setFontSize method should exist', function () {
    spyOn(event, 'preventDefault');
    spyOn(event, 'stopPropagation');
    expect(wysiwygFontSizeButtonFactory.setFontSize).toBeDefined();
    expect(taTools.fontSizeTest).toBeUndefined();

    wysiwygFontSizeButtonFactory.setFontSize('fontSizeTest');

    expect(taTools.fontSizeTest.tooltiptext).toEqual('Change font size');
    expect(taTools.fontSizeTest.display).toEqual('<div class="btn-group dropdown font-size"><button type="button" class="btn" ng-click="toggle($event)" ng-disabled="isDisabled()"><i class="fa fa-font"></i><i class="fa fa-caret-down"></i></button><ul><li ng-repeat="o in options"><a style="font-size:{{o.css}};" ng-click="action($event, o.value)">{{o.name}}</a></li></ul></div>');
    expect(taTools.fontSizeTest.options).toEqual([
      { name: '1 (8pt)', css: 'xx-small', value: 1 },
      { name: '2 (10pt)', css: 'x-small', value: 2 },
      { name: '3 (12pt)', css: 'small', value: 3 },
      { name: '4 (14pt)', css: 'medium', value: 4 },
      { name: '5 (18pt)', css: 'large', value: 5 },
      { name: '6 (24pt)', css: 'x-large', value: 6 },
      { name: '7 (36pt)', css: 'xx-large', value: 7 }
    ]);

    expect(taTools.fontSizeTest.action).toBeDefined();
    expect(taTools.fontSizeTest.toggle).toBeDefined();

    spyOn($.fn, "toggle");
    expect($.fn.toggle).not.toHaveBeenCalled();
    taTools.fontSizeTest.toggle({ event: { currentTarget: "target" } });
    expect($.fn.toggle).toHaveBeenCalled();

    const spy = jasmine.createSpy();
    taTools.fontSizeTest.$editor = function () {
      return {
        wrapSelection: spy
      };
    };

    taTools.fontSizeTest.action(event, '11');
    expect(event.preventDefault).not.toHaveBeenCalled();
    expect(spy).toHaveBeenCalledTimes(1);
    expect(spy).toHaveBeenCalledWith('fontSize', 11);

    event.originalEvent = event;
    taTools.fontSizeTest.action(event, '22');

    expect(event.preventDefault).toHaveBeenCalledTimes(1);
    expect(event.stopPropagation).toHaveBeenCalledTimes(1);

    expect(spy).toHaveBeenCalledTimes(2);
    expect(spy).toHaveBeenCalledWith('fontSize', 22);
  });
});
