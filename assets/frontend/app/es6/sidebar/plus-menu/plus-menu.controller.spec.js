'use strict';

describe('Controller: PlusMenuController', function () {

  // load the controller's module
  beforeEach(module('digitalWorkplaceApp'));

  let $controller;
  let $state;
  let $rootScope;
  let $scope;

  let plusMenuController;
  let topActionState;
  let plusMenuObserver;
  let plusMenu;
  let sidebarObserver;

  beforeEach(inject(function (_$controller_, _$q_, _$rootScope_, _$state_, _plusMenuObserver_, _topActionState_, _sidebarObserver_) {
    $controller = _$controller_;
    $state = _$state_;
    $rootScope = _$rootScope_;
    topActionState = _topActionState_;
    plusMenuObserver = _plusMenuObserver_;
    sidebarObserver = _sidebarObserver_;

    plusMenu = {
      "buttonGroups": [
        {
          "label": "Create Lead Test",
          "sort_order": "10",
          "buttons": [
            {
              "enabled": true,
              "label": "Business",
              "icon": "icon-werkbakken",
              "sort_order": "1",
              "action": {
                "id": "navigate_to_create_lead_guidance"
              }
            },
            {
              "enabled": true,
              "label": "Household",
              "icon": "icon-werkbakken",
              "sort_order": "2",
              "action": {
                "id": "navigate_to_create_lead_guidance"
              }
            }
          ]
        }
      ],
      "buttons": [
        {
          "enabled": true,
          "label": "View quote",
          "icon": "",
          "sort_order": "2",
          "action": {
            "id": "Navigate_to_view_quote_guidance"
          }
        },
        {
          "enabled": true,
          "label": "Update quote",
          "icon": "",
          "sort_order": "1",
          "action": {
            "id": "Navigate_to_update_quote_guidance"
          }
        }
      ]
    };

    spyOn($state, 'transitionTo');
    spyOn(plusMenuObserver, 'registerSetPlusMenuDataCallback');
    spyOn(topActionState, 'setPlusMenuCanBeOpened');

    $scope = $rootScope.$new();
    plusMenuController = $controller('PlusMenuController', { $scope });
    $rootScope.$apply();
  }));

  it('should correctly configure the controller', function () {
    expect(plusMenuController.plusMenu).toEqual({});
    expect(topActionState.setPlusMenuCanBeOpened).toHaveBeenCalledTimes(1);
    expect(topActionState.setPlusMenuCanBeOpened).toHaveBeenCalledWith(false);
  });

  it('should add items when plusMenuObserver.registerSetPlusMenuDataCallback is called', function () {
    expect(plusMenuObserver.registerSetPlusMenuDataCallback).toHaveBeenCalledTimes(1);

    var registerSetPlusMenuDataCallback = plusMenuObserver.registerSetPlusMenuDataCallback.calls.argsFor(0)[0];

    registerSetPlusMenuDataCallback(plusMenu);

    $rootScope.$apply();

    expect(plusMenuController.plusMenu).toEqual(plusMenu);
    expect(topActionState.setPlusMenuCanBeOpened).toHaveBeenCalledTimes(2);
    expect(topActionState.setPlusMenuCanBeOpened).toHaveBeenCalledWith(true);
  });

  describe('when the scope is destroyed', function () {
    beforeEach(function () {
      spyOn(sidebarObserver, 'closeAllSidebarElements');
      //Destroy the scope and trigger the deletion callback.
      $scope.$destroy();
    });

    it('should signal the sidebar observer to close all sidebar elements', function () {
      expect(sidebarObserver.closeAllSidebarElements).toHaveBeenCalledTimes(1);
    });

    it('should call setFiltersCanBeOpened on the topActionState with false', function () {
      //The first invocation happens when first opening the plus menu.
      expect(topActionState.setPlusMenuCanBeOpened).toHaveBeenCalledTimes(2);
      expect(topActionState.setPlusMenuCanBeOpened.calls.mostRecent().args[0]).toBe(false);
    });
  });
});
