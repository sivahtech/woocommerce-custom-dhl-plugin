jQuery(document).ready(function($) {
    // Listen for change events on address fields
    $('body').on('change', 'input#billing_country, input#billing_state, input#billing_postcode, input#billing_city', function() {
        updateDHLExpressShippingRates();
    });

    // Function to update DHL Express shipping rates dynamically
    function updateDHLExpressShippingRates() {
        var data = {
            action: 'update_custom_dhl_express_shipping_rates',
            security: custom_dhl_express_shipping_params.security,
            country: $('input#billing_country').val(),
            state: $('input#billing_state').val(),
            postcode: $('input#billing_postcode').val(),
            city: $('input#billing_city').val()
        };

        $.ajax({
            type: 'POST',
            url: custom_dhl_express_shipping_params.ajax_url,
            data: data,
            success: function(response) {
                $('body').trigger('update_checkout');
            },
            error: function(error) {
                console.log(error);
            }
        });
    }
});