<?php

$amountActiveCodes = 0;
$amountCodes = 0;

$wooCodes = get_posts([
    'post_type' => 'shop_coupon',
    'nopaging'  => 1,
    'orderby'   => 'date'
]);
$activeCodes = get_option('activated_codes_tradedoubler',[]);

foreach ($wooCodes as $ind => $woo_code) {
    $codeNameRaw = ($woo_code->post_title);
    $codeName    = mb_strtoupper($woo_code->post_title);

    $type = ! TradedoublerActions::isCodeUsedOnce($codeNameRaw);
    if ( ! $type || TradedoublerActions::get_referral_coupon_error($codeNameRaw)) {
        continue;
    }
    $amountCodes++;
    if(in_array($codeNameRaw, $activeCodes))
        $amountActiveCodes++;
}



 $state = 0;


 if($amountCodes == 0)
     $state = 1;
 else if ($amountActiveCodes == $amountCodes)
     $state = 4;
 else if ($amountActiveCodes == 0)
     $state = 2;
 else
     $state = 3;
?>

    <?php switch($state){
        case 1: ?>
    <div class="codes-alert -red">
        <a href=<?php echo esc_attr(admin_url('/wp-admin/edit.php?post_type=shop_coupon')) ?>> Create your first discount code </a>
        to start working with some of the largest affiliates. Discount code
        affiliates promote your discounts to their user bases, encouraging them to visit your store.</div>
    <?php break;
      case 2: ?>
    <div class="codes-alert -red">You have <?php echo esc_html($amountCodes) ?> codes available in your store which are not being
        sent to your Grow account. Use the toggles below to share your codes with your affiliates.</div>
    <?php break;
      case 3: ?>
    <div class="codes-alert -yellow">You are sharing <?php echo esc_html($amountActiveCodes) ?> of <?php echo esc_html($amountCodes) ?> codes
        with your affiliates. Use the toggles below to share more.</div>
    <?php break;
      case 4: ?>
          <div class="codes-alert -green">Congratulations! All your codes are being shared with your affiliates.</div>
    <?php break;

    }