jQuery(function($) {
    'use strict';

    // Add webhook field
    $('#wcdn-add-webhook').on('click', function(e) {
        e.preventDefault();
        const template = $('#wcdn-webhook-template').html();
        $('#wcdn-webhooks').append(template);
    });

    // Remove webhook field
    $(document).on('click', '.wcdn-remove-webhook', function(e) {
        e.preventDefault();
        $(this).closest('.wcdn-webhook-row').remove();
    });

    // Test webhook
    $('#wcdn-test-webhook').on('click', function(e) {
        e.preventDefault();
        const $button = $(this);
        
        $button.prop('disabled', true);
        
        $.ajax({
            url: wcdnAdmin.ajaxUrl,
            method: 'POST',
            data: {
                action: 'wcdn_test_webhook',
                _ajax_nonce: wcdnAdmin.nonce
            },
            success: function(response) {
                alert(response.success ? wcdnAdmin.i18n.testSuccess : wcdnAdmin.i18n.testError);
            },
            error: function() {
                alert(wcdnAdmin.i18n.testError);
            },
            complete: function() {
                $button.prop('disabled', false);
            }
        });
    });
});