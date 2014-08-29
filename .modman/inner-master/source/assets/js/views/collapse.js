define([
  'jquery',
  'underscore',
  'backbone',
  'lib/eventBus',
  'velocity',
  'collapse'
],
function($, _, Backbone, Eventbus) {

  var Collapse = Backbone.View.extend({

    initialize: function(){
      this.$el.find('.collapse').collapse({
        toggle: false
      });

      this.$el.find('.collapse').on('shown.bs.collapse hidden.bs.collapse', function(){
        Eventbus.trigger('collapse.changed');
      });

      this.$el.find('.collapse').on('show.bs.collapse', $.proxy(function(event){
        $(event.target).parents('.collapse__panel').addClass('open');
      }, this));

      this.$el.find('.collapse').on('hide.bs.collapse', $.proxy(function(event){
        $(event.target).parents('.collapse__panel').removeClass('open');
      }, this));
    }

  });

  return Collapse;
});