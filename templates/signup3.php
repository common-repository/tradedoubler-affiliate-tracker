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
<main class="sign">
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
                <img src="<?php echo esc_html(TRADEDOUBLER_URI) ?>/assets/images/signup3-current.svg">
                <div class="sign_step_text">Package Selection</div>
            </div>
            <div class="sign_step">
                <img src="<?php echo esc_html(TRADEDOUBLER_URI) ?>/assets/images/signup4.svg">
                <div class="sign_step_text">Program Creation</div>
            </div>
        </div>
    </div>
    <div class="sign_container container -reviews">
        <div class="sign_title -small">Choose your package</div>
        <div class="sign_text">Check the following features and benefits.</div>
        <div class="sign_content">
            <div class="sign_reviews_container">
                <div class="sign_review -blue">
                    <img class="sign_review_image"
                         src="<?php echo esc_html(TRADEDOUBLER_URI) ?>/assets/images/freeBar.svg"/>
                    <div class="sign_review_container">
                        <div class="sign_review_textBox">
                            <div class="sign_review_type">STANDARD</div>
                            <div class="sign_review_priceBig">
                                <span>€0.00</span> /mo
                            </div>
                        </div>
                        <div class="main-review-content">
                            <ul class="sign_review_list">
                                <li class="sign_review_list_item">
                                    <b>1</b> program
                                </li>
                                <li class="sign_review_list_item">
                                    <b>1</b> user
                                </li>
                                <li class="sign_review_list_item">
                                    <b>100k</b> banner impressions
                                </li>
                                <li class="sign_review_list_item">
                                    <b>10,000</b> clicks
                                </li>
                                <li class="sign_review_list_item">
                                    <b>1,000</b> conversions
                                </li>
                                <li class="sign_review_list_item">
                                    <b>50</b> affiliates
                                </li>
                                <li class="sign_review_list_item -inactive">
                                    Premium support
                                </li>
                            </ul>
                        </div>
                        <form action="<?php echo esc_attr($step4) ?>" method="post">
                            <button class="custom_button -white -px-32 -full">
                                Select this plan
                            </button>
                        </form>

                    </div>
                </div>
                <div class="sign_review">
                    <div class="sign_review_container">
                        <div class="sign_review_textBox">
                            <div class="sign_review_type">PREMIUM</div>
                            <div class="sign_review_priceBig">
                                <span>€29.90</span> /mo
                            </div>
                        </div>
                        <div class="main-review-content">
                            <ul class="sign_review_list">
                                <li class="sign_review_list_item">
                                    <b>3</b> programs
                                </li>
                                <li class="sign_review_list_item">
                                    <b>Unlimited</b> users
                                </li>
                                <li class="sign_review_list_item">
                                    <b>100m</b> banner impressions
                                </li>
                                <li class="sign_review_list_item">
                                    <b>100,000</b> clicks
                                </li>
                                <li class="sign_review_list_item">
                                    <b>10,000</b> conversions
                                </li>
                                <li class="sign_review_list_item">
                                    <b>Unlimited</b> affiliates
                                </li>
                                <li class="sign_review_list_item">
                                    Premium support
                                </li>
                            </ul>
                        </div>
                        <a target="_blank"
                           href="https://grow-platform.tradedoubler.com/login?view=sign-up#sign-up-container"
                           class="custom_button -px-32 -full">Select this
                            plan</a>
                    </div>
                </div>
                <div class="sign_review">
                    <div class="sign_review_container">
                        <div class="sign_review_textBox">
                            <div class="sign_review_type">EXPERT</div>
                            <div class="sign_review_priceBig">
                                <span>€59.90</span> /mo
                            </div>
                        </div>
                        <div class="main-review-content">
                            <ul class="sign_review_list">
                                <li class="sign_review_list_item">
                                    <b>5</b> programs
                                </li>
                                <li class="sign_review_list_item">
                                    <b>Unlimited</b> users
                                </li>
                                <li class="sign_review_list_item">
                                    <b>200m</b> banner impressions
                                </li>
                                <li class="sign_review_list_item">
                                    <b>500,000</b> clicks
                                </li>
                                <li class="sign_review_list_item">
                                    <b>50,000
                                    </b> conversions
                                </li>
                                <li class="sign_review_list_item">
                                    <b>Unlimited</b> affiliates
                                </li>
                                <li class="sign_review_list_item">
                                    Premium support
                                </li>
                            </ul>
                        </div>
                        <a target="_blank"
                           href="https://grow-platform.tradedoubler.com/login?view=sign-up#sign-up-container"
                           class="custom_button -px-32 -full">Select this
                            plan</a>
                    </div>
                </div>
            </div>
            <!--        <div class="form_buttonWrapper">-->
            <!--          <a target="_blank" href="#" class="custom_button -transparent -px-32">Back</a>-->
            <!--        </div>-->
        </div>
    </div>
</main>
<footer class="footer">
</footer>