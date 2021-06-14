'use strict';

// Register helpers to 'mockHelpers' because it is a global in the .jshintrc
const mockHelpers = mockHelpers || {};

mockHelpers.createGuidanceFormObserverAccessor = function({ $compile, $rootScope, guidanceFormObserver, validationObserver, suggestionsObserver }) {
  const template = `
    <guidance-observers-accessor
      guidance-form-observer="guidanceFormObserver"
      validation-observer="validationObserver"
      suggestions-observer="suggestionsObserver">
    </guidance-observers-accessor>
  `;

  const scope = $rootScope.$new();
  scope.guidanceFormObserver = guidanceFormObserver;
  scope.validationObserver = validationObserver;
  scope.suggestionsObserver = suggestionsObserver;

  const element = angular.element(template);

  return $compile(element)(scope);
};
