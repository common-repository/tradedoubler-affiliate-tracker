<?php
if ( ! defined('ABSPATH')) {
    exit;
}
$urlCreateAccount = admin_url('admin.php?page=tradedoubler&tab=step1');
$step2            = admin_url('admin.php?page=tradedoubler&tab=step2');
$step3            = admin_url('admin.php?page=tradedoubler&tab=step3');
$step4            = admin_url('admin.php?page=tradedoubler&tab=step4');
$step5            = admin_url('admin.php?page=tradedoubler&tab=step5');
$urlLogin         = admin_url('admin.php?page=tradedoubler');


$values = $_SESSION['form_tb'];
?>
<header class="header">
    <div class="header_container container">
        <a target="_blank" href="#" class="header_logo"><img
                    src="<?php echo esc_html(TRADEDOUBLER_URI) ?>/assets/images/logo.svg"
                    alt="grow"/></a>
        <div class="header_textBox">
            <div class="header_text">
                Need help?
                <a target="_blank"
                   href="https://www.tradedoubler.com/en/contact/"
                   class="header_link">Contact us</a>
            </div>
        </div>
    </div>
</header>
<main class="sign -steps">
    <div class="sign_steps_container">
        <div class="sign_steps">
            <div class="sign_step">
                <img src="<?php echo esc_html(TRADEDOUBLER_URI) ?>/assets/images/signup1-current.svg">
                <div class="sign_step_text">Login</div>
            </div>
            <div class="sign_step">
                <img src="<?php echo esc_html(TRADEDOUBLER_URI) ?>/assets/images/signup2.svg">
                <div class="sign_step_text">Account Details</div>
            </div>
            <div class="sign_step">
                <img src="<?php echo esc_html(TRADEDOUBLER_URI) ?>/assets/images/signup3.svg">
                <div class="sign_step_text">Package Selection</div>
            </div>
            <div class="sign_step">
                <img src="<?php echo esc_html(TRADEDOUBLER_URI) ?>/assets/images/signup4.svg">
                <div class="sign_step_text">Program Creation</div>
            </div>
        </div>
    </div>
    <div class="sign_container container">
        <div class="sign_title">
            Welcome to <span>Grow by Tradedoubler</span>
        </div>
        <div class="sign_text">
            Set up your account in 3 simple steps and start driving sales
            through the Grow network today!
        </div>
        <?php include 'errors_form.php' ?>
        <form action="<?php echo esc_attr($step2) ?>" method="post" class="form" novalidate>
            <div class="form_grid -mb-48">
                <div class="form_control -wide">
                    <div class="form_label">Website URL</div>
                    <input readonly type="text" class="form_input"
                           value="<?php echo esc_attr($values['website-url']) ?>"
                           autocomplete="off" required/>
                </div>
                <div class="form_control">
                    <div class="form_label">Email address <span>*</span></div>
                    <input type="email" class="form_input -image -email"
                           name="email" value="<?php echo esc_attr($values['email']) ?>"
                           autocomplete="off" required/>
                </div>
                <div class="form_control">
                    <div class="form_label">Password <span>*</span></div>
                    <input type="password" class="form_input -image -password"
                           name="password" autocomplete="off" required/>
                    <div class="form_alert">Password must be between a minimum
                        of 7 and a maximum of 14 characters with at least 1
                        uppercase and 1 number
                    </div>
                </div>
            </div>
            <div class="form_agreement">
                <input class="form_checkbox_hidden" type="checkbox"
                       id="agreement1" name="agreement1" required/>
                <label class="form_checkbox_label" for="agreement1">
                    <div class="form_agreement_text">
                        By signing up to Grow, you agree to our
                        <a target="_blank"
                           href="https://grow-platform.tradedoubler.com/files/TCs_Grow_Affiliate_Marketing_Network_20201218.pdf">Terms
                            of Service</a> (including our
                        <a target="_blank"
                           href="https://grow-platform.tradedoubler.com/files/Grow_Fair_Usage_Policy_20200622.pdf">Fair
                            Usage Policy</a>),
                        <a target="_blank"
                           href="https://grow-platform.tradedoubler.com/files/Privacy_Policy_external_20190515.pdf">Privacy
                            Policy</a> and
                        <a target="_blank"
                           href="https://grow-platform.tradedoubler.com/files/GROW_DPA_Advertiser_20200622.pdf">Data
                            Protection Agreement</a><span>*</span>
                    </div>
                </label>


            </div>
            <input type="hidden" name="action" value="loginTD"/>
            <button class="custom_button" type="submit">Get Started</button>
        </form>
    </div>
</main>
<footer class="footer">
    <div class="footer_container container -center">
        <div class="footer_textBox">
            <div class="footer_text">
                Already registered?
                <a target="_blank" href="<?php echo esc_attr($urlLogin) ?>" class="footer_link">Login</a>
            </div>
        </div>
    </div>
</footer>