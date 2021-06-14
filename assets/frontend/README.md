# About

The Digital Workplace application is a beautiful
front-end for various business processes.

# mad-angular generator

This project was generated with the mad-angular generator.

# Developing this application

### Node and NPM

You should install Node, this will include NPM.

Required versions:

* Node: 8.1.3
* NPM: 5.0.3

##### Mac OS X

For mac users we recommend 'nvm' this is stands for node version manager.
It allows you to install multiple Node versions side by side:

https://github.com/creationix/nvm

First follow the installation instructions on nvm then enter the
following commands:

```
nvm install 8.1.3
nvm use 8.1.3
nvm alias default 8.1.3
```

### Grunt

Install [grunt-cli](http://gruntjs.com/getting-started) via npm:
```
npm install -g grunt-cli
```

This installs the grunt command line interface, but not grunt itself! grunt-cli acts as proxy to you projects specific grunt.

You never want to install grunt itself globally, grunt should always be a dependency of the project. This way the grunt version can change per project, this is more future proof than tying yourself to a specific grunt version. You don't want your project to stop building after a few months.

### Bower
Install [bower](http://bower.io/) via npm:
```
npm install -g bower
```

If you receive errors on the 'webdriver manager' it probably means that
you have the wrong Node and NPM version installed. Run the following
commands: `npm -v` and `node -v` and check if you have the required
versions. This project will NOT work with npm version `3.x`.

## Developing

Download this repository and open a terminal there. Now we must
install the develop NPM dependencies using:

```
npm install
```

Next we install the font-end Bower dependencies via:

```
bower install
```

## Configuring constants for dwp

In `.tmp` and `dist` there's a file named `custom.json`.
In this file we can define the default constants we want to overwrite. 
You can do this by copying the `config/custom.json.dist` file to `config/custom.json`. 

In your newly created file you can then overwrite all used constants by adding a line to the json similar to the following:

```
{
    ...,
    "DEBOUNCE_TIME": 1000
}
```

## Contributing

* Make sure to check for notices with `grunt eslint`
* Make sure to run all tests with `grunt karma:dev`
* Check if the tests cover everything, see `http://localhost:9005/coverage/`
* If it's not necessary to have multiple commit messages, squash them into one
* On Bitbucket, write a clear scenario on how to test your changes (be as complete as possible)

# Grunt options

## Build

The build command creates a nice package which is ready for deployment, but only after all the tests succeed.

The command is `grunt build`. It compresses your images, compiles your less, uglifies your JavaScript and CSS's, etc, etc.

You can provide the environment as an option: `grunt build:development` this will build with the development.js constants.

Default environment: production.

The build tasks creates a .zip file, by default it is named appname-SNAPSHOT.zip. You can also build a release version like so:
`grunt build:development:production` it will then create a .zip which includes the app's version: appname-0.0.0.zip.

Default compressTarget: development.

## Serve

To start synergysing your project and perform maximum value transference to achieve maximum value output ... run: `grunt serve`.

This will start your project on port 9000 and opens a browser to the main page.

When ever you change a file your browser automatically refreshes. Based on the actions it needs to perform it can take a little longer. For instance compiling less takes more time than changing some text.

If you want to serve the 'compiled' package run: `grunt serve:dist`. This will run `grunt build:development` behind the scenes. This is handy if you want to double check your application before a release.

Default environment: development.

## Testing

Running `grunt test` will run the unit tests with karma and protractor, and creates
code coverage files in `coverage/`.

If you only want to run the unit tests via karma run: `grunt test:unit`.

If you only want to run end-to-end tests (e2e) run: `grunt test:e2e`.

Default environment: development.

## Karma (continuous testing)
Running `karma:dev` starts up a karma server which whenever a file
changes runs the tests again. This also recalculates the code coverage.
 
 
Running 'karma:debug' starts a karma server in debug mode so you can
set breakpoints. It also reruns the tests whenever a file changes,
but does not produces a coverage file. The debug server runs on:
http://localhost:9002/debug.html. To set breakpoints open your browsers
dev tools set some some breakpoints and refresh the page to rerun the
tests.
 
 
For both `karma:dev` and `karma:debug` to work you must also run a
`grunt serve` in another terminal tab.

## For developers using DWP

Grunt uses a lot of CPU when doing a `grunt serve` because it watches file-changes. 
To make sure grunt doesn't overheat your computer you could configure an interval. 
Official the interval is seated in [config/DEV_CONFIG.js](config/DEV_CONFIG.js) but you can replace the 
value by adding `watch_interval` in [config/DEV_CONFIG.USER.js](config/DEV_CONFIG.USER.js). The original values is `100`
but you recommend to replace it with `5000` (5 sec).
