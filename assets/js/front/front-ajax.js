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
                $('#lr_loading').html('<div class="loader"></div>')
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
                    $('#lr_loading').text('ورود به حساب')
                }
            },

            // After request completes (success or failure)
            complete: function () {
                // Can add cleanup code here if needed
            },
        });
    });
});

//registration ajax handler
jQuery(document).ready(function ($) {
    // Cache DOM elements
    const $phoneInput = $('#phone-input');
    const $phoneError = $('#phone-error-message');
    const $submitBtn = $('#ls_submit_button');
    const $otpInputs = $('.otp-input-bar');
    const $otpError = $('#error_message');
    const $phoneWrap = $('.phone-number-wrap');
    const $verificationWrap = $('.verification-code-wrap');
    const $userDataWrapper = $('.user-data-wrapper');
    const $resendCode = $('#resend-code');
    const $timer = $('#timer');
    let countdownInterval;
    setupSubmitButton();
    setupResendEvents();

    /**
     * Timer handling functions
     */
    function startTimer() {
        let timeLeft = 180; // 3 minutes in seconds
        $timer.removeClass('d-none').addClass('otp-active');
        $resendCode.removeClass('d-none');

        clearInterval(countdownInterval);

        countdownInterval = setInterval(() => {
            timeLeft--;

            if (timeLeft < 0) {
                clearInterval(countdownInterval);
                $timer.addClass('otp-disabled');
                $resendCode.addClass('otp-active');
                return;
            }

            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;

            // Convert to Persian numerals
            const persianTime = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`
                .replace(/[0-9]/g, d => String.fromCharCode(0x06F0 + parseInt(d)));

            $timer.text(persianTime);
        }, 1000);
    }

    /**
     * resend otp code handling functions
     */
    function setupResendEvents() {
        $resendCode.on('click', function (e) {
            e.preventDefault();
            // Reset OTP inputs
            $otpInputs.val('').prop('disabled', true);
            $otpInputs.first().prop('disabled', false);
            $otpError.fadeOut('slow');

            let userPhoneNumber = $phoneInput.val();
            $.ajax({
                url: lr_ajax.ajaxurl,      // WordPress AJAX URL from localized script
                type: 'post',              // HTTP method
                dataType: 'json',          // Expected response type
                data: {
                    action: 'lr_auth_send_sms_verification_code',
                    userPhone: userPhoneNumber,
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

                        // Switch to OTP view
                        $otpInputs.first().focus();
                        $submitBtn.prop('disabled', true)
                            .removeClass('active')
                            .text('اعتبارسنجی کد');
                        startTimer();
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
            // Restart timer
            startTimer();

            // Here you would typically make an API call to resend the OTP
            // handleResendOTP();
        });
    }

    /**
     * Submit button handling
     */
    function setupSubmitButton(message) {
        $submitBtn.on('click', function (e) {
            e.preventDefault();

            if ($submitBtn.hasClass('send-phone')) {

                let $userPhone = $phoneInput.val().replace(/\s/g, '')

                $.ajax({
                    url: lr_ajax.ajaxurl,      // WordPress AJAX URL from localized script
                    type: 'post',              // HTTP method
                    dataType: 'json',          // Expected response type
                    data: {
                        action: 'lr_auth_send_sms_verification_code',
                        userPhone: $userPhone,
                        _nonce: lr_ajax._nonce
                    },

                    beforeSend: function () {
                        // Actions to perform before sending the AJAX request

                        $submitBtn.html('<div class="loader"></div>')

                    },
                    success: function (response) {
                        // Actions to handle successful response --- to get success message use this template: response.message
                        if (response.success) {
                            const phoneNumber = response.phone_number;
                            $phoneInput.val(phoneNumber);
                            // Switch to OTP view
                            $phoneWrap.hide();
                            $verificationWrap.fadeIn('slow');
                            $otpInputs.first().focus();
                            $submitBtn.prop('disabled', true)
                                .removeClass('active')
                                .text('اعتبارسنجی کد');
                            $submitBtn.removeClass('send-phone').addClass('send-verification-code');
                            startTimer();
                            $.toast({
                                text: response.message, // Text that is to be shown in the toast
                                heading: ' ', // Optional heading to be shown on the toast
                                icon: 'success', // Type of toast icon
                                showHideTransition: 'slide', // fade, slide or plain
                                allowToastClose: false, // Boolean value true or false
                                hideAfter: 5000, // false to make it sticky or number representing the miliseconds as time after which toast needs to be hidden
                                stack: 5, // false if there should be only one toast at a time or a number representing the maximum number of toasts to be shown at a time
                                position: 'top-left', // bottom-left or bottom-right or bottom-center or top-left or top-right or top-center or mid-center or an object representing the left, right, top, bottom values


                                textAlign: 'right',  // Text alignment i.e. left, right or center
                                loader: true,  // Whether to show loader or not. True by default
                                loaderBg: '#9EC600',  // Background color of the toast loader
                                beforeShow: function () {
                                }, // will be triggered before the toast is shown
                                afterShown: function () {
                                }, // will be triggered after the toat has been shown
                                beforeHide: function () {
                                }, // will be triggered before the toast gets hidden
                                afterHidden: function () {
                                }  // will be triggered after the toast has been hidden
                            });
                        }

                    },
                    error: function (error) {
                        if (error.error) {
                            // Error handling based on specific error conditions--- to get error message use this template: error.responseJSON.message
                            $.toast({
                                text: error.responseJSON.message, // Text that is to be shown in the toast
                                heading: error.responseJSON.title, // Optional heading to be shown on the toast
                                icon: 'error', // Type of toast icon
                                showHideTransition: 'slide', // fade, slide or plain
                                allowToastClose: false, // Boolean value true or false
                                hideAfter: 5000, // false to make it sticky or number representing the miliseconds as time after which toast needs to be hidden
                                stack: 5, // false if there should be only one toast at a time or a number representing the maximum number of toasts to be shown at a time
                                position: 'top-left', // bottom-left or bottom-right or bottom-center or top-left or top-right or top-center or mid-center or an object representing the left, right, top, bottom values


                                textAlign: 'right',  // Text alignment i.e. left, right or center
                                loader: true,  // Whether to show loader or not. True by default
                                loaderBg: '#9EC600',  // Background color of the toast loader
                                beforeShow: function () {
                                }, // will be triggered before the toast is shown
                                afterShown: function () {
                                }, // will be triggered after the toat has been shown
                                beforeHide: function () {
                                }, // will be triggered before the toast gets hidden
                                afterHidden: function () {
                                }  // will be triggered after the toast has been hidden
                            });
                            $submitBtn.text('دریافت کد')
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
                let userPhone = $phoneInput.val()

                $.ajax({
                    url: lr_ajax.ajaxurl,      // WordPress AJAX URL from localized script
                    type: 'post',              // HTTP method
                    dataType: 'json',          // Expected response type
                    data: {
                        action: 'lr_auth_validate_verification_code',
                        verificationCodeValue: verificationCodeValue,
                        userPhone: userPhone,
                        _nonce: lr_ajax._nonce
                    },

                    beforeSend: function () {
                        // Actions to perform before sending the AJAX request
                        $submitBtn.html('<div class="loader"></div>')
                    },
                    success: function (response) {
                        // Actions to handle successful response --- to get success message use this template: response.message
                        // Switch to name-email-password view
                        if (response.success) {
                            $verificationWrap.html('');
                            $userDataWrapper.fadeIn();
                            $('#display_name').focus();
                            $submitBtn.removeClass('send-verification-code')
                                .addClass('register-btn')
                                .text('ثبت نام')
                                .prop('disabled', true)
                                .removeClass('active');
                        }
                    },
                    error: function (error) {
                        if (error.error) {
                            // Error handling based on specific error conditions--- to get error message use this template: error.responseJSON.message
                            $.toast({
                                text: error.responseJSON.message, // Text that is to be shown in the toast
                                heading: error.responseJSON.title, // Optional heading to be shown on the toast
                                icon: 'error', // Type of toast icon
                                showHideTransition: 'slide', // fade, slide or plain
                                allowToastClose: false, // Boolean value true or false
                                hideAfter: 5000, // false to make it sticky or number representing the miliseconds as time after which toast needs to be hidden
                                stack: 5, // false if there should be only one toast at a time or a number representing the maximum number of toasts to be shown at a time
                                position: 'top-left', // bottom-left or bottom-right or bottom-center or top-left or top-right or top-center or mid-center or an object representing the left, right, top, bottom values


                                textAlign: 'right',  // Text alignment i.e. left, right or center
                                loader: true,  // Whether to show loader or not. True by default
                                loaderBg: '#9EC600',  // Background color of the toast loader
                                beforeShow: function () {
                                }, // will be triggered before the toast is shown
                                afterShown: function () {
                                }, // will be triggered after the toat has been shown
                                beforeHide: function () {
                                }, // will be triggered before the toast gets hidden
                                afterHidden: function () {
                                }  // will be triggered after the toast has been hidden
                            });
                            $submitBtn.text('اعتبارسنجی کد')
                        }
                    },
                    complete: function () {
                        // Actions to perform after the AJAX request completes (regardless of success or failure)

                    },
                });

            }
            if ($submitBtn.hasClass('register-btn')) {
                // Handle registration
                let displayName = $('#display_name').val();
                let email = $('#user_email').val();
                let password = $('#user_password').val();

                $.ajax({
                    url: lr_ajax.ajaxurl,      // WordPress AJAX URL from localized script
                    type: 'post',              // HTTP method
                    dataType: 'json',          // Expected response type
                    data: {
                        action: 'lr_register_user',
                        displayName: displayName,
                        email: email,
                        password: password,
                        _nonce: lr_ajax._nonce
                    },

                    beforeSend: function () {
                        // Actions to perform before sending the AJAX request
                        $submitBtn.html('<div class="loader"></div>')
                    },
                    success: function (response) {
                        // Actions to handle successful response --- to get success message use this template: response.message
                        // Switch to name-email-password view
                        $.toast({
                            text: response.message, // Text that is to be shown in the toast
                            heading: ' ', // Optional heading to be shown on the toast
                            icon: 'success', // Type of toast icon
                            showHideTransition: 'slide', // fade, slide or plain
                            allowToastClose: false, // Boolean value true or false
                            hideAfter: 5000, // false to make it sticky or number representing the miliseconds as time after which toast needs to be hidden
                            stack: 5, // false if there should be only one toast at a time or a number representing the maximum number of toasts to be shown at a time
                            position: 'top-left', // bottom-left or bottom-right or bottom-center or top-left or top-right or top-center or mid-center or an object representing the left, right, top, bottom values


                            textAlign: 'right',  // Text alignment i.e. left, right or center
                            loader: true,  // Whether to show loader or not. True by default
                            loaderBg: '#9EC600',  // Background color of the toast loader
                            beforeShow: function () {
                            }, // will be triggered before the toast is shown
                            afterShown: function () {
                            }, // will be triggered after the toat has been shown
                            beforeHide: function () {
                            }, // will be triggered before the toast gets hidden
                            afterHidden: function () {
                            }  // will be triggered after the toast has been hidden
                        });

                        // redirect to home page after just minutes
                        setTimeout(function () {
                            window.location.href = '/';
                        }, 2000);
                    },
                    error: function (error) {
                        if (error.error) {
                            // Error handling based on specific error conditions--- to get error message use this template: error.responseJSON.message
                            $.toast({
                                text: error.responseJSON.message, // Text that is to be shown in the toast
                                heading: error.responseJSON.title, // Optional heading to be shown on the toast
                                icon: 'error', // Type of toast icon
                                showHideTransition: 'slide', // fade, slide or plain
                                allowToastClose: false, // Boolean value true or false
                                hideAfter: 5000, // false to make it sticky or number representing the miliseconds as time after which toast needs to be hidden
                                stack: 5, // false if there should be only one toast at a time or a number representing the maximum number of toasts to be shown at a time
                                position: 'top-left', // bottom-left or bottom-right or bottom-center or top-left or top-right or top-center or mid-center or an object representing the left, right, top, bottom values


                                textAlign: 'right',  // Text alignment i.e. left, right or center
                                loader: true,  // Whether to show loader or not. True by default
                                loaderBg: '#9EC600',  // Background color of the toast loader
                                beforeShow: function () {
                                }, // will be triggered before the toast is shown
                                afterShown: function () {
                                }, // will be triggered after the toat has been shown
                                beforeHide: function () {
                                }, // will be triggered before the toast gets hidden
                                afterHidden: function () {
                                }  // will be triggered after the toast has been hidden
                            });

                            $submitBtn.text('ثبت نام')
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

// recovery password ajax handler
jQuery(document).ready(function ($) {
    const $submitBtnRecoverPass = $('#submit_button_recover_pass');
    const $userEmailRecoverPass = $('#user_email_recover_pass');
    const $newPassword = $('#new_password')
    const $newPasswordRepeated = $('#new_password_repeated');
    const $recoveryToken = $('#recovery_token');
    setupRecoverButton();

    function setupRecoverButton() {
        $submitBtnRecoverPass.on('click', function (e) {

            e.preventDefault();
            if ($submitBtnRecoverPass.hasClass('recover-pass-send-mail')) {
                let userEmail = $userEmailRecoverPass.val().trim();

                // AJAX request to filter posts
                jQuery.ajax({
                    url: lr_ajax.ajaxurl, //ajax url
                    type: 'post',
                    dataType: 'json',
                    data: {
                        action: 'lr_send_recovery_password_email',
                        userEmail: userEmail,
                        _nonce: lr_ajax._nonce
                    },
                    beforeSend: function () {
                        // Actions to perform before sending the AJAX request
                        $submitBtnRecoverPass.html('<div class="loader"></div>')
                    },
                    success: function (response) {
                        if (response.success) {
                            // Actions to handle successful response --- to get success message use this template: response.message
                            $.toast({
                                text: response.message, // Text that is to be shown in the toast
                                heading: ' ', // Optional heading to be shown on the toast
                                icon: 'success', // Type of toast icon
                                showHideTransition: 'slide', // fade, slide or plain
                                allowToastClose: false, // Boolean value true or false
                                hideAfter: 5000, // false to make it sticky or number representing the miliseconds as time after which toast needs to be hidden
                                stack: 5, // false if there should be only one toast at a time or a number representing the maximum number of toasts to be shown at a time
                                position: 'top-left', // bottom-left or bottom-right or bottom-center or top-left or top-right or top-center or mid-center or an object representing the left, right, top, bottom values


                                textAlign: 'right',  // Text alignment i.e. left, right or center
                                loader: true,  // Whether to show loader or not. True by default
                                loaderBg: '#9EC600',  // Background color of the toast loader
                                beforeShow: function () {
                                }, // will be triggered before the toast is shown
                                afterShown: function () {
                                }, // will be triggered after the toat has been shown
                                beforeHide: function () {
                                }, // will be triggered before the toast gets hidden
                                afterHidden: function () {
                                }  // will be triggered after the toast has been hidden
                            });
                            $submitBtnRecoverPass.text('ایمیل خود را بررسی کنید')
                                .prop('disabled', true);
                        }
                    },
                    error: function (error) {
                        if (error.error) {
                            // Error handling based on specific error conditions--- to get error message use this template: error.responseJSON.message
                            $.toast({
                                text: error.responseJSON.message, // Text that is to be shown in the toast
                                heading: error.responseJSON.title, // Optional heading to be shown on the toast
                                icon: 'error', // Type of toast icon
                                showHideTransition: 'slide', // fade, slide or plain
                                allowToastClose: false, // Boolean value true or false
                                hideAfter: 5000, // false to make it sticky or number representing the miliseconds as time after which toast needs to be hidden
                                stack: 5, // false if there should be only one toast at a time or a number representing the maximum number of toasts to be shown at a time
                                position: 'top-left', // bottom-left or bottom-right or bottom-center or top-left or top-right or top-center or mid-center or an object representing the left, right, top, bottom values


                                textAlign: 'right',  // Text alignment i.e. left, right or center
                                loader: true,  // Whether to show loader or not. True by default
                                loaderBg: '#9EC600',  // Background color of the toast loader
                                beforeShow: function () {
                                }, // will be triggered before the toast is shown
                                afterShown: function () {
                                }, // will be triggered after the toat has been shown
                                beforeHide: function () {
                                }, // will be triggered before the toast gets hidden
                                afterHidden: function () {
                                }  // will be triggered after the toast has been hidden
                            });
                            $submitBtnRecoverPass.text('بازیابی کلمه عبور')
                        }
                    },
                    complete: function () {
                        // Actions to perform after the AJAX request completes (regardless of success or failure)

                    },
                });

            }

            if ($submitBtnRecoverPass.hasClass('recover-pass-change-pass')) {
                let newPassword = $newPassword.val();
                let newPasswordRepeated = $newPasswordRepeated.val();
                let recoveryToken=$recoveryToken.val();
                jQuery.ajax({
                    url: lr_ajax.ajaxurl, //ajax url
                    type: 'post',
                    dataType: 'json',
                    data: {
                        action: 'lr_set_new_password',
                        newPassword: newPassword,
                        newPasswordRepeated:newPasswordRepeated,
                        recoveryToken:recoveryToken,
                        _nonce: lr_ajax._nonce
                    },
                    beforeSend: function () {
                        // Actions to perform before sending the AJAX request
                        $submitBtnRecoverPass.html('<div class="loader"></div>')
                    },
                    success: function (response) {
                        if (response.success) {
                            // Actions to handle successful response --- to get success message use this template: response.message
                            $.toast({
                                text: response.message, // Text that is to be shown in the toast
                                heading: ' ', // Optional heading to be shown on the toast
                                icon: 'success', // Type of toast icon
                                showHideTransition: 'slide', // fade, slide or plain
                                allowToastClose: false, // Boolean value true or false
                                hideAfter: 5000, // false to make it sticky or number representing the miliseconds as time after which toast needs to be hidden
                                stack: 5, // false if there should be only one toast at a time or a number representing the maximum number of toasts to be shown at a time
                                position: 'top-left', // bottom-left or bottom-right or bottom-center or top-left or top-right or top-center or mid-center or an object representing the left, right, top, bottom values


                                textAlign: 'right',  // Text alignment i.e. left, right or center
                                loader: true,  // Whether to show loader or not. True by default
                                loaderBg: '#9EC600',  // Background color of the toast loader
                                beforeShow: function () {
                                }, // will be triggered before the toast is shown
                                afterShown: function () {
                                }, // will be triggered after the toat has been shown
                                beforeHide: function () {
                                }, // will be triggered before the toast gets hidden
                                afterHidden: function () {
                                }  // will be triggered after the toast has been hidden
                            });
                            // redirect to home page after just minutes
                            setTimeout(function () {
                                window.location.href = '/';
                            }, 2000);
                        }
                    },
                    error: function (error) {
                        if (error.error) {
                            // Error handling based on specific error conditions--- to get error message use this template: error.responseJSON.message
                            $.toast({
                                text: error.responseJSON.message, // Text that is to be shown in the toast
                                heading: error.responseJSON.title, // Optional heading to be shown on the toast
                                icon: 'error', // Type of toast icon
                                showHideTransition: 'slide', // fade, slide or plain
                                allowToastClose: false, // Boolean value true or false
                                hideAfter: 5000, // false to make it sticky or number representing the miliseconds as time after which toast needs to be hidden
                                stack: 5, // false if there should be only one toast at a time or a number representing the maximum number of toasts to be shown at a time
                                position: 'top-left', // bottom-left or bottom-right or bottom-center or top-left or top-right or top-center or mid-center or an object representing the left, right, top, bottom values


                                textAlign: 'right',  // Text alignment i.e. left, right or center
                                loader: true,  // Whether to show loader or not. True by default
                                loaderBg: '#9EC600',  // Background color of the toast loader
                                beforeShow: function () {
                                }, // will be triggered before the toast is shown
                                afterShown: function () {
                                }, // will be triggered after the toat has been shown
                                beforeHide: function () {
                                }, // will be triggered before the toast gets hidden
                                afterHidden: function () {
                                }  // will be triggered after the toast has been hidden
                            });

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
