<?php
if ( ! defined('ABSPATH')) {
    exit;
}
$products = admin_url('admin.php?page=tradedoubler&tab=products');
$codes    = admin_url('admin.php?page=tradedoubler&tab=codes');
$tracking = admin_url('admin.php?page=tradedoubler&tab=tracking');
$settings = admin_url('admin.php?page=tradedoubler&tab=settings');
$products = admin_url('admin.php?page=tradedoubler&tab=products');
$urlLogin = admin_url('admin.php?page=tradedoubler');

if(get_option('last_hubspot_update') + 3600 < time()){
    $api = new TradedoublerHotspot('pat-eu1-6850c2f1-32df-4fab-8d40-6b01473d87a0');
    $api->updateProgram(get_option('tradedoubler_program_ID', ''), time()*1000);
    update_option('last_hubspot_update', time());
}

$program_ID_active  = '';
if(isset($_GET['program_id']) && $_GET['program_id'])
    $program_ID_active = $_GET['program_id'];


$api = new TradedoublerAPI(get_option('tradedoubler_credentials',
    ''));

$currency = TradedoublerActions::getCurrency();

$info = $api->getPseudoDashboard($currency['currency'], $program_ID_active);


$user = $api->getUsersMe();

$sales               = (double)$info['sales'];
$salesOrderValue     = $info['salesOrderValue'];
$clicks              = $info['clicks'];
$avg                 = $salesOrderValue / ($sales == 0 ? 1 : $sales);
$publisherCommission = $info['publisherCommission'];

$programs = $api->getProgramInfo()['items']; //we pull that for program switcher


require "header.php";
?>
<main class="main">
    <div class="main_overlay">
        <div class="main_overlay_circle">
            <img alt="doot doot"
                 src="<?php echo esc_html(TRADEDOUBLER_URI) ?>/assets/images/doot-doot.svg">
        </div>
        <div class="main_overlay_title">
            Start by creating your program
        </div>
        <div class="main_overlay_text">
            Set up your campaign to track turnover, conversion and more.
        </div>
        <a target="_blank" href="#" class="main_overlay_button custom_button">
            Get started
        </a>
    </div>
    <div class="main_container container">
        <div class="main_top -dashboard">
            <div class="main_welcome">Welcome,
                <span><?php echo esc_html($user['firstName']) ?></span>
            </div>
            <form id="pageSwitcher" >
                <input type="hidden" value="tradedoubler" name="page"/>
                <select onchange="document.querySelector('#pageSwitcher').submit();" autocomplete="off" style="max-width: none;" name="program_id" class="form_input">
                    <option value="">All programs</option>
                    <?php
                    foreach($programs as $program)
                        printf('<option %s %s value="%s">%s</option>',
                            !$program['active'] || $program['closedProgram'] || $program['paused'] ? 'disabled' : '',
                            $program_ID_active == $program['id'] ? 'selected' : '',
                            esc_html($program['id']),
                            esc_html($program['name'])
                        );
                    ?>
                </select>
            </form>
        </div>
        <div style='padding: 15px; margin-bottom: 20px;
