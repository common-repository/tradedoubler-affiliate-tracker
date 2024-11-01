<?php
if ( ! defined('ABSPATH')) {
    exit;
}
$dashboard = admin_url('admin.php?page=tradedoubler');
$codes     = admin_url('admin.php?page=tradedoubler&tab=codes');
$tracking  = admin_url('admin.php?page=tradedoubler&tab=tracking');
$settings  = admin_url('admin.php?page=tradedoubler&tab=settings');
$products  = admin_url('admin.php?page=tradedoubler&tab=products');
$urlLogin  = admin_url('admin.php?page=tradedoubler');

$vat_commision      = get_option('tradedoubler_vat_commision', 'no');
$shipping_commision = get_option('tradedoubler_shipping_commision', 'no');


$org_ID     = get_option('tradedoubler_org_ID', '');
$event_ID   = get_option('tradedoubler_event_ID', '');
$program_ID = get_option('tradedoubler_program_ID', '');
$segmentID  = get_option('tradedoubler_segment_ID', '');

$api = new TradedoublerAPI(get_option('tradedoubler_credentials', ''));
$programs = $api->getProgramInfo()['items'];

require "header.php";
?>
<main class="main -max">
    <div class="main_container container">
        <div class="main_top">
            <div class="main_welcome">Tracking settings</div>
            <a target="_blank"
               href="https://grow-platform.tradedoubler.com/login"
               class="main_top_button custom_button -desktop">
                <img alt=""
                     src="<?php echo esc_html(TRADEDOUBLER_URI) ?>/assets/images/settings.svg">
                View Tracking Settings
            </a>
            <a target="_blank"
               href="https://grow-platform.tradedoubler.com/login"
               class="main_top_button custom_button -mobile">
                <img alt=""
                     src="<?php echo esc_html(TRADEDOUBLER_URI) ?>/assets/images/settings.svg">
            </a>
        </div>
        <div class="main_top_text">
            Please check that these values are correct & select the affiliate
            commission.
        </div>
        <form method="post" id="tracking-form" class="form" novalidate>
            <div class="main_grid -tracking">
                <div class="tracking_details">
                    <div class="tracking_details_title"
                         title="segment <?php echo esc_attr($segmentID) ?>">Details
                    </div>
                    <div class="tracking_details_box">
                        <div class="form_grid -one">
                            <div class="form_control">
                                <div class="form_label">Program name</div>
                                <select autocomplete="off"
                                        style="max-width: none;background-color:white;"
                                        name="program_new" class="form_input">
                                    <?php
                                    foreach($programs as $program)
                                        printf('<option %s %s value="%s">%s</option>',
                                            $program['closedProgram'] ? '' : '',
                                            $program_ID == $program['id'] ? 'selected' : '',
                                            esc_html($program['id']),
                                            esc_html($program['name'] . ($program_ID == $program['id'] ? ' (active)' : ''))
                                        );
                                    ?>
                                </select>
                            </div>
                            <div class="form_control">
                                <div class="form_label">Organization ID</div>
                                <input type="text" class="form_input" readonly
                                       value="<?php echo esc_attr($org_ID) ?>"/>
                            </div>
                            <div class="form_control">
                                <div class="form_label">Program ID</div>
                                <input type="text" class="form_input" readonly
                                       value="<?php echo esc_attr($program_ID) ?>"/>
                            </div>
                            <div class="form_control">
                                <div class="form_label">Event ID</div>
                                <input type="text" class="form_input" readonly
                                       value="<?php echo esc_attr($event_ID) ?>"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tracking_commission">
                    <div class="tracking_commission_title">Commission</div>
                    <div class="tracking_commission_box">
                        <div class="form_agreement">
                            <input autocomplete="off" <?php echo $vat_commision
                                                          == 'yes' ? 'checked'
                                : '' ?> value="yes" class="form_checkbox_hidden"
                                   type="checkbox" id="commission-vat"
                                   name="commission-vat"
                            />
                            <label class="form_checkbox_label"
                                   for="commission-vat">
                                <span class="form_agreement_text">I wish to pay affiliate commission on VAT</span>
                            </label>
                        </div>
                        <div class="form_agreement">
                            <input autocomplete="off" <?php echo $shipping_commision
                                                          == 'yes' ? 'checked'
                                : '' ?> value="yes" class="form_checkbox_hidden"
                                   type="checkbox" id="commission-delivery"
                                   name="commission-delivery"
                            />
                            <label class="form_checkbox_label"
                                   for="commission-delivery">
                  <span class="form_agreement_text">I wish to pay affiliate commission on
                    delivery/shipping costs</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</main>
<footer class="footer -white">
    <div class="footer_container container -main">
        <a href="<?php echo esc_attr($dashboard) ?>" class="custom_button -whiteRed -px-32">Cancel</a>
        <button type="submit" form="tracking-form" class="custom_button">Save
        </button>
    </div>
</footer>