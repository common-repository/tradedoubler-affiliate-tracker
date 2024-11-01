<?php
if ( ! defined('ABSPATH')) {
    exit;
}
$_GET['tab'] = isset($_GET['tab']) ? $_GET['tab'] : '';
$products    = admin_url('admin.php?page=tradedoubler&tab=products');
$codes       = admin_url('admin.php?page=tradedoubler&tab=codes');
$tracking    = admin_url('admin.php?page=tradedoubler&tab=tracking');
$settings    = admin_url('admin.php?page=tradedoubler&tab=settings');
$products    = admin_url('admin.php?page=tradedoubler&tab=products');
$urlLogin    = admin_url('admin.php?page=tradedoubler');
?>
<header class="header">
    <div class="header_container container">
        <a href="#" class="header_logo"><img
                    src="<?php echo esc_html(TRADEDOUBLER_URI) ?>/assets/images/logo.svg"
                    alt="grow"/></a>
        <div class="header_menu">
            <button class="header_menu_open">
                <img src="<?php echo esc_html(TRADEDOUBLER_URI) ?>/assets/images/menu-open.svg">
            </button>
            <div class="menu">
                <a href="#" class="menu_logo"><img
                            src="<?php echo esc_html(TRADEDOUBLER_URI) ?>/assets/images/logo.svg"
                            alt="grow"/></a>
                <a href="<?php echo esc_attr($urlLogin) ?>"
                   class="menu_item <?php echo $_GET['tab'] == ''
                                        || $_GET['tab'] == 'dashboard'
                       ? ' -current' : '' ?>">
                    <img src="<?php echo esc_html(TRADEDOUBLER_URI) ?>/assets/images/menu-dashboard<?php echo $_GET['tab']
                                                                                      == ''
                                                                                      || $_GET['tab']
                                                                                         == 'dashboard'
                        ? '-current' : '' ?>.svg">
                    <div class="menu_item_text">
                        Dashboard
                    </div>
                </a>
                <a href="<?php echo esc_attr($products) ?>"
                   class="menu_item <?php echo $_GET['tab'] == 'products' ? ' -current'
                       : '' ?>">
                    <img src="<?php echo esc_html(TRADEDOUBLER_URI) ?>/assets/images/menu-products<?php echo $_GET['tab']
                                                                                     == 'products'
                        ? '-current' : '' ?>.svg">
                    <div class="menu_item_text">
                        Products
                    </div>
                </a>
                <a href="<?php echo esc_attr($codes) ?>"
                   class="menu_item <?php echo $_GET['tab'] == 'codes' ? ' -current'
                       : '' ?>">
                    <img src="<?php echo esc_html(TRADEDOUBLER_URI) ?>/assets/images/menu-discount<?php echo $_GET['tab']
                                                                                     == 'codes'
                        ? '-current' : '' ?>.svg">
                    <div class="menu_item_text">
                        Discount Codes
                    </div>
                </a>
                <a href="<?php echo esc_attr($tracking) ?>"
                   class="menu_item <?php echo $_GET['tab'] == 'tracking' ? ' -current'
                       : '' ?>">
                    <img src="<?php echo esc_html(TRADEDOUBLER_URI) ?>/assets/images/menu-tracking<?php echo $_GET['tab']
                                                                                     == 'tracking'
                        ? '-current' : '' ?>.svg">
                    <div class="menu_item_text">
                        Tracking
                    </div>
                </a>
                <a href="<?php echo esc_attr($settings) ?>"
                   class="menu_item <?php echo $_GET['tab'] == 'settings' ? ' -current'
                       : '' ?>">
                    <img src="<?php echo esc_html(TRADEDOUBLER_URI) ?>/assets/images/menu-settings<?php echo $_GET['tab']
                                                                                     == 'settings'
                        ? '-current' : '' ?>.svg">
                    <div class="menu_item_text">
                        Settings
                    </div>
                </a>
                <button class="header_menu_close">
                    <img src="<?php echo esc_html(TRADEDOUBLER_URI) ?>/assets/images/menu-close.svg">
                </button>
            </div>
        </div>
    </div>
</header>