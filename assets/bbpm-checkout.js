(function($) {
    'use strict';

    $(document).on('click', '#place_order', function(e) {
        var selected = $('input[name="payment_method"]:checked').val();
        
        if (selected === BBPM_Data.gatewayId) {
            var fileInput = $(BBPM_Data.fileInputSelector);
            
            if (fileInput.length > 0 && fileInput[0].files.length === 0) {
                alert("Please upload your payment receipt screenshot.");
                e.preventDefault();
                return false;
            }

            // Forced AJAX Disable for file upload compatibility
            $('form.checkout').off('submit'); 
            $('form.checkout').removeClass('processing').unblock();
            $('form.checkout').attr('enctype', 'multipart/form-data');
            
            return true; 
        }
    });

    // File size validation logic restored
    $(document.body).on('change', BBPM_Data.fileInputSelector, function(e) {
        const file = e.target.files[0];
        if (file && file.size > 5242880) { // 5MB
            alert(BBPM_Data.fileSizeError);
            e.target.value = "";
        }
    });

})(jQuery);
