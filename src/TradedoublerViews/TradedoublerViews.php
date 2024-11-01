<?php

class TradedoublerViews
{

    static $errors = [];

    static function signIn()
    {
        include TRADEDOUBLER_DIR . '/templates/signin.php';
    }

    static public function step1()
    {
        include TRADEDOUBLER_DIR . '/templates/signup1.php';
    }

    static public function step2()
    {
        include TRADEDOUBLER_DIR . '/templates/signup2.php';
    }

    static public function step3()
    {
        include TRADEDOUBLER_DIR . '/templates/signup3.php';
    }

    static public function step4()
    {
        include TRADEDOUBLER_DIR . '/templates/signup4.php';
    }

    static public function step5()
    {
        include TRADEDOUBLER_DIR . '/templates/signup5.php';
    }

    static public function settings()
    {
        include TRADEDOUBLER_DIR . '/templates/settings.php';
    }

    static public function codes()
    {
        include TRADEDOUBLER_DIR . '/templates/discount-codes.php';
    }

    static public function products()
    {
        include TRADEDOUBLER_DIR . '/templates/products.php';
    }

    static public function dashboard()
    {
        try {
            include TRADEDOUBLER_DIR . '/templates/dashboard.php';
        } catch (Exception $e) {
            if ($e->getMessage() === 'Bad credentials') {
                self::deleteData();
                $step2url
                    = admin_url('admin.php?page=tradedoubler&tab=step1');
                self::redirect($step2url);
            }
            throw $e;
        }
    }
    static public function redirect($url){
        wp_redirect($url);
        printf ("<script>window.location = '%s';</script>", esc_html($url));
        exit();
    }

    static public function tracking()
    {
        include TRADEDOUBLER_DIR . '/templates/tracking.php';
    }

    static public function processForm()
    {
        $api = new TradedoublerAPI();

        $fields = [
            'email'                     => $_SESSION['form_tb']['email'],
            'password'                  => $_SESSION['form_tb']['password'],
            'firstName'                 => $_SESSION['form_tb']['first-name'],
            'lastName'                  => $_SESSION['form_tb']['last-name'],
            'languageCode'              => 'en',
            'telephone'                 => $_SESSION['form_tb']['phone'],
            'companyName'               => $_SESSION['form_tb']['company-name'],
            'orgTypeId'                 => 1,
            'websiteUrl'                => $_SESSION['form_tb']['website-url'],
            'companyRegistrationNumber' => $_SESSION['form_tb']['company-registration-number'],
            'vatNumber'                 => $_SESSION['form_tb']['vat-number'],
            'primaryAddress'            => [
                "street"      => $_SESSION['form_tb']['street-address'],
                "street2"     => $_SESSION['form_tb']['street-address-2'],
                "postCode"    => $_SESSION['form_tb']['postcode'],
                "city"        => $_SESSION['form_tb']['city'],
                "county"      => $_SESSION['form_tb']['county'],
                "countryCode" => strtoupper($_SESSION['form_tb']['country']),
            ],
            'tdOrganizationId'          => 51,
        ];

        $res = $api->createOrganization($fields);


        if ( ! empty($res['error_list'])) {
            $_SESSION['form_tb']['errors'] = $res['error_list'];
            $step2url
                                           = admin_url('admin.php?page=tradedoubler&tab=step1');
            self::redirect($step2url);
        }

        $_SESSION['form_tb']['errors'] = [];

        update_option('tradedoubler_credentials',
            [
                'username' => $_SESSION['form_tb']['email'],
                'password' => $_SESSION['form_tb']['password']
            ]);

        $api = new TradedoublerAPI(get_option('tradedoubler_credentials'));

        $me = $api->getUsersMe();
        update_option('tradedoubler_org_ID', $me['organizationId']);
    }

