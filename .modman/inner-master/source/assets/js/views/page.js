define([
  'jquery',
  'underscore',
  'backbone',
  'views/base'
],
function($, _, Backbone, BaseView) {

  var PageView = BaseView.extend({

    events: {
      'click a[rel="external"]': 'openExternalWindow'
    },

    initialize: function() {
      this.shimRequestAnimationFrame();
      this.initSubViews();
    },


    /*
      Initialize sub-views which are read via
      a 'data-controller' attribute on an element.
    */
    initSubViews: function() {
      var that = this;
      this.$el.find('[data-controller]').each(function() {
        var $wrappingEl = $(this);
        var controllers = $wrappingEl.data('controller').split(' ');
        _.each(controllers, function(controller){
          var viewPath = 'views/' + controller;
          var el = $wrappingEl;
          require([viewPath], function(View) {
            var view = new View({el: el});
          });
        });
      });
    },

  //-----------------------------------------------------------------------------

    openExternalWindow: function(e) {
      window.open($(e.currentTarget).attr('href'));
      e.preventDefault();
    }

  });


  return PageView;

});
