define([
  'jquery',
  'underscore',
  'backbone',
  'flexslider'
],
function($, _, Backbone) {

    var ProductRecCarousel = Backbone.View.extend({

      initialize: function(){
        this.$el.flexslider({
          animation: "slide",
          animationLoop: false,
          controlNav: false,
          move: 1,
          itemWidth: 318,
          itemMargin: 2,
          minItems: 2,
          maxItems: 4,
          prevText: '',
          nextText: ''
        }).flexslider("stop");
      }

    });

    return ProductRecCarousel;
});