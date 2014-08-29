/*global module:false*/
module.exports = {
  dist: {
    options: {
      sassDir: '<%= appDir %>assets/scss',
      cssDir: '<%= appDir %>assets/css',
      config: 'compass/config.rb', 
      environment: 'production',
      bundleExec: true
    }
  },
  dev: {
    options: {
      sassDir: '<%= appDir %>assets/scss',
      cssDir: '<%= appDir %>assets/css',
      config: 'compass/config.rb', 
      environment: 'development',
      bundleExec: true
    }
  }
};