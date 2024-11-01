<?php
$program_ID = get_option('tradedoubler_program_ID', '');
$api        = new TradedoublerAPI(get_option('tradedoubler_credentials',
    ''));
$currency = TradedoublerActions::getCurrency();
if(isset($_GET['program_id']) && $_GET['program_id'])
    $program_ID = $_GET['program_id'];
$stats = $api->getStatistics($program_ID,$currency['currency']);

$data7  = [];
$data14 = ['labels' => [], 'graphData' => []];
if(!isset($stats['items']) || !is_array($stats['items']))
    $stats['items'] = [];
if (empty($stats['items'])) {
    for ($i = 0; $i < 14; $i++) {
        $stats['items'][] = [
            'date'  => gmdate('Y-m-d', strtotime("-$i days")),
            'sales' => 0
        ];
    }
}

foreach (($stats['items']) as $single) {
    $data14['labels'][]    = gmdate('d.m', strtotime($single['date']));
    $data14['graphData'][] = $single['sales'];
}

$data7['labels']
    = (array_slice(($data14['labels']), -7,
    7));
$data7['graphData']
    = (array_slice(($data14['graphData']), -7,
    7));
?>

<div class="graph_section"
     data-7="<?php echo esc_attr(json_encode($data7)) ?>"
     data-14="<?php echo esc_attr(json_encode($data14)) ?>">
    <div class="graph_section_top">
        <div class="graph_section_title">Total Sales</div>
        <div class="graph_section_range">
            <div class="graph_section_range_label">From</div>
            <div class="graph_section_range_select">
                <select class="graph_select">
                    <option value="7">Last 7 days</option>
                    <option value="14">Last 14 days</option>
                </select>
            </div>
        </div>
    </div>
    <div class="graph_section_graph">
        <canvas id="graph"></canvas>
    </div>
</div>