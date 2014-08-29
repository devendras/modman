/*global module:false*/
module.exports = {
  minify: {
    expand: true,
    cwd: '<%= buildDir %>assets/css/',
    src: ['*.css', '!*.min.css'],
    dest: '<%= buildDir %>assets/css/',
    ext: '.min.css'
  }
};