    static public function verifyPasswordLogin()
    {
        $api = new TradedoublerAPI();

        $fields = [
            'email'        => $_SESSION['form_tb']['email'],
            'password'     => $_SESSION['form_tb']['password'],
            'firstName'    => $_SESSION['form_tb']['first-name'] ?: 'John',
            'lastName'     => $_SESSION['form_tb']['last-name'] ?: 'Doe',
            'languageCode' => 'en',
            //            'telephone' => $_SESSION['form_tb']['phone'],
            //            'companyName' => $_SESSION['form_tb']['company-name'],
            //            'orgTypeId' => 1,
            //           'websiteUrl' => $_SESSION['form_tb']['website-url'],
            //            'companyRegistrationNumber' => $_SESSION['form_tb']['company-registration-number'],
            //            'vatNumber' => $_SESSION['form_tb']['vat-number'],
            //            'tdOrganizationId' => 51,
        ];

        $res = $api->createOrganization($fields);


        $errors = [];
        if (is_array($res['error_list'])) {
            foreach ($res['error_list'] as $single) {
                //check for bad username / password
                if (strpos($single['message'], 'password') !== false
                    || strpos($single['message'], 'username') !== false
                    || strpos($single['message'], 'email') !== false
                ) {
                    $errors[] = $single;
                }
            }
        }

        if ( ! empty($errors)) {
            $_SESSION['form_tb']['errors'] = $errors;
            $step2url
                                           = admin_url('admin.php?page=tradedoubler&tab=step1');
            self::redirect($step2url);
        }

    }

    static public function verifyNonPasswordLoginData()
    {
        $api = new TradedoublerAPI();

        $fields = [
//            'email' => $_SESSION['form_tb']['email'],
//            'password' => $_SESSION['form_tb']['password'],
'firstName'                 => $_SESSION['form_tb']['first-name'],
'lastName'                  => $_SESSION['form_tb']['last-name'],
'languageCode'              => 'en',
'telephone'                 => $_SESSION['form_tb']['phone'],
'companyName'               => $_SESSION['form_tb']['company-name'],
'orgTypeId'                 => 1,
'websiteUrl'                => $_SESSION['form_tb']['website-url'],
'companyRegistrationNumber' => $_SESSION['form_tb']['company-registration-number'],
'vatNumber'                 => $_SESSION['form_tb']['vat-number'],
'primaryAddress'            => [
    "street"      => $_SESSION['form_tb']['street-address'],
    "street2"     => $_SESSION['form_tb']['street-address-2'],
    "postCode"    => $_SESSION['form_tb']['postcode'],
    "city"        => $_SESSION['form_tb']['city'],
    "county"      => $_SESSION['form_tb']['county'],
    "countryCode" => strtoupper($_SESSION['form_tb']['country']),
],
'tdOrganizationId'          => 51,
        ];

        $res = $api->createOrganization($fields);


        $errors = [];
        if (is_array($res['error_list'])) {
            foreach ($res['error_list'] as $single) {
                //check for anything but bad username / password
                if (strpos($single['message'], 'password') === false
                    && strpos($single['message'], 'email') === false
                ) {
                    $errors[] = $single;
                }
            }
        }

        if ( ! empty($errors)) {
            $_SESSION['form_tb']['errors'] = $errors;
            $step2url
                                           = admin_url('admin.php?page=tradedoubler&tab=step2');
            self::redirect($step2url);
        }

    }

    /**
     * @throws Exception
     */
    static function createProgramForm()
    {

        $idProgram = null;
        $errors    = [];
        $api
                   = new TradedoublerAPI(get_option('tradedoubler_credentials'));
        try {

            $user = $api->getUsersMe();

            $name     = $_POST['program-name'];
            $country  = $_POST['country'];
            $category = $_POST['orgTypeId'];
            $logo     = '';

            $atta = self::add_file_from_user($_FILES['logo'], 4, true);

            if (isset($atta['ID']) && $atta['ID']) {
                $logo = wp_get_attachment_url($atta['ID']);
            }

            $currencyCode = 'EUR';
            $percent      = $_POST['tdOrganizationId'];

            $fields = array(
                'name'                 => $name,
                'programTypeId'        => 1,
                'closedProgram'        => false,
                'implementationTypeId' => 3,
                'adminPersonId'        => $user['personId'],
                'techPersonId'         => $user['personId'],
                'homePageUrl'          => home_url(),
                'genericPersonId'      => $user['personId'],
                'invoiceTdOrgId'       => $user['personId'],
                'smb'                  => true,
                'active'               => true,
                'currencyCode'         => $currencyCode,
                'countryCode'          => $country,
                'logoUrl'              => $logo,
                'deepLinking'          => true,
                'categories'           => [$category],
            );

            $res = $api->createProgram($fields);
            if (isset($res['id']) && $res['id'] != '') {
                $idProgram = (int)$res['id'];
                update_option('tradedoubler_program_ID', $idProgram);
            } else {
                $errors = $res;
                throw new Exception('');
            }
            $eventID   = null;
            $segmentID = 1;

            $commissions = $api->getCommissions($idProgram);
            foreach ($commissions['items'] as $segment) {
                if ($segment['segmentId'] == 1
                    && $segment['eventType'] == 'Sales'
                    && $segment['eventRule'] == 'Recurring'
                ) {
                    $eventID = max((int)$eventID, $segment['eventId']);
                }
            }

            $res = $api->changeCommision([
                "segmentId"                  => $segmentID,
                "eventId"                    => $eventID,
                "startDate"                  => gmdate('Ymd'),
                "endDate"                    => null,
                "publisherCommission"        => null,
                "publisherCommissionPercent" => $percent,
                "virtualCommission"          => null,
                "virtualCommissionPercent"   => null
            ], $idProgram);

        } catch (Exception $e) {

            //DELETE program
            if ($idProgram) {
                $api->deleteProgram($idProgram);
            }

            update_option('tradedoubler_program_ID', '');
            $_SESSION['form_tb']['errors2'] = $errors;
            $home
                                            = admin_url('admin.php?page=tradedoubler&tab=step4');
            self::redirect($home);
        }

        $_SESSION['form_tb']['errors2'] = [];

        update_option('tradedoubler_program_ID', $idProgram);
        update_option('tradedoubler_event_ID', $eventID);
    }

