<?php
function lr_email_layout_recovery_password_link($link, $logo_url = ''): string
{
    $site_title = get_bloginfo('name');
    if (!empty($logo_url)) {
        $logo = '
                    <img src="' . $logo_url . '"
                 alt="Logo">
        ';
    } else {
        $logo = '<strong class="alternative_logo">' . $site_title . ' </strong>';
    }
    $font_IRANSans_url = plugin_dir_url(__DIR__) . 'assets/fonts/IRANSansWeb/IRANSansWeb.ttf';
    $image_url = plugin_dir_url(__DIR__) . 'assets/img/reset-password.png';
    $font_vazirmatn_url = 'https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Vazirmatn:wght@100..900&display=swap';
    return '
    <!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Recovery</title>
    <style>
        /* Import Fonts */
        @import url(' . $font_vazirmatn_url . ');
        @import url(' . $font_IRANSans_url . ');

        body {
    margin: 0;
    padding: 0;
    font-family: IRANSansWeb,Vazirmatn, Tahoma, Arial, sans-serif!important;
            background-color: #ffffff;
            color: #292929;
            direction: rtl;

        }
.wrapper {
    background-color: #8d95ff;
    max-width: 600px;
    margin: auto;
    box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 5px 0px, rgba(0, 0, 0, 0.1) 0px 0px 1px 0px;
    border-radius: 10px;
}
        .container {
    max-width: 600px;
            margin: auto;
            background-color: #fff;
            padding: 40px;
            text-align: center;
            border-radius: 40% 20% 50% 35% / 25% 20% 50% 60% ;
        }

        .header {
    margin-bottom: 20px;
    background-color: transparent!important;

        }

        .header img {
    max-width: 100px;
        }

        .message {
    font-size: 16px;
            margin-bottom: 20px;
            line-height: 1.7;
        }
        .secondary-message {
    font-size: 14px;
        }
        .message p {
    text-align: right;
        }

        .message strong {
    color: #d35400; /* Highlighted color for time */
}
.alternative_logo{
font-size:24px;
font-weight: 900;
color: #d35400;
}

        .button {
    display: inline-block;
    padding: 10px 20px;
            font-size: 16px;
            color: #fff!important;
            background-color: #f1c40f!important; /* Yellow button */
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0 30px 0;
        }

        .footer {
    font-size: 12px;
            color: #666;
            margin-top: 20px;
        }

        .image {
    margin: 45px 0 20px 0;
        }

        .image img {
    max-width: 100%;
            border-radius: 8px;
        }

        @media (max-width: 480px) {
    .container {
        padding: 10px;
                border: 5px solid #8d95ff;
            }

            .button {
        width: 80%; /* Ensure button adapts for smaller screens */
        font-size: 14px;
            }
        }
    </style>
</head>
<body>
<section class="wrapper" style="font-family: IRANSansWeb,Vazirmatn, Tahoma, Arial, sans-serif ">
    <div class="container">
        <div class="header">
' . $logo . '
        </div>
        <div class="message">
            <p>.کاربر گرامی سلام</p>
<p>درخواستی برای بازیابی کلمه عبور از طرف شما ارسال شده است. برای تغییر کلمه عبور خود در<strong>' . $site_title . ' </strong> روی دکمه زیر کلیک نمایید</p>
            <a href="' . $link . '" class="button" target="_blank">بازیابی کلمه عبور</a>
            <p class="secondary-message">اگر این درخواست از سمت شما نیست این ایمیل را نادیده بگیرید. لینک پس از <strong>3                ساعت </strong> منقضی می‌شود</p>
        </div>

        <div class="image">
            <img src="' . $image_url . '" alt="Reset Illustration">
        </div>

        <div class="footer">
            <!--            <p dir="ltr">Need help? Contact our support team.</p>-->
            <p>کلیه حقوق برای سایت‌یار محفوظ است 2024 &copy;</p>
        </div>
    </div>
</section>
</body>
</html>

';
}