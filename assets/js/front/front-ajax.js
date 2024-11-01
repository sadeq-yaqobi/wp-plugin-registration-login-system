/**
 * Login form handler using jQuery AJAX
 * Handles user authentication and provides feedback to the user
 */
jQuery(document).ready(function ($) {
    // Handle login form submission
    $('#lr_login').on('submit', function (e) {
        // Prevent default form submission
        e.preventDefault();

        // Get form field values
        let email = $('#email').val();
        let password = $('#password').val();
        let rememberMe = $('#remember_me').prop('checked');  // Get checkbox state (true/false)

        // AJAX request to WordPress backend
        $.ajax({
            url: lr_ajax.ajaxurl,      // WordPress AJAX URL from localized script
            type: 'post',              // HTTP method
            dataType: 'json',          // Expected response type
            data: {                    // Data to be sent to server
                action: 'lr_auth_login',    // WordPress AJAX action hook
                email: email,               // User email
                password: password,         // User password
                rememberMe: rememberMe,     // Remember me preference
                _nonce: lr_ajax._nonce       // Security nonce
            },

            // Before sending the request
            beforeSend: function () {
                // Show loading indicator
                $('#rl_loading').html('<div class="loader"></div>')
            },

            // Handle successful response
            success: function (response) {
                if (response.success) {
                    // Show success message with animation
                    $('#login_message_handler')
                        .attr('class', 'alert alert-success')
                        .text(response.message)
                        .hide()
                        .fadeIn('slow', 'swing');

                    // Redirect after 2 seconds
                    setTimeout(function () {
                        window.location.href = document.documentURI;
                    }, 2000);
                }
            },

            // Handle error response
            error: function (error) {
                if (error.error) {
                    // Show error message with animation
                    $('#login_message_handler')
                        .attr('class', 'alert alert-danger')
                        .text(error.responseJSON.message)
                        .hide()
                        .fadeIn('slow', 'swing');

                    // Reset loading button text
                    $('#rl_loading').text('ورود به حساب')
                }
            },

            // After request completes (success or failure)
            complete: function () {
                // Can add cleanup code here if needed
            },
        });
    });
});


jQuery(document).ready(function ($) {
    // Cache DOM elements
    const $phoneInput = $('#phone-input');
    const $phoneError = $('#phone-error-message');
    const $submitBtn = $('#ls_submit_button');
    const $otpInputs = $('.otp-input-bar');
    const $otpError = $('#error_message');
    const $phoneWrap = $('.phone-number-wrap');
    const $verificationWrap = $('.verification-code-wrap');
    setupSubmitButton();

    /**
     * Submit button handling
     */
    function setupSubmitButton(message) {
        $submitBtn.on('click', function (e) {
            e.preventDefault();

            if ($submitBtn.hasClass('send-phone')) {

                let $userPhoneNumber = $phoneInput.val().replace(/\s/g, '')

                $.ajax({
                    url: lr_ajax.ajaxurl,      // WordPress AJAX URL from localized script
                    type: 'post',              // HTTP method
                    dataType: 'json',          // Expected response type
                    data: {
                        action: 'lr_auth_send_sms_verification_code',
                        userPhone: $userPhoneNumber,
                        _nonce: lr_ajax._nonce
                    },

                    beforeSend: function () {
                        // Actions to perform before sending the AJAX request
                    },
                    success: function (response) {
                        // Actions to handle successful response --- to get success message use this template: response.message
                        if (response.success) {
                            const phoneNumber = response.phone_number;
                            console.log(phoneNumber); // Log the phone number
                            $phoneInput.val(phoneNumber);
                            // Switch to OTP view
                            $phoneWrap.hide();
                            $verificationWrap.fadeIn('slow');
                            $otpInputs.first().focus();
                            $submitBtn.prop('disabled', true)
                                .removeClass('active')
                                .text('اعتبارسنجی کد');
                            $submitBtn.removeClass('send-phone').addClass('send-verification-code')
                        }

                    },
                    error: function (error) {
                        if (error.error) {
                            // Error handling based on specific error conditions--- to get error message use this template: error.responseJSON.message
                            alert(error.responseJSON.message)
                        }
                    },
                    complete: function () {
                        // Actions to perform after the AJAX request completes (regardless of success or failure)
                    },
                });


            }
            if ($submitBtn.hasClass('send-verification-code')) {
                // Handle OTP submission

                const verificationCodeValue = Array.from($otpInputs)
                    .map(input => input.value)
                    .join('');
                const validPhoneNumber = $phoneInput.val();

                $.ajax({
                    url: lr_ajax.ajaxurl,      // WordPress AJAX URL from localized script
                    type: 'post',              // HTTP method
                    dataType: 'json',          // Expected response type
                    data: {
                        action: 'lr_auth_validate_verification_code',
                        verificationCodeValue: verificationCodeValue,
                        validPhoneNumber:validPhoneNumber,
                        _nonce: lr_ajax._nonce
                    },

                    beforeSend: function () {
                        // Actions to perform before sending the AJAX request
                    },
                    success: function (response) {
                        // Actions to handle successful response --- to get success message use this template: response.message
                        // Switch to OTP view


                    },
                    error: function (error) {
                        if (error.error) {
                            // Error handling based on specific error conditions--- to get error message use this template: error.responseJSON.message
                            alert(error.responseJSON.message)
                        }
                    },
                    complete: function () {
                        // Actions to perform after the AJAX request completes (regardless of success or failure)
                    },
                });

            }
        });
    }

});