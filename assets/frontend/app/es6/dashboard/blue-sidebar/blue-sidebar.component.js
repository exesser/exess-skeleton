'use strict';

/**
 * @ngdoc Component
 * @name digitalWorkplaceApp.components:blueSidebar component
 * @description
 * # blueSidebar
 *
 * The blueSidebar renders a blue bar that is traditionally displayed
 * on the left side of the screen. It contains the detail information
 * about the current record.
 *
 * For example:
 *
 * <blue-sidebar recordType="account" id="1337"></blue-sidebar>
 *
 * Component of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .component('blueSidebar', {
    templateUrl: 'es6/dashboard/blue-sidebar/blue-sidebar.component.html',
    controllerAs: 'blueSideBarController',
    bindings: {
      'recordType': '@',
      'id': '@'
    },
    controller: function (blueSidebarDatasource, $state, $stateParams) {
      const blueSideBarController = this;

      const params = {
        recordType: blueSideBarController.recordType,
        id: blueSideBarController.id
      };

      blueSidebarDatasource.get(params).then(function (data) {
        blueSideBarController.data = data;
      });

      blueSideBarController.linkClicked = function (link) {
        $state.go(link.linkTo, link.params);
      };

      blueSideBarController.isActive = function (link) {
        if ($state.current.name === 'dashboard') {
          return $stateParams.dashboardId === link.params.dashboardId;
        } else {
          return $stateParams.focusModeId === link.params.focusModeId;
        }
      };

      blueSideBarController.cssClasses = function () {
        if (_.isUndefined(blueSideBarController.data)) {
          return '';
        }

        let mapClasses = {
          'B2C': 'icon-particulier',
          'B2B': 'icon-bedrijf',
          'FUTURE CUSTOMER': 'status-plus',
          'OLD CUSTOMER': 'status-old',
          'PROSPECT': 'status-star'
        };

        let classes = ['customer__status'];
        classes.push(_.get(mapClasses, blueSideBarController.data.record_type, ''));
        classes.push(_.get(mapClasses, blueSideBarController.data.type, ''));

        return _.join(_.compact(_.map(classes, _.trim)), " ");
      };
    }
  });
