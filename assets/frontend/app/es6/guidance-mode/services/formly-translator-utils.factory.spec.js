'use strict';

describe('Factory: formlyTranslatorUtils', function() {
  // load the controller's module
  beforeEach(module('digitalWorkplaceApp'));

  let formlyTranslatorUtils;

  beforeEach(inject(function(_formlyTranslatorUtils_) {
    formlyTranslatorUtils = _formlyTranslatorUtils_;
  }));

  describe('translateField', function() {
    it('should translate a basic field', function() {
      const field = {
        id: 'last-name',
        type: 'varchar'
      };

      const expected = {
        id: 'last-name',
        key: 'last-name',
        type: 'varchar',
        templateOptions: {}
      };

      const result = formlyTranslatorUtils.translateField(field, {});
      expect(result).toEqual(expected);
    });

    it('should add any validators to the templateOptions', function() {
      const field = {
        id: 'last-name',
        type: 'varchar',
        validation: {
          required: true
        }
      };

      const expected = {
        id: 'last-name',
        key: 'last-name',
        type: 'varchar',
        templateOptions: {
          required: true
        }
      };

      const result = formlyTranslatorUtils.translateField(field, {});
      expect(result).toEqual(expected);
    });

    it('should add all "other" properties to the templateOptions', function() {
      const field = {
        id: 'last-name',
        type: 'varchar',
        blue: true
      };

      const expected = {
        id: 'last-name',
        key: 'last-name',
        type: 'varchar',
        templateOptions: {
          blue: true
        }
      };

      const result = formlyTranslatorUtils.translateField(field, {});
      expect(result).toEqual(expected);
    });

    it('should add "unparsedFieldExpression" when "fieldExpression" exist', function() {
      const field = {
        id: 'last-name',
        type: 'varchar',
        fieldExpression: "true || false"
      };

      const expected = {
        id: 'last-name',
        key: 'last-name',
        type: 'varchar',
        templateOptions: {
          unparsedFieldExpression: "true || false"
        }
      };

      const result = formlyTranslatorUtils.translateField(field, {});
      expect(result).toEqual(expected);
    });

    it('should add "multipleSelect" when "multiple" exist', function() {
      const field = {
        id: 'last-name',
        type: 'varchar',
        multiple: "true || false"
      };

      const expected = {
        id: 'last-name',
        key: 'last-name',
        type: 'varchar',
        templateOptions: {
          multipleSelect: "true || false"
        }
      };

      const result = formlyTranslatorUtils.translateField(field, {});
      expect(result).toEqual(expected);
    });

    it('should add "expressionProperties" when they exist', function() {
      const field = {
        id: 'last-name',
        type: 'varchar',
        expressionProperties: "true || false"
      };

      const expected = {
        id: 'last-name',
        key: 'last-name',
        type: 'varchar',
        expressionProperties: "true || false",
        templateOptions: {}
      };

      const result = formlyTranslatorUtils.translateField(field, {});
      expect(result).toEqual(expected);
    });

    it('should add "className" when they exist', function() {
      const field = {
        id: 'last-name',
        type: 'varchar',
        className: "inline-block"
      };

      const expected = {
        id: 'last-name',
        key: 'last-name',
        type: 'varchar',
        className: "inline-block",
        templateOptions: {}
      };

      const result = formlyTranslatorUtils.translateField(field, {});
      expect(result).toEqual(expected);
    });

    it('should apply a wrapper when the "orientation" key is known', function() {
      const field = {
        id: 'first-name',
        type: 'varchar',
        hideExpression: 'model.showField',
        label: 'What is your first name?',
        orientation: 'label-top'
      };

      const expected = {
        wrapper: 'label-top-fields-bottom-wrapper',
        templateOptions: {
          label: 'What is your first name?'
        },
        hideExpression: 'model.showField',
        fieldGroup: [{
          id: 'first-name',
          key: 'first-name',
          type: 'varchar',
          templateOptions: {
            label: 'What is your first name?'
          }
        }]
      };

      const result = formlyTranslatorUtils.translateField(field, {});
      expect(result).toEqual(expected);
    });

    it('should put hideExpression on the field when no wrapper is applied', function() {
      const field = {
        id: 'first-name',
        type: 'varchar',
        hideExpression: 'model.showField'
      };

       const expected = {
        id: 'first-name',
        key: 'first-name',
        type: 'varchar',
        templateOptions: {},
        hideExpression: 'model.showField'
      };

      const result = formlyTranslatorUtils.translateField(field, { });
      expect(result).toEqual(expected);
    });

    describe('options', function() {
      it('should know how to apply the "typeOverride" option', function() {
        const field = {
          id: 'last-name',
          type: 'varchar'
        };

        const options = { typeOverride: 'email' };

        const expected = {
          id: 'last-name',
          key: 'last-name',
          type: 'email',
          templateOptions: {}
        };

        const result = formlyTranslatorUtils.translateField(field, options);
        expect(result).toEqual(expected);
      });

      it('should know how to apply the "defaults" option', function() {
        const field = {
          id: 'last-name',
          type: 'varchar',
          label: 'What is your lastname'
        };

        const options = {
          defaultTemplateOptions: {
            hasBorder: true, // hasBorder should be applied.
            label: 'Default' // label should be ignored.
          }
        };

        const expected = {
          id: 'last-name',
          key: 'last-name',
          type: 'varchar',
          templateOptions: {
            hasBorder: true,
            label: 'What is your lastname'
          }
        };

        const result = formlyTranslatorUtils.translateField(field, options);
        expect(result).toEqual(expected);
      });

      it('should know how to apply the "defaultWrapper" option', function() {
        const field = {
          id: 'last-name',
          label: 'What is your last-name?',
          type: 'varchar',
          hideExpression: 'model.showField'
        };

        const options = { defaultWrapper: 'label-top-fields-bottom-wrapper' };

        const expected = {
          wrapper: 'label-top-fields-bottom-wrapper',
          templateOptions: {
            label: 'What is your last-name?'
          },
          hideExpression: 'model.showField',
          fieldGroup: [{
            id: 'last-name',
            key: 'last-name',
            type: 'varchar',
            templateOptions: {
              label: "What is your last-name?"
            }
          }]
        };

        const result = formlyTranslatorUtils.translateField(field, options);
        expect(result).toEqual(expected);
      });

      it('should know how to apply the "removeLabel" option', function() {
        const field = {
          id: 'last-name',
          type: 'varchar',
          label: 'What is your last name citizen?'
        };

        const options = { removeLabel: true };

        const expected = {
          id: 'last-name',
          key: 'last-name',
          type: 'varchar',
          templateOptions: {}
        };

        const result = formlyTranslatorUtils.translateField(field, options);
        expect(result).toEqual(expected);
      });
    });
  });

  describe('applyWrapperOrDefault', function() {
    it('should apply a wrapper when the "orientation" key is known', function() {
      function checkOrientationToBeWrapper(orientation, wrapper) {
        const field = {
          id: 'first-name',
          label: 'What is your first name?',
          hideExpression: 'model.showField',
          orientation
        };

        const fieldGroup = [{
          id: 'id',
          key: 'id',
          type: 'hashtagText',
          expressionProperties: {
            "templateOptions.disabled": 'false'
          },
          templateOptions: {
            datasourceName: 'Incoming',
            required: true,
            label: 'Case'
          }
        }];

        const expected = {
          wrapper,
          templateOptions: {
            label: 'What is your first name?'
          },
          hideExpression: 'model.showField',
          fieldGroup
        };

        const result = formlyTranslatorUtils.applyWrapperOrDefault(field, fieldGroup, 'some-default-wrapper');
        expect(result).toEqual(expected);
      }

      checkOrientationToBeWrapper('label-top', 'label-top-fields-bottom-wrapper');
      checkOrientationToBeWrapper('label-left', 'label-left-fields-right-wrapper');
      checkOrientationToBeWrapper('header-top', 'header-top-fields-bottom-wrapper');
    });

    it('should apply the default wrapper when the "orientation" key is not defined', function() {
      const field = {
        id: 'first-name',
        label: 'What is your first name?',
        hideExpression: 'model.showField'
      };

      const fieldGroup = [{
        id: 'id',
        key: 'id',
        type: 'hashtagText',
        expressionProperties: {
          "templateOptions.disabled": 'false'
        },
        templateOptions: {
          datasourceName: 'Incoming',
          required: true,
          label: 'Case'
        }
      }];

      const expected = {
        wrapper: 'some-default-wrapper',
        templateOptions: {
          label: 'What is your first name?'
        },
        hideExpression: 'model.showField',
        fieldGroup
      };

      const result = formlyTranslatorUtils.applyWrapperOrDefault(field, fieldGroup, 'some-default-wrapper');
      expect(result).toEqual(expected);
    });

    it('should throw an error when the "orientation" key is unknown', function() {
      const field = {
        id: 'first-name',
        orientation: 'blaat'
      };

      expect(function() {
        formlyTranslatorUtils.applyWrapperOrDefault(field);
      }).toThrow(new Error(`Error during conversion to formly types, unknown orientation type: "blaat". Field id: "first-name"`));
    });
  });
});
