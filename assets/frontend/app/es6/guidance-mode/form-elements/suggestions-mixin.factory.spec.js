'use strict';

describe('Mixin: suggestionsMixin', function () {

  // load the factory's module
  beforeEach(module('digitalWorkplaceApp'));

  let suggestionsMixin;
  let flashMessageContainer;
  let $rootScope;

  let suggestionsObserver;
  let guidanceFormObserver;

  beforeEach(inject(function (_suggestionsMixin_, _suggestionsObserverFactory_, _flashMessageContainer_,
                              SuggestionsObserver, GuidanceFormObserver, _$rootScope_) {
    suggestionsMixin = _suggestionsMixin_;
    flashMessageContainer = _flashMessageContainer_;
    $rootScope = _$rootScope_;

    suggestionsObserver = new SuggestionsObserver();
    guidanceFormObserver = new GuidanceFormObserver();

    spyOn(flashMessageContainer, 'addMessageOfType');
  }));

  it('should validate that the form element controller has a key.', function () {
    const controller = {};

    expect(function () {
      suggestionsMixin.apply(controller);
    }).toThrow(new Error("Error a form element controller must have a key, the current key is: undefined."));
  });

  it('should throw an error if guidanceObserversAccessor is missing.', function () {
    const controller = { key: "first_name", ngModel: 'fake' };

    expect(function () {
      suggestionsMixin.apply(controller);
    }).toThrow(new Error("Error: a form element controller must have a guidanceObserversAccessor, the current guidanceObserversAccessor is: undefined."));
  });

  describe('when the controller is valid', function () {
    let controller;

    beforeEach(function () {
      controller = {
        key: 'first_name',
        model: {},
        guidanceObserversAccessor: {
          getGuidanceFormObserver() {
            return guidanceFormObserver;
          }
        }
      };
    });

    it('should add a suggestions array to the controller.', function () {
      expect(controller.suggestions).toBeUndefined();

      suggestionsMixin.apply(controller, suggestionsObserver);

      expect(controller.suggestions).toEqual([]);
    });

    describe('registerFieldSuggestionsCallback', function () {
      let suggestionsChangedCallback;
      let getSuggestionsForKeySpy;

      beforeEach(function () {
        spyOn(suggestionsObserver, 'registerSuggestionsChangedCallback');

        expect(suggestionsObserver.registerSuggestionsChangedCallback).not.toHaveBeenCalled();

        //Call the suggestionsMixin to trigger registration to the suggestionsChanged event.
        suggestionsMixin.apply(controller, suggestionsObserver);

        //Mock the response from the getSuggestionsForKey function which is called after the suggestionsMixin's suggestionsChanged callback is invoked.
        const suggestion = { label: "John", model: { first_name: "John", "last_name": "Doe" } };
        suggestionsChangedCallback = suggestionsObserver.registerSuggestionsChangedCallback.calls.allArgs()[0][0]; //First call, first argument
        getSuggestionsForKeySpy = spyOn(suggestionsObserver, 'getSuggestionsForKey').and.returnValue([suggestion]);

        //Check that controller.suggestions is set to the value we mocked in getSuggestionsForKey when we call the suggestionsChanged callback.
        suggestionsChangedCallback();
        expect(controller.suggestions).toEqual([suggestion]);
      });

      it('should set the suggestions to the given array of suggestions when the suggestionsChangedCallback is invoked again and we get other results', function () {
        const alternateSuggestion = { label: "Jane", model: { first_name: "Jane", "last_name": "Doe" } };
        getSuggestionsForKeySpy.and.returnValue([alternateSuggestion]);

        //Manually trigger the suggestions callback again without suggestions to see that they are cleared.
        suggestionsChangedCallback();
        expect(controller.suggestions).toEqual([alternateSuggestion]);
      });

      it('should set the suggestions to an empty array when the suggestionsChangedCallback is invoked', function () {
        getSuggestionsForKeySpy.and.returnValue([]);

        //Manually trigger the suggestions callback again without suggestions to see that they are cleared.
        suggestionsChangedCallback();
        expect(controller.suggestions).toEqual([]);
      });

      it('should keep the old suggestions when suggestionsChangedCallback is invoked and getSuggestionsForKey returns undefined', function () {
        getSuggestionsForKeySpy.and.returnValue(undefined);

        //Manually trigger the suggestions callback again without suggestions to see that they are cleared.
        suggestionsChangedCallback();
        expect(controller.suggestions).toEqual([{ label: "John", model: { first_name: "John", "last_name": "Doe" } }]);
      });
    });

    describe('addSuggestionClicked', function () {
      it('should add an "suggestionClicked" method, which triggers the guidanceFormObserver, and applies the suggestion when run', function () {
        controller.model.middle_name = 'C.';

        suggestionsMixin.apply(controller, suggestionsObserver);

        spyOn(guidanceFormObserver, 'formValueChanged');

        controller.suggestionClicked({ model: { first_name: 'Bob', last_name: 'Martin', extra: {age: 55}}});

        expect(controller.model).toEqual({
          first_name: 'Bob',
          middle_name: 'C.',
          last_name: 'Martin',
          extra: {age: 55}
        });

        expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(1);
        expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
          focus: 'first_name',
          value: "Bob"
        }, false);

        expect(function () {
          controller.suggestionClicked({
            model: { first_name: 'Ken', last_name: 'Block', extra: {age: 55}},
            parentModel: { car: "Ford Focus" }
          });
        }).toThrow(new Error("Error: first_name element controller must have a parentModel"));

        controller.parentModel = { country: "USA" };
        controller.suggestionClicked({
          model: { first_name: 'Ken', last_name: 'Block' },
          parentModel: { car: "Ford Focus" }
        });

        expect(controller.model).toEqual({
          first_name: 'Ken',
          middle_name: 'C.',
          last_name: 'Block',
          extra: {age: 55}
        });

        expect(controller.parentModel).toEqual({
          car: "Ford Focus",
          country: "USA"
        });

        expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(2);
        expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
          focus: 'first_name',
          value: "Ken"
        }, false);
      });

      it('should add a flash message when the suggestion is changing multiple fields', function () {
        controller.model.middle_name = 'C.';
        controller.model.last_name = 'Bakkerud';
        controller.model.number = 13;
        controller.model.car = '';
        controller.model.carType = '';
        controller.parentModel = {};
        controller.parentModel.address = {};
        controller.parentModel.address.country = 'Norway';

        suggestionsMixin.apply(controller, suggestionsObserver);

        spyOn(guidanceFormObserver, 'formValueChanged');
        expect(flashMessageContainer.addMessageOfType).not.toHaveBeenCalled();

        controller.suggestionClicked({
          model: { first_name: 'Ken', last_name: 'Block', number: 43, car: 'Ford', carType: null },
          parentModel: { address: { country: '' } },
          notifyChange: true
        });

        expect(controller.model).toEqual({
          middle_name: 'C.',
          last_name: 'Block',
          number: 43,
          first_name: 'Ken',
          car: 'Ford',
          carType: null
        });

        $rootScope.$apply();

        expect(flashMessageContainer.addMessageOfType).toHaveBeenCalledTimes(1);
        expect(flashMessageContainer.addMessageOfType).toHaveBeenCalledWith(
          'INFORMATION',
          'The selected suggestion also changed the following fields: Last name: Bakkerud -> Block; Number: 13 -> 43; Car: empty -> Ford; Country: Norway -> empty;  are you sure you chose the right suggestion?',
          'INFORMATION_SUGGESTION_CHANGE'
        );

        controller.suggestionClicked({ model: { first_name: 'Andreas', last_name: 'Bakkerud' }, notifyChange: false });
        $rootScope.$apply();
        expect(flashMessageContainer.addMessageOfType).toHaveBeenCalledTimes(1);
      });

      it('should not add an "suggestionClicked" method when "hasCustomSuggestionClicked" is true', function () {
        controller.model.middle_name = 'C.';

        suggestionsMixin.apply(controller, suggestionsObserver, true);

        expect(controller.suggestionClicked).toBe(undefined);
      });
    });
  });
});
