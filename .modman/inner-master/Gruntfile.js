/*global module:false*/
module.exports = function (grunt) {
  'use strict';
  var config = extend(
    require('./tasks/config'),
    loadConfig('./tasks/options/')
  );

  // Load all npm tasks from package.json
  loadNpmTasks(grunt.loadNpmTasks, config.pkg.devDependencies);
  loadNpmTasks(grunt.loadNpmTasks, config.pkg.dependencies);

  // Load project configuration
  grunt.initConfig(config);

  // Default task: Dev
  grunt.registerTask('default', ['concurrent:dev']);

  // Watch Task
  // grunt.registerTask('watch', ['compass:dev']);

  // Build task
  // grunt.registerTask('build', ['clean', 'compass', 'requirejs', 'modernizr', 'cssmin', 'copy', 'usemin', 'imagemin', 'clean:nonMinified']); // 'filerev', 'concat', 'min', 'requirejs', 'usemin'
  grunt.registerTask('build', ['compass:dist', 'middleman:build', 'requirejs', 'modernizr', 'concat', 'concurrent:build']);

  // Allows you to view the final build in your browser. 
  grunt.registerTask('serve', ['connect:build']);
};

/*
 * Utility functions for loading
 */

/**
 * Load configuration files for Grunt
 *
 * @param  {string} path Path to folder with tasks
 * @return {object}      All options
 */
function loadConfig(path) {
  var fs = require('fs');
  var object = {};
  var key;

  fs.readdirSync(path).forEach(function (option) {
    key = option.replace(/\.js$/,'');
    object[key] = require(path + option);
  });

  return object;
}

/**
 * Merge the contents of all passed objects
 *
 * @param  {arguments} the objects to merge
 * @return {object}    the merged object
 */
function extend() {
  var target = {};

  for (var i = 0; i < arguments.length; i++) {
    var source = arguments[i];

    for (var name in source) {
      if (source.hasOwnProperty(name)) {
        target[name] = source[name];
      }
    }
  }

  return target;
}

/**
 * Iterates over a dependency list from package.json and loads their tasks
 *
 * @param {object} loadTasks    the grunt method to call with the module name
 * @param {object} dependencies the dependency list from package.json to parse
 * @return {null}
 */
function loadNpmTasks(loadTasks, dependencies) {
  for (var module in dependencies) {
    if (module.indexOf('grunt-') === 0 && module !== 'grunt-cli') {
      loadTasks(module);
    }
  }
}