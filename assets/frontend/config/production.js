// Defines all angular constants for the 'production' environment
module.exports = function(appConfig) {
  return {
    APP: {
      version: appConfig.version
    },
    ENV: {
      name: 'production'
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
      username: '',
      password: ''
    },
    API_URL: "/nova-crm/Api/V8_Custom/",
    GTM_CONTAINER: '',
    GTM_ENABLED: false
  }
};
