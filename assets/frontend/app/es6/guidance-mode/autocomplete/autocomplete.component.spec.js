'use strict';

describe('Component: autocomplete', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let $rootScope;
  let $compile;
  let $document;
  let elementPosition;

  let scope;
  let element;

  const template = `
    <input type="text" id="candies"/>
    <autocomplete
      for-elements="completionIds"
      suggestion-clicked="suggestionClicked(suggestion)"
      suggestions="suggestions"
      suggestion-left-text-property="label"
      suggestion-right-text-property="labelAddition">
    </autocomplete>
  `;

  beforeEach(inject(function (_$rootScope_, _$compile_, _$document_, $state, _elementPosition_) {
    $rootScope = _$rootScope_;
    $compile = _$compile_;
    $document = _$document_;
    elementPosition = _elementPosition_;

    mockHelpers.blockUIRouter($state);
  }));

  function compile(suggestions) {
    scope = $rootScope.$new(true);

    scope.completionIds = ['candies'];
    scope.model = {
      "favorite_drink": "COLA",
      "favorite_candy": ""
    };
    scope.suggestions = suggestions;

    // This implements the most common usecase
    scope.suggestionClicked = function (suggestion) {
      scope.model = _.merge(scope.model, suggestion.model);
    };

    element = angular.element(template);
    element = $compile(element)(scope);

    $rootScope.$apply();
  }

  describe('when there are no suggestions', function () {
    beforeEach(function () {
      compile([]);
    });

    it('should be invisible when there are no suggestions, and mouse is clicked', function () {
      $document.triggerHandler({ type: 'mousedown', toElement: { id: 'candies' } });

      $rootScope.$apply();

      expect($(element.find(".suggestions")).hasClass('ng-hide')).toBe(true);
    });

    it('should not handle keyboard keys when there are no suggestions', function () {
      expect(element.find("ul li").length).toBe(0);

      $document.triggerHandler({ type: 'keyup', 'keyCode': 55 });
      $rootScope.$apply();

      expect(element.find("ul li").length).toBe(0);
    });
  });

  describe('when there are suggestions', function () {
    beforeEach(function () {
      const suggestions = [{
        "label": "Drop",
        "labelAddition": "lekker",
        "model": { "favorite_candy": "DROP" }
      }, {
        "label": "Chocolate",
        "labelAddition": "jammie",
        "model": { "favorite_candy": "CHOCOLATE" }
      }, {
        "label": "Apple",
        "labelAddition": "Oh la la",
        "model": { "favorite_candy": "APPLE" }
      }, {
        "label": "Gum",
        "labelAddition": "hell yeah",
        "model": { "favorite_candy": "GUM" }
      }, {
        "label": "Caramel",
        "labelAddition": "salty",
        "model": { "favorite_candy": "CARAMEL" }
      }];

      compile(suggestions);

      $document.triggerHandler({ type: 'mousedown', toElement: { id: 'candies' } });

      $rootScope.$apply();

      expect($(element.find(".suggestions")).hasClass('ng-hide')).toBe(false);
    });

    it('should be visible and have five options', function () {
      expect(element.find("ul li").length).toBe(5);

      // Check one element to see if the 'left-text' and 'right-text' are correct.
      const liElement = $(element.find('li')[0]);
      expect($(liElement.find('b')).text()).toBe('Drop');
      expect($(liElement.find('small')).text()).toBe('lekker');

      expect($(element.find(".suggestions")).hasClass('ng-hide')).toBe(false);
    });

    it('should update a model change when clicking on an option', function () {
      expect(scope.suggestions.length).toBe(5);
      expect(scope.model.favorite_candy).toBe("");

      $(element.find("ul li")[1]).click();
      $rootScope.$apply();

      expect(scope.model.favorite_candy).toBe("CHOCOLATE");
      expect(scope.suggestions.length).toBe(5);

      expect($(element.find(".suggestions")).hasClass('ng-hide')).toBe(true);
    });

    describe('UP and DOWN and ENTER keys', function () {
      const UP_KEY = 38;
      const DOWN_KEY = 40;
      const ENTER_KEY = 13;

      describe('when element is active', function () {
        beforeEach(function () {
          // Set the active element to a known element.
          document.activeElement.id = 'candies'; //eslint-disable-line angular/document-service
        });

        it('should be able to set a selection through the UP, DOWN and ENTER keys', function () {
          /*
           For some reason I cannot make spy's on the regular scrollTop.
           So I'm spying on the jQuery scrollTop instead.
           */
          spyOn($.fn, "scrollTop");

          // Get the first two <li>'s and give them a fake heights.
          const liElements = element.find('li');
          $(liElements[0]).height(10);
          $(liElements[1]).height(20);

          // Move down to Chocolate
          $document.triggerHandler({ type: 'keyup', 'keyCode': DOWN_KEY });
          $rootScope.$apply();
          expect($.fn.scrollTop).toHaveBeenCalledTimes(1);
          expect($.fn.scrollTop.calls.argsFor(0)[0]).toBe(0); // Should be ignored

          // Move down to Apple
          $document.triggerHandler({ type: 'keyup', 'keyCode': DOWN_KEY });
          $rootScope.$apply();
          expect($.fn.scrollTop).toHaveBeenCalledTimes(2);
          expect($.fn.scrollTop.calls.argsFor(1)[0]).toBe(0); // Should be ignored

          // Move down to Gum
          $document.triggerHandler({ type: 'keyup', 'keyCode': DOWN_KEY });
          $rootScope.$apply();
          expect($.fn.scrollTop).toHaveBeenCalledTimes(3);
          expect($.fn.scrollTop.calls.argsFor(2)[0]).toBe(10); // Move height of first <li>

          // Move down to Caramel
          $document.triggerHandler({ type: 'keyup', 'keyCode': DOWN_KEY });
          $rootScope.$apply();
          expect($.fn.scrollTop).toHaveBeenCalledTimes(4);
          expect($.fn.scrollTop.calls.argsFor(3)[0]).toBe(30); // Move combined height of second and first <li>

          // Move back up to Gum
          $document.triggerHandler({ type: 'keyup', 'keyCode': UP_KEY });
          $rootScope.$apply();
          expect($.fn.scrollTop).toHaveBeenCalledTimes(5);
          expect($.fn.scrollTop.calls.argsFor(4)[0]).toBe(10); // Move height of first <li>

          // Select Gum
          $document.triggerHandler({ type: 'keyup', 'keyCode': ENTER_KEY });
          $rootScope.$apply();

          expect(scope.model.favorite_candy).toBe("GUM");
          expect($(element.find(".suggestions")).hasClass('ng-hide')).toBe(true);
        });

        it('should not do anything when other keys are clicked', function () {
          // We are not clicking all keyboard keys so I've selected the key N randomly.
          const N_KEY = 78;

          $document.triggerHandler({ type: 'keyup', 'keyCode': N_KEY });
          $rootScope.$apply();

          // Check of nothing changed.
          expect(scope.model.favorite_candy).toBe("");
          expect($(element.find(".suggestions")).hasClass('ng-hide')).toBe(false);
        });
      });

      it('should not do anything when the "activeElement" is not part of our for elements', function () {
        // Set the active element to a non existing element.
        document.activeElement.id = 'something-else'; //eslint-disable-line angular/document-service

        expect(scope.model.favorite_candy).toBe("");

        // Move down to Chocolate
        $document.triggerHandler({ type: 'keyup', 'keyCode': DOWN_KEY });
        $rootScope.$apply();

        expect(scope.model.favorite_candy).toBe("");

        // Select Chocolate
        $document.triggerHandler({ type: 'keyup', 'keyCode': ENTER_KEY });
        $rootScope.$apply();

        // Because we are not active this should not have done anything.
        expect(scope.model.favorite_candy).toBe("");
        expect($(element.find(".suggestions")).hasClass('ng-hide')).toBe(false);
      });
    });

    describe('LEFT, RIGHT, BACKSPACE and ESC keys', function () {
      const LEFT_KEY = 37;
      const RIGHT_KEY = 39;
      const ESC_KEY = 27;
      const BACKSPACE_KEY = 8;

      function checkKey(keyCode) {
        expect(element.find("ul li").length).toBe(5);

        $document.triggerHandler({ type: 'keyup', keyCode });
        $rootScope.$apply();

        expect($(element.find(".suggestions")).hasClass('ng-hide')).toBe(true);
      }

      it('should reset the suggestions when ESC is pressed', function () {
        checkKey(ESC_KEY);
      });

      it('should reset the suggestions when LEFT is pressed', function () {
        checkKey(LEFT_KEY);
      });

      it('should reset the suggestions when RIGHT is pressed', function () {
        checkKey(RIGHT_KEY);
      });

      it('should reset the suggestions when BACKSPACE is pressed', function () {
        checkKey(BACKSPACE_KEY);
      });
    });
  });

  describe('mouse clicks', function () {
    it('should when mouse is clicked somewhere else on the page hide suggestions', function () {
      compile([
        { "label": "Drop", "model": { "favorite_candy": "DROP" } }
      ]);

      expect(element.find("ul li").length).toBe(1);

      $document.triggerHandler({ type: 'mousedown', toElement: { id: 'bogus' } });
      $rootScope.$apply();

      expect($(element.find(".suggestions")).hasClass('ng-hide')).toBe(true);
    });

    it('should when mouse is clicked in the ulElement ignore clicks', function () {
      spyOn($document, 'bind');

      compile([
        { "label": "Drop", "model": { "favorite_candy": "DROP" } }
      ]);

      const mousedown = $document.bind.calls.argsFor(1)[1];

      const liElement = element.find('li')[0];
      mousedown({ target: liElement, toElement: { id: 'bogus' } });

      expect(element.find("ul li").length).toBe(1);

      element.find("ul").triggerHandler({ type: 'mousedown' });
      $rootScope.$apply();

      expect(element.find("ul li").length).toBe(1);
    });
  });

  it('should register and deregister itself for "keyup" and "mousedown" events', function () {
    spyOn($document, 'bind');
    spyOn($document, 'unbind');

    compile([]);

    expect($document.bind).toHaveBeenCalledTimes(2);

    expect($document.bind.calls.argsFor(0)[0]).toBe('keyup');
    expect($document.bind.calls.argsFor(1)[0]).toBe('mousedown');

    scope.$destroy();

    expect($document.unbind).toHaveBeenCalledTimes(2);
    expect($document.unbind.calls.argsFor(0)[0]).toBe('keyup');
    expect($document.unbind.calls.argsFor(1)[0]).toBe('mousedown');
  });

  it('should know when to render above the field', function () {
    compile([
      { "label": "Drop", "model": { "favorite_candy": "DROP" } }
    ]);

    const suggestionsElement = $(element.find(".suggestions"));

    const isAboveFoldSpy = spyOn(elementPosition, 'isAboveFold');
    isAboveFoldSpy.and.returnValue(true);

    expect(suggestionsElement.hasClass('above-field')).toBe(false);

    isAboveFoldSpy.and.returnValue(false);
    $rootScope.$apply();

    expect(suggestionsElement.hasClass('above-field')).toBe(true);
  });
});
