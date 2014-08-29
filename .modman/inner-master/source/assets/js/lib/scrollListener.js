define([
  'jquery',
  'underscore',
  'backbone'
],
function($, _, Backbone) {

    var ScrollListener = Backbone.View.extend({

      callbacks: [],

      didScroll: false,

      lastScrollEvent: null,

      initialize: function(){
        $(window).scroll( _.bind(this._setScrollStatus, this) );
        this._animationLoop();
      },

      _animationLoop: function(){
        window.requestAnimationFrame( _.bind(this._animationLoop, this) );
        this._fireCallbacks();
      },

      _setScrollStatus: function(event){
        this.didScroll = true;
        this.lastScrollEvent = event;
      },

      _fireCallbacks: function(){
        if(this.didScroll){
          var l = this.callbacks.length;

          for(var i = 0; i < l; i++){
            this.callbacks[i](this.lastScrollEvent);
          }

          this._resetScrollCache();
        }
      },

      _resetScrollCache: function(){
        this.didScroll = false;
        this.lastScrollEvent = null;
      },

      // public method to add callbacks to scroll
      // events.
      do: function(callback){
        if(typeof callback == 'function'){
          this.callbacks.push(callback);
        }
        else{
          throw 'Argument must be a function.';
        }
      }

    });

    return new ScrollListener();
})