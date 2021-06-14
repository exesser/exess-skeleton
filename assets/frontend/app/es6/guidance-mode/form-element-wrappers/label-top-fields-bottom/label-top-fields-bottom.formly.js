'use strict';

angular.module('digitalWorkplaceApp')
  .config(function(formlyConfigProvider) {

    formlyConfigProvider.setWrapper({
      name: 'label-top-fields-bottom-wrapper',
      templateUrl: 'es6/guidance-mode/form-element-wrappers/label-top-fields-bottom/label-top-fields-bottom.formly.html'
    });
  });
