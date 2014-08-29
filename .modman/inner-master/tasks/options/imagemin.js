/*global module:false*/
module.exports = {
  target: {
    options: {
      optimizationLevel: 7,
      progressive: true
    },
    files: [{
      // Set to true to enable the following options…
      expand: true,
      // cwd is 'current working directory'
      cwd: '<%= buildDir %>assets/img/',
      src: ['**/*.{png,jpg,gif}'],
      // Could also match cwd line above. i.e. project-directory/img/
      dest: '<%= buildDir %>assets/img/'
    }]
  }
};