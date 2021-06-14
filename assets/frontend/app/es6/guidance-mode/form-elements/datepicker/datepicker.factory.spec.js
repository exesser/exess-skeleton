'use strict';

describe('Factory: datepickerFactory', function () {
  beforeEach(module('digitalWorkplaceApp'));

  // instantiate service
  let datepickerFactory;

  beforeEach(inject(function (_datepickerFactory_, $state) {
    mockHelpers.blockUIRouter($state);

    datepickerFactory = _datepickerFactory_;
  }));

  describe('convertUserDateAndTimeInputToIsoDateTimeString', function() {
    it('should know how to convert to isoDateTimeString', function () {
      const userInputDate = '01/01/2001';
      const userInputTime = '13:37';

      const result = datepickerFactory.convertUserDateAndTimeInputToIsoDateTimeString(userInputDate, userInputTime);
      expect(result).toBe('2001-01-01 13:37:00');
    });
  });

  describe('convertIsoDateAndUserTimeToIsoDateTimeString', function() {
    it('should know how to convert to isoDateTimeString', function () {
      const userInputDate = '2001-01-01';
      const userInputTime = '13:37';

      const result = datepickerFactory.convertIsoDateAndUserTimeToIsoDateTimeString(userInputDate, userInputTime);
      expect(result).toBe('2001-01-01 13:37:00');
    });
  });

  describe('convertUserInputDateToIsoDateString', function() {
    it('should know how to convert to isoDateString', function () {
      const userInputDate = '01/01/2001';

      const result = datepickerFactory.convertUserInputDateToIsoDateString(userInputDate);
      expect(result).toBe('2001-01-01');
    });
  });

  describe('convertIsoDateTimeStringToIsoDateFormat', function() {
    it('should know how to convert to isoDateString', function () {
      const isoDateTime = '2001-01-01 13:37:00';

      const result = datepickerFactory.convertIsoDateTimeStringToIsoDateFormat(isoDateTime);
      expect(result).toBe('2001-01-01');
    });
  });

  describe('convertDateToIsoDateFormat', function() {
    it('should know how to convert to isoDateString', function () {
      const date = new Date(1989, 2, 21); // In JS months start at zero.

      const result = datepickerFactory.convertDateToIsoDateFormat(date);
      expect(result).toBe('1989-03-21');
    });
  });

  describe('convertIsoDateToUserInputDate', function() {
    it('should know how to convert to a user input date', function () {
      const isoDate = '2001-01-01';

      const result = datepickerFactory.convertIsoDateToUserInputDate(isoDate);
      expect(result).toBe('01/01/2001');
    });
  });

  describe('convertIsoDateTimeToUserInputDateTime', function() {
    it('should know how to convert to a user input date time', function () {
      const isoDateTime = '2001-01-01 13:37:00';

      const result = datepickerFactory.convertIsoDateTimeToUserInputDateTime(isoDateTime);
      expect(result).toBe('01/01/2001 13:37');
    });
  });

  describe('convertIsoDateTimeToUserInputTime', function() {
    it('should know how to convert to a user input time', function () {
      const isoDateTime = '2001-01-01 13:37:00';

      const result = datepickerFactory.convertIsoDateTimeToUserInputTime(isoDateTime);
      expect(result).toBe('13:37');
    });

    it('should give back an empty string when no time is present', function() {
      const isoDateTime = '2001-01-01';

      const result = datepickerFactory.convertIsoDateTimeToUserInputTime(isoDateTime);
      expect(result).toBe('');
    });
  });

  describe('isUserInputDateValid', function() {
    it('should consider "01/01/2001" valid', function () {
      const userInputDate = '01/01/2001';

      const result = datepickerFactory.isUserInputDateValid(userInputDate);
      expect(result).toBe(true);
    });

    it('should consider "01/01/20011" invalid', function () {
      const userInputDate = '01/01/20011';

      const result = datepickerFactory.isUserInputDateValid(userInputDate);
      expect(result).toBe(false);
    });
  });

  describe('isUserInputTimeValid', function() {
    it('should consider "16:00" valid', function () {
      const userInputTime = '16:00';

      const result = datepickerFactory.isUserInputTimeValid(userInputTime);
      expect(result).toBe(true);
    });

    it('should consider "16:001" invalid', function () {
      const userInputTime = '16:001';

      const result = datepickerFactory.isUserInputTimeValid(userInputTime);
      expect(result).toBe(false);
    });

    it('should consider "24:00" invalid', function () {
      const userInputTime = '24:00';

      const result = datepickerFactory.isUserInputTimeValid(userInputTime);
      expect(result).toBe(false);
    });
  });
});
