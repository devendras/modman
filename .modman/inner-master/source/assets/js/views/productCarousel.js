define([
  'jquery',
  'underscore',
  'backbone',
  'hammer'
],
function($, _, Backbone) {

    var ProductCarousel = Backbone.View.extend({

      hammer_options : {
        drag: false,
        transform: false
      },

      events: {
        'click .productCarousel__thumbnails--btn' : 'rotateCarousel',
        'click .productCarousel__indicators--item--button' : 'rotateCarousel'
      },

      initialize: function(){
        this.$el.find('.productCarousel__feature__img').hammer(this.hammer_options).on("swipeleft", $.proxy(this.goToNext, this) );
        this.$el.find('.productCarousel__feature__img').hammer(this.hammer_options).on("swiperight", $.proxy(this.goToPrev, this) );
      },

      goToNext: function(event){
        var $this = $(event.target);
        // debugger;
        if(!$this.is(':last-child')){
          // debugger;
          this.$el.find('.productCarousel__thumbnails').find('.active').removeClass('active').next().addClass('active');
          this.$el.find('.productCarousel__indicators--item.active').removeClass('active').next().addClass('active');
          $this.removeClass('active').next().addClass('active');
        }
      },

      goToPrev: function(event){
        var $this = $(event.target);
        // debugger;
        if(!$this.is(':first-child')){
          this.$el.find('.productCarousel__thumbnails').find('.active').removeClass('active').prev().addClass('active');
          this.$el.find('.productCarousel__indicators--item.active').removeClass('active').prev().addClass('active');
          $this.removeClass('active').prev().addClass('active');
        }
      },

      rotateCarousel: function(event){
        var $this = $(event.currentTarget);
        var index = $this.parent().index();
        console.log(index);
        
        if(!$this.hasClass('active')){
          this.$el.find('.productCarousel__thumbnails__item').removeClass('active');
          this.$el.find('.productCarousel__indicators--item').removeClass('active');
          this.$el.find('.productCarousel__feature').find('img').removeClass('active')
          
          this.$el.find('.productCarousel__thumbnails__item').eq(index).addClass('active');
          this.$el.find('.productCarousel__indicators--item').eq(index).addClass('active');
          this.$el.find('.productCarousel__feature').find('img').eq(index).addClass('active');

        }
      }

    });

    return ProductCarousel;
})