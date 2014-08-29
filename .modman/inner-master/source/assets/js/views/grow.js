define([
  'jquery',
  'underscore',
  'backbone',
  'lib/eventBus',
  'velocity'
],
function($, _, Backbone, Eventbus) {

  var Grow = Backbone.View.extend({

    originalMaxHeight: 0,

    events: {
      'click [data-grow="toggle"]': 'toggleHeight'
    },

    initialize: function(){
      this.setMaxHeightCache();
      this.setInitialState();

      var throttledSetMaxHeightCache = _.throttle( _.bind(this.setMaxHeightCache, this), 16);
      $(window).on('resize', throttledSetMaxHeightCache);

      _.bind(this.toggleHeight, this);
    },

    toggleHeight: function(event){
      if(this.$el.data('grown')){
        this.shrink();
      }
      else{
        this.grow(event);
      }
    },

    grow : function(event){
      var $children = this.$el.children().not(event.target);
      var tallestHeight = 0;
      $children.each(function(i, el){
        var $el = $(el);
        tallestHeight = tallestHeight < $el.height() + parseInt($el.css('paddingTop'), 10) + parseInt($el.css('paddingBottom'), 10) ? $el.height() + parseInt($el.css('paddingTop'), 10) + parseInt($el.css('paddingBottom'), 10) : tallestHeight;
      });

      this.$el.css({'max-height' : tallestHeight }).addClass('grown').data({ 'grown' : true });
    },

    shrink: function(){
      this.$el.css({'max-height' : this.originalMaxHeight }).removeClass('grown').data({ 'grown' : false });
    },

    getMaxHeight: function(){
      return this.$el.css('max-height');
    },

    setMaxHeightCache: function(){
      this.originalMaxHeight = this.getMaxHeight();
    },

    setInitialState: function(){
      if(this.$el.hasClass('grown')){
        this.$el.data({ 'grown' : true });
      }
      else{
        this.$el.data({ 'grown' : false });
      }
    }


  });

  return Grow;
});