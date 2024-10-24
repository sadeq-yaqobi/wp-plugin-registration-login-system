/**
 * Login form handler using jQuery AJAX
 * Handles user authentication and provides feedback to the user
 */
jQuery(document).ready(function($) {
    // Handle login form submission
    $('#lr_login').on('submit', function(e) {
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
            beforeSend: function() {
                // Show loading indicator
                $('#rl_loading').html('<div class="loader"></div>')
            },

            // Handle successful response
            success: function(response) {
                if (response.success) {
                    // Show success message with animation
                    $('#login_message_handler')
                        .attr('class', 'alert alert-success')
                        .text(response.message)
                        .hide()
                        .fadeIn('slow', 'swing');

                    // Redirect after 2 seconds
                    setTimeout(function() {
                        window.location.href = document.documentURI;
                    }, 2000);
                }
            },

            // Handle error response
            error: function(error) {
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
            complete: function() {
                // Can add cleanup code here if needed
            },
        });
    });
});