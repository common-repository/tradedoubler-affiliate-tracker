<?php
if ( ! defined('ABSPATH')) {
    exit;
}
$products  = admin_url('admin.php?page=tradedoubler&tab=products');
$codes     = admin_url('admin.php?page=tradedoubler&tab=codes');
$tracking  = admin_url('admin.php?page=tradedoubler&tab=tracking');
$settings  = admin_url('admin.php?page=tradedoubler&tab=settings');
$products  = admin_url('admin.php?page=tradedoubler&tab=products');
$dashboard = admin_url('admin.php?page=tradedoubler');
require "header.php";

$api = new TradedoublerAPI(get_option('tradedoubler_credentials', ''));

$org = TradedoublerActions::getProgram();

$program_id = get_option('tradedoubler_program_ID', '');
$event_ID   = get_option('tradedoubler_event_ID', '');
$segmentID  = get_option('tradedoubler_segment_ID', '');

$isoCountries = getCountriesListTradeDoubler();
$categoryID   = $org['categoryIds'][0];

$commision   = 5;
$commissions = $api->getCommissions($program_id);

foreach ($commissions['items'] as $segment) {
            if ($segment['segmentId'] == $segmentID
                && $segment['eventId'] == $event_ID
            ) {
                $commision = $segment['publisherCommissionPercent'];
            }
}

?>
<main class="main -max">
    <div class="main_container container">
        <div class="main_top">
            <div class="main_welcome">Configure your program</div>
            <div style="display:flex;">
                <a target="_blank" style="margin-right: 20px;"
                   href="https://grow-platform.tradedoubler.com/login#login-container"
                   class="main_top_button custom_button -desktop">
                    <img src="<?php echo esc_html(TRADEDOUBLER_URI) ?>/assets/images/settings.svg">
                    Program Settings
                </a>
                <a target="_blank"
                   href="https://grow-platform.tradedoubler.com/login#login-container"
                   class="main_top_button custom_button -mobile">
                    <img src="<?php echo esc_html(TRADEDOUBLER_URI) ?>/assets/images/settings.svg">
                </a>
            </div>
        </div>
        <div class="main_top_text">

        </div>
        <form method="post" class="form" id="settings-form"
              enctype="multipart/form-data" novalidate>
            <?php
            if (isset(TradedoublerViews::$errors)
                && ! empty(TradedoublerViews::$errors)
            ) {
                foreach (TradedoublerViews::$errors as $single) {
                    if (is_array($single) && count($single) === 1) {
                        $single = $single[0];
                    }
                    if (is_array($single) && isset($single['message'])) {
                        printf("<div style='padding: 15px; margin-bottom: 20px;
border: 1px solid #ebccd1;background-color: #f2dede;color: #a94442; border-radius: 4px'>%s</div>",
                            esc_html($single['message']));
                    }
                }
            } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
                ?>
                <div style='padding: 15px; margin-bottom: 20px;
border: 1px solid #dff0d8;background-color: #dff0d8;color: #3c763d; border-radius: 4px'>
                    Settings have been saved
                </div>
            <?php } ?>
            <div class="main_grid">
                <div class="company_logo">
                    <div class="company_logo_title">
                        Company Logo
                    </div>
                    <input type="dropzone" class="form_dropzone_input"/>
                    <input type="file" name="logo" class="form_dropzone_file"/>
                    <div class="main_dropzone my_dropzone" data-url="/">
                        <div class="main_dropzone_alert">Drop image here</div>
                        <div class="main_dropzone_container">
                            <div class="main_dropzone_preview_container">
                                <div class="main_dropzone_preview"
                                     id="dropzone-preview-container">
                                    <img data-dz-thumbnail=""
                                         src="<?php echo esc_attr($org['logoUrl']) ?>" alt=""/>
                                </div>
                            </div>
                            <div class="main_dropzone_buttons">
                                <div class="main_dropzone_upload"
                                     id="logo-dropzone">
                                    <div class="dz-message" data-dz-message>
                                        Upload new file
                                    </div>
                                </div>
                                <div class="main_dropzone_preview_template"
                                     id="preview-template">
                                    <div class="dz-preview dz-file-preview">
                                        <div class="dz-details">
                                            <img data-dz-thumbnail/>
                                        </div>
                                    </div>
                                </div>
                                <div class="main_dropzone_remove"
                                     id="dropzone-photo-remove">
                                    Remove
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="company_details">
                    <div class="company_details_title">
                        Details
                    </div>
                    <div class="company_details_box">
                        <div class="form_grid">
                            <div class="form_control">
                                <div class="form_label">Country</div>
                                <?php
                                foreach ($isoCountries as $single) {
                                    if ($single['id']
                                        == $org['countryCode']
                                    ) {
                                        printf(' <input type="text" class="form_input"  value="%s" readonly />',
                                            esc_html($single['text'])
                                        );
                                    }
                                }
                                ?>
                            </div>
                            <div class="form_control">
                                <div class="form_label">Program name</div>
                                <input readonly type="text" class="form_input"
                                       name="program-name"
                                       value="<?php echo esc_html($org['name']) ?>"
                                       required/>
                            </div>
                            <div class="form_control">
                                <div class="form_label">Category</div>
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
                                    if ($ind + 1 == $categoryID) {
                                        printf("<input type='text' class='form_input'  readonly value='%s'/> <input type='hidden' value='%s' name='orgTypeId'>",
                                            esc_html($value), (int)($ind + 1));
                                    }
                                } ?>
                            </div>
                            <div class="form_control">
                                <div class="form_label">Commission (payable to
                                    your affiliates)
                                </div>
                                <?php
                                printf("<input type='text' class='form_input' 
readonly value='%s'/> <input type='hidden' value='%s' name='tdOrganizationId'>",
                                    esc_attr($commision . '%'), esc_attr($commision))
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</main>
<footer class="footer -white">

    <div class="footer_container container -main -settings">
        <a style="color:#09c;"  href="?page=tradedoubler&logout=1"
            onclick="return confirm('I acknowledge that by signing out of the Grow application my sales will no longer track and my program may be suspended')"
            class="custom_button -whiteRed -px-32">Logout</a>
        <div>
            <a href="<?php echo esc_attr($dashboard) ?>"
               class="custom_button -whiteRed -px-32">Cancel</a>
            <button type="submit" form="settings-form" class="custom_button">Save
            </button>
        </div>
    </div>
</footer>