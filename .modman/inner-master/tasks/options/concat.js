/*global module:false*/
module.exports = {
  options: {
      separator: ';',
    },
    dist: {
      src: ['<%= buildDir %>assets/vendor/almond/almond.js', '<%= buildDir %>assets/js/main.js'],
      dest: '<%= buildDir %>assets/js/main.js'
    }
};