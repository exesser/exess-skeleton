'use strict';

describe('Dashboard item: dashboard-button-group-item', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let scope;
  let element;

  let $rootScope;
  let $compile;
  let actionDatasource;
  let $q;

  beforeEach(inject(function (_$rootScope_, _$compile_, $state, _actionDatasource_, _$q_) {
    $rootScope = _$rootScope_;
    $compile = _$compile_;
    actionDatasource = _actionDatasource_;
    $q = _$q_;

    mockHelpers.blockUIRouter($state);
  }));

  function compile(template) {
    scope = $rootScope.$new();
    element = angular.element(template);
    element = $compile(element)(scope);

    $rootScope.$apply();
  }

  it('should compile down to a three buttons with labels and amounts.', function () {
    compile(`
      <dashboard-button-group-item
         buttons='[
           { "label": "Elec fix up", "amount": "3" },
           { "label": "Gas fix up", "amount": "2" },
           { "label": "Gas tk up", "amount": "1" }
         ]'>
      </dashboard-button-group-item>
    `);

    const buttons = element.find('a.button');

    expect(buttons.length).toBe(3);

    const elecFixUpElement = $(buttons[0]);
    const gasFixUpElement = $(buttons[1]);
    const gasTkUpElement = $(buttons[2]);

    expect(elecFixUpElement.find('span').text()).toBe('3');
    expect(elecFixUpElement.find('p').text()).toBe('Elec fix up');

    expect(gasFixUpElement.find('span').text()).toBe('2');
    expect(gasFixUpElement.find('p').text()).toBe('Gas fix up');

    expect(gasTkUpElement.find('span').text()).toBe('1');
    expect(gasTkUpElement.find('p').text()).toBe('Gas tk up');
  });

  it('should compile down to two buttons with labels and amounts.', function () {
    compile(`
      <dashboard-button-group-item
         buttons='[
           { "label": "Willy", "amount": "666" },
           { "label": "Nilly", "amount": "1337" }
         ]'>
      </dashboard-button-group-item>
    `);

    const buttons = element.find('a.button');

    expect(buttons.length).toBe(2);

    const willyElement = $(buttons[0]);
    const nillyElement = $(buttons[1]);

    expect(willyElement.find('span').text()).toBe('666');
    expect(willyElement.find('p').text()).toBe('Willy');

    expect(nillyElement.find('span').text()).toBe('1337');
    expect(nillyElement.find('p').text()).toBe('Nilly');
  });

  it('should call the back-end when the action occurs for when a button is clicked.', function () {
    // The result of an Action POST to the back-end is a command to execute for the front-end.
    const navigateCommand = {
      command: "navigate",
      arguments: {
        route: "dashboard",
        params: {
          mainMenuKey: "sales-marketing",
          dashboardId: "leads"
        }
      }
    };
    spyOn(actionDatasource, 'performAndHandle').and.callFake(mockHelpers.resolvedPromise($q, navigateCommand));

    compile(`
      <dashboard-button-group-item
        buttons='[
          {
            "label": "Elec fix up",
            "amount": "3",
            "action": {
              "id": "42",
              "recordId": "1337",
              "recordType": "elec"
            }
          }
        ]'>
      </dashboard-button-group-item>
    `);

    const buttons = element.find('a.button');

    expect(buttons.length).toBe(1);

    const elecFixUpElement = $(buttons[0]);
    elecFixUpElement.click();

    expect(actionDatasource.performAndHandle).toHaveBeenCalledTimes(1);
    expect(actionDatasource.performAndHandle).toHaveBeenCalledWith({
      id: "42",
      recordId: "1337",
      recordType: "elec"
    });
  });
});
