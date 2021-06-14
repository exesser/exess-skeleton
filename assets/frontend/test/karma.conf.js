
'use strict';
module.exports = function(config) {
  // The JavaScript preprocessors for the /app folder.
  var babelPlugins = [];
  if (config.coverage === true) {
    babelPlugins.push(['istanbul', { exclude: ['**/*.spec.js', 'test/**/*.js'] }]);
  }
  config.set({
    // enable / disable watching file and executing tests whenever any file changes
    autoWatch: true,
    // base path, that will be used to resolve files and exclude
    basePath: '../',
    // testing framework to use (jasmine/mocha/qunit/...)
    frameworks: ['jasmine'],
    // list of files / patterns to load in the browser
    files: [
      // bower:js
      'bower_components/jquery/dist/jquery.js',
      'bower_components/angular/angular.js',
      'bower_components/json3/lib/json3.js',
      'bower_components/es5-shim/es5-shim.js',
      'bower_components/lodash/lodash.js',
      'bower_components/angular-ui-router/release/angular-ui-router.js',
      'bower_components/angular-animate/angular-animate.js',
      'bower_components/angular-cookies/angular-cookies.js',
      'bower_components/angular-messages/angular-messages.js',
      'bower_components/angular-sanitize/angular-sanitize.js',
      'bower_components/angular-touch/angular-touch.js',
      'bower_components/angular-translate/angular-translate.js',
      'bower_components/angular-translate-loader-static-files/angular-translate-loader-static-files.js',
      'bower_components/angular-translate-storage-cookie/angular-translate-storage-cookie.js',
      'bower_components/angular-translate-storage-local/angular-translate-storage-local.js',
      'bower_components/api-check/dist/api-check.js',
      'bower_components/angular-formly/dist/formly.js',
      'bower_components/angular-uuid-service/angular-uuid-service.js',
      'bower_components/moment/moment.js',
      'bower_components/pikaday/pikaday.js',
      'bower_components/pikaday-angular/pikaday-angular.js',
      'bower_components/ng-file-upload/ng-file-upload.js',
      'bower_components/angular-loading-bar/build/loading-bar.js',
      'bower_components/angular-elastic/elastic.js',
      'bower_components/rangy/rangy-core.js',
      'bower_components/rangy/rangy-classapplier.js',
      'bower_components/rangy/rangy-highlighter.js',
      'bower_components/rangy/rangy-selectionsaverestore.js',
      'bower_components/rangy/rangy-serializer.js',
      'bower_components/rangy/rangy-textrange.js',
      'bower_components/textAngular/dist/textAngular.js',
      'bower_components/textAngular/dist/textAngular-sanitize.js',
      'bower_components/textAngular/dist/textAngularSetup.js',
      'bower_components/signature_pad/signature_pad.js',
      'bower_components/angular-signature/src/signature.js',
      'bower_components/SHA-1/dist/sha1.umd.js',
      'bower_components/angulartics/src/angulartics.js',
      'bower_components/angulartics-google-tag-manager/lib/angulartics-google-tag-manager.js',
      'bower_components/jsoneditor/dist/jsoneditor.min.js',
      'bower_components/ng-jsoneditor/ng-jsoneditor.min.js',
      'bower_components/angular-hotkeys/build/hotkeys.js',
      // endbower
      'bower_components/angular-mocks/angular-mocks.js',
      'app/es6/app.js',
      'test/mock/**/*.js',
      'app/es6/**/*.js',
      '.tmp/es6/**/*.html',
      '.tmp/scripts/constants.js'
    ],
     // list of files / patterns to exclude
    exclude: [
      'app/es6/**/*.e2e.js'
    ],
    // web server port
    port: 9002,
    // Start these browsers, currently available:
    // - Chrome
    // - ChromeCanary
    // - Firefox
    // - Opera
    // - Safari (only Mac)
    // - PhantomJS
    // - IE (only Windows)
    browsers: ['PhantomJS'],
    // Which plugins to enable
    plugins: [
      'karma-phantomjs-launcher',
      'karma-jasmine',
      'karma-coverage',
      'karma-ng-html2js-preprocessor',
      'karma-junit-reporter',
      'karma-babel-preprocessor',
      'karma-sourcemap-loader',
      'karma-spec-reporter'
    ],
    // Continuous Integration mode
    // if true, it capture browsers, run tests and exit
    singleRun: false,
    colors: true,
    // level of logging
    // possible values: LOG_DISABLE || LOG_ERROR || LOG_WARN || LOG_INFO || LOG_DEBUG
    logLevel: config.LOG_INFO,
    // coverage reporter generates the coverage
    reporters: ['spec', 'coverage', 'junit'],
    preprocessors: {
      'app/**/*.js': ['babel', 'sourcemap'],
      'test/**/*.js': ['babel', 'sourcemap'],
      // Templates to load in '$templateCache' so they are available when testing.
      'app/**/*.html': ['ng-html2js'],
      '.tmp/**/*.html': ['ng-html2js']
    },
    babelPreprocessor: {
      options: {
        presets: ['es2015'],
        sourceMap: "inline",
        plugins: babelPlugins
      }
    },
    // optionally, configure the reporter
    coverageReporter: {
      type: 'lcov',
      dir: 'coverage',
      subdir: 'report'
    },
    // The configure the reporter that is ran in the terminal.
    specReporter: {
      showSpecTiming: true, // print the time elapsed for each spec
      suppressSkipped: true, // ignore skipped tests in output, useful for fdescribe's, and fit's
      suppressErrorSummary: false
    },
    // Put 'angular' view templates in modules for testing.
    ngHtml2JsPreprocessor: {
      // Strip first slash
      cacheIdFromPath: function(filepath) {
        var firstSlashIndex = filepath.indexOf('/') + 1;
        return filepath.substring(firstSlashIndex);
      },
      moduleName: 'digitalWorkplaceAppTemplates'
    },
    // Write the results of the test in a JUnit format so Sonar can analyze it.
    junitReporter: {
      useBrowserName: false,
      outputFile: 'coverage/unit-test-results.xml'
    }
  });
};
