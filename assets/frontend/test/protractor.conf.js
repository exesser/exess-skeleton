'use strict';

exports.config = {
  directConnect: true,

  // Capabilities to be passed to the webdriver instance.
  capabilities: {
    'browserName': 'chrome'
  },

  // Spec patterns are relative to the current working directly when
  // protractor is called.
  specs: [
    '../.tmp/**/*.e2e.js'
  ],

  // Options to be passed to Jasmine-node.
  jasmineNodeOpts: {
    isVerbose: true,
    showColors: true,
    defaultTimeoutInterval: 30000
  },

  mocks: {
    dir: 'mock-requests'
  },
  onPrepare: function() {
    require('protractor-http-mock').config = {
      rootDirectory: __dirname, // default value: process.cwd()
      protractorConfig: 'protractor.conf.js' // default value: 'protractor.conf'
    };
  }
};
