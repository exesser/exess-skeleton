'use strict';

(function (jquery) {
  angular.module('digitalWorkplaceApp')
    .factory('wysiwygFontSizeButtonFactory', function (taRegisterTool) {
      return { setFontSize };

      function setFontSize(buttonKey) {
        taRegisterTool(buttonKey, {
          tooltiptext: 'Change font size',
          display: '<div class="btn-group dropdown font-size"><button type="button" class="btn" ng-click="toggle($event)" ng-disabled="isDisabled()"><i class="fa fa-font"></i><i class="fa fa-caret-down"></i></button><ul><li ng-repeat="o in options"><a style="font-size:{{o.css}};" ng-click="action($event, o.value)">{{o.name}}</a></li></ul></div>',
          action: function (event, size) {
            if (event.originalEvent instanceof Event) {
              event.stopPropagation();
              event.preventDefault();
              jquery('body').trigger('click');
              jquery(event.currentTarget).closest('ul').hide();
            }

            return this.$editor().wrapSelection('fontSize', parseInt(size));
          },
          toggle: function (event) {
            jquery(event.currentTarget).next('ul').toggle();
          },
          options: [
            { name: '1 (8pt)', css: 'xx-small', value: 1 },
            { name: '2 (10pt)', css: 'x-small', value: 2 },
            { name: '3 (12pt)', css: 'small', value: 3 },
            { name: '4 (14pt)', css: 'medium', value: 4 },
            { name: '5 (18pt)', css: 'large', value: 5 },
            { name: '6 (24pt)', css: 'x-large', value: 6 },
            { name: '7 (36pt)', css: 'xx-large', value: 7 }
          ]
        });
      }
    });
})(window.$); //eslint-disable-line angular/window-service
