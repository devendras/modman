/*global module:false*/
module.exports = {
  build: {
    tasks: ['uglify:build_core', 'imagemin','cssmin', 'usemin']
  },
  dev: {
    tasks: ['watch:sass', 'middleman:server'],
    options: {
      logConcurrentOutput: true
    }
  }
};