'use strict';

describe('Form type: upload', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let scope;
  let element;

  let $rootScope;
  let $compile;
  let uploadController;
  let API_URL;
  let $q;
  let $timeout;

  let validationObserver;
  let guidanceFormObserver;

  let guidanceModeBackendState;
  let elementIdGenerator;
  let ACTION_EVENT;

  let template = '<formly-form form="form" model="model" fields="fields"/>';

  let submitElement;
  let fakeElement;
  let realElement;
  let uploadDatasource;
  let $translate;

  beforeEach(inject(function (_$rootScope_, _$compile_, $state, ValidationObserver, GuidanceFormObserver,
                              _API_URL_, _$q_, _$timeout_, _guidanceModeBackendState_, _ACTION_EVENT_,
                              _elementIdGenerator_, _uploadDatasource_, _$translate_) {
    $rootScope = _$rootScope_;
    $compile = _$compile_;

    guidanceModeBackendState = _guidanceModeBackendState_;
    elementIdGenerator = _elementIdGenerator_;
    ACTION_EVENT = _ACTION_EVENT_;

    API_URL = _API_URL_;
    $q = _$q_;
    $timeout = _$timeout_;

    validationObserver = new ValidationObserver();
    guidanceFormObserver = new GuidanceFormObserver();
    uploadDatasource = _uploadDatasource_;
    $translate = _$translate_;

    spyOn(guidanceFormObserver, 'getFullModel').and.returnValue({ companyName: "wky" });
    spyOn(elementIdGenerator, 'generateId').and.returnValue('field-fake-id');

    mockHelpers.blockUIRouter($state);
  }));

  function compile({ required = false, emptyUploads = false } = {}) {
    scope = $rootScope.$new();

    if (emptyUploads === false) {
      scope.model = {
        archive: {
          "contracts": [{
            name: "Already uploaded file",
            url: "https://nova/uploads/Already uploaded file.pdf",
            id: "2e4516c9-7289-4745-92b0-a36cbb80c422"
          }, {
            name: "Already uploaded file number 2",
            url: "https://nova/uploads/Already uploaded file number 2.pdf",
            id: "bdb20fba-fae1-475e-9b69-18a7dce61dbd"
          }]
        }
      };
    } else {
      scope.model = { archive: { contracts: [] } };
    }

    scope.fields = [
      {
        id: "archive.contracts",
        key: "archive.contracts",
        type: "upload",
        templateOptions: {
          label: "Upload file",
          automaticFieldChangeListening: true,
          noBackendInteraction: false,
          maxFileSizeMB: 10,
          accept: ".pdf",
          hasBorder: true,
          readonly: false,
          guid: 'fake-guid',
          required
        }
      }
    ];

    const guidanceFormObserverAccessorElement = mockHelpers.createGuidanceFormObserverAccessor({
      $compile,
      $rootScope,
      guidanceFormObserver,
      validationObserver
    });

    element = angular.element(template);
    element = $compile(element)(scope);
    guidanceFormObserverAccessorElement.append(element);
    $rootScope.$apply();

    const inputs = element.find('input');
    expect(inputs.length).toBe(3);

    submitElement = $(inputs[0]);
    fakeElement = $(inputs[1]);
    realElement = $(inputs[2]);

    uploadController = element.find("upload-form-element").controller("uploadFormElement");

    expect(uploadController).not.toBe(undefined);
  }

  it('should have the correct element id', function () {
    compile();

    expect(elementIdGenerator.generateId).toHaveBeenCalledTimes(1);
    expect(elementIdGenerator.generateId).toHaveBeenCalledWith('archive.contracts', guidanceFormObserver);
    expect(realElement.attr('id')).toBe('field-fake-id');
  });

  it('should create an upload field with three inputs.', function () {
    compile();

    expect(submitElement.attr('type')).toBe('submit');
    expect(fakeElement.attr('type')).toBe('text');
    expect(realElement.attr('type')).toBe('file');
  });

  it('should create a list of already uploaded files.', function () {
    compile();

    const links = element.find('div.uploaded > a:not(.icon-close)');
    expect(links.length).toBe(2);

    expect($(links[0]).attr("href")).toBe("https://nova/uploads/Already uploaded file.pdf");
    expect($(links[1]).attr("href")).toBe("https://nova/uploads/Already uploaded file number 2.pdf");

    const spans = element.find('div.uploaded > span');
    expect(spans.length).toBe(2);

    expect($(spans[0]).text()).toBe("Already uploaded file");
    expect($(spans[1]).text()).toBe("Already uploaded file number 2");
  });

  /*
   File uploading cannot be mocked by clicking on user interface elements
   because the user needs to select a file when the browsers upload
   window pops up. So I will have to call the controller directly to
   test file uploading.
   */
  describe('Uploading files to the back-end', function () {
    it('should not upload the file when it is empty.', function () {
      compile();

      spyOn(uploadController.Upload, 'upload');

      uploadController.uploadFieldChanged(null);

      expect(uploadController.Upload.upload).not.toHaveBeenCalled();
    });

    it('should not upload the file when it is too big.', function () {
      compile();
      spyOn(uploadController.Upload, 'upload');
      spyOn(validationObserver, 'setError');
      spyOn($translate, 'instant');

      uploadController.uploadFieldChanged({"name": "too-big.pdf", "size": 11*1024*1024});

      expect(validationObserver.setError).toHaveBeenCalledTimes(1);
      expect($translate.instant).toHaveBeenCalledTimes(1);
    });

    it('should set the UI values when starting to upload, and keep track of the progress.', function () {
      compile();

      let notify;

      spyOn(uploadController.Upload, 'upload').and.returnValue({
        then: function (then, error, _notify_) {
          notify = _notify_;
        }
      });

      const uploadProgress = $(element.find('.percentage')[0]);

      uploadController.uploadFieldChanged({ name: 'ExesserIsAwesome.pdf' });
      $rootScope.$apply();

      expect(uploadProgress.text()).toBe('0%');
      expect(uploadProgress.hasClass('ng-hide')).toBe(false);

      notify({ loaded: 1, total: 100 });
      $rootScope.$apply();
      expect(uploadProgress.text()).toBe('1%');

      notify({ loaded: 50, total: 100 });
      $rootScope.$apply();
      expect(uploadProgress.text()).toBe('50%');

      notify({ loaded: 100, total: 100 });
      $rootScope.$apply();

      // It should be capped at 95%.
      expect(uploadProgress.text()).toBe('95%');
    });

    it('should hide the upload form when uploading but on error it should reshow the form.', function () {
      compile();

      let error;

      spyOn(uploadController.Upload, 'upload').and.returnValue({
        then: function (then, _error_) {
          error = _error_;
        }
      });
      spyOn(validationObserver, 'setError');

      const progressLi = $(element.find('.file-upload__list > li')[0]);
      expect(progressLi.hasClass('ng-hide')).toBe(true);

      uploadController.uploadFieldChanged({ name: 'ExesserIsAwesome.pdf' });
      $rootScope.$apply();

      expect(progressLi.hasClass('ng-hide')).toBe(false);

      error({
        data: {
          "status": 400,
          "data": [
            "File was too big",
            "File was not a pdf"
          ],
          "message": "Success"
        }
      });
      $rootScope.$apply();

      expect(progressLi.hasClass('ng-hide')).toBe(true);

      expect(validationObserver.setError).toHaveBeenCalledTimes(1);
      expect(validationObserver.setError).toHaveBeenCalledWith('archive.contracts', ["File was too big", "File was not a pdf"]);
    });

    it('should upload the file and update the UI after it is finished.', function () {
      spyOn(guidanceFormObserver, 'formValueChanged');

      compile({ required: true, emptyUploads: true });

      expect(scope.form.$valid).toBe(false);

      const uploadDeferred = $q.defer();
      spyOn(uploadController.Upload, 'upload').and.returnValue(uploadDeferred.promise);

      spyOn(validationObserver, 'clearError');

      const progressLi = $(element.find('.file-upload__list > li')[0]);
      expect(progressLi.hasClass('ng-hide')).toBe(true);

      uploadController.uploadFieldChanged({ name: 'ExesserIsAwesome.pdf' });
      $rootScope.$apply();

      expect(progressLi.hasClass('ng-hide')).toBe(false);

      expect(uploadController.Upload.upload).toHaveBeenCalledTimes(1);
      expect(uploadController.Upload.upload).toHaveBeenCalledWith({
        url: API_URL + 'fileupload',
        file: { name: 'ExesserIsAwesome.pdf' },
        headers: { 'X-LOG-DESCRIPTION': 'Upload file' },
        data: {
          model: {
            companyName: 'wky'
          },
          fieldGuid: 'fake-guid'
        }
      });

      uploadDeferred.resolve({
        data: {
          data: {
            name: "ExesserIsAwesome.pdf",
            url: "https://nova/uploads/ExesserIsAwesome.pdf",
            id: "2e453523516c9-7289-4745-92b0-saasdfasdfasdf"
          }
        }
      });

      $rootScope.$apply();

      // Check that the final 100% is shown to the user.
      const uploadProgress = $(element.find('.percentage')[0]);
      expect(uploadProgress.text()).toBe('100%');

      $timeout.flush();

      const links = element.find('div.uploaded > a:not(.icon-close)');
      expect(links.length).toBe(1);

      expect($(links[0]).attr('href')).toBe("https://nova/uploads/ExesserIsAwesome.pdf");

      const spans = element.find('div.uploaded > span');
      expect(spans.length).toBe(1);

      expect($(spans[0]).text()).toBe("ExesserIsAwesome.pdf");

      expect(progressLi.hasClass('ng-hide')).toBe(true);

      expect(validationObserver.clearError).toHaveBeenCalledTimes(1);
      expect(validationObserver.clearError).toHaveBeenCalledWith('archive.contracts');

      expect(scope.form.$valid).toBe(true);

      expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(1);
      expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
        focus: 'archive.contracts',
        value: [{
          name: "ExesserIsAwesome.pdf",
          url: "https://nova/uploads/ExesserIsAwesome.pdf",
          id: "2e453523516c9-7289-4745-92b0-saasdfasdfasdf"
        }]
      }, false);
    });

    it('should let the "guidanceFormObserver" know that the values have changed when noBackendInteraction is true', function () {
      spyOn(guidanceFormObserver, 'formValueChanged');

      compile({ emptyUploads: true });

      expect(element.find('div.uploaded > a:not(.icon-close)').length).toBe(0);
      expect(guidanceFormObserver.formValueChanged).not.toHaveBeenCalled();

      scope.fields[0].templateOptions.noBackendInteraction = true;
      $rootScope.$apply();

      const uploadDeferred = $q.defer();
      spyOn(uploadController.Upload, 'upload').and.returnValue(uploadDeferred.promise);

      uploadController.uploadFieldChanged({ name: 'wky.pdf' });
      $rootScope.$apply();

      uploadDeferred.resolve({
        data: {
          data: {
            name: "wky.pdf",
            url: "https://nova/uploads/wky.pdf",
            id: "123-456-789"
          }
        }
      });

      $rootScope.$apply();

      $timeout.flush();

      const links = element.find('div.uploaded > a:not(.icon-close)');
      expect(links.length).toBe(1);
      expect($(links[0]).attr('href')).toBe("https://nova/uploads/wky.pdf");
      expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledTimes(1);
      expect(guidanceFormObserver.formValueChanged).toHaveBeenCalledWith({
        focus: 'archive.contracts',
        value: [{
          name: "wky.pdf",
          url: "https://nova/uploads/wky.pdf",
          id: "123-456-789"
        }]
      }, true);
    });
  });

  describe('Deleting files', function () {
    it('should keep the model when X is clicked and backend failed', function () {
      compile();

      spyOn(uploadDatasource, 'removeFile').and.callFake(mockHelpers.rejectedPromise($q));

      const files = element.find('a.icon-close');
      expect(files.length).toBe(2);

      $(files[0]).click();
      $rootScope.$apply();

      expect(uploadDatasource.removeFile).toHaveBeenCalledTimes(1);
      expect(scope.model.archive.contracts).toEqual([
        {
          name: "Already uploaded file",
          url: "https://nova/uploads/Already uploaded file.pdf",
          id: "2e4516c9-7289-4745-92b0-a36cbb80c422"
        }, {
          name: 'Already uploaded file number 2',
          url: 'https://nova/uploads/Already uploaded file number 2.pdf',
          id: 'bdb20fba-fae1-475e-9b69-18a7dce61dbd'
        }
      ]);
    });

    it('should remove file from the model when X is clicked', function () {
      compile();

      const files = element.find('a.icon-close');
      expect(files.length).toBe(2);

      const firstFile = $(files[0]);
      const secondFile = $(files[1]);
      const deleteDeferred = $q.defer();
      spyOn(uploadDatasource, 'removeFile').and.returnValue(deleteDeferred.promise);

      firstFile.click();
      deleteDeferred.resolve({
        data: {
          data: {
            name: "Already uploaded file",
            url: "https://nova/uploads/Already uploaded file.pdf",
            id: "2e4516c9-7289-4745-92b0-a36cbb80c422"
          }
        }
      });
      $rootScope.$apply();

      expect(element.find('a.icon-close').length).toBe(1);
      expect(scope.model.archive.contracts).toEqual([
        {
          name: 'Already uploaded file number 2',
          url: 'https://nova/uploads/Already uploaded file number 2.pdf',
          id: 'bdb20fba-fae1-475e-9b69-18a7dce61dbd'
        }
      ]);

      secondFile.click();
      deleteDeferred.resolve({
        data: {
          data: {
            name: "Already uploaded file number 2",
            url: "https://nova/uploads/Already uploaded file number 2.pdf",
            id: "bdb20fba-fae1-475e-9b69-18a7dce61dbd"
          }
        }
      });
      $rootScope.$apply();

      expect(element.find('a.icon-close').length).toBe(0);
      expect(scope.model.archive.contracts).toEqual([]);
      expect(uploadDatasource.removeFile).toHaveBeenCalledTimes(2);
    });

    it('should remove file from the model when X is clicked and no fileId found', function () {
      compile({ emptyUploads: true });

      expect(element.find('input').length).toBe(3);
      expect(element.find('a').length).toBe(0);

      scope.model = {
        archive: {
          "contracts": [{
            name: "Already uploaded file",
            url: "https://nova/uploads/Already uploaded file.pdf",
            stream: "just-some-random-file-stream"
          }]
        }
      };
      $rootScope.$apply();

      const files = element.find('a.icon-close');
      expect(files.length).toBe(1);

      const firstFile = $(files[0]);
      const deleteDeferred = $q.defer();
      spyOn(uploadDatasource, 'removeFile');

      firstFile.click();
      deleteDeferred.resolve({
        data: {
          data: {
            name: "Already uploaded file",
            url: "https://nova/uploads/Already uploaded file.pdf",
            stream: "just-some-random-file-stream"
          }
        }
      });
      $rootScope.$apply();

      expect(element.find('a.icon-close').length).toBe(0);
      expect(scope.model.archive.contracts).toEqual([]);
      expect(uploadDatasource.removeFile).toHaveBeenCalledTimes(0);
    });
  });

  describe("the 'disabled' functionality", function () {
    it('should made the field read-only if the templateOptions.disabled property evaluates to true', function () {
      compile();

      expect(submitElement.prop('disabled')).toBe(false);
      expect(fakeElement.prop('disabled')).toBe(false);
      expect(realElement.prop('disabled')).toBe(false);

      scope.fields[0].templateOptions.disabled = true;
      $rootScope.$apply();

      expect(submitElement.prop('disabled')).toBe(true);
      expect(fakeElement.prop('disabled')).toBe(true);
      expect(realElement.prop('disabled')).toBe(true);
    });

    it('should make the field disabled if the backend is busy', function () {
      spyOn(guidanceModeBackendState, 'getBackendIsBusy').and.returnValue(true);
      spyOn(guidanceModeBackendState, 'getPerformedAction').and.returnValue({
        event: ACTION_EVENT.CHANGED,
        focus: 'field'
      });
      compile();

      expect(submitElement.prop('disabled')).toBe(true);
      expect(fakeElement.prop('disabled')).toBe(true);
      expect(realElement.prop('disabled')).toBe(true);
    });
  });

  describe("the 'readonly' functionality", function () {
    it('should made the field readonly if the templateOptions.readonly property evaluates to true', function () {
      compile({ emptyUploads: true });

      expect(element.find('input').length).toBe(3);
      expect(element.find('a').length).toBe(0);

      scope.model = {
        archive: {
          "contracts": [{
            name: "Already uploaded file",
            url: "https://nova/uploads/Already uploaded file.pdf",
            id: "2e4516c9-7289-4745-92b0-a36cbb80c422"
          }, {
            name: "Already uploaded file number 2",
            url: "https://nova/uploads/Already uploaded file number 2.pdf",
            id: "bdb20fba-fae1-475e-9b69-18a7dce61dbd"
          }]
        }
      };
      $rootScope.$apply();

      expect(element.find('textarea').length).toBe(0);

      const links = element.find('div.uploaded > a:not(.icon-close)');
      expect(links.length).toBe(2);

      expect($(links[0]).attr("href")).toBe("https://nova/uploads/Already uploaded file.pdf");
      expect($(links[1]).attr("href")).toBe("https://nova/uploads/Already uploaded file number 2.pdf");

      const spans = element.find('div.uploaded > span');
      expect(spans.length).toBe(2);

      expect($(spans[0]).text()).toBe("Already uploaded file");
      expect($(spans[1]).text()).toBe("Already uploaded file number 2");
    });
  });

  describe('the validation functionality', function () {
    it('should set the field to erroneous if field errors occur', function () {
      compile();

      expect(scope.form.$valid).toBe(true);

      validationObserver.setErrors({
        'archive.contracts': ["That is not a good PDF file."]
      });
      $rootScope.$apply();
      expect(scope.form.$valid).toBe(false);
      expect(scope.form['archive.contracts'].$error.BACK_END_ERROR).toBe(true);
    });
  });

  describe("the 'required' functionality", function () {
    it('should not set any errors when the field is not required', function () {
      compile();
      expect(scope.form.$valid).toBe(true);
    });

    it('should set errors when the field is required and the object is empty', function () {
      compile({ required: true, emptyUploads: true });

      expect(scope.form.$valid).toBe(false);
      expect(scope.form['archive.contracts'].$error.required).toBe(true);
    });
  });
});
