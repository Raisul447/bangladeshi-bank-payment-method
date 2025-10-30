(function($) {
    'use strict';

    var BBPM = {
        gatewayId: 'bangladeshi_bank_payment',
        init: function() {
            this.bindEvents();
            this.handleInitialPaymentMethod();
        },
        bindEvents: function() {
            $(document.body).on('change', 'input[name="payment_method"]', this.handlePaymentMethodChange.bind(this));
            $('form.checkout').on('submit', this.handleFormSubmit.bind(this));
        },
        handleInitialPaymentMethod: function() {
            this.toggleFormAttributes();
        },
        handlePaymentMethodChange: function() {
            this.toggleFormAttributes();
        },
        toggleFormAttributes: function() {
            var form = $('form.checkout');
            if (!form.length) return;

            var selected = $('input[name="payment_method"]:checked').val();
            if (selected === this.gatewayId) {
                form.attr('enctype', 'multipart/form-data');
                $(document.body).off('submit', 'form.checkout');
                $(document.body).off('checkout_place_order');
                $(document.body).off('checkout_form_data');
            } else {
                form.removeAttr('enctype');
            }
        },
        handleFormSubmit: function(e) {
            var selected = $('input[name="payment_method"]:checked').val();
            if (selected === this.gatewayId) {
                e.stopImmediatePropagation();
            }
        }
    };

    if ($('form.checkout').length > 0) {
        BBPM.init();
    }

})(jQuery);