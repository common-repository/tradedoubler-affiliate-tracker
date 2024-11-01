<?php
if ( ! defined('ABSPATH')) {
    exit;
}
$dashboard = admin_url('admin.php?page=tradedoubler');
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
                <img src="<?php echo esc_html(TRADEDOUBLER_URI) ?>/assets/images/signup-done.svg">
                <div class="sign_step_text">Login</div>
            </div>
            <div class="sign_step">
                <img src="<?php echo esc_html(TRADEDOUBLER_URI) ?>/assets/images/signup-done.svg">
                <div class="sign_step_text">Account Details</div>
            </div>
            <div class="sign_step">
                <img src="<?php echo esc_html(TRADEDOUBLER_URI) ?>/assets/images/signup-done.svg">
                <div class="sign_step_text">Package Selection</div>
            </div>
            <div class="sign_step">
                <img src="<?php echo esc_html(TRADEDOUBLER_URI) ?>/assets/images/signup-done.svg">
                <div class="sign_step_text">Program Creation</div>
            </div>
        </div>
    </div>
    <div class="sign_container container">
        <div class="sign_content">
            <img class="sign_finalImage"
                 src="<?php echo esc_html(TRADEDOUBLER_URI) ?>/assets/images/finalImage.png"/>
            <div class="sign_title -small">
                You are <span>ready to grow!</span>
            </div>
            <div class="sign_text -ready">

            </div>
        </div>
        <div class="form_buttonWrapper -final">
            <a target="_blank" href="<?php echo esc_html($dashboard) ?>"
               class="form_next -final">Go to the app</a>
        </div>
    </div>
</main>
<footer class="footer">
</footer>