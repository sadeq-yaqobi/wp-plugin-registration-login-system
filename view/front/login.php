<?php
function wp_sr_login_layout()
{
    ?>
    <!-- Log In Modal -->
    <ul class="nav-menu nav-menu-social align-to-left">

        <li class="login_click light">
            <a href="#" data-toggle="modal" data-target="#login">ورود</a>
        </li>
        <li class="login_click theme-bg">
            <a href="<?php echo site_url('registration')?>" >ثبت نام</a>
        </li>
    </ul>
    <div class="modal fade" id="login" tabindex="-1" role="dialog" aria-labelledby="registermodal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered login-pop-form" role="document">
            <div class="modal-content" id="registermodal">
                <span class="mod-close" data-dismiss="modal" aria-hidden="true"><i class="ti-close"></i></span>
                <div class="modal-body">
                    <h4 class="modal-header-title">ورود به حساب</h4>
                    <div class="login-form">
<div id="login_message_handler" class="alert"></div>

                        <form id="lr_login">
                            <div class="form-group">
                                <label for="email">ایمیل</label>
                                <input type="email" id="email" class="form-control" placeholder="ایمیل">
                            </div>

                            <div class="form-group">
                                <label for="password">رمز عبور</label>
                                <input type="password" id="password" class="form-control" placeholder="*******">
                            </div>
                            <div class="social-login mb-3">
                                <ul>
                                    <li>
                                        <input id="remember_me" class="checkbox-custom" name="remember_me" type="checkbox">
                                        <label for="remember_me" class="checkbox-custom-label">مرا به خاطر بسپار</label>
                                    </li>
                                    <li class="left"><a href="#" class="theme-cl">فراموشی رمز عبور</a></li>
                                </ul>
                            </div>
                            <div class="form-group">
                                <button type="submit" id="rl_loading" class="btn btn-md full-width pop-login">ورود به حساب</button>

                            </div>

                        </form>
                    </div>



                    <div class="modal-divider"><span>یـا</span></div>
                    <div class="social-login ntr mb-3">
                        <ul>
                            <li><a href="#" class="btn connect-fb"><i class="ti-facebook"></i>Facebook</a></li>
                            <li><a href="#" class="btn connect-google"><i class="ti-google"></i>Google</a></li>
                        </ul>
                    </div>

                    <div class="text-center">
                        <p class="mt-2">حساب کاربری دارید؟ <a href="register.html" class="link">ورود به اکانت</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal -->
    <?php
}
add_shortcode('ls-login','wp_sr_login_layout');