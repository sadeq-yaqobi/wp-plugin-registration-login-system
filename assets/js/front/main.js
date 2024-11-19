/**
 * phone-verification.js
 * Handles phone number input and OTP verification using jQuery in procedural style
 */

jQuery(document).ready(function($) {
    // Cache DOM elements
    const $phoneInput = $('#phone-input');
    const $phoneError = $('#phone-error-message');
    const $submitBtn = $('#ls_submit_button');
    const $otpInputs = $('.otp-input-bar');
    const $otpError = $('#error_message');
    const $phoneWrap = $('.phone-number-wrap');
    const $verificationWrap = $('.verification-code-wrap');

    // Initialize form
    initializeForm();
    setupPhoneEvents();
    setupOtpEvents();

    /**
     * Form initialization
     */
    function initializeForm() {
        $submitBtn.prop('disabled', true);
        $phoneInput.attr('placeholder', '09× ××× ××××');
        $otpInputs.prop('disabled', true);
        $otpInputs.first().prop('disabled', false);
    }

    /**
     * Phone number handling functions
     */
    function formatPhoneNumber(value) {
        // Remove non-digits
        const number = value.replace(/\D/g, '');

        // Validate starting digits
        if (!number.startsWith('0')) return '';
        if (number.length > 1 && number[1] !== '9') return number.slice(0, 1);

        // Format with spaces
        if (number.length <= 4) return number;
        if (number.length <= 7) return `${number.slice(0, 4)} ${number.slice(4)}`;
        return `${number.slice(0, 4)} ${number.slice(4, 7)} ${number.slice(7, 11)}`;
    }

    function isValidPhoneNumber(number) {
        return /^09\d{9}$/.test(number.replace(/\s/g, ''));
    }

    function updatePhoneUI(value) {
        const isValid = isValidPhoneNumber(value);
        const cleanValue = value.replace(/\s/g, '');

        $submitBtn.prop('disabled', !isValid).toggleClass('active', isValid);

        if (!value.length) {
            $phoneError.fadeOut('slow');
            $phoneInput.removeClass('invalid');
            return;
        }

        if (!isValid && cleanValue.length >= 11) {
            $phoneError.html('شماره موبایل نامعتبر است').fadeIn('slow');
            $phoneInput.addClass('invalid');
        } else {
            $phoneError.fadeOut('slow');
            $phoneInput.removeClass('invalid');
        }
    }

    function setupPhoneEvents() {
        // Handle phone input
        $phoneInput.on('input', function() {
            const value = formatPhoneNumber($(this).val());
            $(this).val(value);
            updatePhoneUI(value);
        });

        // Handle phone paste
        $phoneInput.on('paste', function(e) {
            e.preventDefault();
            const pastedText = (e.originalEvent.clipboardData || window.clipboardData).getData('text');
            const formattedNumber = formatPhoneNumber(pastedText);
            $(this).val(formattedNumber);
            updatePhoneUI(formattedNumber);
        });

        // Handle phone keydown
        $phoneInput.on('keydown', function(e) {
            if (!/^\d$/.test(e.key) && !['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab'].includes(e.key)) {
                e.preventDefault();
            }
        });
    }

    /**
     * OTP handling functions
     */
    function handleOtpInput($input) {
        const value = $input.val();
        $otpError.fadeOut('slow');

        // Validate numeric input
        if (!/^\d*$/.test(value)) {
            $input.val('');
            $otpError.html('عدد وارد نمایید').fadeIn('slow');
            return;
        }

        // Move to next input if value entered
        if (value.length === 1) {
            const $next = $input.next('.otp-input-bar');
            if ($next.length) {
                $next.prop('disabled', false).focus();
            }
        }

        checkOtpCompletion();
    }

    function handleOtpDelete($input) {
        $input.val('');
        const $prev = $input.prev('.otp-input-bar');

        if ($prev.length) {
            $input.prop('disabled', true);
            $input.nextAll('.otp-input-bar').prop('disabled', true).val('');
            $prev.focus();
        }

        checkOtpCompletion();
    }

    function handleOtpPaste(e) {
        e.preventDefault();
        const pastedData = (e.originalEvent.clipboardData || window.clipboardData)
            .getData('text')
            .replace(/\D/g, '')
            .slice(0, 6);

        // Reset all inputs
        $otpInputs.val('').prop('disabled', true);

        // Fill inputs with pasted data
        pastedData.split('').forEach((digit, index) => {
            const $input = $otpInputs.eq(index);
            $input.prop('disabled', false).val(digit);

            if (index < $otpInputs.length - 1) {
                $otpInputs.eq(index + 1).prop('disabled', false);
            }
        });

        // Focus on appropriate input
        const $emptyInput = $otpInputs.filter(function() {
            return !this.value && !this.disabled;
        }).first();

        ($emptyInput.length ? $emptyInput : $otpInputs.filter(':enabled').last()).focus();

        checkOtpCompletion();
    }

    function checkOtpCompletion() {
        const isComplete = $otpInputs.filter(function() {
            return this.value.length === 1;
        }).length === 6;

        $submitBtn.prop('disabled', !isComplete).toggleClass('active', isComplete);
    }

    function setupOtpEvents() {
        // Handle OTP input
        $otpInputs.on('input', function() {
            handleOtpInput($(this));
        });

        // Handle OTP keydown
        $otpInputs.on('keydown', function(e) {
            if (e.key === 'Backspace' || e.key === 'Delete') {
                e.preventDefault();
                handleOtpDelete($(this));
            } else if (!/^\d$/.test(e.key) && !['Tab', 'ArrowLeft', 'ArrowRight'].includes(e.key)) {
                e.preventDefault();
            }
        });

        // Handle OTP paste
        $('.otp-form-group').on('paste', handleOtpPaste);
    }

    // registration part include display name, email, and password
    const $displayName = $('#display_name');
    const $email = $('#user_email');
    const $password = $('#user_password');
    const $togglePassword = $('.toggle-password');
    const $passwordRequirements = $('.password-requirements');

    toggleShowPassword($password,$togglePassword);
    showPasswordRequirement($password);

    // Password visibility toggle
    function toggleShowPassword(passwordInputElement,toggleIcon) {
        toggleIcon.on('click', function () {
            const $pwd = passwordInputElement;
            const type = $pwd.attr('type') === 'password' ? 'text' : 'password';
            $pwd.attr('type', type);

            // Toggle eye icon
            const $eyeIcon = $(this).find('img');
            if (type === 'password') {
                let srcEye = $eyeIcon.attr('src').replace('eye-off.svg', 'eye.svg')
                $eyeIcon.attr('src', srcEye);
            } else {
                let srcEyeOff = $eyeIcon.attr('src').replace('eye.svg', 'eye-off.svg')
                $eyeIcon.attr('src', srcEyeOff);
            }
        });
    }

    function showPasswordRequirement(passwordInputElement) {
        passwordInputElement.on('focus', function () {
            $passwordRequirements.fadeIn();
        });
        passwordInputElement.on('blur', function () {
            $passwordRequirements.fadeOut();
        });
    }

    // Password validation patterns
    const patterns = {
        length: /.{8,}/,
        number: /[0-9]/,
        lowercase: /[a-z]/,
        uppercase: /[A-Z]/,
        special: /[!@#$%^&*]/
    };
    function validatePassword(password) {
        // Check each requirement
        for (const [requirement, pattern] of Object.entries(patterns)) {
            const isValid = pattern.test(password);
            const $requirementElement = $(`#${requirement}`);
            const $iconElement = $requirementElement.prev('img');

            $requirementElement.toggleClass('valid', isValid);
            if (isValid) {
                const $src = $iconElement.attr('src').replace('x.svg', 'check.svg')
                $iconElement.attr('src', $src);
            } else {
                const $src = $iconElement.attr('src').replace('check.svg', 'x.svg')
                $iconElement.attr('src', $src);
            }
        }

        // Check if all requirements are met
        return Object.values(patterns).every(pattern => pattern.test(password));
    }

    function validateForm() {
        //input value
        const nameValue = $displayName.val().trim();
        const emailValue = $email.val().trim();
        const passwordValue = $password.val().trim();

        validatePassword(passwordValue); // to check password requirements independently

        // Email validation pattern
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const isEmailValid = emailPattern.test(emailValue);

        // Check if all fields are valid
        const isValid = nameValue !== '' &&
            isEmailValid &&
            validatePassword(passwordValue);

        // Enable/disable submit button
        $submitBtn.prop('disabled', !isValid)
            .toggleClass('active', isValid);
    }

    // Add input event listeners
    $displayName.on('input', validateForm);
    $email.on('input', validateForm);
    $password.on('input', validateForm);


// recovery password part
    const $submitBtnRecoverPass = $('#submit_button_recover_pass');
    const $userEmailRecoverPass = $('#user_email_recover_pass');
    const $userPassRecoverPass=$('#user_password_recover_pass')
    const $userPassRepeatRecoverPass = $('#user_password_recover_pass_repeat');
    const $toggleRepeatedPassword = $('.toggle-repeated-password');

    function validateEmailRecoveryPass() {
        const emailValue = $userEmailRecoverPass.val().trim();

        // Email validation pattern
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        // Check if email is valid
        const isValid = emailPattern.test(emailValue);

        // Enable/disable submit button
        $submitBtnRecoverPass.prop('disabled', !isValid)
            .toggleClass('active', isValid);
    }

    $userEmailRecoverPass.on('input', validateEmailRecoveryPass);


    toggleShowPassword($userPassRecoverPass,$togglePassword);
    toggleShowPassword($userPassRepeatRecoverPass,$toggleRepeatedPassword);
    showPasswordRequirement($userPassRecoverPass);

    function validatePassRecoveryPass() {
        //input value
        const passwordValue = $userPassRecoverPass.val().trim();
        const repeatedPassValue = $userPassRepeatRecoverPass.val().trim();

        validatePassword(passwordValue); // to check password requirements independently
        const isValid = validatePassword(passwordValue) && passwordValue === repeatedPassValue
        // Enable/disable submit button
        $submitBtnRecoverPass.prop('disabled', !isValid)
            .toggleClass('active', isValid);
    }
    $userPassRecoverPass.on('input', validatePassRecoveryPass);
    $userPassRepeatRecoverPass.on('input',validatePassRecoveryPass);


});