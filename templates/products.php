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
require "header.php";

$api = new TradedoublerAPI(get_option('tradedoubler_credentials', ''));

$feeds = $api->getProductFeeds();
if ( ! is_array($feeds)) {
    $feeds = [];
}
$id = TradedoublerActions::getFeedDefaultId();

if (empty($feeds)) {
    $feeds = [];
}
$lastUpdate = gmdate('Y-m-d H:i:s', get_option('tradedoubler_last_time_feed_update'));
?>
<main class="main">
    <div class="main_container container">
        <div class="main_top">
            <div class="main_welcome">Products</div>
            <a target="_blank"
               href="https://grow-platform.tradedoubler.com/login"
               class="main_top_button custom_button -desktop">
                <img alt="settings icon"
                     src="<?php echo esc_html(TRADEDOUBLER_URI) ?>/assets/images/settings.svg">
                Manage Product Feed
            </a>
            <a target="_blank"
               href="https://grow-platform.tradedoubler.com/login"
               class="main_top_button custom_button -mobile">
                <img alt="settings icon"
                     src="<?php echo esc_html(TRADEDOUBLER_URI) ?>/assets/images/settings.svg">
            </a>
        </div>
        <div class="main_top_text">
            Sign in to the platform to edit these settings.
        </div>
        <div class="products_table sorting_table">
            <?php if (empty($feeds)) { ?>
                We are busy creating and processing your product feed. It will be available to your publishers within the next 24 hours
            <?php } elseif ( ! empty($feeds)) { ?>
                <table>
                    <tr>
                        <td class="sorting_table_item" data-order="upDown"
                            data-type="text">
                            <div class="products_flex">
                                Feed Name <img alt="feed name sort"
                                               class="products_sort"
                                               src="<?php echo esc_html(TRADEDOUBLER_URI) ?>/assets/images/chevron-upDown.svg">
                            </div>
                        </td>
                        <td class="sorting_table_item" data-order="upDown"
                            data-type="date">
                            <div class="products_flex">
                                Last Updated <img alt="Updated name sort"
                                                  class="products_sort"
                                                  src="<?php echo esc_html(TRADEDOUBLER_URI) ?>/assets/images/chevron-upDown.svg">
                            </div>
                        </td>
                        <td class="sorting_table_item" data-order="upDown"
                            data-type="number">
                            <div class="products_flex">
                                Products <img alt="Products name sort"
                                              class="products_sort"
                                              src="<?php echo esc_html(TRADEDOUBLER_URI) ?>/assets/images/chevron-upDown.svg">
                            </div>
                        </td>
                        <td class="sorting_table_item" data-order="upDown"
                            data-type="text">
                            <div class="products_flex">
                                Program <img alt="programs name sort"
                                              class="products_sort"
                                              src="<?php echo esc_html(TRADEDOUBLER_URI) ?>/assets/images/chevron-upDown.svg">
                            </div>
                        </td>
                        <td class="sorting_table_item" data-order="upDown"
                            data-type="date">
                            <div class="products_flex">
                                Active <img alt="active name sort"
                                            class="products_sort"
                                            src="<?php echo esc_html(TRADEDOUBLER_URI) ?>/assets/images/chevron-upDown.svg">
                            </div>
                        </td>
                        <td class="sorting_table_item" data-order="upDown"
                            data-type="date">
                            <div class="products_flex">
                                <img alt="active name sort"
                                     class="products_sort"
                                     src="<?php echo esc_html(TRADEDOUBLER_URI) ?>/assets/images/chevron-upDown.svg">
                            </div>
                        </td>
                    </tr>
                    <?php foreach ($feeds as $feed) {
                        $programs = [];
                        foreach($feed['programs'] as $program)
                            $programs[] = $program['name'];
                        ?>
                        <tr>
                            <td>
                                <?php echo esc_html( str_replace(['_Active:N', '_Active:Y'], '',
                                    $feed['name'])) ?>
                            </td>
                            <td>
                                <?php echo esc_html($feed['lastModifiedTime'] === '-'
                                    ? '-'
                                    : gmdate('d/m/Y H:i',
                                        strtotime($feed['lastModifiedTime']))) ?>
                            </td>
                            <td>
                                <?php echo (int)$feed['numberOfProducts'] ?>
                            </td>
                            <td>
                                <?php echo esc_html(implode(', ', $programs)); ?>
                            </td>
                            <td>
                                <?php echo $feed['active'] ? 'active' : 'inactive'; ?>
                            </td>
                            <td>
                                <?php
                                if ((int)$feed['feedId'] != (int)$id) {
                                    echo "<div class='discount_status -red'>Legacy</div>";
                                } else {
                                    echo "<div title='" . esc_attr($lastUpdate) . " GMT' class='discount_status -green'>Auto</div>";
                                }
                                ?>
                            </td>
                        </tr>
                    <?php } ?>
                    <tr class="products_table_bg table_bg"></tr>
                </table>
            <?php } ?>
        </div>
    </div>
</main>