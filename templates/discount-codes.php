<?php
if ( ! defined('ABSPATH')) {
    exit;
}
$dashboard = admin_url('admin.php?page=tradedoubler');
$products = admin_url('admin.php?page=tradedoubler&tab=products');
$tracking = admin_url('admin.php?page=tradedoubler&tab=tracking');
$settings = admin_url('admin.php?page=tradedoubler&tab=settings');
$products = admin_url('admin.php?page=tradedoubler&tab=products');
$urlLogin = admin_url('admin.php?page=tradedoubler');


require "header.php";

$api = new TradedoublerAPI(get_option('tradedoubler_credentials', ''));
$codes = $api->getVouchers();

$program_ID = get_option('tradedoubler_program_ID', '');
$programs = $api->getProgramInfo()['items'];
$programName = '';
foreach($programs as $program)
    if($program_ID == $program['id'])
        $programName = $program['name'];

$wooCodes = get_posts([
    'post_type' => 'shop_coupon',
    'nopaging'  => 1,
    'orderby'   => 'date'
]);

$activeCodes = [];
if(isset($_POST['voucher']) && is_array($_POST['voucher'])){
    foreach($_POST['voucher'] as $code => $useless)
    {
        $activeCodes[] = $code;
    }
    if(!empty($_POST)){
        update_option('activated_codes_tradedoubler',$activeCodes);
    }
}


TradedoublerActions::loadCodesIntoPlatform();
$codes = $api->getVouchers();

