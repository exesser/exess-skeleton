// Generated on 2016-01-22 using generator-mad-angular 0.3.1, updated to 1.0.2 on 2017-02-1
'use strict';

var _ = require('lodash');

var serveStatic = require('serve-static');

module.exports = function (grunt) {

  // Load grunt tasks automatically
  require('load-grunt-tasks')(grunt);

  // Time how long tasks take. Can help when optimizing build times
  require('time-grunt')(grunt);

  var DEV_CONFIG = require('./config/DEV_CONFIG');
  var bowerJson = require('./bower.json');

  // Configurable paths for the application
  var appConfig = {
    app: bowerJson.appPath || 'app',
    version: bowerJson.version || 'unknown',
    name: bowerJson.name || 'unknown',
    dist: '../../public/dist', // used by yeoman project settings
    testPort: grunt.option('testPort') || 9001
  };

  // Define the configuration for all the tasks
  grunt.initConfig({

    // Project settings
    yeoman: appConfig,

    // Watches files for changes and runs tasks based on the changed files
    watch: {
      indexTpl: {
        files: ['<%= yeoman.app %>/index.tpl.html'],
        tasks: ['includeSource:server']
      },
      scripts: {
        files: ['<%= yeoman.app %>/es6/**/*.js'],
        tasks: ['newer:babel:server', 'includeSource:server'],
        options: {
          event: ['added', 'deleted'] // Preform when file name changes or file gets added / removed.
        }
      },
      bower: {
        files: ['bower.json'],
        tasks: ['wiredep']
      },
      js: {
        files: ['<%= yeoman.app %>/es6/**/*.js'],
        tasks: ['newer:babel:server', 'eslint']
      },
      directiveHtml: {
        files: ['<%= yeoman.app %>/es6/**/*.html'],
        tasks: ['newer:copy:directiveHtml']
      },
      config: {
        files: ['config/**/*.js', '<%= yeoman.app %>/languages/**/*.json'],
        tasks: ['ngconstant:development', 'jsbeautifier']
      },
      custom_config: {
        files: ['config/custom.json', 'config/custom.json.dist'],
        tasks: ['concurrent:tmp']
      },
      sass: {
        files: ['<%= yeoman.app %>/**/*.scss'],
        tasks: ['sass:compile', 'autoprefixer']
      },
      gruntfile: {
        files: ['Gruntfile.js']
      },
      livereload: {
        options: {
          livereload: {
            host: '127.0.0.10',
            port: '<%= connect.options.livereload %>'
          }
        },
        files: [
          '<%= yeoman.app %>/**/*.html',
          '.tmp/scripts/**/*.js',
          '.tmp/scripts/**/*.html',
          '.tmp/styles/**/*.css',
          '.tmp/custom.json',
          '<%= yeoman.app %>/images/**/*.{png,jpg,jpeg,gif,webp,svg}'
        ]
      },
      options: {
        interval: DEV_CONFIG.watch_interval
      }
    },

    // The actual grunt server settings
    connect: {
      options: DEV_CONFIG.server,
      api: {
        proxies: [DEV_CONFIG.proxy]
      },
      livereload: {
        options: {
          open: true,
          middleware: function (connect) {
            return [
              require('grunt-connect-proxy2/lib/utils').proxyRequest,
              serveStatic(require('path').resolve('app')),
              serveStatic(require('path').resolve('.tmp')),
              connect().use(
                '/bower_components',
                serveStatic('./bower_components')
              ),
              // Map /swagger to our own swagger/index.html file where we do custom swagger configuration
              connect().use(
                '/swagger',
                serveStatic('./swagger')
              ),
              // Add swagger dependencies such as CSS, JS, and images on the path
              connect().use(
                '/swagger-dist',
                serveStatic('./node_modules/swagger-ui/dist')
              ),
              // Map /coverage to Istanbul's reporting directory.
              connect().use(
                '/coverage',
                serveStatic('./coverage/report/lcov-report')
              ),
              serveStatic(appConfig.app)
            ];
          }
        }
      },
      test: {
        options: {
          port: appConfig.testPort,
          middleware: function (connect) {
            return [
              serveStatic('.tmp'),
              serveStatic('test'),
              connect().use(
                '/bower_components',
                serveStatic('./bower_components')
              ),
              serveStatic(appConfig.app)
            ];
          }
        }
      },
      dist: {
        options: {
          open: true,
          middleware: function() {
            return [
              require('grunt-connect-proxy/lib/utils').proxyRequest,
              serveStatic(require('path').resolve('dist'))
            ];
          }
        }
      }
    },

    // Compile sass file to css and put it in the .tmp/styles/main.css
    sass: {
      compile: {
        options: {
          sourceMap: true
        },
        files: {
          '.tmp/styles/main.css': '<%= yeoman.app %>/sass/main.scss'
        }
      }
    },

    // Make sure code styles are up to par and there are no obvious mistakes
    eslint: {
      all: {
        options: {
          configFile: '.eslintrc'
        },
        src: [
          'Gruntfile.js',
          '<%= yeoman.app %>/es6/**/*.js',
          '!<%= yeoman.app %>/es6/**/*.spec.js',
          '!<%= yeoman.app %>/es6/**/*.e2e.js'
        ]
      },
      test: {
        options: {
          configFile: 'test/.eslintrc'
        },
        src: [
          'test/**/*.js',
          '<%= yeoman.app %>/es6/**/*.spec.js',
          '<%= yeoman.app %>/es6/**/*.e2e.js'
        ]
      }
    },

    // Empties folders to start fresh
    clean: {
      dist: {
        files: [{
          dot: true,
          src: [
            '.tmp',
            '<%= yeoman.dist %>/{,*/}*',
            '!<%= yeoman.dist %>/.git{,*/}*'
          ]
        }]
      },
      server: '.tmp'
    },

    // Add vendor prefixed styles
    autoprefixer: {
      options: {
        browsers: ['last 1 version']
      },
      dist: {
        files: [{
          expand: true,
          cwd: '.tmp/styles/',
          src: '**/*.css',
          dest: '.tmp/styles/'
        }]
      }
    },

    // Automatically inject Bower components into the index.html
    wiredep: {
      app: {
        src: ['<%= yeoman.app %>/index.tpl.html'],
        ignorePath: /\.\.\//
      },
      test: {
        src: 'test/karma.conf.js',
        fileTypes: {
          js: {
            block: /(([\s\t]*)\/\/\s*bower:*(\S*))(\n|\r|.)*?(\/\/\s*endbower)/gi,
            detect: {
              js: function(detect) {
                return /'.*\.js'/gi.test(detect);
              }
            },
            replace: {
              js: function(filePath) {
                // Remove '../' from filePath
                var path = filePath.substring('../'.length);
                return '\'' + path + '\',';
              }
            }
          }
        }
      }
    },

    // Renames files for browser caching purposes
    filerev: {
      dist: {
        src: [
          '<%= yeoman.dist %>/scripts/{,*/}*.js',
          '<%= yeoman.dist %>/styles/{,*/}*.css',
          '<%= yeoman.dist %>/images/{,*/}*.{png,jpg,jpeg,gif,webp,svg}',
          '<%= yeoman.dist %>/fonts/*'
        ]
      }
    },

    // Reads HTML for usemin blocks to enable smart builds that automatically
    // concat, minify and revision files. Creates configurations in memory so
    // additional tasks can operate on them
    useminPrepare: {
      html: '<%= yeoman.dist %>/index.html',
      options: {
        dest: '<%= yeoman.dist %>',
        flow: {
          html: {
            steps: {
              js: ['concat', 'uglify'],
              css: ['cssmin']
            },
            post: {}
          }
        }
      }
    },

    // Performs rewrites based on filerev and the useminPrepare configuration
    usemin: {
      html: ['<%= yeoman.dist %>/{,*/}*.html'],
      css: ['<%= yeoman.dist %>/styles/{,*/}*.css'],
      js: ['<%= yeoman.dist %>/scripts/{,*/}*.js'],
      options: {
        assetsDirs: ['<%= yeoman.dist %>', '<%= yeoman.dist %>/images'],
        patterns: {
          js: [[/(images\/[^''""]*\.(png|jpg|jpeg|gif|webp|svg))/g, 'Replacing references to images']]
        },
        // Make sure usemin and cdnify work nicely together, usemin by default undo's cdnify's work.
        blockReplacements: {
          js: function (block) {
            // Get all cdn'ed scripts, and ignore the others.
            var cdnSources = _.filter(block.src, function(src) {
              return src.substring(0, 2) === '//';
            });

            // Also add the 'block.destination'
            cdnSources.push(block.dest);

            // Create a block of script tags from the cdnSources.
            return _.reduce(cdnSources, function(total, src) {
              return total += '<script src="' + src + '"></script>';
            }, '');
          }
        }
      }
    },

    // Apply lossless compression to images.
    imagemin: {
      dist: {
        files: [{
          expand: true,
          cwd: '<%= yeoman.app %>/images',
          src: '**/*.{png,jpg,jpeg,gif}',
          dest: '<%= yeoman.dist %>/images'
        }]
      }
    },

    // Minify svg images.
    svgmin: {
      dist: {
        files: [{
          expand: true,
          cwd: '<%= yeoman.app %>/images',
          src: '**/*.svg',
          dest: '<%= yeoman.dist %>/images'
        }]
      }
    },

    // Minify the HTML, includes view and directive templates.
    htmlmin: {
      dist: {
        options: {
          collapseWhitespace: true,
          conservativeCollapse: true,
          collapseBooleanAttributes: true,
          removeCommentsFromCDATA: true,
          removeOptionalTags: true,
          keepClosingSlash: true
        },
        files: [{
          expand: true,
          cwd: '<%= yeoman.dist %>',
          src: ['*.html'],
          dest: '<%= yeoman.dist %>'
        }]
      }
    },

    // ng-annotate tries to make the code safe for minification automatically
    // by using the Angular long form for dependency injection.
    ngAnnotate: {
      dist: {
        files: [{
          expand: true,
          cwd: '.tmp/concat/scripts',
          src: ['*.js', '!oldieshim.js', '!*.spec.js', '!*.e2e.js'],
          dest: '.tmp/concat/scripts'
        }]
      }
    },

    // Replace vendor includes with Google CDN references.
    cdnify: {
      dist: {
        html: ['<%= yeoman.dist %>/*.html']
      }
    },

    // Copies remaining files to places other tasks can use
    copy: {
      tmp: {
        files: [{
          expand: true,
          dot: true,
          cwd: 'config',
          dest: '.tmp',
          src: [
            'custom.json.dist'
          ],
          rename: function () {
            return '.tmp/custom.json';
          },
          filter: function () {
            return !(grunt.file.exists('config/custom.json'));
          }
        }, {
          expand: true,
          dot: true,
          cwd: 'config',
          dest: '.tmp',
          src: [
            'custom.json'
          ],
          filter: function () {
            return grunt.file.exists('config/custom.json');
          }
        }]
      },
      dist: {
        files: [{
          expand: true,
          dot: true,
          cwd: '<%= yeoman.app %>',
          dest: '<%= yeoman.dist %>',
          src: [
            '*.{ico,png,txt,xml,jpg}',
            '.htaccess',
            '*.html',
            '!index.tpl.html', // Don't copy the index template.
            '!**/*.spec.js', // Don't copy the spec files
            '!**/*.e2e.js', // Don't copy the e2e files
            'images/**/*.{webp}',
            'fonts/**/*.*',
            'languages/{,*/}*.json' // Copy the translations.
          ]
        }, {
          expand: true,
          cwd: '.tmp/images',
          dest: '<%= yeoman.dist %>/images',
          src: ['generated/*']
        }, {
          expand: true,
          cwd: '.tmp',
          src: 'fonts/*',
          dest: '<%= yeoman.dist %>'
        }, {
          expand: true,
          cwd: '.tmp',
          src: 'custom.json',
          dest: '<%= yeoman.dist %>'
        }, {
          expand: true,
          cwd: 'bower_components/jsoneditor/dist/img',
          src: '*',
          dest: '<%= yeoman.dist %>/styles/img'
        }]
      },
      fonts: { // Put font-awesome in .tmp when serving.
        expand: true,
        cwd: 'bower_components/font-awesome/web-fonts-with-css/webfonts',
        src: '*',
        dest: '.tmp/fonts/'
      },
      directiveHtml: { // Put directive html files in .tmp when serving.
        expand: true,
        cwd: '<%= yeoman.app %>',
        src: 'es6/**/*.html',
        dest: '.tmp'
      }
    },

    // Run some tasks in parallel to speed up the build process
    concurrent: {
      fonts: [
        'copy:fonts'
      ],
      tmp: [
        'copy:tmp'
      ],
      dist: [
        'imagemin',
        'svgmin'
      ]
    },

    // Unit test settings
    karma: {
      options: {
        configFile: 'test/karma.conf.js'
      },
      single: { // Run once with coverage
        singleRun: true,
        coverage: true
      },
      debug: { // Run continuously without coverage so you can set breakpoints.
        singleRun: false,
        coverage: false
      },
      dev: { // Run continuously and report coverage.
        singleRun: false,
        coverage: true,
        port: 9003
      }
    },

    // e2e test settings
    protractor: {
      options: {
        keepAlive: false,
        noColor: false,
        args: {
          baseUrl: 'http://localhost:' + appConfig.testPort
        }
      },
      e2e: {
        options: {
          configFile: 'test/protractor.conf.js'
        }
      }
    },

    // zip the dist folder.
    compress: {
      release: {
        options: {
          archive: appConfig.name + '-' + appConfig.version + '.zip'
        },
        files: [{
          src: ['**/*'],
          cwd: 'dist/',
          expand: true,
          dest: '.'
        }]
      }
    },

    // Write make environment variables available as angular constant modules.
    ngconstant: {
      // Options for all targets
      options: {
        space: '  ',
        wrap: '// Auto-Generated: never change this file manually!\n\n\'use strict\';\n\n {%= __ngModule %}',
        name: 'digitalWorkplaceApp.Constants',
        dest: '.tmp/scripts/constants.js'
      },
      // Environment targets
      development: {
        constants: function() {
          return require('./config/development')(appConfig);
        }
      },
      production: {
        constants: function() {
          return require('./config/production')(appConfig);
        }
      }
    },

    // Make the constants modules generated by 'ngconstants' readable.
    jsbeautifier: {
      files: ['.tmp/scripts/constants.js'],
      options: {
        js: {
          indentSize: 2
        }
      }
    },

    karmaSonar: {
      sonar: {
        project: {
          key: appConfig.name,
          name: appConfig.name,
          version: appConfig.version
        },
        paths: [
          {
            src: '.tmp/scripts',
            test: 'test',
            reports: {
              coverage: 'coverage/report/lcov.info',
              unit: 'coverage/unit-test-results.xml'
            }
          }
        ]
      }
    },

    bump: {
      options: {
        files: ['package.json', 'bower.json'],
        commitFiles: ['package.json', 'bower.json'],
        pushTo: 'origin',
        tagName: appConfig.name + '-%VERSION%'
      }
    },

    // Automatically include all .js files from app/es6 in the index.html.
    includeSource: {
      options: {
        basePath: '.tmp',
        baseUrl: '/'
      },
      server: {
        files: {
          '.tmp/index.html': '<%= yeoman.app %>/index.tpl.html'
        }
      },
      dist: {
        files: {
          '<%= yeoman.dist %>/index.html': '<%= yeoman.app %>/index.tpl.html'
        }
      }
    },

    babel: {
      options: {
        sourceMap: true,
        presets: ['es2015']
      },
      server: {
        expand: true,
        cwd: '<%= yeoman.app %>/es6',
        src: '**/*.js',
        dest: '.tmp/scripts/'
      },
      e2e: {
        expand: true,
        dot: true,
        cwd: 'test',
        dest: '.tmp/test',
        src: [
          '**/*.e2e.js'
        ]
      }
    },

    ngtemplates: {
      dist: {
        options: {
          module: 'digitalWorkplaceApp',
          htmlmin: '<%= htmlmin.dist.options %>',
          usemin: 'scripts/scripts.js'
        },
        cwd: '<%= yeoman.app %>',
        src: [
          'views/**/*.html',
          'es6/**/*.html'
        ],
        dest: '.tmp/templateCache.js'
      }
    }
  });

  grunt.registerTask('serve', 'Compile then start a connect web server', function (target, environment) {
    if (target === 'dist') {
      var env = environment || 'development';
      return grunt.task.run(['build:' + env, 'configureProxies:api', 'connect:dist:keepalive']);
    }

    grunt.task.run([
      'clean:server',
      'copy:directiveHtml',
      'babel:server',
      'includeSource:server',
      'ngconstant:development',
      'wiredep',
      'concurrent:tmp',
      'concurrent:fonts',
      'sass:compile',
      'autoprefixer',
      'connect:livereload',
      'configureProxies:api',
      'watch'
    ]);
  });

  grunt.registerTask('test', function (target) {
    var tasks = [
      'clean:server',
      'copy:directiveHtml',
      'babel:server',
      'babel:e2e',
      'includeSource:server',
      'ngconstant:development',
      'wiredep',
      'concurrent:tmp',
      'sass:compile',
      'eslint',
      'autoprefixer',
      'connect:test'
    ];

    if (target === 'unit') {
      tasks.push('karma:single');
    } else if (target === 'e2e') {
      //tasks.push('protractor');
    } else {
      tasks.push('karma:single');
      //tasks.push('protractor');
    }

    grunt.task.run(tasks);
  });

  grunt.registerTask('build', function(environment) {
    var env = environment || 'production';

    grunt.task.run([
      'test:unit', // Test App before building.
      'clean:server',
      'clean:dist',
      'babel:server',
      'ngconstant:' + env,
      'includeSource:dist',
      'wiredep',
      // 'cdnify',  DEPRECATED - https://bower.herokuapp.com/packages/search/jquery
      'sass:compile',
      'useminPrepare',
      'concurrent:tmp',
      'concurrent:fonts',
      'concurrent:dist',
      'ngtemplates',
      'autoprefixer',
      'concat',
      'ngAnnotate',
      'copy:directiveHtml',
      'copy:dist',
      'cssmin',
      'uglify',
      'filerev',
      'usemin',
      'htmlmin',
      'compress'
    ]);
  });

  grunt.registerTask('build-continuous', function(buildNumber) {
    // Alter the version so 'ngconstant' creates a configuration where the version is the buildnumber.
    appConfig.version = appConfig.version + "-SNAPSHOT-" + buildNumber;

    grunt.task.run('build:production');
  });

  grunt.registerTask('sonar', [
    'test:unit',
    'karmaSonar:sonar'
  ]);
};
