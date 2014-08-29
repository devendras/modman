requirejs.config({

  baseUrl: '/assets/js/',

  paths: {
    backbone: '../vendor/backbone/backbone',
    jquery: '../vendor/jquery/dist/jquery',
    underscore: '../vendor/underscore/underscore',
    velocity: '../vendor/velocity/jquery.velocity',
    facebook: '//connect.facebook.net/en_US/all',
    flexslider: '../vendor/flexslider-scss/jquery.flexslider',
    affix: '../vendor/twbs-bootstrap-sass/assets/javascripts/bootstrap/affix',
    collapse: '../vendor/twbs-bootstrap-sass/assets/javascripts/bootstrap/collapse',
    transition: '../vendor/twbs-bootstrap-sass/assets/javascripts/bootstrap/transition',
    select2: '../vendor/select2/select2',
    hammer: '../vendor/jquery-hammerjs/jquery.hammer-full',
    page: 'views/page'
  }, 
 
  shim: {
    jquery: {
      exports: '$'
    },
    underscore: {
      exports: '_'
    },
    backbone: {
      deps: ['underscore', 'jquery'],
      exports: 'Backbone'
    },
    facebook : {
      exports: 'FB'
    },
    velocity: {
      deps: ['jquery']
    },
    flexslider: {
      deps: ['jquery']
    },
    affix: {
      deps: ['jquery']
    },
    collapse: {
      deps: ['jquery', 'transition']
    },
    transition: {
      deps: ['jquery']
    },
    select2: {
      deps: ['jquery']
    },
    hammer: {
      deps: ['jquery']
    },
    page: {
      deps: [
        'lib/eventBus',
        'lib/facebook',
        'lib/scrollListener',
        'lib/scrollTo',
        'views/grow',
        'views/popover',
        'views/collapse',
        'views/productCarousel',
        'views/rateProduct',
        'views/productSidebar',
        'views/productRecCarousel',
        'views/promotionScroll'
      ],
      exports: 'page'
    }

  }

});


/*
  Console Shim
*/
if(typeof(console) === 'undefined') { var console = {}; console.log = console.error = console.info = console.debug = console.warn = console.trace = console.dir = console.dirxml = console.group = console.groupEnd = console.time = console.timeEnd = console.assert = console.profile = function() {}; }


/*
  Bootstrap the application
*/

//
require(['jquery', 'underscore', 'backbone', 'page'], function($, _, Backbone, PageView) {
  var page = new PageView({el: $('body')});
});
