'use strict';

describe('Form type: list-dropdown-cell', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let scope;
  let element;
  let commandHandler;

  let $rootScope;
  let $compile;
  let $q;

  const template = `<list-dropdown-cell 
    default-option="3 contacts" 
    dropdown-options='[{"label":"kristof vc","action":{"command":"navigate","arguments":{"linkTo":"focus-mode","params":{"mainMenuKey":"sales-marketing","focusModeId":"test","recordId":"kristof"}}}},
    {"label":"birgit matthe","action":{"command":"navigate","arguments":{"linkTo":"focus-mode","params":{"mainMenuKey":"sales-marketing","focusModeId":"test","recordId":"birgit"}}}},
    {"label":"blubber vis","action":{"command":"navigate","arguments":{"linkTo":"focus-mode","params":{"mainMenuKey":"sales-marketing","focusModeId":"test","recordId":"blubber"}}}}]'
  ></list-dropdown-cell>`;

  beforeEach(inject(function (_$rootScope_, _$compile_, _$q_, _commandHandler_) {
    $rootScope = _$rootScope_;
    $compile = _$compile_;
    $q = _$q_;
    commandHandler = _commandHandler_;

    scope = $rootScope.$new();
    element = angular.element(template);
    element = $compile(element)(scope);

    $rootScope.$apply();
  }));

  it('should compile down to a directive with dropdown inside', function() {
    const options = element.find('option');
    expect($(options).length).toBe(4);
    expect($(options[0]).text()).toBe('3 contacts');
    expect($(options[1]).text()).toBe('kristof vc');
    expect($(options[2]).text()).toBe('birgit matthe');
    expect($(options[3]).text()).toBe('blubber vis');
  });

  it('should navigate to action when changing select', function () {
    spyOn(commandHandler, 'handle').and.callFake(mockHelpers.resolvedPromise($q));

    const options = element.find('option');
    $(options[2]).prop('selected', true).change();
    $rootScope.$apply();

    $(options[0]).prop('selected', true).change();
    $rootScope.$apply();

    expect(commandHandler.handle).toHaveBeenCalledTimes(1);
    expect(commandHandler.handle).toHaveBeenCalledWith({
      command: "navigate",
      arguments: {
        linkTo: "focus-mode",
        params: {
          mainMenuKey: "sales-marketing",
          focusModeId: "test",
          recordId: "birgit"
        }
      }
    });
  });
});
