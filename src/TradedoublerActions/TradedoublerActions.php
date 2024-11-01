<?php

class TradedoublerActions
{

    static $instance = null;
    static $dev = false;

    static function init()
    {
        if (self::$instance === null) {
            self::$instance = new TradedoublerActions();
        }
    }

    public function __construct()
    {
        $this->actions();
    }

    private function actions()
    {
        add_action('admin_head', [$this, 'admin_head'], 1, 1);
        add_action('admin_footer', [$this, 'admin_footer']);
        if (is_admin()) {
            add_action('init', [$this, 'loaded']);
        }
        add_action('admin_notices', [$this, 'admin_notices']);
        add_action('wp_loaded', [$this, 'wp_loaded']);

        add_action('wp_head', [$this, 'wp_head']);
        add_action('woocommerce_thankyou', [$this, 'woocommerce_thankyou']);

        add_action('wp_ajax_tm_load_data', function () {
            $component =  $_REQUEST['component'];
            if(current_user_can('manage_options') && in_array($component,
                ['affi', 'codes-alerts', 'stats', 'transactions'])){
                include TRADEDOUBLER_DIR . '/templates/partials/'
                    . $component . '.php';
            }

            die();
        });

        add_action('pre_post_update', function($post_id, $data){
            if($data['post_type'] == 'shop_coupon'){
                $post = get_post($post_id);
                if($post->post_status != 'publish' && $data['post_status'] == 'publish'){
                    TradedoublerActions::removeCode($data['post_title']);
                }
            }
        },99,99);
    }

    public function admin_head()
    {
        if (isset($_GET['page']) && $_GET['page'] === 'tradedoubler') {
            wp_enqueue_style('google-fonts-inter', 'https://fonts.googleapis.com/css?family=Inter:600|Inter:400|Inter:500&display=swap', [], '1.0');

            // Enqueue styles
            wp_enqueue_style('tradedoubler-dropzone', TRADEDOUBLER_URI . '/assets/styles/dropzone.min.css', [], TRADEDOUBLER_PLUG_VER);
            wp_enqueue_style('tradedoubler-select2', TRADEDOUBLER_URI . '/assets/styles/select2.min.css', [], TRADEDOUBLER_PLUG_VER);
            wp_enqueue_style('tradedoubler-flag-icon', TRADEDOUBLER_URI . '/assets/styles/flag-icon.min.css', [], TRADEDOUBLER_PLUG_VER);
            wp_enqueue_style('tradedoubler-style', TRADEDOUBLER_URI . '/assets/styles/style.css', [], TRADEDOUBLER_PLUG_VER);

            // Enqueue scripts
            wp_enqueue_script('tradedoubler-dropzone', TRADEDOUBLER_URI . '/assets/scripts/dropzone.min.js', [], TRADEDOUBLER_PLUG_VER, false);
            wp_enqueue_script('tradedoubler-select2', TRADEDOUBLER_URI . '/assets/scripts/select2.full.min.js', [], TRADEDOUBLER_PLUG_VER, false);
            wp_enqueue_script('tradedoubler-chart', TRADEDOUBLER_URI . '/assets/scripts/chart.min.js', [], TRADEDOUBLER_PLUG_VER, false);

            wp_enqueue_script('tradedoubler-chart', TRADEDOUBLER_URI . '/assets/scripts/chart.min.js', [], TRADEDOUBLER_PLUG_VER, false);

            wp_enqueue_script('tradedoubler-script', TRADEDOUBLER_URI . '/assets/scripts/scripts.js', [], TRADEDOUBLER_PLUG_VER, false);
        }
    }

    public function admin_footer()
    {
    }

    public function loaded()
    {
        if ( ! is_user_logged_in()
             || ! is_array(get_option('tradedoubler_credentials', ''))
        ) {
            return;
        }

//		$this->autocomplete_session();

        add_action('wp_insert_post', function ($post_ID) {
            if (get_post($post_ID)
                && (get_post($post_ID))->post_status == 'publish'
                && (get_post($post_ID))->post_type == 'product'
            ) {
                self::refreshFeed(true, $post_ID);
            }
        });
    }