?>
<main class="main">
    <div class="main_container container">
        <div class="main_top">
            <div class="main_welcome">Discount Codes</div>
            <a target="_blank"
               href="https://grow-platform.tradedoubler.com/login"
               class="main_top_button custom_button -manage">
                Manage
            </a>
            <a target="_blank"
               href="<?php echo esc_attr(admin_url('/edit.php?post_type=shop_coupon')) ?>"
               class="main_top_button custom_button -desktop">
                <img alt="plus"
                     src="<?php echo esc_html(TRADEDOUBLER_URI) ?>/assets/images/plus.svg">
                New Discount Code
            </a>
            <a target="_blank"
               href="<?php echo esc_attr(admin_url('/edit.php?post_type=shop_coupon')) ?>"
               class="main_top_button custom_button -mobile">
                <img alt="settings"
                     src="<?php echo esc_html(TRADEDOUBLER_URI) ?>/assets/images/settings.svg">
            </a>
        </div>
        <div class="main_top_text">
            Discount codes are retrieved automatically for your convenience.<br>
            Please note that we do not automatically sync your codes, turn them
            on to share them with your affiliates or sign in to the platform to edit these settings.<br>
            You are currently viewing discount codes for your '<?php echo esc_html($programName) ?>'
            program only, as selected in the "Tracking" tab.<br>
            Please note that limited-use codes are not imported,
            please contact us to add these to your account for you.

            <?php include 'partials/codes-alerts.php'; ?>
        </div>

        <form method="post" id="tracking-form" class="discount_table sorting_table" novalidate>
            <input type="hidden" name="action" value="update_voucher">
            <table>
                <tr>
                    <td class="sorting_table_item" data-order="upDown"
                        data-type="text">
                        <div class="discount_flex">
                            Voucher Code <img class="discount_sort"
                                              src="<?php echo esc_html(TRADEDOUBLER_URI) ?>/assets/images/chevron-upDown.svg">
                        </div>
                    </td>
                    <td class="sorting_table_item" data-order="upDown"
                        data-type="text">
                        <div class="discount_flex">
                            Description <img class="discount_sort"
                                             src="<?php echo esc_html(TRADEDOUBLER_URI) ?>/assets/images/chevron-upDown.svg">
                        </div>
                    </td>
                    <td class="sorting_table_item" data-order="upDown"
                        data-type="text">
                        <div class="discount_flex">
                            Discount <img class="discount_sort"
                                          src="<?php echo esc_html(TRADEDOUBLER_URI) ?>/assets/images/chevron-upDown.svg">
                        </div>
                    </td>
                    <td class="sorting_table_item" data-order="upDown"
                        data-type="text">
                        <div class="discount_flex">
                            Type <img class="discount_sort"
                                      src="<?php echo esc_html(TRADEDOUBLER_URI) ?>/assets/images/chevron-upDown.svg">
                        </div>
                    </td>
                    <td>
                        Date Range
                    </td>
                    <td class="sorting_table_item" data-order="upDown"
                        data-type="date">
                        <div class="discount_flex">
                            Creation Date <img class="discount_sort"
                                               src="<?php echo esc_html(TRADEDOUBLER_URI) ?>/assets/images/chevron-upDown.svg">
                        </div>
                    </td>
                    <td>
                        Commission
                    </td>
                    <td>
                        Status
                    </td>
                    <td>

                    </td>
                </tr>
                <?php
                usort($wooCodes, function ($a, $b){
                    return TradedoublerActions::is_referral_coupon_valid(mb_strtoupper($a->post_title)) <
                           TradedoublerActions::is_referral_coupon_valid(mb_strtoupper($b->post_title));
                });

                foreach ($wooCodes as $ind => $woo_code) {
                    $codeNameRaw = ($woo_code->post_title);
                    $codeName = mb_strtoupper($woo_code->post_title);

                    $type      = !TradedoublerActions::isCodeUsedOnce($codeNameRaw);
                    if(!$type)
                        continue;

                    $description = $woo_code->post_excerpt;
                    $meta        = get_post_meta($woo_code->ID);

                    $discType  = '';
                    $discValue = $meta['coupon_amount'][0];


                    $code       = null;
                    $codeStart  = '';
                    $codeEnd    = '';
                    $codeUpdate = gmdate('d/m/Y H:i',strtotime($woo_code->post_date));

                    foreach($codes as $singleCode)
                    {
                        if(isset($singleCode['code']) && $singleCode['code'] == $codeNameRaw)
                        {
                            $code = $singleCode;
                            break;
                        }
                    }

                    if($code){
                        $codeStart = (gmdate('d/m/Y H:i',
                            ($code['startDate'] / 1000)));
                        $codeEnd = gmdate('d/m/Y H:i',
                            ($code['endDate'] / 1000));
                        $codeUpdate = gmdate('d/m/Y H:i',
                            ($code['updateDate'] / 1000));
                    }

                    $useType =  $meta['discount_type'][0] == 'percent';

                    if ($useType != 1) {
                        $discType = sprintf("%s %s off everything", $discValue,
                            get_woocommerce_currency());
                    }
                    if ($useType == 1) {
                        $discType = sprintf("%s%% off everything",
                            $discValue);
                    }

                    $code['active'] = false;
                    $activeCodes = get_option('activated_codes_tradedoubler',[]);
                    if(in_array($codeNameRaw, $activeCodes))
                        $code['active'] = true;

                    $isLive = true;
                    ?>
                    <tr>
                        <td><?php echo esc_html($codeName) ?></td>
                        <td><?php echo esc_html($description) ?></td>
                        <td><?php echo esc_html($discType) ?></td>
                        <td><?php echo $type ? 'Generic' : 'Exclusive' ?></td>
                        <td><?php echo esc_html($codeStart . ' - <br>' . $codeEnd) ?></td>
                        <td><?php echo esc_html($codeUpdate) ?></td>
                        <td>Default</td>
                        <td title="<?php echo esc_attr(TradedoublerActions::get_referral_coupon_error($codeNameRaw)) ?>">
                            <div class="discount_status <?php echo TradedoublerActions::is_referral_coupon_valid($codeNameRaw)
                                ? '-green' : '-red' ?>">
                                <?php echo TradedoublerActions::is_referral_coupon_valid($codeNameRaw)
                                    ? 'Live' : 'Expired' ?>
                            </div>
                        </td>
                        <td>
                              <?php if( TradedoublerActions::is_referral_coupon_valid($codeNameRaw ) ) {  ?>
                            <input autocomplete="off" id="discount-<?php echo esc_attr($ind) ?>"
                                   type="checkbox" name="voucher[<?php echo esc_attr($codeNameRaw) ?>]"
                                   class="discount_toggle_input"
                                <?php echo $code['active'] ? 'checked' : '' ?>>
                            <label for="discount-<?php echo esc_attr($ind) ?>" class="discount_toggle_label">
                                <div class="discount_toggle_switch"></div>
                            </label>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
                <tr class="discount_table_bg table_bg"></tr>
            </table>
            <input type="hidden" name="voucher['']" value="on" />
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