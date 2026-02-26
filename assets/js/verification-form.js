/**
 * Serial Validator - Frontend Verification Form
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        
        // Auto-fill code from URL parameter
        const urlParams = new URLSearchParams(window.location.search);
        const codeParam = urlParams.get('code');
        
        if (codeParam) {
            $('#sv-code').val(codeParam);
        }
        
        // Form submission
        $('#sv-verification-form').on('submit', function(e) {
            e.preventDefault();
            
            const $form = $(this);
            const $submitBtn = $form.find('.sv-submit-btn');
            const $result = $('#sv-result');
            const $loading = $('#sv-loading');
            
            // Gather form data
            const formData = {
                action: 'sv_verify_code',
                nonce: svData.nonce,
                code: $('#sv-code').val(),
                name: $('#sv-name').val(),
                email: $('#sv-email').val(),
                phone: $('#sv-phone').val()
            };
            
            // Add reCAPTCHA response if enabled
            if (svData.recaptchaEnabled && typeof grecaptcha !== 'undefined') {
                formData.recaptcha_response = grecaptcha.getResponse();
            }
            
            // Show loading state
            $submitBtn.prop('disabled', true);
            $result.hide();
            $loading.show();
            
            // AJAX request
            $.ajax({
                url: svData.ajaxurl,
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    $loading.hide();
                    
                    if (response.success) {
                        // Valid code
                        showResult('success', response.data);
                    } else {
                        // Invalid, used, blocked, or error
                        showResult('error', response.data);
                    }
                    
                    // Reset reCAPTCHA if enabled
                    if (svData.recaptchaEnabled && typeof grecaptcha !== 'undefined') {
                        grecaptcha.reset();
                    }
                },
                error: function() {
                    $loading.hide();
                    showResult('error', {
                        message: 'An error occurred. Please try again.'
                    });
                },
                complete: function() {
                    $submitBtn.prop('disabled', false);
                }
            });
        });
        
        // Show result
        function showResult(type, data) {
            const $result = $('#sv-result');
            const $wrapper = $('.sv-verification-form-wrapper');
            
            // Clear previous result
            $result.removeClass('sv-success sv-error sv-warning').empty();
            
            // Build result HTML
            let html = '<div class="sv-result-title">';
            
            if (type === 'success') {
                $result.addClass('sv-success');
                html += '✓ ' + (data.message || 'Verification Successful');
            } else {
                if (data.status === 'used') {
                    $result.addClass('sv-warning');
                    html += '⚠ ' + (data.message || 'Code Already Used');
                } else {
                    $result.addClass('sv-error');
                    html += '✗ ' + (data.message || 'Verification Failed');
                }
            }
            
            html += '</div>';
            
            // Add details for valid codes
            if (type === 'success' && data.product_name) {
                html += '<div class="sv-result-details">';
                html += '<div class="sv-result-detail"><strong>Product:</strong> ' + escapeHtml(data.product_name) + '</div>';
                
                if (data.batch) {
                    html += '<div class="sv-result-detail"><strong>Batch:</strong> ' + escapeHtml(data.batch) + '</div>';
                }
                
                if (data.warranty_info) {
                    html += '<div class="sv-result-detail"><strong>Warranty:</strong> ' + escapeHtml(data.warranty_info) + '</div>';
                }
                
                html += '</div>';
            }
            
            // Add first verification date for used codes
            if (data.status === 'used' && data.first_verified) {
                html += '<div class="sv-result-details">';
                html += '<div class="sv-result-detail"><strong>First Verified:</strong> ' + escapeHtml(data.first_verified) + '</div>';
                html += '</div>';
            }
            
            $result.html(html).show();
            
            // Scroll to result
            $('html, body').animate({
                scrollTop: $result.offset().top - 100
            }, 500);
        }
        
        // Escape HTML to prevent XSS
        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }
    });

})(jQuery);
