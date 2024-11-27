<?php
// Hook the function into phpmailer_init to modify the PHPMailer instance before sending emails
add_action( 'phpmailer_init', 'lr_send_mail' );
function lr_send_mail( $phpmailer ): void
{
    $email_host = get_option('_lr_email_service_host');
    $email_port = get_option('_lr_email_service_port');
    $email_username = get_option('_lr_email_service_username');
    $email_password = get_option('_lr_email_service_password');
    $site_title = get_bloginfo('name');
    // Set the mailer to use SMTP
    $phpmailer->isSMTP();

    // Specify the SMTP server to send through
    $phpmailer->Host = $email_host;

    // Enable SMTP authentication
    $phpmailer->SMTPAuth = true; // This enables authentication using the Username and Password properties

    // Set the SMTP port
    $phpmailer->Port = $email_port; // Common ports: 25, 587 (STARTTLS), 465 (SSL)

    // Provide the SMTP username and password for authentication
    $phpmailer->Username = $email_username; // Your SMTP username
    $phpmailer->Password = $email_password; // Your SMTP password

    // Uncomment the line below if using encryption for SMTP (recommended)
    // $phpmailer->SMTPSecure = 'tls'; // Choose 'ssl' for SMTPS on port 465, or 'tls' for SMTP+STARTTLS on port 25 or 587

    // Set the "From" email address
//    $phpmailer->From = "info@siteyar.net"; // The email address the message will appear to be sent from

    // Set the "From" name
    $phpmailer->FromName = $site_title; // The name the message will appear to be sent from
}



function lr_mail_recovery_password_link($email, $link): void
{
    $headers = ['Content-Type:text/html;charset=UTF-8'];
    $message = lr_email_layout_recovery_password_link($link);
    $send_mail = wp_mail($email, 'بازیابی کلمه عبور', $message, $headers);
    if (!$send_mail) {
        wp_send_json([
            'error' => true,
            'message' => 'در ارسال لینک بازیابی به ایمیل شما خطایی رخ داده است.مجددا تلاش کنید..'
        ], 400);  // Respond with a 400 Internal Server Error status code
    }
    wp_send_json([
        'success' => true,
        'message' => 'لینک بازیابی با موفقیت به ایمیل شما ارسال شد.'
    ], 200);  // Respond with a 200 OK status code

}