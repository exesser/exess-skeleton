'use strict';

describe('Component: gridlr', function () {
  // load the component's module
  beforeEach(module('digitalWorkplaceApp'));

  let $rootScope;
  let $compile;
  let element;
  let gridlrScope;

  const template = `<gridlr definition='definition'></gridlr>`;

  beforeEach(inject(function(_$rootScope_, _$compile_, $state) {
    $rootScope = _$rootScope_;
    $compile = _$compile_;

    mockHelpers.blockUIRouter($state);
  }));

  function compileComponent(gridJson) {
    gridlrScope = $rootScope.$new();
    gridlrScope.definition = gridJson;

    element = angular.element(template);
    element = $compile(element)(gridlrScope);

    $rootScope.$apply();
  }

  it('should compile to a grid with two columns which have rows which contain awesome-thing elements.', function() {
    compileComponent({
      "columns": [{
        "size": "1-4",
        "cssClasses": ['blue', 'red', 'yellow'],
        "rows": [{
          "size": "1-1",
          "type": "awesomeThing",
          "cssClasses": ['pink'],
          "options": {
            "title": "hello",
            "description": "world"
          }
        }]
      }, {
        "size": "3-4",
        "rows": [{
          "size": "1-3",
          "type": "awesomeThing",
          "options": {
            "title": "foo",
            "description": "bar"
          }
        }, {
          "size": "2-3",
          "type": "awesomeThing",
          "options": {
            "title": "ultimate",
            "description": "answer"
          }
        }]
      }],
      cssClasses: ['cool-grid']
    });

    const grid = element.find('.grid');

    expect(grid).not.toBe(undefined);
    expect(grid.length).toBe(1);
    expect(grid[0].className).toBe('grid cool-grid');

    const awesomeThings = element.find('awesome-thing');
    expect(awesomeThings.length).toBe(3);

    const firstAwesomeThing = awesomeThings[0];

    expect(firstAwesomeThing.getAttribute('title')).toBe('hello');
    expect(firstAwesomeThing.getAttribute('description')).toBe('world');
    expect(firstAwesomeThing.parentElement.className).toBe('row-1-1 pink');
    expect(firstAwesomeThing.parentElement.parentElement.className).toBe('col-1-4 blue red yellow');

    const secondAwesomeThing = awesomeThings[1];

    expect(secondAwesomeThing.getAttribute('title')).toBe('foo');
    expect(secondAwesomeThing.getAttribute('description')).toBe('bar');
    expect(secondAwesomeThing.parentElement.className).toBe('row-1-3');
    expect(secondAwesomeThing.parentElement.parentElement.className).toBe('col-3-4');

    const thirdAwesomeThing = awesomeThings[2];

    expect(thirdAwesomeThing.getAttribute('title')).toBe('ultimate');
    expect(thirdAwesomeThing.getAttribute('description')).toBe('answer');
    expect(thirdAwesomeThing.parentElement.className).toBe('row-2-3');
    expect(thirdAwesomeThing.parentElement.parentElement.className).toBe('col-3-4');
  });

  it('should re-render a grid when the definition changes.', function() {
    compileComponent({
      "columns": [{
        "size": "1-4",
        "rows": [{
          "size": "1-1",
          "type": "awesomeThing",
          "options": {
            "title": "hello",
            "description": "world"
          }
        }]
      }]
    });

    let grid = element.find('.grid');

    expect(grid).not.toBe(undefined);
    expect(grid.length).toBe(1);

    let awesomeThings = element.find('awesome-thing');
    expect(awesomeThings.length).toBe(1);

    let firstAwesomeThing = awesomeThings[0];

    expect(firstAwesomeThing.getAttribute('title')).toBe('hello');
    expect(firstAwesomeThing.getAttribute('description')).toBe('world');
    expect(firstAwesomeThing.parentElement.className).toBe('row-1-1');
    expect(firstAwesomeThing.parentElement.parentElement.className).toBe('col-1-4');

    gridlrScope.definition = {
      "columns": [{
        "size": "1-1",
        "rows": [{
          "size": "1-2",
          "type": "awesomeThing",
          "options": {
            "title": "foo",
            "description": "bar"
          }
        }]
      }]
    };

    $rootScope.$apply();

    grid = element.find('.grid');

    expect(grid).not.toBe(undefined);
    expect(grid.length).toBe(1);

    awesomeThings = element.find('awesome-thing');
    expect(awesomeThings.length).toBe(1);

    firstAwesomeThing = awesomeThings[0];

    expect(firstAwesomeThing.getAttribute('title')).toBe('foo');
    expect(firstAwesomeThing.getAttribute('description')).toBe('bar');
    expect(firstAwesomeThing.parentElement.className).toBe('row-1-2');
    expect(firstAwesomeThing.parentElement.parentElement.className).toBe('col-1-1');
  });

  it('should compile grids which contain subgrids.', function() {
    compileComponent({
      "columns": [{
        "size": "2-3",
        "rows": [{
          "size": "1-3",
          "grid": {
            columns: [{
              "size": "1-2",
              "rows": [{
                "size": "1-1",
                "type": "awesomeThing",
                "options": {
                  "title": "hello",
                  "description": "world"
                }
              }]
            }]
          }
        }]
      }]
    });

    const awesomeThings = element.find('awesome-thing');
    expect(awesomeThings.length).toBe(1);

    const firstAwesomeThing = awesomeThings[0];

    expect(firstAwesomeThing.getAttribute('title')).toBe('hello');
    expect(firstAwesomeThing.getAttribute('description')).toBe('world');
    expect(firstAwesomeThing.parentElement.className).toBe('row-1-1');
    expect(firstAwesomeThing.parentElement.parentElement.className).toBe('col-1-2');
    expect(firstAwesomeThing.parentElement.parentElement.parentElement.parentElement.parentElement.className).toBe('row-1-3');
    expect(firstAwesomeThing.parentElement.parentElement.parentElement.parentElement.parentElement.parentElement.className).toBe('col-2-3');
  });

  it('should convert Object options to JSON.', function() {
    const argument = {
      "awesomeKey": "awesomeValue"
    };

    compileComponent({
      "columns": [{
        "size": "1-4",
        "rows": [{
          "size": "1-1",
          "type": "optionsRenderer",
          "options": {
            "argument": argument
          }
        }]
      }]
    });

    const optionsRenderer = element.find("options-renderer");
    expect(optionsRenderer.length).toBe(1);
    expect(angular.fromJson(optionsRenderer.text())).toEqual(argument);
  });

  it('should convert Array options to JSON.', function() {
    const argument = ["awesomeElement1", "awesomeElement2"];

    compileComponent({
      "columns": [{
        "size": "1-4",
        "rows": [{
          "size": "1-1",
          "type": "optionsRenderer",
          "options": {
            "argument": argument
          }
        }]
      }]
    });

    const optionsRenderer = element.find("options-renderer");
    expect(optionsRenderer.length).toBe(1);
    expect(angular.fromJson(optionsRenderer.text())).toEqual(argument);
  });

  it('should know how to render multiple children.', function() {
    compileComponent({
      "columns": [{
        "size": "2-3",
        "rows": [{
          "size": "1-3",
          "cssClasses": ["hot", "pink"],
          "children": [{
              "type": "awesomeThing",
              "options": {
                "title": "hello",
                "description": "world"
              }
            }, {
              "type": "awesomeThing",
              "options": {
                "title": "Goodbye",
                "description": "Cruel world"
              }
            }
          ]
        }]
      }]
    });

    const awesomeThings = element.find('awesome-thing');
    expect(awesomeThings.length).toBe(2);

    const firstAwesomeThing = awesomeThings[0];

    expect(firstAwesomeThing.getAttribute('title')).toBe('hello');
    expect(firstAwesomeThing.getAttribute('description')).toBe('world');

    const secondAwesomeThing = awesomeThings[1];

    expect(secondAwesomeThing.getAttribute('title')).toBe('Goodbye');
    expect(secondAwesomeThing.getAttribute('description')).toBe('Cruel world');

    expect(secondAwesomeThing.parentElement === firstAwesomeThing.parentElement).toBe(true);

    const rowElement = firstAwesomeThing.parentElement;
    expect(rowElement.children.length).toBe(2);
    expect(rowElement.className).toBe('row-1-3 hot pink');

    const colElement = rowElement.parentElement;
    expect(colElement.children.length).toBe(1);
    expect(colElement.className).toBe('col-2-3');
  });

  it('should throw an error when a row is created with multiple modes property.', function() {
    expect(function() {
      compileComponent({
        "columns": [{
          "size": "1-4",
          "rows": [{
            "size": "1-1",
            "type": "awesomeThing",
            "grid": { "size": "something" }
          }]
        }]
      });
    }).toThrow(new Error("gridlr: A row can be in one of three modes but not two at once. Define either a 'grid', 'type' or 'children' property but not all at once."));
  });

  it('should throw an error when gridlr definition is invalid.', function() {
    expect(function() {
      // An empty object is fine, it just renders no grid at all.
      compileComponent({});
    }).not.toThrow(new Error("gridlr: invalid definition provided, cannot render grid, this is a configuration error."));

    expect(function() {
      compileComponent(null);
    }).toThrow(new Error("gridlr: invalid definition provided, cannot render grid, this is a configuration error."));

    expect(function() {
      compileComponent(undefined);
    }).toThrow(new Error("gridlr: invalid definition provided, cannot render grid, this is a configuration error."));
  });

  it('should destroy the scope of a rendered component when the element is destroyed', function() {
    compileComponent({
      "columns": [{
        "size": "1-4",
        "rows": [{
          "size": "1-1",
          "type": "awesomeThing",
          "options": {
            "title": "hello",
            "description": "world"
          }
        }]
      }]
    });

    const awesomeThing = element.find('awesome-thing');
    const awesomeThingScope = $(awesomeThing).scope();
    expect(awesomeThingScope.$$destroyed).toBe(false);

    //Remove the element and check that the scope is destroyed.
    awesomeThing.remove();
    expect(awesomeThingScope.$$destroyed).toBe(true);
  });

  it('should destroy the scope of the rendered things when the scope of the surrounding gridlr is destroyed', function() {
    compileComponent({
      "columns": [{
        "size": "1-4",
        "rows": [{
          "size": "1-1",
          "type": "awesomeThing",
          "options": {
            "title": "hello",
            "description": "world"
          }
        }]
      }]
    });

    const awesomeThing = element.find('awesome-thing');
    const awesomeThingScope = $(awesomeThing).scope();
    expect(awesomeThingScope.$$destroyed).toBe(false);

    expect(gridlrScope.$$destroyed).toBe(false);
    expect(gridlrScope).not.toEqual(awesomeThingScope);

    /*
       When we destroy the scope of the surrounding gridlr we expect the scope of the 'awesomeThing' also to be destroyed.
       This way the entire grid (and its contents) are torn down in a nice top-down manner.
       This is especially important in the case of nested gridlr definitions, because we had issues of inner gridlr directives staying alive and rerendering
       the inner grid even though the outer grid was already in the progress of rerendering.
     */
    gridlrScope.$destroy();
    expect(gridlrScope.$$destroyed).toBe(true);
    expect(awesomeThingScope.$$destroyed).toBe(true);
  });

  it('should support "inherited" scopes for the rendered components even though the "surrounding" scope is created prototypically', function() {
    const gridDefinition = {
      "columns": [{
        "size": "1-4",
        "rows": [{
          "size": "1-1",
          "type": "awesomeThing"
        }]
      }]
    };

    compileComponent(gridDefinition);

    const awesomeThing = element.find('awesome-thing');
    const awesomeThingScope = $(awesomeThing).scope();

    expect(gridlrScope.definition).toEqual(gridDefinition);
    expect(awesomeThingScope.gridlrController.definition).toBe(gridDefinition);
  });

  it('should support isolate scopes for the rendered components even though the "surrounding" scope is created prototypically', function() {
    const gridDefinition = {
      "columns": [{
        "size": "1-4",
        "rows": [{
          "size": "1-1",
          "type": "awesomeIsolateThing"
        }]
      }]
    };

    compileComponent(gridDefinition);

    const awesomeIsolateThing = element.find('awesome-isolate-thing');
    const awesomeIsolateThingScope = $(awesomeIsolateThing).scope();

    expect(gridlrScope.definition).toEqual(gridDefinition);
    expect(awesomeIsolateThingScope.definition).toBe(undefined);
  });
});

