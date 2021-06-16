// Defines all angular constants for the 'development' environment
module.exports = function(appConfig) {
  return {
    APP: {
      version: appConfig.version
    },
    ENV: {
      name: 'development'
    },
    /*
      Defines the debounce time which is used to determine how long
      the time needs to be between two request, which would otherwise
      happen too frequently behind each other.

      For example validation and suggestions request in the Guidance Mode
      would otherwise happen every keystroke. But also when the filters
      for a list change.
     */
    DEBOUNCE_TIME: 500,
    DEFAULT_TRANSLATION: require('../app/languages/en_BE.json'),
    STANDARD_USER: {
      username: 'superadmin',
      password: 'ch4ng3m3pl5'
    },
    API_URL: '/Api/V8_Custom/',
    API_PATH: '/Api/',
    GTM_CONTAINER: '',
    GTM_ENABLED: false
  }
};
