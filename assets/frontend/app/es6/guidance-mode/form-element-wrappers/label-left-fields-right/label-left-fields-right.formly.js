'use strict';

angular.module('digitalWorkplaceApp')
  .config(function(formlyConfigProvider) {

    formlyConfigProvider.setWrapper({
      name: 'label-left-fields-right-wrapper',
      templateUrl: 'es6/guidance-mode/form-element-wrappers/label-left-fields-right/label-left-fields-right.formly.html'
    });
  });
