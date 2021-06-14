'use strict';

angular.module('digitalWorkplaceApp')
  .config(function (formlyConfigProvider) {

    formlyConfigProvider.setWrapper({
      name: 'header-top-fields-bottom-wrapper',
      templateUrl: 'es6/guidance-mode/form-element-wrappers/header-top-fields-bottom/header-top-fields-bottom.formly.html'
    });
  });
