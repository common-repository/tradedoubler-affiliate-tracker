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

$isoCountries = getCountriesListTradeDoubler();

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
                <img src="<?php echo esc_html(TRADEDOUBLER_URI) ?>/assets/images/signup-done.svg">
                <div class="sign_step_text">Login</div>
            </div>
            <div class="sign_step">
                <img src="<?php echo esc_html(TRADEDOUBLER_URI) ?>/assets/images/signup2-current.svg">
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
        <div class="sign_title -small">Confirm your account information</div>
        <div class="sign_text">
            This information will also be available to your affiliates.
        </div>
        <?php include 'errors_form.php' ?>
        <div class="sign_content">
            <form action="<?php echo esc_attr($step3) ?>" method="post" class="form" novalidate>
                <div class="form_grid -mb-56">
                    <div class="form_control">
                        <div class="form_label">First name <span>*</span></div>
                        <input type="text" class="form_input"
                               name="first-name"
                               value="<?php echo esc_attr($values['first-name']) ?>" required/>
                    </div>
                    <div class="form_control">
                        <div class="form_label">Last name <span>*</span></div>
                        <input type="text" class="form_input"
                               name="last-name"
                               value="<?php echo esc_attr($values['last-name']) ?>" required/>
                    </div>
                    <div class="form_control">
                        <div class="form_label">Company name <span>*</span>
                        </div>
                        <input type="text" class="form_input"
                               name="company-name"
                               value="<?php echo esc_attr($values['company-name']) ?>" required/>
                    </div>
                    <div class="form_control">
                        <div class="form_label">Phone <span>*</span></div>
                        <input type="text" class="form_input"
                               name="phone" value="<?php echo esc_attr($values['phone']) ?>"
                               required/>
                    </div>
                    <div class="form_control">
                        <div class="form_label">Company registration number
                        </div>
                        <input type="text" class="form_input"
                               name="company-registration-number"
                               value="<?php echo esc_attr($values['company-registration-number']) ?>"/>
                    </div>
                    <div class="form_control">
                        <div class="form_label">VAT number</div>
                        <input type="text" class="form_input"
                               name="vat-number"
                               value="<?php echo esc_attr($values['vat-number']) ?>"/>
                    </div>
                    <div class="form_control">
                        <div class="form_label">Street address <span>*</span>
                        </div>
                        <input type="text" class="form_input"
                               name="street-address"
                               value="<?php echo esc_attr($values['street-address']) ?>"
                               required/>
                    </div>
                    <div class="form_control">
                        <div class="form_label">Street address line 2
                            <span>*</span></div>
                        <input type="text" class="form_input"
                               name="street-address-2"
                               value="<?php echo esc_attr($values['street-address-2']) ?>"
                               required/>
                    </div>
                    <div class="form_control">
                        <div class="form_label">Postcode <span>*</span></div>
                        <input type="text" class="form_input"
                               name="postcode"
                               value="<?php echo esc_attr($values['postcode']) ?>" required/>
                    </div>
                    <div class="form_control">
                        <div class="form_label">County <span>*</span></div>
                        <input type="text" class="form_input"
                               name="county" value="<?php echo esc_attr($values['county']) ?>"
                               required/>
                    </div>
                    <div class="form_control">
                        <div class="form_label">City <span>*</span></div>
                        <input type="text" class="form_input"
                               name="city" value="<?php echo esc_attr($values['city']) ?>"
                               required/>
                    </div>
                    <div class="form_control">
                        <div class="form_label">Country <span>*</span></div>
                        <select class="form_select" name="country" required>
                            <?php if ($values['country']) {
                                foreach ($isoCountries as $single) {
                                    if ($single['id'] == $values['country']) {
                                        printf("<option value='%s'>%s</option>",
                                            esc_attr($single['id']), esc_html($single['text'])
                                        );
                                    }
                                }
                            } ?>
                        </select>
                    </div>
                </div>
                <div class="form_buttonWrapper">
                    <a target="_blank" href="<?php echo esc_attr($urlCreateAccount) ?>"
                       class="custom_button -transparent -px-32">Back</a>
                    <button class="custom_button" type="submit">Next</button>
                </div>
            </form>
        </div>
    </div>
</main>
<footer class="footer">
</footer>
