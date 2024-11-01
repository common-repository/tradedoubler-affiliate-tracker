<?php
$api          = new TradedoublerAPI(get_option('tradedoubler_credentials',
    ''));
$currency = TradedoublerActions::getCurrency();
$program_ID = '';
if(isset($_GET['program_id']) && $_GET['program_id'])
    $program_ID = $_GET['program_id'];
$transactions = $api->getRecentTransactions($currency['currency'], $program_ID);
?>
<table>
    <tr>
        <td>Affiliate</td>
        <td>Transaction Time</td>
        <td>Total Sales</td>
        <td>Commission</td>
        <td>Order Reference</td>
    </tr>
    <?php
    if (isset($transactions['items'])
        && is_array($transactions['items'])
    ) {
        foreach ($transactions['items'] as $single) { ?>
            <tr>
                <td><?php echo esc_html($single['sourceName']) ?>
                    <span><?php echo esc_html($single['sourceId']) ?></span>
                </td>
                <td><?php echo esc_html(gmdate('d.m.Y H:i',
                        strtotime($single['timeOfTransaction']))) ?></td>
                <td>
                    <?php echo  esc_html(wp_strip_all_tags(wc_price(number_format($single['orderValue']),
                        2))) ?></td>
                <td>
                    <?php echo  esc_html(wp_strip_all_tags(wc_price(number_format($single['commission'],
                        2)))) ?></td>
                <td><?php echo esc_html($single['orderNumber']) ?></td>
            </tr>
        <?php }
    } ?>
</table>