'use strict';

// Register helpers to 'mockHelpers' because it is a global in the .jshintrc
const mockHelpers = mockHelpers || {};

mockHelpers.logHeadersInterceptor = function () {

  module('digitalWorkplaceApp', function config($provide) {
    $provide.value('logHeadersInterceptor', {
      request: function (config) {
        return config;
      }
    });
  });
};

