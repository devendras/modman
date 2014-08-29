define([
  'jquery',
  'underscore',
  'backbone',
  'velocity'
],
function($, _, Backbone) {

  var Popover = Backbone.View.extend({

    events: {
      'mouseenter' : 'invertPopover'
    },

    invertPopover: function(){
      var $item = this.$el.parent();
      
      if( 
        !this.isFirst($item) && 
        this.isLastInRow($item)
      ){
        this.$el.addClass('invert');
      }
    },

    isFirst: function($item){
      return $item.index() === 0
    },

    isFirstInRow: function($item){
      if($item.prev().length){
        return $item.offset().top !== $item.prev().offset().top;
      }else{
        return true
      }
    },

    isLast: function($item){
      return $item.last().index() === --($item.length);
    },

    isLastInRow: function($item){
      if($item.next().length){
        return $item.offset().top !== $item.next().offset().top;
      }else{
        return true
      }
      
    }

  });

  return Popover;

});
