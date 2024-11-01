<?php
if ( ! defined('ABSPATH')) {
    exit;
}
if (isset($_SESSION['form_tb']['errors'])) {
    foreach ($_SESSION['form_tb']['errors'] as $single) {
        if (is_array($single)) {
            printf("<div style='padding: 15px; margin-bottom: 20px;
border: 1px solid #ebccd1;background-color: #f2dede;color: #a94442; border-radius: 4px'>%s</div>",
                esc_html($single['message']));
        }
    }
}
$_SESSION['form_tb']['errors'] = [];