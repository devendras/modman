/*global module:false*/
module.exports = {
  compile: {
    options: {
      name: 'main',
      baseUrl: "<%= buildDir %>assets/js/",
      optimize: "none",
      mainConfigFile: '<%= buildDir %>assets/js/main.js',
      out: '<%= buildDir %>assets/js/main.js'
    }
  }
};