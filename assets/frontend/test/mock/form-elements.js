'use strict';

// Register helpers to 'mockHelpers' because it is a global in the .jshintrc
const mockHelpers = mockHelpers || {};

mockHelpers.createValidationMixinMock = function(validationMixin) {
  spyOn(validationMixin, 'apply').and.callFake(function(controller) {
    controller.errorMessages = [];
  });
};

mockHelpers.createSuggestionsMixinMock = function(suggestionsMixin) {
  spyOn(suggestionsMixin, 'apply').and.callFake(function(controller) {
    controller.suggestions = [];
  });
};

mockHelpers.createGuidanceFormControllerMixinMock = function({ guidanceFormControllerMixin, model, fields }) {
  spyOn(guidanceFormControllerMixin, 'apply').and.callFake(function({ controller }) {
    controller.model = model;
    controller.fields = fields;
  });
};
