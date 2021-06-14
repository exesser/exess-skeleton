"use strict";

/**
 * @ngdoc function
 * @name digitalWorkplaceApp.component:uploadFormElement
 * @description
 * # uploadFormElement
 * Component of the digitalWorkplaceApp
 *
 * This component creates an upload form element.
 *
 * Example usage:
 *
 * <upload-form-element
 *   ng-model
 *   id="lead.hasGas" <!-- The id for the form element, used for e2e testing -->
 *   key="lead.hasGas" <!-- The key to bind to in the model -->
 *   accept="pdf" <!-- Standard HTML accept attribute. Hints the browser of the supported file type(s). -->
 *   is-readonly="false" <!-- Expression that makes the field readonly when it evaluates to true -->
 *   no-backend-interaction="false" <!-- Indicates whether or not to make a call to backend when the field is changed -->
 *   is-disabled="false"> <!-- Expression that disables this field when it evaluates to true -->
 * </upload-form-element>
 *
 * The reason the 'uploadFormElement' is necessary, and why the
 * 'upload' formly types uses this Component, is because we need to
 * require the 'guidanceObserversAccessor' in order to obtain the
 * validationObserver.
 *
 */
angular.module('digitalWorkplaceApp')
  .component('uploadFormElement', {
    templateUrl: 'es6/guidance-mode/form-elements/upload/upload-form-element.component.html',
    require: {
      ngModel: 'ngModel',
      guidanceObserversAccessor: '^guidanceObserversAccessor'
    },
    bindings: {
      id: "@",
      key: "@",
      maxFileSizeMB: "@",
      accept: "@",
      guid: "@",
      isDisabled: "<",
      required: "<",
      isReadonly: "<",
      noBackendInteraction: "<"
    },
    controllerAs: 'uploadFormElementController',
    controller: function ($scope, validationMixin, isDisabledMixin, modelChangedMixin, validationObserverFactory,
                          Upload, API_URL, $timeout, elementIdGenerator, uploadDatasource, $translate, LOG_HEADERS_KEYS) {
      const uploadFormElementController = this;

      // Set Upload on the controller so it can be mocked in the test.
      uploadFormElementController.Upload = Upload;

      uploadFormElementController.isUploading = false;
      uploadFormElementController.selectedFile = '';
      uploadFormElementController.progressPercentage = 0;
      uploadFormElementController.fullModel = {};

      let validationObserver;
      uploadFormElementController.$onInit = function () {
        validationObserver = uploadFormElementController.guidanceObserversAccessor.getValidationObserver();
        validationMixin.apply(uploadFormElementController, validationObserver);

        isDisabledMixin.apply(uploadFormElementController);

        const guidanceFormObserver = uploadFormElementController.guidanceObserversAccessor.getGuidanceFormObserver();
        uploadFormElementController.fullModel = guidanceFormObserver.getFullModel();

        modelChangedMixin.apply(uploadFormElementController, 'uploadFormElementController', $scope, false);

        uploadFormElementController.elementId = elementIdGenerator.generateId(uploadFormElementController.id, guidanceFormObserver);
      };

      $scope.$watch("uploadFormElementController.ngModel.$viewValue", function (value) {
        uploadFormElementController.internalModelValue = value;

        if (uploadFormElementController.required === true) {
          const validity = _.isEmpty(value) === false;
          uploadFormElementController.ngModel.$setValidity('required', validity);
        }
      });

      uploadFormElementController.uploadFieldChanged = function (file) {
        /*
         The file will be null in between two file uploads. So when
         you upload the first file and then select a second file the file
         will be null very briefly.

         Since we cannot upload null we guard against this here.
         */
        if (_.isNull(file) === false) {
          if (file.size/1024/1024 >= uploadFormElementController.maxFileSizeMB) {
            validationObserver.setError(
              uploadFormElementController.key,
              [
                $translate.instant('MAX_FILE_SIZE_ERROR', {'max_file_size': uploadFormElementController.maxFileSizeMB})
              ]
            );
          } else {
            upload(file);
          }
        }
      };

      // remove file after upload
      uploadFormElementController.removeFile = function (fileId) {
        if (_.isEmpty(fileId)) {
          uploadFormElementController.internalModelValue = _.filter(uploadFormElementController.internalModelValue, (file) => {
            return file.id !== fileId;
          });
          uploadFormElementController.internalModelValueChanged();
        } else {
          uploadDatasource.removeFile({
            model: uploadFormElementController.fullModel,
            docGuid: fileId
          }).then(function () {
            uploadFormElementController.internalModelValue = _.filter(uploadFormElementController.internalModelValue, (file) => {
              return file.id !== fileId;
            });
            uploadFormElementController.internalModelValueChanged();
          });
        }
      };

      function upload(file) {
        uploadFormElementController.progressPercentage = 0;
        uploadFormElementController.isUploading = true;
        uploadFormElementController.selectedFile = file.name;

        const headers = {};
        headers[LOG_HEADERS_KEYS.DESCRIPTION] = `Upload file`;
        Upload.upload({
          url: API_URL + 'fileupload',
          file: file,
          headers: headers,
          data: {
              model: uploadFormElementController.fullModel,
              fieldGuid: uploadFormElementController.guid
          }
        }).then(success, error, notify);
      }

      function success(response) {
        // Tell the user we are done with the final 5%
        uploadFormElementController.progressPercentage = 100;

        // Let the user stare at 100% for a short time.
        $timeout(function () {
          uploadFormElementController.isUploading = false;

          uploadFormElementController.ngModel.$setValidity('BACK_END_ERROR', true);
          uploadFormElementController.ngModel.$setValidity('required', true);

          validationObserver.clearError(uploadFormElementController.key);

          uploadFormElementController.internalModelValue.push(response.data.data);

          uploadFormElementController.internalModelValueChanged();
        }, 500);
      }

      function error(response) {
        const errorMessages = response.data.data;
        uploadFormElementController.ngModel.$setValidity('BACK_END_ERROR', errorMessages.length === 0);
        validationObserver.setError(uploadFormElementController.key, errorMessages);

        uploadFormElementController.isUploading = false;
      }

      function notify(event) {
        const progress = parseInt(100.0 * event.loaded / event.total);

        /*
         Cap the progress to 95% because the back-end does a lot
         of processing after the upload is complete. The user will
         then look at 100% for a while, which is bad UX. So we
         will do the final 5% after the back-end tells us it is
         done.
         */
        uploadFormElementController.progressPercentage = Math.min(95, progress);
      }
    }
  });
