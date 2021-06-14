'use strict';

angular.module('digitalWorkplaceApp')
  .config(function (formlyConfigProvider) {

    formlyConfigProvider.setWrapper({
      name: 'checkable-icons-group-wrapper',
      templateUrl: 'es6/guidance-mode/form-element-wrappers/checkable-icons-group/checkable-icons-group.formly.html'
    });
  });