//Note: this is an example directive with an isolated scope to use in tests
angular.module('digitalWorkplaceApp')
  .directive('awesomeIsolateThing', function() {
    return {
      restrict: 'E',
      template: `
        <h4><span class="fa fa-gear"></span>{{awesomeIsolateThing.title}}</h4>
        <p>{{awesomeIsolateThing.description}}</p>
      `,
      controllerAs: 'awesomeIsolateThing',
      scope: {},
      bindToController: {
        title: '@',
        description: '@'
      },
      controller: _.noop
    };
  });

'use strict';

//Note: this is an example directive with an inherited scope to use in tests
angular.module('digitalWorkplaceApp')
  .directive('awesomeThing', function ($window) {
    return {
      restrict: 'E',
      template: `
        <h4><span class="fa fa-gear" ng-click="awesomeThing.click()"></span>{{awesomeThing.title}}</h4>
        <p>{{awesomeThing.description}}</p>
      `,
      controllerAs: 'awesomeThing',
      scope: true,
      bindToController: {
        title: '@',
        description: '@'
      },
      controller: function() {
        const awesomeThing = this;

        awesomeThing.click = function () {
          $window.alert(awesomeThing.title + '!');
        };
      }
    };
  });

'use strict';

//Note: this is an example directive to test gridlr
angular.module('digitalWorkplaceApp')
  .directive('optionsRenderer', function () {
    return {
      restrict: 'E',
      template: `
        <pre>{{optionsRendererController.argument}}</pre>
      `,
      controllerAs: 'optionsRendererController',
      scope: true,
      bindToController: {
        argument: '@'
      },
      controller: _.noop
    };
  });