    public function autocomplete_session()
    {
        $data = get_user_meta(get_current_user_id());
        if ( ! isset($_SESSION['form_tb'])) {
            $_SESSION['form_tb'] = [];
        }

        if ( ! isset($data['billing_address_2'][0])) {
            $data['billing_address_2'][0] = '';
        }

        if ( ! isset($data['billing_company'][0])) {
            $data['billing_company'][0] = '';
        }

        if ( ! isset($_SESSION['form_tb']['website-url'])) {
            $_SESSION['form_tb']['website-url'] = home_url('/');
        }
        if ( ! isset($_SESSION['form_tb']['email'])) {
            $_SESSION['form_tb']['email'] = get_option('admin_email');
        }
        if ( ! isset($_SESSION['form_tb']['password'])) {
            $_SESSION['form_tb']['password'] = '';
        }
        if ( ! isset($_SESSION['form_tb']['first-name'])) {
            $_SESSION['form_tb']['first-name'] = $data['billing_first_name'][0];
        }
        if ( ! isset($_SESSION['form_tb']['last-name'])) {
            $_SESSION['form_tb']['last-name'] = $data['billing_last_name'][0];
        }
        if ( ! isset($_SESSION['form_tb']['company-name'])) {
            $_SESSION['form_tb']['company-name'] = $data['billing_company'][0];
        }
        if ( ! isset($_SESSION['form_tb']['phone'])) {
            $_SESSION['form_tb']['phone'] = $data['billing_phone'][0];
        }
        if ( ! isset($_SESSION['form_tb']['company-registration-number'])) {
            $_SESSION['form_tb']['company-registration-number'] = '';
        }
        if ( ! isset($_SESSION['form_tb']['vat-number'])) {
            $_SESSION['form_tb']['vat-number'] = '';
        }
        if ( ! isset($_SESSION['form_tb']['street-address-2'])) {
            $_SESSION['form_tb']['street-address-2']
                = $data['billing_address_2'][0];
        }
        if ( ! isset($_SESSION['form_tb']['county'])) {
            $_SESSION['form_tb']['county'] = '';
        }
        if ( ! isset($_SESSION['form_tb']['street-address'])) {
            $_SESSION['form_tb']['street-address']
                = trim($data['billing_address_1'][0]);
        }
        if ( ! isset($_SESSION['form_tb']['postcode'])) {
            $_SESSION['form_tb']['postcode'] = $data['billing_postcode'][0];
        }
        if ( ! isset($_SESSION['form_tb']['city'])) {
            $_SESSION['form_tb']['city'] = $data['billing_city'][0];
        }
        if ( ! isset($_SESSION['form_tb']['country'])) {
            $_SESSION['form_tb']['country'] = $data['billing_country'][0];
        }
    }

