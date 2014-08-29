/**
 * 
 */
var Omxdiscount = Class.create();
Omxdiscount.prototype = {
		initialize: function(form, saveUrl) {
			this.form = form;
			if($(this.form)) {
				 $(this.form).observe('submit', function(event){this.save();Event.stop(event);}.bind(this));
			}
			this.saveUrl = saveUrl;
	        this.onSave = this.nextStep.bindAsEventListener(this);
	        this.onComplete = this.resetLoadWaiting.bindAsEventListener(this);
		},
		save : function() {
           if (checkout.loadWaiting!=false) return;
           checkout.setLoadWaiting('omxdiscount');
           var request = new Ajax.Request(
                this.saveUrl,
                {
                    method: 'post',
                    onComplete: this.onComplete,
                    onSuccess: this.onSave,
                    onFailure: checkout.ajaxFailure.bind(checkout),
                    parameters: Form.serialize(this.form)
                }
            );
	    },
		resetLoadWaiting: function(transport){
			checkout.setLoadWaiting(false);
		},
		nextStep: function (transport) {
			console.log(transport);
			if (transport && transport.responseText){
	            try{
	                response = eval('(' + transport.responseText + ')');
	            }
	            catch (e) {
	                response = {};
	            }
	        }
			if (response.error) {
	            alert(response.message);
	            return false;
	        }

	        if (response.update_section) {
	        	$('checkout-'+response.update_section.name+'-load').update(response.update_section.html);
	        }

	        payment.initWhatIsCvvListeners();
	        
	        if(response.omxdiscount_html) {
	        	$('checkout-omxdiscount-load').update(response.omxdiscount_html);
	        }
	        if (response.goto_section) {
	        	checkout.gotoSection(response.goto_section);
	            checkout.reloadProgressBlock();
	            return;
	        }

	        if (response.payment_methods_html) {
	            $('checkout-payment-method-load').update(response.payment_methods_html);
	        }
	        //checkout.setOmxdiscount(); extend checkout class and set progress block here
	   
		}
};
