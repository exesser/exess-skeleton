'use strict';

describe('Form type: image', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let scope;
  let element;

  let $rootScope;
  let $compile;
  let actionDatasource;

  const template = '<formly-form model="model" fields="fields"></formly-form>';

  beforeEach(inject(function (_$rootScope_, _$compile_, $state, GuidanceFormObserver,
                              ValidationObserver, SuggestionsObserver, _actionDatasource_) {
      $rootScope = _$rootScope_;
      $compile = _$compile_;
      actionDatasource = _actionDatasource_;
      mockHelpers.blockUIRouter($state);

      scope = $rootScope.$new();

      scope.model = {
        image: "",
        name: "Ken Block",
        number: "43"
      };

      scope.fields = [
        {
          id: "image",
          key: "image",
          type: 'image',
          templateOptions: {
            text: "The name is: {%name%}.",
            action: "action-id",
            actionText: "GO {%number%}!",
            actionModel: {
              driver_name: "name"
            },
            actionParams: {
              driver_number: "number"
            },
            imageUrl: "http://crm.be/image-path-{%number%}.jpeg"
          }
        }
      ];

      const guidanceFormObserver = new GuidanceFormObserver();
      const validationObserver = new ValidationObserver();
      const suggestionsObserver = new SuggestionsObserver();
      spyOn(guidanceFormObserver, 'getFullModel').and.returnValue(scope.model);
      spyOn(actionDatasource, 'performAndHandle');

      const guidanceFormObserverAccessorElement = mockHelpers.createGuidanceFormObserverAccessor({
        $compile,
        $rootScope,
        guidanceFormObserver,
        validationObserver,
        suggestionsObserver
      });

      element = angular.element(template);
      element = $compile(element)(scope);

      guidanceFormObserverAccessorElement.append(element);

      $rootScope.$apply();
    }
  ))
  ;

  it('should display a image, with a text and button', function () {
    const blockquote = $(element.find('blockquote')[0]);
    expect(blockquote.text()).toContain('The name is: Ken Block.');

    const link = $(element.find('a')[0]);
    expect(link.text()).toContain('GO 43!');

    const container = $(element.find('div.m-banner')[0]);
    expect(container.css("background-image")).toBe('url(http://crm.be/image-path-43.jpeg)');
  });

  it('should call actionDatasource.performAndHandle when we click on button', function () {
    expect(actionDatasource.performAndHandle).not.toHaveBeenCalled();

    element.find('a').click();
    $rootScope.$apply();

    expect(actionDatasource.performAndHandle).toHaveBeenCalledTimes(1);
    expect(actionDatasource.performAndHandle).toHaveBeenCalledWith({
      id: 'action-id',
      params: { driver_number: '43', model: { driver_name: 'Ken Block' } }
    });
  });
});
