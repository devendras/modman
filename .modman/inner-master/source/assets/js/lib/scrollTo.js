define([
  'jquery',
  'underscore',
  'backbone',
  'velocity'
],
function($, _, Backbone) {

  // The trigger element should have the selector as the href.
  // If the trigger isn't an <a> than use data-target="#selector"

  var ScrollTo = Backbone.View.extend({

    to : function($el){
      $el.trigger('scrollStart');

      var target = $el.attr('href') || $el.data('target');
      var $target = $(target).first();

      $el.trigger('scrolling');
      $target.velocity('scroll', 500, function(){
        $el.trigger('scrollEnd');
      });
    }

  });

  return ScrollTo;

});