    /**
     * @throws Exception
     */
    static function updateProgramForm()
    {

        $api = new TradedoublerAPI(get_option('tradedoubler_credentials'));

        if (isset($_POST['logout-check']) && $_POST['logout-check'] == '1') {
            TradedoublerViews::logout();
        }


        $program_id = get_option('tradedoubler_program_ID', '');

        $program = $api->getSingleProgramInfo((int)$program_id);

        $user = $api->getUsersMe();

        $name     = $_POST['program-name'];
        $category = $_POST['orgTypeId'];
        $percent  = $_POST['tdOrganizationId'];
        $logo     = $program['logoUrl'];

        $atta = self::add_file_from_user($_FILES['logo'], 4, true);

        if (isset($atta['ID']) && $atta['ID']) {
            $logo = wp_get_attachment_url($atta['ID']);
        }


        $fields = array(
            'name'                 => $name,
            'programTypeId'        => 1,
            'homePageUrl'          => home_url(),
            'implementationTypeId' => 3,
            'genericPersonId'      => $user['personId'],
            'invoiceTdOrgId'       => $user['personId'],
            'impressionTracking'   => $program['impressionTracking'],
            'startDate'            => $program['startDate'] ? gmdate('Ymd',
                strtotime($program['startDate'])) : gmdate('Ymd'),
            'adminPersonId'        => $user['personId'],
            'uniqueClickTime'      => $program['uniqueClickTime'],
            'techPersonId'         => $user['personId'],
            'closedProgram'        => $program['closedProgram'],
            'autoConnect'          => $program['autoConnect'],
            'cookieWindow'         => $program['cookieWindow'],
            'mobileTracking'       => $program['mobileTracking'],
            'voucherTracking'      => $program['voucherTracking'],

            'smb'          => true,
            'active'       => true,
            'currencyCode' => 'EUR',
            'logoUrl'      => $logo,
            'deepLinking'  => true,
            'categories'   => [$category],
        );


        $res = $api->updateProgramInfo($fields, $program_id);
        if (isset($res[0]['message'])) {
            self::$errors = $res;
        }

        $eventID   = get_option('tradedoubler_event_ID', '');
        $idProgram = get_option('tradedoubler_program_ID', '');
        $segmentID = get_option('tradedoubler_segment_ID', '');
        if ($segmentID == '') {
            TradedoublerViews::refreshData(get_option('tradedoubler_credentials')['username'],
                get_option('tradedoubler_credentials')['password']);
            $segmentID = get_option('tradedoubler_segment_ID', '');
        }


        $res = $api->changeCommision([
            "segmentId"                  => $segmentID,
            "eventId"                    => $eventID,
            "startDate"                  => gmdate('Ymd'),
            "endDate"                    => null,
            "publisherCommission"        => null,
            "publisherCommissionPercent" => $percent,
            "virtualCommission"          => null,
            "virtualCommissionPercent"   => null
        ], $idProgram);

        if (isset($res['error_list']['segmentId'])
            && $res['error_list']['segmentId'] != ''
        ) {

        } else {
            self::$errors = $res;
        }
    }


