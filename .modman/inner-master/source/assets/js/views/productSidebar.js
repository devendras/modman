define([
  'jquery',
  'underscore',
  'backbone',
  'lib/eventBus',
  'lib/scrollTo',
  'affix'
],
function($, _, Backbone, EventBus, Scroll) {

    var ProductSidebar = Backbone.View.extend({
      
      events: {
        'click .productSidebar__ratings__link' : 'scrollToReviews',
        'keypress .productSidebar__input--quantity' : 'restrictInput'
      },

      TOP_OFFSET : null,

      initialize: function(){
        var that = this;
        this.TOP_OFFSET = this.$el.offset().top;
        
        this.$el.affix({
          offset: {
            top: this.TOP_OFFSET,
            bottom: this.calculateBottomOffset()
          }
        });

        this.$el.on('affix.bs.affix', _.bind(this.updateSidebarMetrics, this) );
        $( window ).resize(_.throttle( _.bind(this.updateSidebarMetrics, this), 16));
        EventBus.on('collapse.changed', function(){
          that.$el.trigger('scroll.bs.affix.data-api');
        });
      },

      calculateBottomOffset: function () {
        var topOffset = $('[data-affix-stop="sidebar"]').offset().top;
        var paddingTop = parseInt(this.$el.css('paddingTop'), 10);
        var paddingBottom = parseInt(this.$el.css('paddingBottom'), 10);

        return (this.bottom = $(document).height() - topOffset + paddingTop + paddingBottom + 2);
      },

      updateSidebarWidth: function(){
        var parentWidth = this.$el.parent().width();
        var paddingLeft = parseInt(this.$el.css('paddingLeft'), 10);
        var paddingRight = parseInt(this.$el.css('paddingRight'), 10);
        var borderRightWidth = parseInt(this.$el.css('borderRightWidth'), 10);
        var borderLeftWidth = parseInt(this.$el.css('borderLeftWidth'), 10);

        this.$el.width(parentWidth - paddingLeft - paddingRight - borderRightWidth - borderLeftWidth);
      },

      updateSidebarOffsets: function(){
        var bottomOffset = this.calculateBottomOffset();
        var offsets = {
          top: this.TOP_OFFSET,
          bottom: bottomOffset
        }

        this.$el.data('bs.affix').options.offset = offsets;
      },

      updateSidebarMetrics: function(){
        this.updateSidebarOffsets();
        this.updateSidebarWidth();
      },

      scrollToReviews: function(event){
        event.preventDefault();
        var scroll = new Scroll;
        var $evtTarget = $(event.target);
        scroll.to($evtTarget);
        $($evtTarget.attr('href')).find('.panel-collapse').collapse('show');
      },

      restrictInput : function(evt){
        var charCode = evt.which || evt.keyCode;
        if(charCode === 8 || charCode === 37 || charCode === 39){
          return true;
        }
        else if( charCode < 48 || charCode > 57 ) {
          return false;
        }
        else{
          return true;
        }
      }

    });

    return ProductSidebar;
});