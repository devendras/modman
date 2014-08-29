define([
  'jquery',
  'underscore',
  'backbone'
],
function($, _, Backbone) {

  var RateProduct = Backbone.View.extend({

    events: {
        'click .icon__heart' : 'getRating'
    },

    getRating: function(event){
      var $heart = $(event.target);
      var index = $heart.index();
      var rating = index + 1;
      
      this.$el.find('.productSidebar__rating__input').val(rating);
      this.render(rating);
    },

    render: function(rating){
      if(rating){
        var $hearts = this.$el.find('.icon__heart');
        $hearts.removeClass('icon__heart--active');
        for(var i = 0; i < rating; i++){
          $hearts.eq(i).addClass('icon__heart--active');
        }
      }
    }

  });

  return RateProduct;

});
