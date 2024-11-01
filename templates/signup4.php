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
                <img src="<?php echo esc_html(TRADEDOUBLER_URI) ?>/assets/images/signup-done.svg">
                <div class="sign_step_text">Account Details</div>
            </div>
            <div class="sign_step">
                <img src="<?php echo esc_html(TRADEDOUBLER_URI) ?>/assets/images/signup-done.svg">
                <div class="sign_step_text">Package Selection</div>
            </div>
            <div class="sign_step">
                <img src="<?php echo esc_html(TRADEDOUBLER_URI) ?>/assets/images/signup4-current.svg">
                <div class="sign_step_text">Program Creation</div>
            </div>
        </div>
    </div>

    <div class="sign_container container">
        <div class="sign_title -small">Configure your program</div>
        <div class="sign_text">

        </div>
        <div class="sign_content">
            <?php
            if (isset($_SESSION['form_tb']['errors2'])) {
                foreach ($_SESSION['form_tb']['errors2'] as $single) {
                    if (is_array($single)) {
                        printf("<div style='padding: 15px; margin-bottom: 20px;
border: 1px solid #ebccd1;background-color: #f2dede;color: #a94442; border-radius: 4px'>%s</div>",
                            esc_html($single['message']));
                    }
                }
            } ?>
            <form action="<?php echo esc_attr($step5) ?>" method="post"
                  enctype="multipart/form-data" class="form" novalidate>
                <div class="form_grid -mb-56">
                    <div class="form_control -wide">
                        <div class="form_label">Company logo</div>
                        <input type="dropzone" class="form_dropzone_input"
                               required/>
                        <input type="file" name="logo"
                               class="form_dropzone_file"/>
                        <div class="sign_dropzone my_dropzone" data-url="/">
                            <div class="sign_dropzone_alert">Drop image here
                            </div>
                            <div class="sign_dropzone_container">
                                <div class="sign_dropzone_preview"
                                     id="dropzone-preview-container"></div>
                                <div class="sign_dropzone_upload"
                                     id="photo-dropzone">
                                    <div class="dz-message" data-dz-message>
                                        Upload new file
                                    </div>
                                </div>
                                <div class="sign_dropzone_preview_template"
                                     id="preview-template">
                                    <div class="dz-preview dz-file-preview">
                                        <div class="dz-details">
                                            <img data-dz-thumbnail/>
                                        </div>
                                    </div>
                                </div>
                                <div class="sign_dropzone_remove"
                                     id="dropzone-photo-remove">
                                    Remove
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form_control">
                        <div class="form_label">Country</div>
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
                    <div class="form_control">
                        <div class="form_label">Program name</div>
                        <input type="text" class="form_input"
                               name="program-name" required/>
                    </div>

                    <div class="form_control">
                        <div class="form_label">Category</div>
                        <select class="form_select" name="orgTypeId" required>
                            <?php foreach (
                                [
                                    "Gaming",
                                    "Finance",
                                    "Educations & Careers",
                                    "Insurance",
                                    "Health & Beauty ",
                                    "Social & Dating",
                                    "Fashion",
                                    "Telecoms & Utilities",
                                    "Sports & Recreation",
                                    "Computers & Electronics",
                                    "Travel",
                                    "Business To Business",
                                    "Media & Entertainment",
                                    "Home & Garden",
                                    "Non Profit Organizations",
                                    "Shopping & Retail",
                                    "Food & Drink",
                                    "Alcohol & Tobacco",
                                    "Automotive"
                                ] as $ind => $value
                            ) {
                                printf("<option value='%s'>%s</option>",
                                    esc_html($ind + 1), esc_html($value));
                            } ?>
                        </select>
                    </div>
                    <div class="form_control">
                        <div class="form_label">
                            Commission (payable to your affiliates)
                        </div>
                        <select class="form_select" name="tdOrganizationId"
                                autocomplete="off" required>
                            <?php for ($i = 4; $i < 20; $i++)
                                printf('<option value="%d" %s>%d%%</option>',
                                    esc_html($i), $i == 10 ? 'selected' : '', esc_html($i))
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form_buttonWrapper">
                    <a style="visibility: hidden" href="#"
                       class="custom_button -transparent -px-32">Back</a>
                    <a style="" href="?page=tradedoubler&logout=1"
                       class="custom_button -transparent -px-32">Logout</a>
                    <button class="custom_button" type="submit">Finalize
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>
<footer class="footer">
</footer>