    static function signInForm()
    {
        $username = $_POST['username'];
        $password = $_POST['password'];

        try {
            $api = new TradedoublerAPI([
                'username' => $username,
                'password' => $password
            ]);
            self::refreshData($username, $password);
            if (get_option('tradedoubler_event_ID', '') == '') {
                //this user needs to create event for this website
                throw new Exception('NO_ACCOUNT_EVENT');
            }
        } catch (Exception $e) {
            self::deleteData();
            $home = $e->getMessage() == 'NO_ACCOUNT_EVENT'
                ?
                admin_url('admin.php?page=tradedoubler&error=2')
                :
                admin_url('admin.php?page=tradedoubler&error=1');
            self::redirect($home);
        }

        TradedoublerActions::refreshFeed(false);
        TradedoublerActions::refreshFeed(true);
        TradedoublerActions::loadCodesIntoPlatform();

        $home = admin_url('admin.php?page=tradedoubler');
        self::redirect($home);
    }

    static function logout()
    {
        self::deleteData();

        $home = admin_url('admin.php?page=tradedoubler');
        self::redirect($home);
    }

    static function trackingForm()
    {
        $program_id = get_option('tradedoubler_program_ID', '');
        if($_POST['program_new'] && $_POST['program_new'] != $program_id)
        {
            self::refreshDataProgram($_POST['program_new']);
            //check for errors ?
        }

        update_option('tradedoubler_vat_commision',
            isset($_POST['commission-vat'])
            && $_POST['commission-vat'] === 'yes' ? 'yes' : 'no');
        update_option('tradedoubler_shipping_commision',
            isset($_POST['commission-delivery'])
            && $_POST['commission-delivery'] === 'yes' ? 'yes' : 'no');


    }

    /**
     * @param $username
     * @param $password
     * @return void
     * @throws Exception
     */
    static function refreshData($username, $password)
    {
        update_option('tradedoubler_credentials',
            ['username' => $username, 'password' => $password]
        );

        $api = new TradedoublerAPI(get_option('tradedoubler_credentials'));

        $programs = $api->getProgramInfo();
        foreach ($programs['items'] as $programSingle) {
            header('Content-type: text/plain');
            $additionalInfo
                = $api->getSingleProgramInfo((int)$programSingle['id']);

            if (isset($additionalInfo['homePageUrl'])) {
                $program    = $programSingle;
                $program_id = (int)$program['id'];
                if ($program['closedProgram']
                    || !$program['active']
                ) {
                    $program['closedProgram'] = false;
                    $program['active']        = true;
                    unset($program['id']);
                    $api->updateProgramInfo($program, $program_id);
                }
                if(self::refreshDataProgram($program_id))
                    break; //stop going through programs until you find one with proper commissions
            }
        }
    }

    /**
     * @param $program_id
     * @return bool true if there's event for this program, false otherwise
     * @throws Exception
     */
    public static function refreshDataProgram($program_id){
        $eventID    = '';
        $api = new TradedoublerAPI(get_option('tradedoubler_credentials'));
        $segmentID = 1;
        $me = $api->getUsersMe();

        if ($program_id !== '') {
            $commissions = $api->getCommissions($program_id);

            foreach ($commissions['items'] as $segment) {
                        if ($segment['segmentId'] == 1
                            && $segment['eventType'] == 'Sales'
                            && $segment['eventRule'] == 'Recurring'
                        ) {
                            $eventID   = max((int)$eventID,
                                $segment['eventId']);
                            $segmentID = 1;
                        }
            }
        }

        update_option('tradedoubler_org_ID', $me['organizationId']);
        update_option('tradedoubler_program_ID', $program_id);
        update_option('tradedoubler_event_ID', $eventID);
        update_option('tradedoubler_segment_ID', $segmentID);

        if($program_id){
            $feed = TradedoublerActions::getFeedDefault();
            if($feed['description'] == 'wordpress plugin feed')
                $api->updateProductFeed([
                    "name"         => $feed['name'],
                    "active"       => true,
                    "secret"       => false,
                    "visible"      => true,
                    "currencyCode" => get_woocommerce_currency(),
                    "languageCode" => 'EN',
                    "description"  => "wordpress woo plugin feed",
                    "programId"    => $program_id,
                    "protocol"     => "http",
                    "format"       => "csv"
                ], $feed['feedId']);
        }
        return $eventID != '';
    }

