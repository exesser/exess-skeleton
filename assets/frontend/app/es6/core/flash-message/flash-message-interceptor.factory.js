'use strict';

/**
 * @ngdoc service
 * @name digitalWorkplaceApp.flashMessageInterceptor
 * @description
 * # flashMessageInterceptor
 *
 * On every request the front-end receives from the back-end. The
 * back-end can tell the front-end to display a list of flash messages.
 *
 * The envelope of the digital workplace looks like this:
 *
 * ```JSON
 * {
 *   "status": 200,
 *   "flashMessages": [
 *       { "type": "ERROR", "text": "This is an error, it will be red.", "group": "price" },
 *       { "type": "WARNING", "text": "This is a warning, it will be yellow.", "group": "price" },
 *       { "type": "SUCCESS", "text": "This is a success, it will be green", "group": "create-quote" },
 *       { "type": "INFORMATION", "text": "This is information, it will be blue", "group": "create-quote" }
 *   ],
 *   "data": {
 *     // The data is here
 *   },
 *   "message": "Success"
 * }
 * ```
 *
 * The 'flashMessages' will be parsed by this factory: the flashMessageInterceptor,
 * and will display them to the user. If the 'flashMessages' does not
 * exist as a property on the response it is simply ignored.
 *
 * This happens for any request regardless of the status code. So both
 * errors and successes can display flashMessages.
 *
 * Factory in the digitalWorkplaceApp is a $http.interceptor.
 */
angular.module('digitalWorkplaceApp')
  .factory('flashMessageInterceptor', function ($q, flashMessageContainer) {
    return { response, responseError };

    /**
     * Whenever a HTTP request has flash messages show those messages
     * via the flash message factory.
     * @param  {Object} rejection The rejection object explaining what went wrong.
     */
    function response(response) {
      addMessagesIfNotEmpty(response);

      return response;
    }

    /**
     * Whenever a HTTP request fails, and it has flash messages, show
     * those messages via the flash message factory.
     * @param  {Object} rejection The rejection object explaining what went wrong.
     */
    function responseError(response) {
      addMessagesIfNotEmpty(response);

      return $q.reject(response);
    }

    function addMessagesIfNotEmpty(response) {
      const flashMessages = _.get(response, 'data.flashMessages', []);

      if (_.isEmpty(flashMessages) === false) {
        _.each(flashMessages, (flash) => {
          flashMessageContainer.addMessageOfType(flash.type, flash.text, flash.group);
       });
      }
    }
  });
