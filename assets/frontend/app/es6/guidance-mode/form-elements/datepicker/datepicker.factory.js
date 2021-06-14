'use strict';

/**
 * @ngdoc service
 * @name digitalWorkplaceApp.datepickerFactory
 * @description
 * # datepickerFactory
 *
 * The datepickerFactory helps the datepicker-form-element by handling
 * things such as getting transforming dates to various formats, and
 * checking if user inputs are valid.
 *
 * Factory in the digitalWorkplaceApp.
 */
angular.module('digitalWorkplaceApp')
  .factory('datepickerFactory', function () {

    const isoDateFormat = 'YYYY-MM-DD';
    const isoDateTimeFormat = 'YYYY-MM-DD HH:mm:ss';

    const userInputDateFormat = 'DD/MM/YYYY';
    const userInputTimeFormat = 'HH:mm';

    const userInputDateTimeFormat = 'DD/MM/YYYY HH:mm';

    return {
      convertUserDateAndTimeInputToIsoDateTimeString,
      convertIsoDateAndUserTimeToIsoDateTimeString,
      convertUserInputDateToIsoDateString,
      convertIsoDateTimeStringToIsoDateFormat,
      convertDateToIsoDateFormat,
      convertIsoDateToUserInputDate,
      convertIsoDateTimeToUserInputDateTime,
      convertIsoDateTimeToUserInputTime,

      isUserInputDateValid,
      isUserInputTimeValid
    };

    /**
     * Combines a userInputDate and a userInputTime and returns a
     * isoDateTimeFormat formatted string.
     *
     * @param  {String} userInputDate A string in the format: DD/MM/YYYY
     * @param  {String} userInputTime A string in the format: HH:mm
     * @return {String}               A string in the format: YYYY-MM-DD HH:mm:ss
     */
    function convertUserDateAndTimeInputToIsoDateTimeString(userInputDate, userInputTime) {
      const dateTime = `${userInputDate} ${userInputTime}`;
      const momentDate = moment(dateTime, userInputDateTimeFormat, true);
      return momentDate.format(isoDateTimeFormat);
    }

    /**
     * Combines a isoDate and a userInputTime and returns a
     * isoDateTimeFormat formatted string. The seconds will always
     * be '00'.
     *
     * @param  {String} isoDate       A string in the format: YYYY-MM-DD
     * @param  {String} userInputTime A string in the format: HH:mm
     * @return {String}               A string in the format: YYYY-MM-DD HH:mm:ss
     */
    function convertIsoDateAndUserTimeToIsoDateTimeString(isoDate, userInputTime) {
      return `${isoDate} ${userInputTime}:00`;
    }

    /**
     * Takes a userInputDate and transforms it into a isoDate.
     *
     * @param  {String} isoDate       A string in the format: DD/MM/YYYY
     * @return {String}               A string in the format: YYYY-MM-DD
     */
    function convertUserInputDateToIsoDateString(userInputDate) {
      const momentDate = moment(userInputDate, userInputDateFormat, true);
      return momentDate.format(isoDateFormat);
    }

    /**
     * Takes a isoDateTime and transforms it into a isoDate.
     *
     * @param  {String} isoDateTime   A string in the format: YYYY-MM-DD HH:mm:ss
     * @return {String}               A string in the format: YYYY-MM-DD
     */
    function convertIsoDateTimeStringToIsoDateFormat(isoDateTime) {
      return isoDateTime.substring(0, isoDateFormat.length);
    }

    /**
     * Converts a JavaScript Date object to a isoDate
     *
     * @param  {Date} date   A Date object
     * @return {String}      A string in the format: YYYY-MM-DD
     */
    function convertDateToIsoDateFormat(date) {
      return moment(date.getTime()).format(isoDateFormat);
    }

    /**
     * Converts isoDate to a userInputDateFormat
     *
     * @param  {String} isoDate   A string in the format: YYYY-MM-DD
     * @return {String}           A string in the format: DD/MM/YYYY
     */
    function convertIsoDateToUserInputDate(isoDate) {
      const momentDate = moment(isoDate, isoDateFormat, true);
      return momentDate.format(userInputDateFormat);
    }

    /**
     * Converts isoDateDate to a userInputDateFormat
     *
     * @param  {String} isoDateTime   A string in the format: YYYY-MM-DD HH:mm:ss
     * @return {String}               A string in the format: DD/MM/YYYY HH:mm
     */
    function convertIsoDateTimeToUserInputDateTime(isoDateTime) {
      const momentDate = moment(isoDateTime, isoDateTimeFormat, true);
      return momentDate.format(userInputDateTimeFormat);
    }

    /**
     * Converts isoDateTime to a userInputTimeFormat.
     *
     * If the isoDateTime has no time it will return an
     * empty string.
     *
     * @param  {String} isoDateTime   A string in the format: YYYY-MM-DD HH:mm:ss
     * @return {String}               A string in the format: HH:mm
     */
    function convertIsoDateTimeToUserInputTime(isoDateTime) {
      const timeWithSeconds = isoDateTime.split(" ")[1];

      if (_.isUndefined(timeWithSeconds)) {
        return '';
      } else {
        return timeWithSeconds.substring(0, userInputTimeFormat.length);
      }
    }

    /**
     * Checks if the userInputDate is valid according to the 'DD/MM/YYYY'
     * format.
     *
     * @param  {String}  userInputDate The string you want checked.
     * @return {Boolean}               Whether or not the userInputDate has a valid 'DD/MM/YYYY' format
     */
    function isUserInputDateValid(userInputDate) {
      const date = moment(userInputDate, userInputDateFormat, true);
      return date.isValid();
    }

    /**
     * Checks if the userInputTime is valid according to the 'HH:mm'
     * format.
     *
     * @param  {String}  userInputTime The string you want checked.
     * @return {Boolean}               Whether or not the userInputTime has a valid 'HH:mm' format
     */
    function isUserInputTimeValid(userInputTime) {
      // Don't allow '24:00' as it will cause the 'date' to go to the next day.
      if (userInputTime === '24:00') {
        return false;
      } else {
        const time = moment(userInputTime, userInputTimeFormat, true);
        return time.isValid();
      }
    }
  });
