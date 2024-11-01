<?php
$program_ID     = get_option('tradedoubler_program_ID', '');
$api            = new TradedoublerAPI(get_option('tradedoubler_credentials',
    ''));
if(isset($_GET['program_id']) && $_GET['program_id'])
    $program_ID = $_GET['program_id'];
$pendingSources = $api->getPendingAffi($program_ID);
?>
<table>
    <tr>
        <td>#</td>
        <td>Affiliate Name</td>
    </tr>
    <?php
    if (isset($pendingSources['items'])
        && is_array($pendingSources['items'])
    ) {
        foreach (
            $pendingSources['items'] as $ind => $source
        ) {
            printf('<tr><td>%d</td><td>%s <span>%s</span></td>',
                (int)($ind + 1), esc_html($source['sourceName']),
                esc_html($source['sourceId']));
        }
    }
    ?>
</table>