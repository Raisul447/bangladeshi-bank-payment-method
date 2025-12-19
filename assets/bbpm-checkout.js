(function($) {
    'use strict';

    var BBPM = {
        gatewayId: 'bangladeshi_bank_payment',
        // BBPM_Data object (containing fileSizeError and fileInputSelector) is automatically available here via wp_localize_script
        init: function() {
            this.bindEvents();
            this.handleInitialPaymentMethod();
        },
        bindEvents: function() {
            $(document.body).on('change', 'input[name="payment_method"]', this.handlePaymentMethodChange.bind(this));
            $('form.checkout').on('submit', this.handleFormSubmit.bind(this));
            
            // NEW: Bind file validation event using the localized selector
            if (typeof BBPM_Data !== 'undefined' && BBPM_Data.fileInputSelector) {
                 $(document.body).on('change', BBPM_Data.fileInputSelector, this.handleFileValidation.bind(this));
            }
        },
        handleInitialPaymentMethod: function() {
            this.toggleFormAttributes();
        },
        handlePaymentMethodChange: function() {
            this.toggleFormAttributes();
        },
        handleFileValidation: function(e) {
            const file = e.target.files[0];
            const maxSizeBytes = 1048576;

            if (file && file.size > maxSizeBytes) {
                // Use the localized error message
                alert(BBPM_Data.fileSizeError);
                e.target.value = ""; // Clear the file input
            }
        },
        toggleFormAttributes: function() {
            var form = $('form.checkout');
            if (!form.length) return;

            var selected = $('input[name="payment_method"]:checked').val();
            if (selected === this.gatewayId) {
                form.attr('enctype', 'multipart/form-data');
                // Original Working Code Logic to disable WC AJAX
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
