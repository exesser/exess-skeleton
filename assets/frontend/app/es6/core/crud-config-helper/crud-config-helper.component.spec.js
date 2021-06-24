'use strict';

describe('Component: crudConfigHelper', function () {
  afterEach(function () {
    sessionStorage.clear();
  });

  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let $rootScope;
  let $httpBackend;
  let $compile;
  let $state;
  let element;
  let API_PATH;
  let LOG_HEADERS_KEYS;

  const template = `<crud-config-helper></crud-config-helper>`;

  beforeEach(inject(function (_$rootScope_, _$compile_, _$state_, _$httpBackend_, _API_PATH_, _LOG_HEADERS_KEYS_) {
    $rootScope = _$rootScope_;
    $state = _$state_;
    $compile = _$compile_;
    $httpBackend = _$httpBackend_;
    API_PATH = _API_PATH_;
    LOG_HEADERS_KEYS = _LOG_HEADERS_KEYS_;
  }));

  it('should create a dropdown with a default empty option, two selectable options, and an arrow down.', function () {
    compile();

    const options = element.find("select option");
    expect(options.size()).toBe(4);

    expect($(options[0]).text()).toBe('');

    expect($(options[1]).val()).toBe('string:ExEss\\Cms\\Entity\\Flow');
    expect($(options[1]).text()).toBe('ExEss\\Cms\\Entity\\Flow');

    expect($(options[2]).val()).toBe('string:ExEss\\Cms\\Entity\\FlowStep');
    expect($(options[2]).text()).toBe('ExEss\\Cms\\Entity\\FlowStep');

    expect($(options[3]).val()).toBe('string:ExEss\\Cms\\Entity\\FlowStepLink');
    expect($(options[3]).text()).toBe('ExEss\\Cms\\Entity\\FlowStepLink');

    expect(element.find("span.icon-arrow-down").length).toBe(1);
  });

  it('should take the records from session when available', function () {
    compile(false, true);

    const options = element.find("select option");
    expect(options.size()).toBe(4);

    expect($(options[0]).text()).toBe('');

    expect($(options[1]).val()).toBe('string:TestSession1');
    expect($(options[1]).text()).toBe('TestSession1');

    expect($(options[2]).val()).toBe('string:TestSession2');
    expect($(options[2]).text()).toBe('TestSession2');

    expect($(options[3]).val()).toBe('string:TestSession3');
    expect($(options[3]).text()).toBe('TestSession3');

    expect(element.find("span.icon-arrow-down").length).toBe(1);
  });

  it('should NOT take the records from session when is expired', function () {
    compile(true, true, _.now() - 96400000);

    const options = element.find("select option");
    expect(options.size()).toBe(4);

    expect($(options[0]).text()).toBe('');

    expect($(options[1]).val()).toBe('string:ExEss\\Cms\\Entity\\Flow');
    expect($(options[1]).text()).toBe('ExEss\\Cms\\Entity\\Flow');

    expect($(options[2]).val()).toBe('string:ExEss\\Cms\\Entity\\FlowStep');
    expect($(options[2]).text()).toBe('ExEss\\Cms\\Entity\\FlowStep');

    expect($(options[3]).val()).toBe('string:ExEss\\Cms\\Entity\\FlowStepLink');
    expect($(options[3]).text()).toBe('ExEss\\Cms\\Entity\\FlowStepLink');

    expect(element.find("span.icon-arrow-down").length).toBe(1);
  });


  it('should generate the id', function () {
    compile();

    const mainrecordOptions = element.find("select option");

    // select Flows
    $(mainrecordOptions[1]).prop('selected', true).change();
    $rootScope.$apply();

    const configHelperRecordFlow = element.find("crud-config-helper-record");
    expect(configHelperRecordFlow.size()).toBe(1);

    const selectsInRecord = configHelperRecordFlow.find("select");
    expect(selectsInRecord.size()).toBe(2);

    const relationSelect = $(selectsInRecord[0]);
    const fieldSelect = $(selectsInRecord[1]);

    let relationOptions = relationSelect.find("option");
    expect(relationOptions.size()).toBe(6);

    expect($(relationOptions[0]).text()).toBe('');

    expect($(relationOptions[1]).val()).toBe('string:action');
    expect($(relationOptions[1]).text()).toBe('[ExEss\\Cms\\Entity\\FlowAction] action');

    expect($(relationOptions[2]).val()).toBe('string:stepLinks');
    expect($(relationOptions[2]).text()).toBe('[ExEss\\Cms\\Entity\\FlowStepLink] stepLinks');

    expect($(relationOptions[3]).val()).toBe('string:securityGroups');
    expect($(relationOptions[3]).text()).toBe('[ExEss\\Cms\\Entity\\SecurityGroup] securityGroups');

    expect($(relationOptions[4]).val()).toBe('string:createdBy');
    expect($(relationOptions[4]).text()).toBe('[ExEss\\Cms\\Entity\\User] createdBy');

    expect($(relationOptions[5]).val()).toBe('string:modifiedUser');
    expect($(relationOptions[5]).text()).toBe('[ExEss\\Cms\\Entity\\User] modifiedUser');

    let fieldOptions = fieldSelect.find("option");
    expect(fieldOptions.size()).toBe(15);

    // select relation to FlowStepsLink
    $(relationOptions[2]).prop('selected', true).change();
    $rootScope.$apply();

    // main expect
    expectCompiledId('stepLinks[]', 2);

    const configHelperRecordFlowStepsLink = configHelperRecordFlow.find("crud-config-helper-record");
    expect(configHelperRecordFlowStepsLink.size()).toBe(1);

    const selectsInRecordFlowStepsLink = configHelperRecordFlowStepsLink.find("select");
    expect(selectsInRecordFlowStepsLink.size()).toBe(2);

    const relationSelectFlowSteps = $(selectsInRecordFlowStepsLink[0]);

    relationOptions = relationSelectFlowSteps.find("option");
    expect(relationOptions.size()).toBe(6);

    expect($(relationOptions[0]).text()).toBe('');

    expect($(relationOptions[1]).val()).toBe('string:flow');
    expect($(relationOptions[1]).text()).toBe('[ExEss\\Cms\\Entity\\Flow] flow');

    expect($(relationOptions[2]).val()).toBe('string:flowStep');
    expect($(relationOptions[2]).text()).toBe('[ExEss\\Cms\\Entity\\FlowStep] flowStep');

    expect($(relationOptions[3]).val()).toBe('string:securityGroups');
    expect($(relationOptions[3]).text()).toBe('[ExEss\\Cms\\Entity\\SecurityGroup] securityGroups');

    expect($(relationOptions[4]).val()).toBe('string:createdBy');
    expect($(relationOptions[4]).text()).toBe('[ExEss\\Cms\\Entity\\User] createdBy');

    expect($(relationOptions[5]).val()).toBe('string:modifiedUser');
    expect($(relationOptions[5]).text()).toBe('[ExEss\\Cms\\Entity\\User] modifiedUser');

    // select relation to FlowSteps
    $(relationOptions[2]).prop('selected', true).change();
    $rootScope.$apply();

    // main expect
    expectCompiledId('stepLinks[]|flowStep', 3);

    // add some filters
    const filtersButtons = configHelperRecordFlow.find("button.button");
    // two buttons, one on main FLW_Flows and one on FLW_FlowStepsLink
    expect(filtersButtons.size()).toBe(2);
    $(filtersButtons[1]).click();
    $rootScope.$apply();

    // fill the filter fields - ENUM
    let filterSelects = configHelperRecordFlowStepsLink.find("div.crud-record-filters select");
    expect(filterSelects.size()).toBe(1);

    relationOptions = $(filterSelects[0]).find("option");
    expect(relationOptions.size()).toBe(11);

    expect($(relationOptions[0]).text()).toBe('');
    expect($(relationOptions[10]).val()).toBe('string:type');
    expect($(relationOptions[10]).text()).toBe('type [enum]');

    $(relationOptions[10]).prop('selected', true).change();
    $rootScope.$apply();

    filterSelects = configHelperRecordFlowStepsLink.find("div.crud-record-filters select");
    expect(filterSelects.size()).toBe(2); // now we also have a select for value

    relationOptions = $(filterSelects[1]).find("option");
    expect(relationOptions.size()).toBe(2);

    expect($(relationOptions[1]).val()).toBe('string:DEFAULT');
    expect($(relationOptions[1]).text()).toBe('Default');

    $(relationOptions[1]).prop('selected', true).change();
    $rootScope.$apply();

    expectCompiledId("stepLinks[]|flowStep(type='DEFAULT')", 3);

    // add filter on FLW_FlowStepsLink
    $(filtersButtons[1]).click();
    $rootScope.$apply();

    // fill the filter fields - BOOL
    filterSelects = configHelperRecordFlowStepsLink.find("div.crud-record-filters select");
    expect(filterSelects.size()).toBe(3);

    relationOptions = $(filterSelects[2]).find("option");
    expect(relationOptions.size()).toBe(11);

    expect($(relationOptions[0]).text()).toBe('');

    expect($(relationOptions[5]).val()).toBe('string:isCard');
    expect($(relationOptions[5]).text()).toBe('isCard [boolean]');

    $(relationOptions[5]).prop('selected', true).change();
    $rootScope.$apply();

    filterSelects = configHelperRecordFlowStepsLink.find("div.crud-record-filters select");
    expect(filterSelects.size()).toBe(4); // now we also have a select for value

    relationOptions = $(filterSelects[3]).find("option");
    expect(relationOptions.size()).toBe(2);

    expect($(relationOptions[0]).val()).toBe('number:0');
    expect($(relationOptions[0]).text()).toBe('FALSE');

    expect($(relationOptions[1]).val()).toBe('number:1');
    expect($(relationOptions[1]).text()).toBe('TRUE');

    $(relationOptions[1]).prop('selected', true).change();
    $rootScope.$apply();

    // main expect
    expectCompiledId("stepLinks[]|flowStep(type='DEFAULT';isCard=1)", 3);

    // add another filter - VARCHAR
    $(filtersButtons[1]).click();
    $rootScope.$apply();

    filterSelects = configHelperRecordFlowStepsLink.find("div.crud-record-filters select");
    expect(filterSelects.size()).toBe(5); // now we also have a select for second field
    relationOptions = $(filterSelects[2]).find("option");
    expect(relationOptions.size()).toBe(10);

    expect($(relationOptions[7]).val()).toBe('string:label');
    expect($(relationOptions[7]).text()).toBe('label [string]');

    $(relationOptions[7]).prop('selected', true).change();
    $rootScope.$apply();

    let filterInput = configHelperRecordFlowStepsLink.find("div.crud-record-filters input");
    expect(filterInput.size()).toBe(2);

    $(filterInput[0]).val('LabelValue').change();
    $rootScope.$apply();

    // main expect
    expectCompiledId("stepLinks[]|flowStep(type='DEFAULT';label='LabelValue')", 3);

    // delete one filter
    const deleteFilter = configHelperRecordFlow.find("div.crud-record-filters a.action-delete");
    expect(deleteFilter.size()).toBe(3);
    $(deleteFilter[0]).click();
    $rootScope.$apply();

    // main expect
    expectCompiledId("stepLinks[]|flowStep(label='LabelValue')", 3);

    // select a field on last record
    const configHelperRecordFlow_2 = $(element.find("crud-config-helper-record")[2]);

    const selectsInRecord_2 = configHelperRecordFlow_2.find("select");
    expect(selectsInRecord_2.size()).toBe(2);

    fieldOptions = $(selectsInRecord_2[1]).find("option");
    expect(fieldOptions.size()).toBe(11);

    $(fieldOptions[2]).prop('selected', true).change();
    $rootScope.$apply();

    // main expect
    expectCompiledId("stepLinks[]|flowStep(label='LabelValue')|dateModified", 3);
  });

  function expectCompiledId(expectValue, count) {
    const compiledIdPlaceholder = element.find("div.alert.is-success");
    expect(compiledIdPlaceholder.size()).toBe(count);
    expect(_.trim($(compiledIdPlaceholder[0]).text())).toEqual(expectValue);

    if (count > 1) {
      expect(_.trim($(compiledIdPlaceholder[1]).text())).toEqual('');
    }
  }

  function compile(api = true, session = false, time = _.now()) {
    if (session) {
      sessionStorage.setItem("CONFIG_HELPER_RECORDS", angular.toJson({
        "cacheTime": time,
        "records": [
          {
            recordName: "TestSession1",
            relations: [],
            fields: []
          },
          {
            recordName: "TestSession2",
            relations: [],
            fields: []
          },
          {
            recordName: "TestSession3",
            relations: [],
            fields: []
          }
        ]
      }));
    }

    if (api) {
      const mockResponse = [
        {
          "recordName": "ExEss\\Cms\\Entity\\Flow",
          "relations": [
            {
              "name": "action",
              "multiRelation": false,
              "record": "ExEss\\Cms\\Entity\\FlowAction"
            },
            {
              "name": "stepLinks",
              "multiRelation": true,
              "record": "ExEss\\Cms\\Entity\\FlowStepLink"
            },
            {
              "name": "createdBy",
              "multiRelation": false,
              "record": "ExEss\\Cms\\Entity\\User"
            },
            {
              "name": "modifiedUser",
              "multiRelation": false,
              "record": "ExEss\\Cms\\Entity\\User"
            },
            {
              "name": "securityGroups",
              "multiRelation": true,
              "record": "ExEss\\Cms\\Entity\\SecurityGroup"
            }
          ],
          "fields": [
            {
              "name": "key",
              "type": "string"
            },
            {
              "name": "type",
              "type": "enum",
              "enumValues": [
                {
                  "key": "STANDARD",
                  "value": "Standard"
                },
                {
                  "key": "Dashvoard",
                  "value": "Dashboard"
                },
                {
                  "key": "DEFAULT",
                  "value": "Default"
                },
                {
                  "key": "FORCECREATE",
                  "value": "Force create"
                }
              ]
            },
            {
              "name": "baseObject",
              "type": "string"
            },
            {
              "name": "loadingMessage",
              "type": "string"
            },
            {
              "name": "errorMessage",
              "type": "text"
            },
            {
              "name": "external",
              "type": "boolean"
            },
            {
              "name": "label",
              "type": "string"
            },
            {
              "name": "useApiLabel",
              "type": "boolean"
            },
            {
              "name": "isConfig",
              "type": "boolean"
            },
            {
              "name": "id",
              "type": "string"
            },
            {
              "name": "dateEntered",
              "type": "datetime"
            },
            {
              "name": "dateModified",
              "type": "datetime"
            },
            {
              "name": "name",
              "type": "string"
            },
            {
              "name": "description",
              "type": "text"
            }
          ]
        },
        {
          "recordName": "ExEss\\Cms\\Entity\\FlowStepLink",
          "relations": [
            {
              "name": "flow",
              "multiRelation": false,
              "record": "ExEss\\Cms\\Entity\\Flow"
            },
            {
              "name": "flowStep",
              "multiRelation": false,
              "record": "ExEss\\Cms\\Entity\\FlowStep"
            },
            {
              "name": "createdBy",
              "multiRelation": false,
              "record": "ExEss\\Cms\\Entity\\User"
            },
            {
              "name": "modifiedUser",
              "multiRelation": false,
              "record": "ExEss\\Cms\\Entity\\User"
            },
            {
              "name": "securityGroups",
              "multiRelation": true,
              "record": "ExEss\\Cms\\Entity\\SecurityGroup"
            }
          ],
          "fields": [
            {
              "name": "order",
              "type": "integer"
            },
            {
              "name": "id",
              "type": "string"
            },
            {
              "name": "dateEntered",
              "type": "datetime"
            },
            {
              "name": "dateModified",
              "type": "datetime"
            },
            {
              "name": "name",
              "type": "string"
            },
            {
              "name": "description",
              "type": "text"
            }
          ]
        },
        {
          "recordName": "ExEss\\Cms\\Entity\\FlowStep",
          "relations": [
            {
              "name": "fields",
              "multiRelation": true,
              "record": "ExEss\\Cms\\Entity\\FlowField"
            },
            {
              "name": "properties",
              "multiRelation": true,
              "record": "ExEss\\Cms\\Entity\\Property"
            },
            {
              "name": "stepLinks",
              "multiRelation": true,
              "record": "ExEss\\Cms\\Entity\\FlowStepLink"
            },
            {
              "name": "gridTemplate",
              "multiRelation": false,
              "record": "ExEss\\Cms\\Entity\\GridTemplate"
            },
            {
              "name": "createdBy",
              "multiRelation": false,
              "record": "ExEss\\Cms\\Entity\\User"
            },
            {
              "name": "modifiedUser",
              "multiRelation": false,
              "record": "ExEss\\Cms\\Entity\\User"
            }
          ],
          "fields": [
            {
              "name": "type",
              "type": "enum",
              "enumValues": [
                {
                  "key": "DEFAULT",
                  "value": "Default"
                }
              ]
            },
            {
              "name": "jsonFields",
              "type": "text"
            },
            {
              "name": "key",
              "type": "string"
            },
            {
              "name": "isCard",
              "type": "boolean"
            },
            {
              "name": "label",
              "type": "string"
            },
            {
              "name": "id",
              "type": "string"
            },
            {
              "name": "dateEntered",
              "type": "datetime"
            },
            {
              "name": "dateModified",
              "type": "datetime"
            },
            {
              "name": "name",
              "type": "string"
            },
            {
              "name": "description",
              "type": "text"
            }
          ]
        }
      ];

      $httpBackend.expectGET(API_PATH + 'crud/record/information', function (headers) {
        return headers[LOG_HEADERS_KEYS.DESCRIPTION] === 'Crud records information | URL: /Api/crud/record/information';
      }).respond({data: mockResponse});
    }

    const scope = $rootScope.$new(true);
    element = angular.element(template);
    element = $compile(element)(scope);
    $rootScope.$apply();

    if (api) {
      $httpBackend.flush();
      spyOn($state, 'go');
    }
  }
});