    public static function deleteData()
    {
        try{
            $api       = new TradedoublerAPI(get_option('tradedoubler_credentials',
                ''));

            $feedID = TradedoublerActions::getFeedDefaultId();
            if($feedID)
                $api->deleteFeedScheduler($feedID);
        }
        catch (Exception $e){

        }
        delete_option('tradedoubler_credentials');
        delete_option('tradedoubler_org_ID');
        delete_option('tradedoubler_program_ID');
        delete_option('tradedoubler_event_ID');
        delete_option('tradedoubler_segment_ID');
        delete_option('tradedoubler_last_time_feed_update');
        delete_option('tradedoubler_feeds');
        self::clearCache();

    }

    public static function clearCache()
    {
        foreach ([
                     'user_token_tradedoublerAPI',
                     'program_name_tradedoublerAPI',
                     'token_res_tradedoublerAPI',
                     'product_feeds_tradedoublerAPI',
                 ] as $transient) {
            delete_transient($transient);
        }
    }

    /**
     * @param $file array element of $_FILES array
     * @param $size int max upload size
     *
     * @return array with error or ID of file
     */
    private static function add_file_from_user(
        array $file,
        $size = 20,
        $isImage = false
    ) {
        require_once(ABSPATH . "wp-admin" . '/includes/image.php');
        require_once(ABSPATH . "wp-admin" . '/includes/file.php');
        if (($file['error'] != 4 && $file['error'] != 0)
            || $file['size'] > $size * 1024 * 1024
        ) {

            if ($file['error'] == 1 || $file['error'] == 2
                || $file['size'] > $size * 1024 * 1024
            ) {
                $result = array(
                    'error' => true,
                    'msg'   => __('File size was too big, try to upload smaller file.',
                        'tradedoubler'),
                );
            } else {
                $result = array(
                    'error' => true,
                    /* translators: %d: server error code */
                    'msg'   => sprintf(__('Server error %d, try to contact with administrator of this site.',
                        'tradedoubler'), $file['error']),
                    'code'  => $file['error'],
                );
            }
        } elseif ($file['error'] != 4) {

            $arr_file_type      = wp_check_filetype(basename($file['name']));
            $uploaded_file_type = $arr_file_type['type'];
            $upload_overrides   = array('test_form' => false);
            $uploaded_file      = wp_handle_upload($file, $upload_overrides);
            if (isset($uploaded_file['file'])) {
                $file_name_and_location = $uploaded_file['file'];
                $file_title_for_media_library
                                        = md5(self::generateRandomString(10));
                $attachment             = array(
                    'post_mime_type' => $uploaded_file_type,
                    'post_title'     => 'Uploaded user file '
                                        . addslashes($file_title_for_media_library),
                    'post_content'   => '',
                    'post_status'    => 'inherit'
                );
                $attach_id
                                        = wp_insert_attachment($attachment,
                    $file_name_and_location);
                $result                 = array(
                    'error' => false,
                    'ID'    => $attach_id,
                    'url'   => wp_get_attachment_url($attach_id)
                );
                if ($isImage) {
                    $attach_data = wp_generate_attachment_metadata($attach_id,
                        $file_name_and_location);
                    wp_update_attachment_metadata($attach_id, $attach_data);
                }

            } else {
                $result = array(
                    'error' => true,
                    'msg'   => __('Server error 11, try to contact with administrator of this site.',
                        'tradedoubler')
                );
            }


        } else {
            $result = array(
                'error' => null,
                'msg'   => __('No image', 'tradedoubler')
            );
        }


        return $result;
    }

    private static function generateRandomString($length = 20): string
    {
        $characters
                          = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGIJKLMNOPRSTUWYZ';
        $charactersLength = strlen($characters);
        $randomString     = '';
        for ($i = 0; $i < $length; $i++) {
            try {
                $randomString .= $characters[@random_int(0,
                    $charactersLength - 1)];
            } catch (\Exception $e) {
                $randomString .= $characters[wp_rand(0,
                    $charactersLength - 1)];
            }
        }

        return $randomString;
    }

}