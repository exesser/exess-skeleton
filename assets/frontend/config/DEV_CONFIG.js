'use strict';

/**
 * Develop configuration
 * You can replace all these properties in DEV_CONFIG.USER.js
 */
module.exports = {
  /*
   This config states that the development server should
   start at localhost:9005
   */
  server: {
    port: 9005,
    hostname: '0.0.0.0',
    livereload: 35729
  },
  /*
   This config states that all traffic to: "http://localhost:9005/Api" should be redirected to: "webserver:80/Api"
   For more information on the proxy see: https://github.com/drewzboto/grunt-connect-proxy
   */
  proxy: {
    context: '/Api',
    host: 'webserver',
    port: 80,
    https: false
  },
  // Ability to change the grunt watch interval
  watch_interval: 500
};
