'use strict';

(function (jquery) {
  /**
   * @ngdoc component
   * @name digitalWorkplaceApp.gridlr component
   * @description
   *
   * Takes an abstract representation of a grid represented in JSON:
   *
   * {
   *   "cssClasses": ["black"], <--- 'cssClasses' allows you to define extra CSS classes.
   *   "columns": [{
   *     "size": "1-2",
   *     "cssClasses": ["red", "blue", "yellow"],
   *     "rows": [{
   *       "cssClasses": ["green"],
   *       "size": "1-1",
   *       "type": "leadInfo",
   *       "options": { <----------- 'options' define element attributes.
   *         "title": "Lead Info",
   *         "recordId": "12"
   *       }
   *     }]
   *   }, {
   *     "size": "1-2",
   *     "rows": [{
   *       "size": "1-2",
   *       "type": "oppInfo"
   *     }, {
   *       "size": "1-2",
   *       "type": "quote-info"
   *     }]
   *   }]
   * }
   *
   * When this component is used on a div:
   *
   * <gridlr definition="dashboardController.grid"></gridlr>
   *
   * It gets turned into the following template:
   *
   * <div class="grid black">
   *   <div class="col-1-2 red blue yellow">
   *     <div class="row-1-1 green">
   *       <lead-info title="Lead Info" record-id="12"></lead-info>
   *     </div>
   *   </div>
   *   <div class="col-1-2">
   *     <div class="row-1-2">
   *       <opp-info></opp-info>
   *     </div>
   *     <div class="row-1-2">
   *       <quote-info></quote-info>
   *     </div>
   *   </div>
   * </div>
   *
   * Which represents the following page:
   *
   *   -----------------------------
   *   |           |               |
   *   |           |    oppInfo    |
   *   |           |               |
   *   | leadInfo  |---------------|
   *   |           |               |
   *   |           |   quoteInfo   |
   *   |           |               |
   *   -----------------------------
   *
   * Gridlr also supports nested grids. You do this by giving another
   * grid definition instead of a 'type' in the definition of a row:
   *
   * {
   *   "columns": [{
   *     "size": "1-2",
   *     "rows": [{
   *       "size": "1-2",
   *       "grid": { <------------ 'grid' defines a sub grid definition.
   *         "cssClasses": ["has-default-margins"]
   *         "columns": [{
   *           "size": "1-2",
   *           "rows": [{
   *             "size": "1-1",
   *             "type": "leadInfo"
   *           }]
   *         }]
   *       }
   *     }]
   *   }]
   * }
   *
   * Gridlr also supports multiple children inside one row. You can do
   * this by passing in a 'children' array.
   *
   * {
   *   "columns": [{
   *     "size": "1-2",
   *     "rows": [{
   *       "size": "1-2",
   *       "children": [{ <----------- 'children' define multiple siblings.
   *         "type": "leadInfo",
   *         "options": {
   *           "title": "Lead Info",
   *           "recordId": "12"
   *         }
   *       }, {
   *         "type": "oppInfo",
   *         "options": {
   *           "title": "Opportunity Info",
   *           "recordId": "14"
   *         }
   *       }]
   *     }]
   *   }]
   * }
   *
   * You must use either define a subgrid, children or a single element
   * inside of a row definition. You cannot use the row in multiple
   * modes at the same time. If you attempt to do this you will get
   * an error.
   *
   * Component in the digitalWorkplaceApp.
   */
  angular.module('digitalWorkplaceApp')
    .component('gridlr', {
      template: '<div class="grid"></div>',
      bindings: {
        definition: '<'
      },
      controllerAs: 'gridlrController',
      controller: function($scope, $element, $compile) {
        const gridlrController = this;

        const gridElement = $element.find('.grid')[0];
        const jqueryGridElement = jquery(gridElement);

        /**
         * Callback function to be executed when one of the bindings change.
         * There is only one 'binding' in this component so the change will always be definition.
         *
         * The function is also called however when the reference changes but the object
         * stays the same. This caused multiple instantiations of the same components in nested grids,
         * which were all destroyed again except for the last 'run'.
         *
         * Instead, we now do an equality comparison to decide whether or not to rerender the definition.
         * This has one disadvantage however, in the case where you load in the exact same same grid, nothing will happen.
         * The only scenario where this seems likely however is in a guidance when you click on the same step again where
         * you already were. But in that particular case it makes sense that nothing happens since you are already on the
         * step you wish to 'navigate to'.
         *
         * In all other cases there is still the 'reloadPage' command which will rerender because the entire page
         * (including the gridlr component) is reloaded.
         *
         * @param changes object containing the changed bindings
         */
        gridlrController.$onChanges = function(changes) {
          if (_.isEqual(changes.definition.currentValue, changes.definition.previousValue) === false) {
            jqueryGridElement.empty(); // Clear the old grid.

            /*
              Throw an error when grid is either undefined or null now,
              otherwise it would cause vague errors further down the line.
              Now it is clear that the error is due to configuration.

              Note: that an empty definition object: {} is allowed.
            */
            if (_.isUndefined(gridlrController.definition) || _.isNull(gridlrController.definition)) {
              throw new Error('gridlr: invalid definition provided, cannot render grid, this is a configuration error.');
            } else {
              addCSSClassesOn(gridElement, gridlrController.definition.cssClasses);
            }

            _.forEach(gridlrController.definition.columns, function(column) {
              appendColumnElement(gridElement, column);
            });
          }
        };

        function appendColumnElement(gridElement, column) {
          const columnElement = createElement('div');
          columnElement.className = `col-${column.size}`;

          addCSSClassesOn(columnElement, column.cssClasses);

          gridElement.appendChild(columnElement);

          _.forEach(column.rows, function(row) {
            appendRowElement(columnElement, row);
          });
        }

        function appendRowElement(columnElement, row) {
          const typeExists = _.isUndefined(row.type) === false;
          const gridExists = _.isUndefined(row.grid) === false;
          const childrenExists = _.isUndefined(row.children) === false;

          const modesActive = _([typeExists, gridExists, childrenExists])
                            .filter((attrExists) => attrExists === true)
                            .size();

          if (modesActive !== 1) {
            throw new Error("gridlr: A row can be in one of three modes but not two at once. Define either a 'grid', 'type' or 'children' property but not all at once.");
          }

          const rowElement = createElement('div');

          rowElement.className = `row-${row.size}`;

          addCSSClassesOn(rowElement, row.cssClasses);

          columnElement.appendChild(rowElement);

          // Render another gridlr when the grid property is defined.
          if (gridExists) {
            appendSubGridElement(row.grid, rowElement);
          } else if (childrenExists) { // Render children if the array is defined
            _.forEach(row.children, function(component) {
              appendComponentElement(rowElement, component);
            });
          } else {
            const component = { type: row.type, options: row.options };
            appendComponentElement(rowElement, component);
          }
        }

        function appendComponentElement(rowElement, { type, options }) {
          const componentElement = createElement(_.kebabCase(type));

          _.forOwn(options, function(value, key) {
            const attributeValue = _.isObject(value) ? angular.toJson(value) : value;
            componentElement.setAttribute(_.kebabCase(key), attributeValue);
          });

          rowElement.appendChild(componentElement);

          compile(componentElement);
        }

        function appendSubGridElement(grid, rowElement) {
          const subGridElement = createElement('gridlr');

          subGridElement.setAttribute('definition', 'grid');

          rowElement.appendChild(subGridElement);

          compile(subGridElement, { grid });
        }

        // Quick function so the eslint-disable doesn't have to appear multiple times.
        function createElement(tagName) {
          return document.createElement(tagName); //eslint-disable-line angular/document-service
        }

        // Adds the cssClasses (array of CSS classes as strings) to the element.
        function addCSSClassesOn(element, cssClasses) {
          if (_.isEmpty(cssClasses) === false) {
            element.className += ' ' + cssClasses.join(' ');
          }
        }

        /**
         * Compiles an element with a new scope, optionally containing the given additional properties.
         *
         * The scope prototypically inherits from the gridlr scope.
         * This way the entire grid (and its contents) are torn down in a top-down manner.
         * This is especially important in the case of nested gridlr definitions, because we had issues of inner
         * gridlr directives staying alive and rerendering the inner grid even though the outer grid was
         * already in the progress of rerendering (which means that the outer grid should have been
         * destroyed at that point, since it was part of the inner grid).
         *
         * The 'gotcha' here is that we are not actually the scope of the directive/component here, but we are creating
         * the 'containing scope' of the directive/component. Gridlr serves as a dynamic replacement of
         * manually created templates that contain directives / components. In this scenario the 'containing' scopes
         * also inherit prototypically, and it is up to the component or directive to decide if it wants to render
         * in an inherited or isolate scope.
         *
         * This is still possible with our current implementation of Gridlr. In a directive you can try this out by setting
         * scope to either 'true' or '{}'. The difference is that the directive / component's 'containing' scope is now destroyed
         * when the scope of Gridlr is destroyed.
         *
         * However, just because you CAN does not mean that you SHOULD use inherited scopes. If possible, using isolate scopes
         * in components (which is the only option there) or directives is always better because it makes the component / directive
         * easier to reason about and keeps some isolation between the components. The thing is however that in a normal Angular
         * application where you create the template in the html files manually this decision is up to the component or directive.
         * In a Gridlr-rendered template it should work in the same way.
         *
         * @param element The element to compile
         * @param additionalProperties (optional) additional properties to merge into the scope.
         */
        function compile(element, additionalProperties) {
          const scope = $scope.$new();
          _.merge(scope, additionalProperties);
          $compile(element)(scope);

          //When the element is destroyed, destroy the scope as well.
          angular.element(element).on("$destroy", function() {
            scope.$destroy();
          });
        }
      }
    });
})(window.$); //eslint-disable-line angular/window-service
