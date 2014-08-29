/*global module:false*/
module.exports = {
  build_core:{
    options: {
      mangle: {
        except: ['jQuery', 'Backbone', '$', '_']
      },
      compress: {
        drop_console: true
      }
    },
    files: {
      '<%= buildDir %>assets/js/main.min.js' : ["<%= buildDir %>assets/js/main.js"]
    }
  },
  build_selectivizr:{
    options: {
      compress: {
        drop_console: true,
        dead_code: false
      },
      preserveComments: 'some'
    },
    files: {
      '<%= buildDir %>assets/vendor/selectivizr/selectivizr.min.js' : ["<%= buildDir %>assets/vendor/selectivizr/selectivizr.js"]
    }
  }
};