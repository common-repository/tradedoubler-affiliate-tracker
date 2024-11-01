<?php
$api     = new TradedoublerAPI(get_option('tradedoubler_credentials',
    ''));
$currency = TradedoublerActions::getCurrency();
$program_ID_active  = '';
if(isset($_GET['program_id']) && $_GET['program_id'])
    $program_ID_active = $_GET['program_id'];
$topAffi = $api->getTopAffi($currency['currency'], $program_ID_active);
?>
<table>
    <tr>
        <td>#</td>
        <td>Affiliate Name</td>
        <td>Total Sales</td>
    </tr>
    <?php foreach ($topAffi['items'] as $ind => $single) { ?>
        <tr>
            <td><?php echo (int)($ind + 1) ?></td>
            <td><?php echo esc_html($single['sourceName']) ?>
                <span><?php echo esc_html($single['sourceId']) ?></span></td>
            <td><?php echo esc_html(wp_strip_all_tags(wc_price(number_format($single['salesOrderValue'], 2)))) ?></td>
        </tr>
    <?php } ?>
</table>