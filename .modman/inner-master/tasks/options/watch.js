/*global module:false*/
module.exports = {
  sass: {
    files: ['<%= appDir %>assets/scss/**/*.scss'],
    tasks: ['compass:dev']
  },
  js: {
    files: ['<%= appDir %>assets/js/**/*.js'],
    tasks: ['jshint:dev']
  }
};