    public function admin_notices()
    {
        if ( ! class_exists('WooCommerce', true)) {
            $class   = 'notice notice-error';
            $message = __('Tradedoubler plugin requires woocoomerce',
                'tradedoubler');

            printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class),
                esc_html($message));
        }
    }

    public function wp_loaded()
    {
        if(isset($_GET['tduid'])){
            self::setFirstPartyServerCookie($_GET['tduid']);
        }
        if (TradedoublerRouting::userIsActive()) {
            self::refreshFeed();
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            TradedoublerRouting::postRouting();
        }
    }

    public function wp_head()
    {
        $org_id     = get_option('tradedoubler_org_ID');
        $program_id = get_option('tradedoubler_program_ID');
        $event_id   = get_option('tradedoubler_event_ID');
        if ( ! $org_id || ! $program_id) {
            return;
        }
        ?>
        <!-- Start TradeDoubler Landing Page Tag Insert on all landing pages to handle first party cookie-->
        <script>
            (function (i, s, o, g, r, a, m) {
                i['TDConversionObject'] = r;
                i[r] = i[r] || function () {
                    (i[r].q = i[r].q || []).push(arguments)
                }, i[r].l = 1 * new Date();
                a = s.createElement(o), m = s.getElementsByTagName(o)[0];
                a.async = 1;
                a.src = g;
                m.parentNode.insertBefore(a, m)
            })(window, document, 'script', 'https://svht.tradedoubler.com/tr_sdk.js?org=<?php echo esc_html($org_id) ?>&prog=<?php echo esc_html($program_id) ?>&rand=' + Math.random(), 'tdconv');
        </script>
        <!-- End TradeDoubler tag-->
        <?php
    }

    public function woocommerce_thankyou($order_id)
    {
        // Get order object
        $order      = wc_get_order($order_id);
        $order_data = $order->get_data();

        // Get data from settings
        $org_id             = get_option('tradedoubler_org_ID');
        $program_id         = get_option('tradedoubler_program_ID');
        $event_id           = get_option('tradedoubler_event_ID');
        $vat_commision      = get_option('tradedoubler_vat_commision', 'no');
        $shipping_commision = get_option('tradedoubler_shipping_commision',
            'no');


        // Order details variables
        $order_value    = $order->get_total();
        $order_number   = $order->get_order_number();
        $order_currency = $order->get_currency();
        $coupon_codes   = $order->get_coupon_codes();
        $total_tax      = $order_data['total_tax'];
        $total_shipping = $order_data['shipping_total'];
        $first_coupon   = isset($coupon_codes[0]) ? $coupon_codes[0] : '';

        /*
        // Business logic
            If (VAT Box == False) && (Shipping Box == False) Then
            Total Order Amount - VAT - Shipping
            ElseIf (VAT Box == False) && (Shipping Box == True) Then
            Total Order Amount - VAT
            (VAT Box == True) && (Shipping Box == False) Then
            Total Order Amount - Shipping
            Else
            Total Order Amount
        */

        if ($vat_commision === 'no') {
            $order_value -= $total_tax;
        }

        if ($shipping_commision === 'no') {
            $order_value -= $total_shipping;
        }

        if ( ! $org_id || ! $program_id) {
            return;
        }

        $s2sUrl = "https://tbs.tradedoubler.com/report?organization={$org_id}"
            . "&event={$event_id}&orderNumber={$order_number}&orderValue={$order_value}"
            . "&currency={$order_currency}&voucher={$first_coupon}&convtagtid=13"
            . "&tduid=".self::getFirstPartyCookie();
        if(null !== self::getFirstPartyCookie()){
            @fopen($s2sUrl,'r');
        }


        // Inject tradeoubler conversion tag
        echo '<!-- Start TradeDoubler Conversion Tag -->' . PHP_EOL;
        echo "
            <script>
                var TDConversion = {
                    'transactionId': '" . esc_js($order_number) . "',
                    'ordervalue': '" . esc_js($order_value) . "',
                    'voucher': '" . esc_js($first_coupon) . "',
                    'currency': '" . esc_js($order_currency) . "',
                    'event': " . intval($event_id) . "
                };
                (function(o,w,v){var r,c=TDConversion,d=c.products,l=w.localStorage;function getCookie(e){var t=v.cookie,r=e+'=',c=t.indexOf('; '+r);if(-1==c){if(0!=(c=t.indexOf(r)))return null}else c+=2;var o=v.cookie.indexOf(';',c);return-1==o&&(o=t.length),unescape(t.substring(c+r.length,o))}if(void 0!==c.reportInfo)r=c.reportInfo;else if(Array.isArray(d)&&d.length>0){for(var p=0,products=[];p<d.length;p++)products.push('f1='+d[p].id+'&f2='+d[p].name+'&f3='+d[p].price+'&f4='+d[p].qty);r=products.join('|')}r=encodeURIComponent(r),w.tdfallback=w.setTimeout(function(){var e=v.createElement('iframe');e.async='yes',e.width='1',e.height='1',e.frameBorder='0';var t='';'false'!==l.getItem('tdtrackingconsent')?(t='&tduid='+getCookie('tduid'),c.cdt&&(t+='&extid='+c.cdt.extId+'&exttype='+c.cdt.extType)):l.getItem('tdprog')&&l.getItem('tdaff')&&(t='&program='+l.getItem('tdprog')+'&affiliate='+l.getItem('tdaff'));var d=getCookie('tdclid_sn');if(d&&(t+='&tdpeh='+d),''!==t){!1===l.getItem('tdcookieconsent')&&(t+='&cons=false');var n='https://a.imgstatics.com/report?organization='+o+'&event='+c.event+'&orderNumber='+c.transactionId;n+=c.ordervalue?'&orderValue='+c.ordervalue:'',n+=c.currency?'&currency='+c.currency:'',n+=c.voucher?'&voucher='+c.voucher:'',n+=c.eventTime?'&event_time='+c.eventTime:'',n+=c.validOn?'&validOn='+c.validOn:'',n+=c.ttid?'&ttid='+c.ttid:'',n+=c.trafficSource?'&trafficSource='+encodeURIComponent(c.trafficSource):'',n+=t+'&convtagtid=34&type=iframe&reportInfo='+r,e.src=n,v.body.appendChild(e)}},3e3);
                    tdconv('init', o, {element: 'iframe'});
                    tdconv('track', 'sale', TDConversion);
                })('" . esc_js($org_id) . "', window, document);
            </script>" . PHP_EOL;
        // wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/tracking-script.js', array(), $this->version, false );
        echo '<!-- End TradeDoubler Conversion tag -->' . PHP_EOL;

    }

    public static function setFirstPartyServerCookie($tduid){
        setcookie('sGuid', $tduid, time()+(60*60*24*365), '/', $_SERVER['SERVER_NAME'], false, true);
    }

    public static function getFirstPartyCookie(){
        if(isset($_COOKIE['sGuid'])){
            return $_COOKIE['sGuid'];
        }
        elseif(isset($_COOKIE['tduid'])) {
            return $_COOKIE['tduid'];
        }
        else return null;
    }

    public static function removeCode($codeName){
        $codes = get_option('activated_codes_tradedoubler',[]);
        if (($key = array_search($codeName, $codes)) !== false) {
            unset($codes[$key]) ;
        }
        update_option('activated_codes_tradedoubler',$codes);
    }

    public static function isCodeUsedOnce($coupon_code){
        $coupon         = new \WC_Coupon($coupon_code);
        return ((int)get_post_meta($coupon->get_id(), 'usage_count', true)) != 0;
    }

    public static function get_referral_coupon_error($coupon_code)
    {
        $coupon         = new \WC_Coupon($coupon_code);
        $discounts      = new \TradedoublerDiscount(WC()->cart);
        $valid_response = $discounts->is_coupon_valid_limited($coupon);
        if (is_wp_error($valid_response) || self::isCodeUsedOnce($coupon_code)) {
            return $valid_response->get_error_message();
        } else {
            return '';
        }
    }

    public static function is_referral_coupon_valid($coupon_code)
    {
        $coupon         = new \WC_Coupon($coupon_code);
        $discounts      = new \TradedoublerDiscount(WC()->cart);
        $valid_response = $discounts->is_coupon_valid_limited($coupon);
        if (is_wp_error($valid_response) || self::isCodeUsedOnce($coupon_code)) {
            return false;
        } else {
            return $valid_response;
        }
    }

    public static function loadCodesIntoPlatform($post_ID = null)
    {
        $api      = new TradedoublerAPI(get_option('tradedoubler_credentials',
            ''));
        $vouchers = $api->getVouchers();

        $codes = get_posts([
            'post_type' => 'shop_coupon',
            'nopaging'  => 1,
            'orderby'   => 'date'
        ]);

        foreach ($codes as $code) {
            if ($post_ID !== null && $code->ID != $post_ID) {
                continue;
            }

            $pickedVoucher = null;
            foreach ($vouchers as $voucher) {
                if (isset($voucher['code']) &&
                    ($voucher['code']) == ($code->post_title)
                ) {
                    $pickedVoucher = $voucher;
                    break;
                }
            }
            $codeName   = $code->post_title;
            $codeActive = true;
            $activeCodes = get_option('activated_codes_tradedoubler',[]);
            if(!in_array($codeName, $activeCodes) )
                $codeActive = false;

            if ($pickedVoucher === null && $codeActive
                && TradedoublerActions::is_referral_coupon_valid($codeName)) {
                $program_id = self::getProgramIdCodes();

                $meta        = get_post_meta($code->ID);
                $description = $code->post_excerpt;

                if (strlen($description) < 100) {
                    $shortDesc = $description;
                } else {
                    $shortDesc = mb_substr($description, 0, 80);
                }

                $discValue = $meta['coupon_amount'][0];
                $useType   = $meta['usage_limit'][0];
                $end       = isset($meta['date_expires'][0])
                    ? $meta['date_expires'][0] : '';

                $isPerc = $meta['discount_type'][0] == 'percent';


                $endMiniStamp
                    = //strtotime('2022-02-20 18:00')*1000;
                    $end == '' || $end == 0
                        ?
                        strtotime('+5 months') * 1000
                        :
                        $end * 1000;

                $args = [
                    'title'            => $codeName,
                    'programId'        => $program_id,
                    'voucherTypeId'    => 1, // 2 = discount
                    'code'             => $codeName,
                    'shortDescription' => $shortDesc . ' ',
                    'description'      => $description . ' ',
                    "publishStartDate" => time() * 1000,
                    "publishEndDate"   => $endMiniStamp,
                    'startDate'        => time() * 1000,
                    'endDate'          => $endMiniStamp,
                    'discountAmount'   => $discValue,
                    'isPercentage'     => $isPerc,
                ];


                $api->createVoucher($args);
            }

            if ($pickedVoucher !== null && (!$codeActive
                                            || !TradedoublerActions::is_referral_coupon_valid($codeName))){
                $res = $api->deleteVoucher($pickedVoucher['id']);
            }
        }
    }

    static function getProgramIdCodes()
    {
        return get_option('tradedoubler_program_ID', '');
    }

    static function getProgram()
    {
        $api = new TradedoublerAPI(get_option('tradedoubler_credentials',
            ''));

        $orgs = $api->getProgramInfo();

        $program_id = get_option('tradedoubler_program_ID', '');
        $org        = null;

        if (is_array($orgs['items'])) {
            foreach ($orgs['items'] as $single) {
                if ($single['id'] == $program_id) {
                    $org = $single;
                }
            }
        }

        if ($org === null) {
            throw new Exception('No organization');
        }

        return $org;
    }

    static function getFeedDefaultName()
    {
        return esc_html(self::getProgramName() . ' - all products');
    }

    static function getProgramName()
    {

        if (get_transient('program_name_tradedoublerAPI') !== false) {
            return get_transient('program_name_tradedoublerAPI');
        }
        $res = self::getProgram();

        if ($res['name']) {
            set_transient(
                'program_name_tradedoublerAPI',
                $res['name'],
                200);
        }

        return $res['name'];
    }

    /**
     * @return array|null
     * @throws Exception
     */
    static function getFeedDefault()
    {
        $api       = new TradedoublerAPI(get_option('tradedoubler_credentials',
            ''));
        $programID = get_option('tradedoubler_program_ID', '');
        $feeds     = $api->getProductFeedsTM();

        if ( ! is_array($feeds)) {
            throw new Exception('Malformed input');
        }

        $feedPicked = null;
        foreach ($feeds as $feed) {
            // get the newest one from current program
            if ($feed['programId'] == $programID && $feed['active'] == true) {
                $feedPicked = $feed;
                break;
            }
        }

        return $feedPicked;
    }

    /**
     * @return int|null
     * @throws Exception
     */
    static function getFeedDefaultId()
    {
        $feed = self::getFeedDefault();
        if($feed === null)
            return null;

        return $feed['feedId'];
    }

    static function refreshFeed(
        $forceRefresh = false,
        $post_ID = null,
        $recursion = false
    ) {
        try {
            $lastTime = get_option('tradedoubler_last_time_feed_update');
            if (time() - $lastTime < 60 * 60 * 23
                && ! $forceRefresh
            ) //refresh every day
            {
                return;
            }
            $api       = new TradedoublerAPI(get_option('tradedoubler_credentials',
                ''));
            $programID = get_option('tradedoubler_program_ID', '');
            $nameFeed  = TradedoublerActions::getFeedDefaultName();

            $feedID = self::getFeedDefaultId();

            if ($feedID == null) {

                $orgs = $api->getProgramInfo();

                $org = null;

                if (is_array($orgs['items'])) {
                    foreach ($orgs['items'] as $single) {
                        if ($single['id'] == $programID) {
                            $org = $single;
                        }
                    }
                }

                if ($org != null) {
                    $api->createFeed(
                        $nameFeed,
                        $programID,
                        get_woocommerce_currency(),
                        'EN'
                    );
                    if ( ! $recursion) {
                        self::refreshFeed(true, null, true);
                    }

                    return;
                }
            }

            if ($feedID) {
                (self::setFeedScheduler());
            }

            if ($post_ID === null) {
                update_option('tradedoubler_last_time_feed_update', time());
            }
        } catch (Exception $e) {
        }
    }
    
    static function setFeedScheduler(){
        $feedID = self::getFeedDefaultId();
        $api
            = new TradedoublerAPI(get_option('tradedoubler_credentials', ''));

        $url = home_url('/') . 'wp-content/plugins/tradedoubler-affiliate-tracker/feed.php';
        $values = [
            'feedId'                 => $feedID,
            "httpDownloadUrl"        => $url,
            "downloadCronExpression" => "0 0 1,2,3,4 1/1 * ?",
            "pfCsvConfig"            => [
                "fieldSeparator"   => ",",
                "concatCategories" => false, // fix this
                "escapeString"     => "\"", //fix this
                "fieldsPerLine"    => 7,
                "ignoreFirstLine"  => false,
                "csvColumns"       => (object)[
                    "0" => "productId",
                    "1" => "productname",
                    "2" => "imageUrl",
                    "3" => "description",
                    "4" => "category",
                    "5" => "price",
                    "6" => "productUrl"
                ]
            ]
        ];

        return $api->setFeedScheduler($values);
    }

    /**
     * @deprecated
     * @param $feedID
     * @param $productID
     *
     * @return void
     * @throws Exception
     */
    static function updateProductFeed($feedID, $productID = null)
    {
        $products = self::getProductsForFeed();

        if ( ! empty($products)) {
            $api = new TradedoublerAPI(get_option('tradedoubler_credentials',
                ''));
            $res = $api->updateFeed($products, $feedID, $productID == null);
        }
    }

    static function getProductsForFeed()
    {
        $productsWP = get_posts([
            'post_type'   => 'product',
            'post_status' => 'publish',
            'nopaging'    => 1,
            'meta_query'  => array(
                array(
                    'key'     => '_stock_status',
                    'value'   => 'outofstock',
                    'compare' => '!=',
                )
            ),
        ]);

        $products = [];

        foreach ($productsWP as $product) {
            $product_wc = new WC_Product($product->ID);

            $imageID = get_post_thumbnail_id($product);
            $image   = wp_get_attachment_metadata($imageID);
            $categories = [];
            foreach($product_wc->get_category_ids() as $term_id)
            {
                $term = get_term($term_id);
                if($term && $term->name)
                    $categories[] = ['name' => $term->name];
            }

            if ($imageID) {
                $imageArray = [
                    'url'    => wp_get_attachment_url($imageID),
                    'width'  => $image['width'],
                    'height' => $image['height'],
                ];
            } else {
                $imageArray = ['url' => wc_placeholder_img_src()];
            }

            if ($product_wc->get_price()) {
                $products[] = [
                    'categories'      => [
                        empty($categories) ? ['id' => 2] : $categories,
                    ],
                    'name'            => get_the_title($product),
                    'description'     => $product_wc->get_description() . ' ',
                    'productImage'    => $imageArray,
                    'price'           => wc_get_price_including_tax($product_wc),
                    'productUrl'      => get_the_permalink($product->ID),
                    'availability'    => $product_wc->get_availability()['class'],
                    'sourceProductId' => 'wp_' . $product->ID,
                ];
            }
        }

        return $products;
    }

    static function getCurrency()
    {
        return [
            'currency' => get_woocommerce_currency(),
            'currencySymbol' => get_woocommerce_currency_symbol(),
        ];
    }

}