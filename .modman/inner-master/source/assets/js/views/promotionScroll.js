define([
  'jquery',
  'underscore',
  'backbone',
  'velocity'
],
function($, _, Backbone) {

    var PromotionScroll = Backbone.View.extend({

        initialize: function(){
          this.jumpTo();
        },

        jumpTo: function(){
          this.$el.show();

          if(!this.toScrollOrNotToScroll()){
            var scrollHeight = this.$el.height() + parseInt(this.$el.css('padding-top'), 10 ) + parseInt(this.$el.css('padding-bottom'), 10 );
            $(window).scrollTop(scrollHeight);
          }
        },

        toScrollOrNotToScroll: function(){
          return $('body').hasClass('index');
        }

    });

    return PromotionScroll;

});