border: 1px solid #dff0d8;background-color: #dff0d8;color: #3c763d; border-radius: 4px'>
            This plugin provides you with high level performance stats and
            allows you to configure your program settings and tracking code. We
            also take care of creating your product feeds and sending them,
            along with your discount codes to the Grow system. More features and
            functionality are available on the <a target="_blank"
                                                  href="https://grow-platform.tradedoubler.com/login">Grow
                platform.</a>
            <br><br><a target="_blank"
                       href="https://knowledge.tradedoubler.com/grow-knowledge/kb-tickets/new">Please
                contact your Grow team for more assistance.</a>
        </div>
        <div class="main_grid">
            <div class="info_boxes">
                <div class="info_boxes_item">
                    <div class="info_boxes_item_flex">
                        <div class="info_boxes_item_circle">
                            <div class="info_boxes_item_circle_inner">
                                <img src="<?php echo esc_html(TRADEDOUBLER_URI) ?>/assets/images/total-sales.svg">
                            </div>
                        </div>
                        <div class="info_boxes_item_textbox">
                            <div class="info_boxes_item_title">Total Sales
                                <span>(30 days)</span></div>
                            <div class="info_boxes_item_value">
                                <?php echo  esc_html( wp_strip_all_tags(wc_price(number_format($salesOrderValue,
                                    2)))) ?></div>
                        </div>
                    </div>
                </div>
                <div class="info_boxes_item">
                    <div class="info_boxes_item_flex">
                        <div class="info_boxes_item_circle">
                            <div class="info_boxes_item_circle_inner">
                                <img src="<?php echo esc_html(TRADEDOUBLER_URI) ?>/assets/images/transactions.svg">
                            </div>
                        </div>
                        <div class="info_boxes_item_textbox">
                            <div class="info_boxes_item_title">Transactions
                                <span>(30 days)</span></div>
                            <div class="info_boxes_item_value"><?php echo  esc_html($sales) ?></div>
                        </div>
                    </div>
                </div>
                <div class="info_boxes_item">
                    <div class="info_boxes_item_flex">
                        <div class="info_boxes_item_circle">
                            <div class="info_boxes_item_circle_inner">
                                <svg width="12" height="12" viewBox="0 0 12 12"
                                     fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9.318 4.20225L5.25 3.75V1.125C5.25 0.504 4.746 0 4.125 0C3.504 0 3 0.504 3 1.125V6.75H2.25V5.25H1.875C1.254 5.25 0.75 5.754 0.75 6.375V7.74975C0.75 8.56125 1.01325 9.351 1.5 9.99975L3 12H9.75L11.2245 7.0845C11.628 5.739 10.7137 4.35675 9.318 4.20225ZM6 9.75H5.25V6H6V9.75ZM8.25 9.75H7.5V6H8.25V9.75Z"
                                          fill="#0099CC"/>
                                </svg>
                            </div>
                        </div>
                        <div class="info_boxes_item_textbox">
                            <div class="info_boxes_item_title">Clicks <span>(30 days)</span>
                            </div>
                            <div class="info_boxes_item_value"><?php echo number_format($clicks) ?></div>
                        </div>
                    </div>
                </div>
                <div class="info_boxes_item">
                    <div class="info_boxes_item_flex">
                        <div class="info_boxes_item_circle">
                            <div class="info_boxes_item_circle_inner">
                                <img src="<?php echo esc_html(TRADEDOUBLER_URI) ?>/assets/images/avg-order-value.svg">
                            </div>
                        </div>
                        <div class="info_boxes_item_textbox">
                            <div class="info_boxes_item_title">Average Order
                                Value
                            </div>
                            <div class="info_boxes_item_value">
                                <?php echo esc_html(wp_strip_all_tags(wc_price(number_format($avg, 2)))) ?></div>
                        </div>
                    </div>
                </div>
                <div class="info_boxes_item">
                    <div class="info_boxes_item_flex">
                        <div class="info_boxes_item_circle">
                            <div class="info_boxes_item_circle_inner">
                                <img src="<?php echo esc_html(TRADEDOUBLER_URI) ?>/assets/images/avg-cost.svg">
                            </div>
                        </div>
                        <div class="info_boxes_item_textbox">
                            <div class="info_boxes_item_title">Average Cost Per
                                Order
                            </div>
                            <div class="info_boxes_item_value">
                                <?php echo  esc_html(wp_strip_all_tags(wc_price(number_format($avg
                                                   * ($publisherCommission
                                                      / 10000), 2)))) ?></div>
                        </div>
                    </div>
                </div>
                <div class="info_boxes_item -blue">
                    <div class="info_boxes_item_flex">
                        <div class="info_boxes_item_circle">
                            <div class="info_boxes_item_circle_inner">
                                <img src="<?php echo esc_html(TRADEDOUBLER_URI) ?>/assets/images/more-stats.svg">
                            </div>
                        </div>
                        <div class="info_boxes_item_textbox">
                            <div class="info_boxes_item_title">Want more
                                stats?
                            </div>
                            <div class="info_boxes_item_text">Check our
                                platform!
                            </div>
                        </div>
                    </div>
                    <a target="_blank"
                       href="https://grow-platform.tradedoubler.com/login"
                       class="info_boxes_item_button custom_button -white">Visit</a>
                </div>
            </div>
            <?php
            include 'partials/stats.php';
            ?>
            <div class="affiliates_table">
                <div class="affiliates_table_top">
                    <div class="table_top">
                        <div class="table_top_title">Top Affiliates</div>
                        <a target="_blank"
                           href="https://grow-platform.tradedoubler.com/login"
                           class="table_top_link">
                            Show All
                            <img alt="external"
                                 src="<?php echo esc_html(TRADEDOUBLER_URI) ?>/assets/images/external.svg">
                        </a>
                    </div>
                </div>
                <div class="affiliates_table_table -loading" data-source="affi">
                    <div class="loader">Loading...</div>
                    <?php
                    //                include 'partials/affi.php'
                    ?>
                </div>
            </div>
            <div class="transactions_table">
                <div class="transactions_table_top">
                    <div class="table_top">
                        <div class="table_top_title">Most Recent Transactions
                        </div>
                        <a target="_blank"
                           href="https://grow-platform.tradedoubler.com/login"
                           class="table_top_link">
                            Show All
                            <img src="<?php echo esc_html(TRADEDOUBLER_URI) ?>/assets/images/external.svg">
                        </a>
                    </div>
                </div>
                <div class="transactions_table_table -loading"
                     data-source="transactions">
                    <div class="loader">Loading...</div>
                </div>
            </div>
            <div class="affiliates_table">
                <div class="affiliates_table_top">
                    <div class="table_top">
                        <div class="table_top_title">Pending Affiliates</div>
                        <a target="_blank"
                           href="https://grow-platform.tradedoubler.com/login"
                           class="table_top_link">
                            Show All
                            <img alt="external"
                                 src="<?php echo esc_html(TRADEDOUBLER_URI) ?>/assets/images/external.svg">
                        </a>
                    </div>
                </div>
                <div class="affiliates_table_table -loading"
                     data-source="pending">
                    <div class="loader">Loading...</div>
                </div>
            </div>
        </div>
    </div>
</main>