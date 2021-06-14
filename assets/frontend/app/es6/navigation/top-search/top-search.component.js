'use strict';

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.top-search component
 * @description
 * # top-search
 *
 * This component will display a search field, by adding data and hitting enter it will
 * navigate to a search dashboard with the field value as a query parameter.
 *
 * Example usage:
 * <top-search></top-search>
 *
 * Component of the digitalWorkplaceApp
 */
angular.module('digitalWorkplaceApp')
  .component('topSearch', {
    templateUrl: 'es6/navigation/top-search/top-search.component.html',
    controllerAs: 'topSearchController',
    controller: function ($state, $stateParams, $timeout, topSearchObserver) {
      const topSearchController = this;

      topSearchController.linkTo = null;
      topSearchController.params = null;

      // we add $timeout to wait until $stateParams.query is initialized
      $timeout(function () {
        topSearchController.query = _.get($stateParams, 'query', '');
      }, 200);

      topSearchObserver.registerSetTopSearchDataCallback(function (searchData) {
        topSearchController.linkTo = searchData.linkTo;
        topSearchController.params = searchData.params;
      });

      topSearchController.topSearchCanBeDisplayed = function () {
        return _.isEmpty(topSearchController.linkTo) === false;
      };

      topSearchController.onKeyUp = function (event) {
        const enterKeyCode = 13;
        if (event.keyCode === enterKeyCode && topSearchController.linkTo !== null && topSearchController.params !== null) {
          topSearchController.params.query = topSearchController.query;
          $state.go(topSearchController.linkTo, topSearchController.params);
        }
      };
    }
  });
