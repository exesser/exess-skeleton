'use strict';

describe('checkable-icon event integration test', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let $rootScope;

  let guidanceFormObserver;
  let validationObserver;
  let suggestionsObserver;

  let element;

  const template = "<basic-formly-form form-key='default'></basic-formly-form>";

  beforeEach(inject(function ($compile, $state, _$rootScope_, guidanceFormObserverFactory,
                              GuidanceFormObserver, ValidationObserver, SuggestionsObserver) {
    $rootScope = _$rootScope_;

    mockHelpers.blockUIRouter($state);

    guidanceFormObserver = guidanceFormObserverFactory.createGuidanceFormObserver();
    spyOn(guidanceFormObserver, 'formValueChanged');

    validationObserver = new ValidationObserver();
    suggestionsObserver = new SuggestionsObserver();

    const guidanceFormObserverAccessorElement = mockHelpers.createGuidanceFormObserverAccessor({
      $compile,
      $rootScope,
      guidanceFormObserver,
      validationObserver,
      suggestionsObserver
    });

    const scope = $rootScope.$new();

    element = angular.element(template);
    element = $compile(element)(scope);

    guidanceFormObserverAccessorElement.append(element);

    $rootScope.$apply();
  }));

  describe('for checkable icons an event', function () {
    beforeEach(function () {
      guidanceFormObserver.stepChangeOccurred({
        model: {
          nestedObject: {
            gas: false,
            electricity: false
          }
        },
        form: {
          default: {
            fields: [
              {
                id: "checkboxGroup",
                type: "IconCheckboxGroup",
                fields: [
                  {
                    id: "nestedObject.gas",
                    iconClass: "icon-aardgas"
                  },
                  {
                    id: "nestedObject.electricity",
                    iconClass: "icon-elektriciteit"
                  }
                ]
              }
            ]
          }
        }
      });
      $rootScope.$apply();
    });

    it('should should be sent out when checking or unchecking either option', function () {
      expect(guidanceFormObserver.formValueChanged).not.toHaveBeenCalled();

      const gasIcon = element.find("input")[0];
      const elecIcon = element.find("input")[1];

      // Gas icon

      gasIcon.click();
      $rootScope.$apply();
      expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(1);
      expect(guidanceFormObserver.formValueChanged.calls.mostRecent().args[0]).toEqual({
        focus: 'nestedObject.gas',
        value: true
      });

      gasIcon.click();
      $rootScope.$apply();
      expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(2);
      expect(guidanceFormObserver.formValueChanged.calls.mostRecent().args[0]).toEqual({
        focus: 'nestedObject.gas',
        value: false
      });

      gasIcon.click();
      $rootScope.$apply();
      expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(3);
      expect(guidanceFormObserver.formValueChanged.calls.mostRecent().args[0]).toEqual({
        focus: 'nestedObject.gas',
        value: true
      });

      // Electricity icon

      elecIcon.click();
      $rootScope.$apply();
      expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(4);
      expect(guidanceFormObserver.formValueChanged.calls.mostRecent().args[0]).toEqual({
        focus: 'nestedObject.electricity',
        value: true
      });

      elecIcon.click();
      $rootScope.$apply();
      expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(5);
      expect(guidanceFormObserver.formValueChanged.calls.mostRecent().args[0]).toEqual({
        focus: 'nestedObject.electricity',
        value: false
      });

      elecIcon.click();
      $rootScope.$apply();
      expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(6);
      expect(guidanceFormObserver.formValueChanged.calls.mostRecent().args[0]).toEqual({
        focus: 'nestedObject.electricity',
        value: true
      });
    });
  });
});
