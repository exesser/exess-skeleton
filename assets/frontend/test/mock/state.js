'use strict';

// Register helpers to 'mockHelpers' because it is a global in the .jshintrc
const mockHelpers = mockHelpers || {};

/*
  Function that handles expecting various $state transitions.
  Some test load the app.js which automatically transitions
  to new states. So we spy on them to override their functionality
  so no actual transitions or go's occur.

  See http://stackoverflow.com/questions/27724956/ui-router-extras-breaks-my-unit-tests-with-unexpected-results-error#answer-27856097
*/
mockHelpers.blockUIRouter = function($state) {
  spyOn($state, 'go');
  spyOn($state, 'transitionTo');
};
