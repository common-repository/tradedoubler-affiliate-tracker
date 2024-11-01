<?php
if ( ! defined('ABSPATH')) {
    exit;
}
//    $urlCreateAccount = admin_url('admin.php?page=tradedoubler&tab=step1');
$urlCreateAccount
          = 'https://grow-platform.tradedoubler.com/login#sign-up-container';
$login    = admin_url('admin.php?page=tradedoubler&tab=signInForm');
$step2    = admin_url('admin.php?page=tradedoubler&tab=step2');
$step3    = admin_url('admin.php?page=tradedoubler&tab=step3');
$step4    = admin_url('admin.php?page=tradedoubler&tab=step4');
$step5    = admin_url('admin.php?page=tradedoubler&tab=step5');
$urlLogin = admin_url('admin.php?page=tradedoubler');
?>
<header class="header">
    <div class="header_container container">
        <a target="_blank" href="#" class="header_logo"><img
                    src="<?php echo esc_html(TRADEDOUBLER_URI) ?>/assets/images/logo.svg"
                    alt="grow"/></a>
    </div>
</header>
<main class="sign">
    <div class="sign_container container -signin">
        <div class="sign_content">
            <div class="sign_title">Sign in to your Grow account</div>
            <div class="sign_text">
                Enter your details below to sign in to your program.
                Please note you first need to
                <a href="https://knowledge.tradedoubler.com/grow-knowledge/what-are-the-stages-of-set-up-prestashop">create
                    a program</a>
                before you can use this module
            </div>
            <?php if (isset($_GET['error']) && $_GET['error'] == '1') { ?>
                <div style='padding: 15px; margin-bottom: 20px;
border: 1px solid #ebccd1;background-color: #f2dede;color: #a94442; border-radius: 4px'>
                    Incorrect login/password
                </div>
            <?php } ?>
            <?php if (isset($_GET['error']) && $_GET['error'] == '2') { ?>
                <div
                        style='padding: 15px; margin-bottom: 20px;background-color: #ffdf7f;color: #9f7700; border-radius: 4px'>
                    There are no programs associated with this account. To use
                    this module, please
                    <a target="_blank"
                       href="https://knowledge.tradedoubler.com/grow-knowledge/what-are-the-stages-of-set-up-prestashop">
                        create your first program
                    </a>on the Grow platform.<Br><br>
                    Please feel free to
                    <a target="_blank"
                       href="https://knowledge.tradedoubler.com/grow-knowledge/kb-tickets/new">
                        contact us
                    </a> for further assistance
                </div>
            <?php } ?>
            <form action="<?php echo esc_attr($login) ?>" method="post" class="form -novalidate">
                <div class="form_grid -mb-40">
                    <div class="form_control">
                        <div class="form_label">Email address</div>
                        <input type="email" class="form_input -image -email"
                               name="username" required/>
                    </div>
                    <div class="form_control">
                        <div class="form_label">
                            Password
                            <a target="_blank"
                               href="https://grow-platform.tradedoubler.com/login?redirect_to=%2F#password-request-container"
                               class="form_forgot">Forgot password?</a>
                        </div>
                        <input type="password"
                               class="form_input -image -password"
                               name="password" required/>
                    </div>
                </div>
                <button class="custom_button" type="submit">Sign In</button>
            </form>
        </div>
    </div>
</main>
<footer class="footer">
    <div class="footer_container container -center">
        <div class="footer_textBox">
            <div class="footer_text">
                New to Tradedoubler?
                <a target="_blank" href="<?php echo esc_attr($urlCreateAccount) ?>"
                   class="footer_link">Create an account</a>
            </div>
        </div>
    </div>
</footer>

