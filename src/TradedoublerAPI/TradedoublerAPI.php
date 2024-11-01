<?php

class TradedoublerAPI
{

    private $tokenBasic;
    private $tokenBasic3;
    private $tokenBearer = '';
    private $tokenUser = '';
    private $tokenVoucher = '';
    public $route = '';
    public $routeVoucher = 'https://api.tradedoubler.com';
    public $routeAgency = '';

    private $modeBasic = 1;
    private $modeBearer = 0;
    private $modeUser = 2;
    private $modeVoucher = 3;
    private $modeBasic3 = 5;
    private $modeNone = 6;

    private $curlTimeout = 20;
    private $curlConnectionTimeout = 20;

    private $tokenTTL = 3600;

    /**
     * @param null $userData
     *
     * @throws Exception
     */
    public function __construct($userData = null)
    {
        $this->route       = 'https://connect.tradedoubler.com';
        $this->routeAgency = 'https://connect.tradedoubler.com';
        $this->tokenBasic
                           = 'MDVlNTg5MDEtMGQ3ZS0zZjk1LThmMDctODkxNTY4YjdmMTFiOmRlM2UwZDU0YmE5ZjE0YmE';
        $this->tokenBasic3
                           = 'dGRjb25uZWN0X3B1Ymxpc2hlcjoxMjM0NTY=';

        if (is_array($userData)) {
            $this->obtainUserToken($userData);
        }
    }


    /**
     * @param $userData
     * @param $force
     *
     * @throws Exception
     */
    public function obtainUserToken($userData, $force = false)
    {

        $userData['username'] = mb_strtolower($userData['username']);

        $userData['username'] = urlencode($userData['username']);
        $userData['password'] = urlencode($userData['password']);

        if (get_transient('user_token_tradedoublerAPI') !== false && !$force) {
            $this->tokenUser = get_transient('user_token_tradedoublerAPI');
        } else {
            $res = json_decode($this->postRequest(
                "{$this->routeAgency}/uaa/oauth/token",
                "grant_type=password&username={$userData['username']}&password={$userData['password']}",
                $this->modeBasic3
            ), true);

            if ( ! isset($res['access_token'])) {
                throw new Exception('Bad credentials');
            }

            $this->tokenUser = $res['access_token'];

            set_transient(
                'user_token_tradedoublerAPI',
                $this->tokenUser,
                $this->tokenTTL);
        }

    }

    function getUsersMe()
    {
        return json_decode($this->getRequest(
            "{$this->route}/usermanagement/users/me",
            $this->modeUser
        ), true);
    }

    function getUsers()
    {

        return json_decode($this->getRequest(
            "{$this->route}/usermanagement/users",
            $this->modeUser
        ), true);
    }

    /**
     * @return string|null
     */
    function getVoucherToken()
    {
        if (get_transient('token_res_tradedoublerAPI') !== false) {
            $res = get_transient('token_res_tradedoublerAPI');
        } else {
            $res = json_decode($this->getRequest(
                "{$this->route}/advertiser/tokens/",
                $this->modeUser
            ), true);
            if (is_array($res) && isset($res['tokens'])) {
                set_transient('token_res_tradedoublerAPI', $res, $this->tokenTTL);
            }
        }


        if (isset($res['tokens'])) {
            foreach ($res['tokens'] as $single) {
                if ($single['tokenSystem'] === 'VOUCHERS') {
                    return (string)$single['token'];
                }
            }
        }

        return null;
    }

    function getProductToken()
    {
        if (get_transient('token_res_tradedoublerAPI') !== false) {
            $res = get_transient('token_res_tradedoublerAPI');
        } else {
            $res = json_decode($this->getRequest(
                "{$this->route}/advertiser/tokens/",
                $this->modeUser
            ), true);
            if (is_array($res) && isset($res['tokens'])) {
                set_transient('token_res_tradedoublerAPI', $res, $this->tokenTTL);
            }
        }

        if (isset($res['tokens'])) {
            foreach ($res['tokens'] as $single) {
                if ($single['tokenSystem'] === 'PRODUCTS') {
                    return (string)$single['token'];
                }
            }
        }

        return null;
    }

    function getProductFeeds()
    {
        $a = json_decode($this->getRequest(
            "{$this->routeVoucher}/1.0/productFeeds?token="
            . $this->getProductToken(),
            $this->modeNone
        ), true);

        return $a['feeds'];
    }


    function getProductFeedsTM()
    {
        if (get_transient('product_feeds_tradedoublerAPI') !== false) {
            return get_transient('product_feeds_tradedoublerAPI');
        }

        $res = json_decode($this->getRequest(
            "{$this->routeAgency}/advertiser/productfeeds",
            $this->modeUser

        ), true);
        if ($res) {
            set_transient('product_feeds_tradedoublerAPI',
                is_array($res) ? $res : [], 200);
        }

        return $res;
    }

    /**
     * @return array
     */
    function getVouchers()
    {
        $a = json_decode($this->getRequest(
            "{$this->routeVoucher}/1.0/vouchers.json?pageSize=500&token="
            . $this->getVoucherToken(),
            $this->modeNone
        ), true);

        return is_array($a) ? $a : [];
    }

    function createFeed($name, $programID, $currencyCode, $languageCode)
    {
        $string = json_encode([
            "name"         => $name,
            "active"       => true,
            "secret"       => false,
            "visible"      => true,
            "currencyCode" => $currencyCode,
            "languageCode" => $languageCode,
            "description"  => "wordpress woo plugin feed",
            "programId"    => $programID,
            "protocol"     => "http",
            "format"       => "csv"
        ]);

        $res = json_decode($this->postRequest(
            "{$this->routeAgency}/advertiser/productfeeds",
            $string, $this->modeUser
        ), true);
        delete_transient('product_feeds_tradedoublerAPI');

        return [
            'error_list' => ! isset($res['organizationId']) ? $res : [],
        ];
    }

    public function updateProductFeed($data, $feedID)
    {
        $res = json_decode($this->putRequest(
            "{$this->routeAgency}/advertiser/productfeeds/$feedID",
            json_encode($data),
            $this->modeUser
        ), true);
        delete_transient('product_feeds_tradedoublerAPI');

        return $res;
    }

    function deleteFeed($feedID)
    {

        $res = json_decode($this->deleteRequest(
            "https://connect.tradedoubler.com/advertiser/v1/productfeeds/scheduler/$feedID",
            $this->modeUser
        ), true);
        delete_transient('product_feeds_tradedoublerAPI');

        return $res;
    }


    function createVoucher($fields)
    {
        return json_decode($this->postRequest(
            "{$this->routeVoucher}/1.0/vouchers?token="
            . $this->getVoucherToken(),
            json_encode($fields),
            $this->modeNone
        ), true);
    }

    public function deleteVoucher($voucher_id){
        return json_decode($this->deleteRequest(
            "{$this->routeVoucher}/1.0/vouchers/$voucher_id?token="
            . $this->getVoucherToken(),
            $this->modeNone
        ), true);
    }

    /**
     * @deprecated use getPseudoDashboard instead
     * @param $currency
     *
     * @return mixed
     */
    function getDashboard($currency = 'EUR')
    {
        return json_decode($this->getRequest(
            "{$this->route}/advertiser/report/dashboard" .
            "?intervalType=LAST_30_DAYS&reportCurrencyCode=$currency",
            $this->modeUser
        ), true);
    }

    function getPseudoDashboard($currency = 'EUR', $programID = '')
    {
        $res = json_decode($this->getRequest(
            "{$this->route}/advertiser/report/statistics" .
            "?intervalType=day&limit=40&reportCurrencyCode=$currency&reportType=date&fromDate="
            . gmdate('Ymd', strtotime('-29 days'))
            . "&toDate=" . gmdate('Ymd') . ( $programID == '' ? '' : ('&programId=' . $programID)),
            $this->modeUser
        ), true);

        $values = [
            'sales' => 0, //amount
            'impressions' => 0,
            'leads' => 0,
            'salesOrderValue' => 0, //total value
            'clicks' => 0,
            'publisherCommission' => 0,
            'tdCommission' => 0,
            'reportCurrencyCode' => !isset($res['reportCurrencyCode']) ? '' : $res['reportCurrencyCode'],
        ];

        if(isset($res['items']))
            foreach($res['items'] as $item)
            {
                $values['sales'] += $item['sales'];
                $values['impressions'] += $item['impressions'];
                $values['leads'] += $item['leads'];
                $values['clicks'] += $item['clicks'];
                $values['salesOrderValue'] += $item['orderValue'];

                $values['publisherCommission'] += $item['commission']['publisherCommission'];

                $values['tdCommission'] += $item['commission']['tdCommission'];
            }

        return $values;
    }

    function getTopAffi($currency = 'EUR', $programID = '')
    {
        $res = $this->getRequest(
            "{$this->route}/advertiser/report/dashboard/sources" .
            "?intervalType=LAST_30_DAYS&reportCurrencyCode=$currency",
            $this->modeUser
        );

        return json_decode($res, true);
    }

    function getStatistics($programID, $currency = 'EUR')
    {
        $res = $this->getRequest(
            "{$this->route}/advertiser/report/statistics" .
            "?intervalType=day&reportCurrencyCode=$currency&reportType=date&fromDate="
            . gmdate('Ymd', strtotime('-14 days'))
            . "&toDate=" . gmdate('Ymd') . '&programId=' . $programID,
            $this->modeUser
        );

        return json_decode($res, true);
    }

    function getPendingAffi($programID)
    {
        $res = $this->getRequest(
            "{$this->route}/advertiser/programs/$programID/sources"
            . "?statusId=1&limit=5&sortBy=lastChanged&sortOrder=desc",
            $this->modeUser
        );

        return json_decode($res, true);
    }

    function getRecentTransactions($currency, $programID)
    {
        $res = $this->getRequest(
            "{$this->route}/advertiser/report/transactions"
            . "?fromDate=" . gmdate('Ymd', strtotime('-80 days')) . "&toDate="
            . gmdate('Ymd') . "&reportCurrencyCode=$currency&limit=5&programId=$programID"
            , $this->modeUser
        );

        return json_decode($res, true);
    }

    function createEvent()
    {
        $string = json_encode([
            "name"          => "Woocommerce tracking",
            "eventTypeId"   => 5,
            "ruleId"        => 4,
            "pending"       => true,
            "pendingPeriod" => 30
        ]);

        $res = json_decode($this->postRequest(
            "{$this->routeAgency}/advertiser/events",
            $string, $this->modeUser

        ), true);

        return [
            'error_list' => ! isset($res['organizationId']) ? $res : [],
        ];
    }


    function changeCommision($fields, $programID)
    {
        $string = json_encode($fields);

        $res = json_decode($this->postRequest(
            "{$this->routeAgency}/advertiser/programs/$programID/commissions",
            $string, $this->modeUser

        ), true);

        return [
            'error_list' => ! isset($res['organizationId']) ? $res : [],
        ];
    }

    function createSegment($programID)
    {
        $string = json_encode([
            "segmentName" => "Woocommerce plugin"
        ]);

        $res = json_decode($this->postRequest(
            "{$this->routeAgency}/advertiser/programs/$programID/segments",
            $string, $this->modeUser

        ), true);

        return [
            'error_list' => ! isset($res['organizationId']) ? $res : [],
        ];
    }

    function createOrganization($fields)
    {

        $string = json_encode($fields);

        $res = json_decode($this->postRequest(
            "{$this->routeAgency}/agency/organizations",
            $string, $this->modeBearer
        ), true);

        return [
            'error_list' => ! isset($res['organizationId']) ? $res : [],
        ];
    }

    public function getProgramInfo()
    {
        $res = $this->getRequest(
            "{$this->route}/advertiser/programs?active=true&limit=40"
            , $this->modeUser

        );

        return json_decode($res, true);
    }

    public function getSingleProgramInfo($programID)
    {
        $res = $this->getRequest(
            "{$this->route}/advertiser/programs/$programID"
            , $this->modeUser

        );

        return json_decode($res, true);
    }

    public function showCommision($programID)
    {
        $res = $this->getRequest(
            "{$this->route}/advertiser/programs/$programID/commissions"
            , $this->modeUser

        );

        return json_decode($res, true);
    }

    public function getCommissions($programID)
    {
        $res = $this->getRequest(
            "{$this->route}/advertiser/programs/$programID/commissions"
            , $this->modeUser

        );

        return json_decode($res, true);
    }

    public function createProgram($fields)
    {
        $string = json_encode($fields);

        $res = json_decode($this->postRequest(
            "{$this->routeAgency}/advertiser/programs",
            $string, $this->modeUser

        ), true);

        return $res;
    }

    public function updateProgramInfo($fields, $programID)
    {
        $string = json_encode($fields);

        $res = json_decode($this->putRequest(
            "{$this->routeAgency}/advertiser/programs/$programID",
            $string, $this->modeUser

        ), true);

        return $res;
    }


    public function deleteProgram($programID)
    {
        //somehow deleting is not working in API, let's just change it into inactive program
        $program = $this->getSingleProgramInfo((int)$programID);
        $user    = $this->getUsersMe();

        $fields = [
            'active'             => false,
            'homePageUrl'        => home_url(),
            'startDate'          => gmdate('Ymd'),
            'name'               => $program['name'],
            'impressionTracking' => $program['impressionTracking'],
            'adminPersonId'      => $user['personId'],
            'uniqueClickTime'    => $program['uniqueClickTime'],
            'techPersonId'       => $user['personId'],
            'closedProgram'      => $program['closedProgram'],
            'autoConnect'        => $program['autoConnect'],
            'cookieWindow'       => $program['cookieWindow'],
            'mobileTracking'     => $program['mobileTracking'],
            'voucherTracking'    => $program['voucherTracking'],
            'logoUrl'            => $program['logoUrl'],
        ];

        $res = $this->updateProgramInfo($fields, $programID);

        return $res;
    }

    private function postRequest($url, $values, $secretSettings = 0, $recursion = false)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->curlConnectionTimeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->curlTimeout);


        if ($values) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $values);
        }

        $headers = $this->getHeaders($secretSettings);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);

        curl_close($ch);

        //check for invalid token
        $result = explode("\r\n\r\n", $response, 2)[1];
        $res = json_decode($result, true);
        if(!$recursion && isset($res['error']) && $res['error'] == 'invalid_token')
        {
            $this->obtainUserToken(get_option('tradedoubler_credentials',
                ''), true);
            return $this->postRequest($url, $values, $secretSettings , true);
        }

        return $result;
    }


    private function putRequest($url, $values, $secretSettings = 0, $recursion = false)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->curlConnectionTimeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->curlTimeout);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_HEADER, 1);


        if ($values) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $values);
        }

        $headers = $this->getHeaders($secretSettings);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);

        curl_close($ch);

        //check for invalid token
        $result = explode("\r\n\r\n", $response, 2)[1];
        $res = json_decode($result, true);
        if(!$recursion && isset($res['error']) && $res['error'] == 'invalid_token')
        {
            $this->obtainUserToken(get_option('tradedoubler_credentials',
                ''), true);
            return $this->putRequest($url, $values, $secretSettings , true);
        }

        return $result;
    }


    private function deleteRequest($url, $secretSettings = 0, $recursion = false)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->curlConnectionTimeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->curlTimeout);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_HEADER, 1);


        $headers = $this->getHeaders($secretSettings);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);

        curl_close($ch);

        //check for invalid token
        $result = explode("\r\n\r\n", $response, 2)[1];
        $res = json_decode($result, true);
        if(!$recursion && isset($res['error']) && $res['error'] == 'invalid_token')
        {
            $this->obtainUserToken(get_option('tradedoubler_credentials',
                ''), true);
            return $this->deleteRequest($url, $secretSettings , true);
        }

        return $result;
    }

    private function getRequest($url, $secretSettings = 0, $recursion = false)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->curlConnectionTimeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->curlTimeout);

        curl_setopt($ch, CURLOPT_HEADER, 1);

        $headers = $this->getHeaders($secretSettings);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);

        curl_close($ch);

        //check for invalid token
        $result = explode("\r\n\r\n", $response, 2)[1];
        $res = json_decode($result, true);
        if(!$recursion && isset($res['error']) && $res['error'] == 'invalid_token')
        {
            $this->obtainUserToken(get_option('tradedoubler_credentials',
                ''), true);
            return $this->getRequest($url, $secretSettings , true);
        }

        return $result;
    }

    private function getRequestTokenGet($url, $secretSettings = 0)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . '?token=' . $this->tokenBasic);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->curlConnectionTimeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->curlTimeout);

        curl_setopt($ch, CURLOPT_HEADER, 1);

        $response = curl_exec($ch);

        curl_close($ch);

        return $response; //explode("\r\n\r\n",$response,2)[1];
    }

    private function getHeaders($secretSettings)
    {
        $headers = [];
        switch ($secretSettings) {
            case $this->modeBasic:
            default:
                $headers = array(
                    "Content-Type: application/x-www-form-urlencoded",
                    'Authorization: Basic ' . $this->tokenBasic
                );
                break;
            case $this->modeNone:
                $headers = array(
                    "Content-Type: application/json; charset=utf-8",
                );
                break;
            case $this->modeBearer:
                $headers = array(
                    "Content-Type: application/json; charset=utf-8",
                    'Authorization: Bearer ' . $this->tokenBearer
                );
                break;
            case $this->modeUser:
                $headers = array(
                    "Content-Type: application/json; charset=utf-8",
                    'Authorization: Bearer ' . $this->tokenUser
                );
                break;
            case $this->modeVoucher:
                $headers = array(
                    "Content-Type: application/json; charset=utf-8",
                    'Authorization: Bearer ' . $this->tokenVoucher
                );
                break;
            case $this->modeBasic3:
                $headers = array(
                    "Content-Type: application/x-www-form-urlencoded",
                    'Authorization: Basic ' . $this->tokenBasic3
                );
                break;
        }

        return $headers;
    }

    public function setFeedScheduler($values){
        $string = json_encode($values);
        $res = json_decode($this->postRequest(
            "{$this->routeAgency}/advertiser/productfeeds/scheduler/http/csv",
            $string,
            $this->modeUser
        ), true);
        return $res;
    }

    public function deleteFeedScheduler($feedID){
        $res = json_decode($this->deleteRequest(
            "{$this->routeAgency}/advertiser/productfeeds/scheduler/$feedID",
            $this->modeUser
        ), true);

        return $res;
    }

    /**
     * @deprecated
     * @param $products
     * @param $feedID
     * @param $replace
     *
     * @return mixed
     */
    public function updateFeed($products, $feedID, $replace = true)
    {
        $values = json_encode(['products' => $products]);
        $mode   = $replace ? 'mode=replace' : 'mode=merge';

        $a = json_decode($this->postRequest(
            "{$this->routeVoucher}/1.0/products;fid=$feedID;$mode?token="
            . $this->getProductToken(),
            $values,
            $this->modeNone
        ), true);

        return $a;
    }

    static function getCategoriesProducts()
    {
        return json_decode('[{"ID":899,"CATEGORY NAME":"Art and Antiques","PARENT_ID":1,"PARENT_CATEGORY":"Root"},{"ID":901,"CATEGORY NAME":"Antiques","PARENT_ID":899,"PARENT_CATEGORY":"Art and Antiques"},{"ID":900,"CATEGORY NAME":"Art","PARENT_ID":899,"PARENT_CATEGORY":"Art and Antiques"},{"ID":1629,"CATEGORY NAME":"Fine Art Prints","PARENT_ID":900,"PARENT_CATEGORY":"Art"},{"ID":1627,"CATEGORY NAME":"Posters","PARENT_ID":899,"PARENT_CATEGORY":"Art and Antiques"},{"ID":879,"CATEGORY NAME":"Bikes","PARENT_ID":8,"PARENT_CATEGORY":"Automotive"},{"ID":878,"CATEGORY NAME":"Boats","PARENT_ID":8,"PARENT_CATEGORY":"Automotive"},{"ID":2250,"CATEGORY NAME":"Audi","PARENT_ID":12,"PARENT_CATEGORY":"Used cars"},{"ID":2251,"CATEGORY NAME":"BMW","PARENT_ID":12,"PARENT_CATEGORY":"Used cars"},{"ID":2252,"CATEGORY NAME":"Citroen","PARENT_ID":12,"PARENT_CATEGORY":"Used cars"},{"ID":2253,"CATEGORY NAME":"Fiat","PARENT_ID":12,"PARENT_CATEGORY":"Used cars"},{"ID":2254,"CATEGORY NAME":"Ford","PARENT_ID":12,"PARENT_CATEGORY":"Used cars"},{"ID":2255,"CATEGORY NAME":"Honda","PARENT_ID":12,"PARENT_CATEGORY":"Used cars"},{"ID":2256,"CATEGORY NAME":"Hyundai","PARENT_ID":12,"PARENT_CATEGORY":"Used cars"},{"ID":2257,"CATEGORY NAME":"Jaguar","PARENT_ID":12,"PARENT_CATEGORY":"Used cars"},{"ID":2258,"CATEGORY NAME":"Kia","PARENT_ID":12,"PARENT_CATEGORY":"Used cars"},{"ID":2259,"CATEGORY NAME":"Mazda","PARENT_ID":12,"PARENT_CATEGORY":"Used cars"},{"ID":2260,"CATEGORY NAME":"Mercedes","PARENT_ID":12,"PARENT_CATEGORY":"Used cars"},{"ID":2261,"CATEGORY NAME":"Mini","PARENT_ID":12,"PARENT_CATEGORY":"Used cars"},{"ID":2262,"CATEGORY NAME":"Mitsubishi","PARENT_ID":12,"PARENT_CATEGORY":"Used cars"},{"ID":2263,"CATEGORY NAME":"Nissan","PARENT_ID":12,"PARENT_CATEGORY":"Used cars"},{"ID":2264,"CATEGORY NAME":"Peugeot","PARENT_ID":12,"PARENT_CATEGORY":"Used cars"},{"ID":2265,"CATEGORY NAME":"Renault","PARENT_ID":12,"PARENT_CATEGORY":"Used cars"},{"ID":2266,"CATEGORY NAME":"Saab","PARENT_ID":12,"PARENT_CATEGORY":"Used cars"},{"ID":2267,"CATEGORY NAME":"Seat","PARENT_ID":12,"PARENT_CATEGORY":"Used cars"},{"ID":2268,"CATEGORY NAME":"Skoda","PARENT_ID":12,"PARENT_CATEGORY":"Used cars"},{"ID":2269,"CATEGORY NAME":"Suzuki","PARENT_ID":12,"PARENT_CATEGORY":"Used cars"},{"ID":2270,"CATEGORY NAME":"Toyota","PARENT_ID":12,"PARENT_CATEGORY":"Used cars"},{"ID":2271,"CATEGORY NAME":"Vauxhall","PARENT_ID":12,"PARENT_CATEGORY":"Used cars"},{"ID":2272,"CATEGORY NAME":"Volkswagen","PARENT_ID":12,"PARENT_CATEGORY":"Used cars"},{"ID":2273,"CATEGORY NAME":"Volvo","PARENT_ID":12,"PARENT_CATEGORY":"Used cars"},{"ID":501,"CATEGORY NAME":"Art and Pictures","PARENT_ID":4,"PARENT_CATEGORY":"Books"},{"ID":5,"CATEGORY NAME":"Audio books","PARENT_ID":4,"PARENT_CATEGORY":"Books"},{"ID":6,"CATEGORY NAME":"Bookclubs","PARENT_ID":4,"PARENT_CATEGORY":"Books"},{"ID":7,"CATEGORY NAME":"Books","PARENT_ID":4,"PARENT_CATEGORY":"Books"},{"ID":502,"CATEGORY NAME":"Business and Finance","PARENT_ID":4,"PARENT_CATEGORY":"Books"},{"ID":898,"CATEGORY NAME":"Calenders","PARENT_ID":4,"PARENT_CATEGORY":"Books"},{"ID":639,"CATEGORY NAME":"Children","PARENT_ID":4,"PARENT_CATEGORY":"Books"},{"ID":739,"CATEGORY NAME":"Collections","PARENT_ID":4,"PARENT_CATEGORY":"Books"},{"ID":2096,"CATEGORY NAME":"Comedy","PARENT_ID":4,"PARENT_CATEGORY":"Books"},{"ID":608,"CATEGORY NAME":"Comics","PARENT_ID":4,"PARENT_CATEGORY":"Books"},{"ID":506,"CATEGORY NAME":"Computers and Internet","PARENT_ID":4,"PARENT_CATEGORY":"Books"},{"ID":2093,"CATEGORY NAME":"Crime","PARENT_ID":4,"PARENT_CATEGORY":"Books"},{"ID":2150,"CATEGORY NAME":"E Books","PARENT_ID":4,"PARENT_CATEGORY":"Books"},{"ID":577,"CATEGORY NAME":"Eco books","PARENT_ID":4,"PARENT_CATEGORY":"Books"},{"ID":520,"CATEGORY NAME":"Erotic","PARENT_ID":4,"PARENT_CATEGORY":"Books"},{"ID":575,"CATEGORY NAME":"Esotherism","PARENT_ID":4,"PARENT_CATEGORY":"Books"},{"ID":2094,"CATEGORY NAME":"Fiction","PARENT_ID":4,"PARENT_CATEGORY":"Books"},{"ID":576,"CATEGORY NAME":"Finance and Law","PARENT_ID":4,"PARENT_CATEGORY":"Books"},{"ID":504,"CATEGORY NAME":"Food and Beverage","PARENT_ID":4,"PARENT_CATEGORY":"Books"},{"ID":740,"CATEGORY NAME":"Geography","PARENT_ID":4,"PARENT_CATEGORY":"Books"},{"ID":513,"CATEGORY NAME":"Health and Beauty","PARENT_ID":4,"PARENT_CATEGORY":"Books"},{"ID":2090,"CATEGORY NAME":"History","PARENT_ID":4,"PARENT_CATEGORY":"Books"},{"ID":541,"CATEGORY NAME":"Hi Tech and Web","PARENT_ID":4,"PARENT_CATEGORY":"Books"},{"ID":2095,"CATEGORY NAME":"Home and Garden","PARENT_ID":4,"PARENT_CATEGORY":"Books"},{"ID":515,"CATEGORY NAME":"Human Science","PARENT_ID":4,"PARENT_CATEGORY":"Books"},{"ID":507,"CATEGORY NAME":"Languages","PARENT_ID":4,"PARENT_CATEGORY":"Books"},{"ID":741,"CATEGORY NAME":"Linguistics","PARENT_ID":4,"PARENT_CATEGORY":"Books"},{"ID":545,"CATEGORY NAME":"Local council","PARENT_ID":4,"PARENT_CATEGORY":"Books"},{"ID":880,"CATEGORY NAME":"Magazine","PARENT_ID":4,"PARENT_CATEGORY":"Books"},{"ID":510,"CATEGORY NAME":"Male Books","PARENT_ID":4,"PARENT_CATEGORY":"Books"},{"ID":738,"CATEGORY NAME":"Mathematics","PARENT_ID":4,"PARENT_CATEGORY":"Books"},{"ID":519,"CATEGORY NAME":"Music","PARENT_ID":4,"PARENT_CATEGORY":"Books"},{"ID":508,"CATEGORY NAME":"Music and Entertainment","PARENT_ID":4,"PARENT_CATEGORY":"Books"},{"ID":548,"CATEGORY NAME":"Non profit","PARENT_ID":4,"PARENT_CATEGORY":"Books"},{"ID":512,"CATEGORY NAME":"Novels","PARENT_ID":4,"PARENT_CATEGORY":"Books"},{"ID":2092,"CATEGORY NAME":"Pets","PARENT_ID":4,"PARENT_CATEGORY":"Books"},{"ID":742,"CATEGORY NAME":"Philosophy","PARENT_ID":4,"PARENT_CATEGORY":"Books"},{"ID":544,"CATEGORY NAME":"Poetry","PARENT_ID":4,"PARENT_CATEGORY":"Books"},{"ID":509,"CATEGORY NAME":"Politics","PARENT_ID":4,"PARENT_CATEGORY":"Books"},{"ID":638,"CATEGORY NAME":"Psychology","PARENT_ID":4,"PARENT_CATEGORY":"Books"},{"ID":547,"CATEGORY NAME":"Real estate","PARENT_ID":4,"PARENT_CATEGORY":"Books"},{"ID":511,"CATEGORY NAME":"Religion","PARENT_ID":4,"PARENT_CATEGORY":"Books"},{"ID":2097,"CATEGORY NAME":"Romance","PARENT_ID":4,"PARENT_CATEGORY":"Books"},{"ID":514,"CATEGORY NAME":"Science and Technology","PARENT_ID":4,"PARENT_CATEGORY":"Books"},{"ID":521,"CATEGORY NAME":"Science Fiction","PARENT_ID":4,"PARENT_CATEGORY":"Books"},{"ID":2098,"CATEGORY NAME":"Sport","PARENT_ID":4,"PARENT_CATEGORY":"Books"},{"ID":542,"CATEGORY NAME":"Sport Hobbies and Games","PARENT_ID":4,"PARENT_CATEGORY":"Books"},{"ID":543,"CATEGORY NAME":"Story and Biography","PARENT_ID":4,"PARENT_CATEGORY":"Books"},{"ID":546,"CATEGORY NAME":"Tax matters","PARENT_ID":4,"PARENT_CATEGORY":"Books"},{"ID":517,"CATEGORY NAME":"Theatre","PARENT_ID":4,"PARENT_CATEGORY":"Books"},{"ID":505,"CATEGORY NAME":"Thriller and Fantasy","PARENT_ID":4,"PARENT_CATEGORY":"Books"},{"ID":2091,"CATEGORY NAME":"Transport","PARENT_ID":4,"PARENT_CATEGORY":"Books"},{"ID":516,"CATEGORY NAME":"Travel and Holiday","PARENT_ID":4,"PARENT_CATEGORY":"Books"},{"ID":503,"CATEGORY NAME":"Women","PARENT_ID":4,"PARENT_CATEGORY":"Books"},{"ID":518,"CATEGORY NAME":"Work","PARENT_ID":4,"PARENT_CATEGORY":"Books"},{"ID":1990,"CATEGORY NAME":"Business","PARENT_ID":1,"PARENT_CATEGORY":"Root"},{"ID":1991,"CATEGORY NAME":"Recruitment","PARENT_ID":1990,"PARENT_CATEGORY":"Business"},{"ID":8,"CATEGORY NAME":"Automotive","PARENT_ID":1,"PARENT_CATEGORY":"Root"},{"ID":13,"CATEGORY NAME":"Car and motor accessories","PARENT_ID":8,"PARENT_CATEGORY":"Automotive"},{"ID":9,"CATEGORY NAME":"Car insurance","PARENT_ID":8,"PARENT_CATEGORY":"Automotive"},{"ID":10,"CATEGORY NAME":"Motorcycles","PARENT_ID":8,"PARENT_CATEGORY":"Automotive"},{"ID":11,"CATEGORY NAME":"New cars","PARENT_ID":8,"PARENT_CATEGORY":"Automotive"},{"ID":12,"CATEGORY NAME":"Used cars","PARENT_ID":8,"PARENT_CATEGORY":"Automotive"},{"ID":14,"CATEGORY NAME":"Computer hardware and software","PARENT_ID":1,"PARENT_CATEGORY":"Root"},{"ID":15,"CATEGORY NAME":"Broadband and ISP","PARENT_ID":14,"PARENT_CATEGORY":"Computer hardware and software"},{"ID":461,"CATEGORY NAME":"Cases","PARENT_ID":14,"PARENT_CATEGORY":"Computer hardware and software"},{"ID":16,"CATEGORY NAME":"Computer accessories","PARENT_ID":14,"PARENT_CATEGORY":"Computer hardware and software"},{"ID":421,"CATEGORY NAME":"graphical tablet","PARENT_ID":16,"PARENT_CATEGORY":"Computer accessories"},{"ID":424,"CATEGORY NAME":"joystick","PARENT_ID":16,"PARENT_CATEGORY":"Computer accessories"},{"ID":422,"CATEGORY NAME":"keyboard","PARENT_ID":16,"PARENT_CATEGORY":"Computer accessories"},{"ID":918,"CATEGORY NAME":"Laptop Accessories","PARENT_ID":16,"PARENT_CATEGORY":"Computer accessories"},{"ID":423,"CATEGORY NAME":"mouse","PARENT_ID":16,"PARENT_CATEGORY":"Computer accessories"},{"ID":381,"CATEGORY NAME":"webcam","PARENT_ID":16,"PARENT_CATEGORY":"Computer accessories"},{"ID":463,"CATEGORY NAME":"Controller Cards","PARENT_ID":14,"PARENT_CATEGORY":"Computer hardware and software"},{"ID":17,"CATEGORY NAME":"Desktops","PARENT_ID":14,"PARENT_CATEGORY":"Computer hardware and software"},{"ID":21,"CATEGORY NAME":"Graphics cards","PARENT_ID":14,"PARENT_CATEGORY":"Computer hardware and software"},{"ID":18,"CATEGORY NAME":"Hard drives","PARENT_ID":14,"PARENT_CATEGORY":"Computer hardware and software"},{"ID":39,"CATEGORY NAME":"Hosting","PARENT_ID":14,"PARENT_CATEGORY":"Computer hardware and software"},{"ID":245,"CATEGORY NAME":"Input devices","PARENT_ID":14,"PARENT_CATEGORY":"Computer hardware and software"},{"ID":19,"CATEGORY NAME":"Laptops","PARENT_ID":14,"PARENT_CATEGORY":"Computer hardware and software"},{"ID":20,"CATEGORY NAME":"Memory","PARENT_ID":14,"PARENT_CATEGORY":"Computer hardware and software"},{"ID":22,"CATEGORY NAME":"Modems","PARENT_ID":14,"PARENT_CATEGORY":"Computer hardware and software"},{"ID":23,"CATEGORY NAME":"Monitors","PARENT_ID":14,"PARENT_CATEGORY":"Computer hardware and software"},{"ID":382,"CATEGORY NAME":"Motherboards","PARENT_ID":14,"PARENT_CATEGORY":"Computer hardware and software"},{"ID":2230,"CATEGORY NAME":"Netbooks","PARENT_ID":14,"PARENT_CATEGORY":"Computer hardware and software"},{"ID":221,"CATEGORY NAME":"Networking","PARENT_ID":14,"PARENT_CATEGORY":"Computer hardware and software"},{"ID":24,"CATEGORY NAME":"PDAs","PARENT_ID":14,"PARENT_CATEGORY":"Computer hardware and software"},{"ID":462,"CATEGORY NAME":"Power","PARENT_ID":14,"PARENT_CATEGORY":"Computer hardware and software"},{"ID":25,"CATEGORY NAME":"Printers","PARENT_ID":14,"PARENT_CATEGORY":"Computer hardware and software"},{"ID":383,"CATEGORY NAME":"Processors","PARENT_ID":14,"PARENT_CATEGORY":"Computer hardware and software"},{"ID":26,"CATEGORY NAME":"Scanners","PARENT_ID":14,"PARENT_CATEGORY":"Computer hardware and software"},{"ID":958,"CATEGORY NAME":"Security","PARENT_ID":14,"PARENT_CATEGORY":"Computer hardware and software"},{"ID":938,"CATEGORY NAME":"Servers","PARENT_ID":14,"PARENT_CATEGORY":"Computer hardware and software"},{"ID":939,"CATEGORY NAME":"Accessories","PARENT_ID":938,"PARENT_CATEGORY":"Servers"},{"ID":27,"CATEGORY NAME":"Software","PARENT_ID":14,"PARENT_CATEGORY":"Computer hardware and software"},{"ID":28,"CATEGORY NAME":"Sound cards","PARENT_ID":14,"PARENT_CATEGORY":"Computer hardware and software"},{"ID":29,"CATEGORY NAME":"Storage","PARENT_ID":14,"PARENT_CATEGORY":"Computer hardware and software"},{"ID":222,"CATEGORY NAME":"Blankmedia","PARENT_ID":29,"PARENT_CATEGORY":"Storage"},{"ID":31,"CATEGORY NAME":"CD ROM drives","PARENT_ID":29,"PARENT_CATEGORY":"Storage"},{"ID":30,"CATEGORY NAME":"CD writers","PARENT_ID":29,"PARENT_CATEGORY":"Storage"},{"ID":32,"CATEGORY NAME":"DAT drives","PARENT_ID":29,"PARENT_CATEGORY":"Storage"},{"ID":33,"CATEGORY NAME":"DVD drives","PARENT_ID":29,"PARENT_CATEGORY":"Storage"},{"ID":34,"CATEGORY NAME":"DVD writers","PARENT_ID":29,"PARENT_CATEGORY":"Storage"},{"ID":243,"CATEGORY NAME":"Floppy","PARENT_ID":29,"PARENT_CATEGORY":"Storage"},{"ID":35,"CATEGORY NAME":"Floppy disk drives","PARENT_ID":29,"PARENT_CATEGORY":"Storage"},{"ID":36,"CATEGORY NAME":"Optical drives","PARENT_ID":29,"PARENT_CATEGORY":"Storage"},{"ID":244,"CATEGORY NAME":"Portable storage","PARENT_ID":29,"PARENT_CATEGORY":"Storage"},{"ID":37,"CATEGORY NAME":"Tape drives","PARENT_ID":29,"PARENT_CATEGORY":"Storage"},{"ID":38,"CATEGORY NAME":"Zip drives","PARENT_ID":29,"PARENT_CATEGORY":"Storage"},{"ID":2210,"CATEGORY NAME":"Tablets","PARENT_ID":14,"PARENT_CATEGORY":"Computer hardware and software"},{"ID":464,"CATEGORY NAME":"USB Devices","PARENT_ID":14,"PARENT_CATEGORY":"Computer hardware and software"},{"ID":590,"CATEGORY NAME":"Utilities","PARENT_ID":14,"PARENT_CATEGORY":"Computer hardware and software"},{"ID":589,"CATEGORY NAME":"Videogames","PARENT_ID":14,"PARENT_CATEGORY":"Computer hardware and software"},{"ID":361,"CATEGORY NAME":"Education","PARENT_ID":1,"PARENT_CATEGORY":"Root"},{"ID":1628,"CATEGORY NAME":"College","PARENT_ID":361,"PARENT_CATEGORY":"Education"},{"ID":363,"CATEGORY NAME":"Computer Based Training","PARENT_ID":361,"PARENT_CATEGORY":"Education"},{"ID":362,"CATEGORY NAME":"E learning","PARENT_ID":361,"PARENT_CATEGORY":"Education"},{"ID":364,"CATEGORY NAME":"Manuals","PARENT_ID":361,"PARENT_CATEGORY":"Education"},{"ID":40,"CATEGORY NAME":"Electronics","PARENT_ID":1,"PARENT_CATEGORY":"Root"},{"ID":322,"CATEGORY NAME":"Accessories","PARENT_ID":40,"PARENT_CATEGORY":"Electronics"},{"ID":41,"CATEGORY NAME":"Amplifiers","PARENT_ID":40,"PARENT_CATEGORY":"Electronics"},{"ID":246,"CATEGORY NAME":"Audio peripherals","PARENT_ID":40,"PARENT_CATEGORY":"Electronics"},{"ID":42,"CATEGORY NAME":"Audio systems","PARENT_ID":40,"PARENT_CATEGORY":"Electronics"},{"ID":323,"CATEGORY NAME":"Cables","PARENT_ID":40,"PARENT_CATEGORY":"Electronics"},{"ID":43,"CATEGORY NAME":"Camcorders","PARENT_ID":40,"PARENT_CATEGORY":"Electronics"},{"ID":44,"CATEGORY NAME":"Cameras","PARENT_ID":40,"PARENT_CATEGORY":"Electronics"},{"ID":45,"CATEGORY NAME":"Cassete decks","PARENT_ID":40,"PARENT_CATEGORY":"Electronics"},{"ID":46,"CATEGORY NAME":"CD players","PARENT_ID":40,"PARENT_CATEGORY":"Electronics"},{"ID":47,"CATEGORY NAME":"Clock radios","PARENT_ID":40,"PARENT_CATEGORY":"Electronics"},{"ID":301,"CATEGORY NAME":"Digital radios","PARENT_ID":40,"PARENT_CATEGORY":"Electronics"},{"ID":48,"CATEGORY NAME":"DVD players","PARENT_ID":40,"PARENT_CATEGORY":"Electronics"},{"ID":49,"CATEGORY NAME":"GPS","PARENT_ID":40,"PARENT_CATEGORY":"Electronics"},{"ID":1910,"CATEGORY NAME":"Accessories","PARENT_ID":49,"PARENT_CATEGORY":"GPS"},{"ID":1911,"CATEGORY NAME":"Maps","PARENT_ID":49,"PARENT_CATEGORY":"GPS"},{"ID":50,"CATEGORY NAME":"Headphones","PARENT_ID":40,"PARENT_CATEGORY":"Electronics"},{"ID":51,"CATEGORY NAME":"Home cinema","PARENT_ID":40,"PARENT_CATEGORY":"Electronics"},{"ID":52,"CATEGORY NAME":"Memory stick","PARENT_ID":40,"PARENT_CATEGORY":"Electronics"},{"ID":53,"CATEGORY NAME":"Minidisc","PARENT_ID":40,"PARENT_CATEGORY":"Electronics"},{"ID":54,"CATEGORY NAME":"Mobiles phones and faxes","PARENT_ID":40,"PARENT_CATEGORY":"Electronics"},{"ID":55,"CATEGORY NAME":"All in ones","PARENT_ID":54,"PARENT_CATEGORY":"Mobiles phones and faxes"},{"ID":2311,"CATEGORY NAME":"Assistive Mobile Phones","PARENT_ID":54,"PARENT_CATEGORY":"Mobiles phones and faxes"},{"ID":2310,"CATEGORY NAME":"Assistive Telephones","PARENT_ID":54,"PARENT_CATEGORY":"Mobiles phones and faxes"},{"ID":56,"CATEGORY NAME":"Fax machines","PARENT_ID":54,"PARENT_CATEGORY":"Mobiles phones and faxes"},{"ID":57,"CATEGORY NAME":"Mobile phone accessories","PARENT_ID":54,"PARENT_CATEGORY":"Mobiles phones and faxes"},{"ID":59,"CATEGORY NAME":"Prepayment phones","PARENT_ID":54,"PARENT_CATEGORY":"Mobiles phones and faxes"},{"ID":60,"CATEGORY NAME":"Ringtones and logos","PARENT_ID":54,"PARENT_CATEGORY":"Mobiles phones and faxes"},{"ID":1444,"CATEGORY NAME":"Erotic Downloads","PARENT_ID":60,"PARENT_CATEGORY":"Ringtones and logos"},{"ID":1441,"CATEGORY NAME":"Logos and Wallpapers","PARENT_ID":60,"PARENT_CATEGORY":"Ringtones and logos"},{"ID":1469,"CATEGORY NAME":"Animated Logos","PARENT_ID":1441,"PARENT_CATEGORY":"Logos and Wallpapers"},{"ID":1452,"CATEGORY NAME":"Animated Screensavers","PARENT_ID":1441,"PARENT_CATEGORY":"Logos and Wallpapers"},{"ID":1470,"CATEGORY NAME":"Coloured Logos","PARENT_ID":1441,"PARENT_CATEGORY":"Logos and Wallpapers"},{"ID":1451,"CATEGORY NAME":"Colour Wallpapers","PARENT_ID":1441,"PARENT_CATEGORY":"Logos and Wallpapers"},{"ID":1501,"CATEGORY NAME":"Logos B and W","PARENT_ID":1441,"PARENT_CATEGORY":"Logos and Wallpapers"},{"ID":1471,"CATEGORY NAME":"Mobile Pictures","PARENT_ID":1441,"PARENT_CATEGORY":"Logos and Wallpapers"},{"ID":1453,"CATEGORY NAME":"Themes","PARENT_ID":1441,"PARENT_CATEGORY":"Logos and Wallpapers"},{"ID":1445,"CATEGORY NAME":"MMS","PARENT_ID":60,"PARENT_CATEGORY":"Ringtones and logos"},{"ID":1454,"CATEGORY NAME":"Mobile Games","PARENT_ID":60,"PARENT_CATEGORY":"Ringtones and logos"},{"ID":1455,"CATEGORY NAME":"Mobile Software","PARENT_ID":60,"PARENT_CATEGORY":"Ringtones and logos"},{"ID":1472,"CATEGORY NAME":"Mobile Videos","PARENT_ID":60,"PARENT_CATEGORY":"Ringtones and logos"},{"ID":1440,"CATEGORY NAME":"Ringtones","PARENT_ID":60,"PARENT_CATEGORY":"Ringtones and logos"},{"ID":1449,"CATEGORY NAME":"Fun Sounds","PARENT_ID":1440,"PARENT_CATEGORY":"Ringtones"},{"ID":1500,"CATEGORY NAME":"Mono Ringtones","PARENT_ID":1440,"PARENT_CATEGORY":"Ringtones"},{"ID":1447,"CATEGORY NAME":"Polyphonic Ringtones","PARENT_ID":1440,"PARENT_CATEGORY":"Ringtones"},{"ID":1446,"CATEGORY NAME":"Real Music Ringtones","PARENT_ID":1440,"PARENT_CATEGORY":"Ringtones"},{"ID":1448,"CATEGORY NAME":"Video Ringtones","PARENT_ID":1440,"PARENT_CATEGORY":"Ringtones"},{"ID":58,"CATEGORY NAME":"Subscription phones","PARENT_ID":54,"PARENT_CATEGORY":"Mobiles phones and faxes"},{"ID":61,"CATEGORY NAME":"Telephones","PARENT_ID":54,"PARENT_CATEGORY":"Mobiles phones and faxes"},{"ID":321,"CATEGORY NAME":"Portable music","PARENT_ID":40,"PARENT_CATEGORY":"Electronics"},{"ID":302,"CATEGORY NAME":"Portable radios","PARENT_ID":40,"PARENT_CATEGORY":"Electronics"},{"ID":62,"CATEGORY NAME":"Projectors","PARENT_ID":40,"PARENT_CATEGORY":"Electronics"},{"ID":63,"CATEGORY NAME":"Remote controls","PARENT_ID":40,"PARENT_CATEGORY":"Electronics"},{"ID":64,"CATEGORY NAME":"Speakers","PARENT_ID":40,"PARENT_CATEGORY":"Electronics"},{"ID":65,"CATEGORY NAME":"Televisions","PARENT_ID":40,"PARENT_CATEGORY":"Electronics"},{"ID":66,"CATEGORY NAME":"Tuners","PARENT_ID":40,"PARENT_CATEGORY":"Electronics"},{"ID":67,"CATEGORY NAME":"VCRs","PARENT_ID":40,"PARENT_CATEGORY":"Electronics"},{"ID":303,"CATEGORY NAME":"Weather stations","PARENT_ID":40,"PARENT_CATEGORY":"Electronics"},{"ID":68,"CATEGORY NAME":"Fashion","PARENT_ID":1,"PARENT_CATEGORY":"Root"},{"ID":800,"CATEGORY NAME":"Adult clothing","PARENT_ID":68,"PARENT_CATEGORY":"Fashion"},{"ID":241,"CATEGORY NAME":"Baby and toddler clothing","PARENT_ID":68,"PARENT_CATEGORY":"Fashion"},{"ID":2370,"CATEGORY NAME":"Bags","PARENT_ID":68,"PARENT_CATEGORY":"Fashion"},{"ID":2371,"CATEGORY NAME":"Belts","PARENT_ID":68,"PARENT_CATEGORY":"Fashion"},{"ID":2372,"CATEGORY NAME":"Blouses","PARENT_ID":68,"PARENT_CATEGORY":"Fashion"},{"ID":242,"CATEGORY NAME":"Children clothing","PARENT_ID":68,"PARENT_CATEGORY":"Fashion"},{"ID":2070,"CATEGORY NAME":"boys","PARENT_ID":242,"PARENT_CATEGORY":"Children clothing"},{"ID":2071,"CATEGORY NAME":"girls","PARENT_ID":242,"PARENT_CATEGORY":"Children clothing"},{"ID":69,"CATEGORY NAME":"Clothing accessories","PARENT_ID":68,"PARENT_CATEGORY":"Fashion"},{"ID":981,"CATEGORY NAME":"Children","PARENT_ID":69,"PARENT_CATEGORY":"Clothing accessories"},{"ID":980,"CATEGORY NAME":"Men","PARENT_ID":69,"PARENT_CATEGORY":"Clothing accessories"},{"ID":979,"CATEGORY NAME":"Women","PARENT_ID":69,"PARENT_CATEGORY":"Clothing accessories"},{"ID":2373,"CATEGORY NAME":"Dresses","PARENT_ID":68,"PARENT_CATEGORY":"Fashion"},{"ID":2374,"CATEGORY NAME":"Jackets","PARENT_ID":68,"PARENT_CATEGORY":"Fashion"},{"ID":201,"CATEGORY NAME":"Jeans","PARENT_ID":68,"PARENT_CATEGORY":"Fashion"},{"ID":70,"CATEGORY NAME":"Jewellery","PARENT_ID":68,"PARENT_CATEGORY":"Fashion"},{"ID":2375,"CATEGORY NAME":"Knitwear","PARENT_ID":68,"PARENT_CATEGORY":"Fashion"},{"ID":2050,"CATEGORY NAME":"Large size clothing","PARENT_ID":68,"PARENT_CATEGORY":"Fashion"},{"ID":71,"CATEGORY NAME":"Lingerie","PARENT_ID":68,"PARENT_CATEGORY":"Fashion"},{"ID":72,"CATEGORY NAME":"Basques bustiers and corsets","PARENT_ID":71,"PARENT_CATEGORY":"Lingerie"},{"ID":73,"CATEGORY NAME":"Bodies","PARENT_ID":71,"PARENT_CATEGORY":"Lingerie"},{"ID":74,"CATEGORY NAME":"Bras","PARENT_ID":71,"PARENT_CATEGORY":"Lingerie"},{"ID":75,"CATEGORY NAME":"Breifs","PARENT_ID":71,"PARENT_CATEGORY":"Lingerie"},{"ID":76,"CATEGORY NAME":"Combination sets","PARENT_ID":71,"PARENT_CATEGORY":"Lingerie"},{"ID":678,"CATEGORY NAME":"Hotpants","PARENT_ID":71,"PARENT_CATEGORY":"Lingerie"},{"ID":77,"CATEGORY NAME":"Nightwear","PARENT_ID":71,"PARENT_CATEGORY":"Lingerie"},{"ID":78,"CATEGORY NAME":"Socks and hosiery","PARENT_ID":71,"PARENT_CATEGORY":"Lingerie"},{"ID":79,"CATEGORY NAME":"Suspenders and garters","PARENT_ID":71,"PARENT_CATEGORY":"Lingerie"},{"ID":80,"CATEGORY NAME":"Thongs and G strings","PARENT_ID":71,"PARENT_CATEGORY":"Lingerie"},{"ID":81,"CATEGORY NAME":"Mens clothing","PARENT_ID":68,"PARENT_CATEGORY":"Fashion"},{"ID":984,"CATEGORY NAME":"Outdoor clothing","PARENT_ID":68,"PARENT_CATEGORY":"Fashion"},{"ID":986,"CATEGORY NAME":"Men","PARENT_ID":984,"PARENT_CATEGORY":"Outdoor clothing"},{"ID":985,"CATEGORY NAME":"Women","PARENT_ID":984,"PARENT_CATEGORY":"Outdoor clothing"},{"ID":82,"CATEGORY NAME":"Perfume","PARENT_ID":68,"PARENT_CATEGORY":"Fashion"},{"ID":2383,"CATEGORY NAME":"Shirts","PARENT_ID":68,"PARENT_CATEGORY":"Fashion"},{"ID":83,"CATEGORY NAME":"Shoes","PARENT_ID":68,"PARENT_CATEGORY":"Fashion"},{"ID":759,"CATEGORY NAME":"Children","PARENT_ID":83,"PARENT_CATEGORY":"Shoes"},{"ID":761,"CATEGORY NAME":"Gentlemen","PARENT_ID":83,"PARENT_CATEGORY":"Shoes"},{"ID":760,"CATEGORY NAME":"Ladies","PARENT_ID":83,"PARENT_CATEGORY":"Shoes"},{"ID":2382,"CATEGORY NAME":"Skirts","PARENT_ID":68,"PARENT_CATEGORY":"Fashion"},{"ID":763,"CATEGORY NAME":"Socks","PARENT_ID":68,"PARENT_CATEGORY":"Fashion"},{"ID":202,"CATEGORY NAME":"Sport","PARENT_ID":68,"PARENT_CATEGORY":"Fashion"},{"ID":2376,"CATEGORY NAME":"Suits","PARENT_ID":68,"PARENT_CATEGORY":"Fashion"},{"ID":2377,"CATEGORY NAME":"Sweaters","PARENT_ID":68,"PARENT_CATEGORY":"Fashion"},{"ID":84,"CATEGORY NAME":"Swimwear","PARENT_ID":68,"PARENT_CATEGORY":"Fashion"},{"ID":982,"CATEGORY NAME":"Men","PARENT_ID":84,"PARENT_CATEGORY":"Swimwear"},{"ID":983,"CATEGORY NAME":"Women","PARENT_ID":84,"PARENT_CATEGORY":"Swimwear"},{"ID":2378,"CATEGORY NAME":"Tops","PARENT_ID":68,"PARENT_CATEGORY":"Fashion"},{"ID":2379,"CATEGORY NAME":"Trousers","PARENT_ID":68,"PARENT_CATEGORY":"Fashion"},{"ID":2380,"CATEGORY NAME":"TShirts","PARENT_ID":68,"PARENT_CATEGORY":"Fashion"},{"ID":2381,"CATEGORY NAME":"Underwear","PARENT_ID":68,"PARENT_CATEGORY":"Fashion"},{"ID":85,"CATEGORY NAME":"Womens clothing","PARENT_ID":68,"PARENT_CATEGORY":"Fashion"},{"ID":2330,"CATEGORY NAME":"Maternity","PARENT_ID":85,"PARENT_CATEGORY":"Womens clothing"},{"ID":86,"CATEGORY NAME":"Films, Television and Theatre","PARENT_ID":1,"PARENT_CATEGORY":"Root"},{"ID":1814,"CATEGORY NAME":"Show Tickets","PARENT_ID":86,"PARENT_CATEGORY":"Films, Television and Theatre"},{"ID":1479,"CATEGORY NAME":"Theatre tickets","PARENT_ID":86,"PARENT_CATEGORY":"Films, Television and Theatre"},{"ID":87,"CATEGORY NAME":"DVDs","PARENT_ID":86,"PARENT_CATEGORY":"Films, Television and Theatre"},{"ID":534,"CATEGORY NAME":"Action","PARENT_ID":87,"PARENT_CATEGORY":"DVDs"},{"ID":533,"CATEGORY NAME":"Adventure","PARENT_ID":87,"PARENT_CATEGORY":"DVDs"},{"ID":531,"CATEGORY NAME":"Animation","PARENT_ID":87,"PARENT_CATEGORY":"DVDs"},{"ID":552,"CATEGORY NAME":"Anime and Manga","PARENT_ID":87,"PARENT_CATEGORY":"DVDs"},{"ID":535,"CATEGORY NAME":"Biography","PARENT_ID":87,"PARENT_CATEGORY":"DVDs"},{"ID":532,"CATEGORY NAME":"Cartoon","PARENT_ID":87,"PARENT_CATEGORY":"DVDs"},{"ID":718,"CATEGORY NAME":"Children","PARENT_ID":87,"PARENT_CATEGORY":"DVDs"},{"ID":580,"CATEGORY NAME":"Classical","PARENT_ID":87,"PARENT_CATEGORY":"DVDs"},{"ID":536,"CATEGORY NAME":"Comedy","PARENT_ID":87,"PARENT_CATEGORY":"DVDs"},{"ID":553,"CATEGORY NAME":"Comic","PARENT_ID":87,"PARENT_CATEGORY":"DVDs"},{"ID":578,"CATEGORY NAME":"Coming soon","PARENT_ID":87,"PARENT_CATEGORY":"DVDs"},{"ID":537,"CATEGORY NAME":"Documentary","PARENT_ID":87,"PARENT_CATEGORY":"DVDs"},{"ID":538,"CATEGORY NAME":"Drama","PARENT_ID":87,"PARENT_CATEGORY":"DVDs"},{"ID":550,"CATEGORY NAME":"Erotic","PARENT_ID":87,"PARENT_CATEGORY":"DVDs"},{"ID":522,"CATEGORY NAME":"Family and Child","PARENT_ID":87,"PARENT_CATEGORY":"DVDs"},{"ID":523,"CATEGORY NAME":"Fantasy","PARENT_ID":87,"PARENT_CATEGORY":"DVDs"},{"ID":528,"CATEGORY NAME":"History","PARENT_ID":87,"PARENT_CATEGORY":"DVDs"},{"ID":525,"CATEGORY NAME":"Horror","PARENT_ID":87,"PARENT_CATEGORY":"DVDs"},{"ID":579,"CATEGORY NAME":"Independent","PARENT_ID":87,"PARENT_CATEGORY":"DVDs"},{"ID":549,"CATEGORY NAME":"Music","PARENT_ID":87,"PARENT_CATEGORY":"DVDs"},{"ID":526,"CATEGORY NAME":"Musical","PARENT_ID":87,"PARENT_CATEGORY":"DVDs"},{"ID":527,"CATEGORY NAME":"Romantic","PARENT_ID":87,"PARENT_CATEGORY":"DVDs"},{"ID":551,"CATEGORY NAME":"Science Fiction","PARENT_ID":87,"PARENT_CATEGORY":"DVDs"},{"ID":720,"CATEGORY NAME":"Special offer","PARENT_ID":87,"PARENT_CATEGORY":"DVDs"},{"ID":719,"CATEGORY NAME":"Sport","PARENT_ID":87,"PARENT_CATEGORY":"DVDs"},{"ID":529,"CATEGORY NAME":"Thriller","PARENT_ID":87,"PARENT_CATEGORY":"DVDs"},{"ID":524,"CATEGORY NAME":"War","PARENT_ID":87,"PARENT_CATEGORY":"DVDs"},{"ID":530,"CATEGORY NAME":"Western","PARENT_ID":87,"PARENT_CATEGORY":"DVDs"},{"ID":1059,"CATEGORY NAME":"Movie tickets","PARENT_ID":86,"PARENT_CATEGORY":"Films, Television and Theatre"},{"ID":2290,"CATEGORY NAME":"BluRay","PARENT_ID":86,"PARENT_CATEGORY":"Films, Television and Theatre"},{"ID":569,"CATEGORY NAME":"Action","PARENT_ID":88,"PARENT_CATEGORY":"Videos"},{"ID":568,"CATEGORY NAME":"Adventure","PARENT_ID":88,"PARENT_CATEGORY":"Videos"},{"ID":566,"CATEGORY NAME":"Anime and Manga","PARENT_ID":88,"PARENT_CATEGORY":"Videos"},{"ID":570,"CATEGORY NAME":"Biograpy","PARENT_ID":88,"PARENT_CATEGORY":"Videos"},{"ID":567,"CATEGORY NAME":"Cartoon","PARENT_ID":88,"PARENT_CATEGORY":"Videos"},{"ID":572,"CATEGORY NAME":"Comedy","PARENT_ID":88,"PARENT_CATEGORY":"Videos"},{"ID":571,"CATEGORY NAME":"Comic","PARENT_ID":88,"PARENT_CATEGORY":"Videos"},{"ID":573,"CATEGORY NAME":"Documentary","PARENT_ID":88,"PARENT_CATEGORY":"Videos"},{"ID":574,"CATEGORY NAME":"Drama","PARENT_ID":88,"PARENT_CATEGORY":"Videos"},{"ID":555,"CATEGORY NAME":"Erotic","PARENT_ID":88,"PARENT_CATEGORY":"Videos"},{"ID":557,"CATEGORY NAME":"Family and Child","PARENT_ID":88,"PARENT_CATEGORY":"Videos"},{"ID":558,"CATEGORY NAME":"Fantasy","PARENT_ID":88,"PARENT_CATEGORY":"Videos"},{"ID":563,"CATEGORY NAME":"History","PARENT_ID":88,"PARENT_CATEGORY":"Videos"},{"ID":560,"CATEGORY NAME":"Horror","PARENT_ID":88,"PARENT_CATEGORY":"Videos"},{"ID":554,"CATEGORY NAME":"Music","PARENT_ID":88,"PARENT_CATEGORY":"Videos"},{"ID":561,"CATEGORY NAME":"Musical","PARENT_ID":88,"PARENT_CATEGORY":"Videos"},{"ID":562,"CATEGORY NAME":"Romantic","PARENT_ID":88,"PARENT_CATEGORY":"Videos"},{"ID":556,"CATEGORY NAME":"Science Fiction","PARENT_ID":88,"PARENT_CATEGORY":"Videos"},{"ID":564,"CATEGORY NAME":"Thriller","PARENT_ID":88,"PARENT_CATEGORY":"Videos"},{"ID":559,"CATEGORY NAME":"War","PARENT_ID":88,"PARENT_CATEGORY":"Videos"},{"ID":565,"CATEGORY NAME":"Western","PARENT_ID":88,"PARENT_CATEGORY":"Videos"},{"ID":88,"CATEGORY NAME":"Videos","PARENT_ID":86,"PARENT_CATEGORY":"Films, Television and Theatre"},{"ID":89,"CATEGORY NAME":"Food and drink","PARENT_ID":1,"PARENT_CATEGORY":"Root"},{"ID":90,"CATEGORY NAME":"Beers","PARENT_ID":89,"PARENT_CATEGORY":"Food and drink"},{"ID":91,"CATEGORY NAME":"Champagne","PARENT_ID":89,"PARENT_CATEGORY":"Food and drink"},{"ID":441,"CATEGORY NAME":"Confectionery","PARENT_ID":89,"PARENT_CATEGORY":"Food and drink"},{"ID":1478,"CATEGORY NAME":"Restaurants","PARENT_ID":89,"PARENT_CATEGORY":"Food and drink"},{"ID":92,"CATEGORY NAME":"Spirits","PARENT_ID":89,"PARENT_CATEGORY":"Food and drink"},{"ID":93,"CATEGORY NAME":"Vitamins and supplements","PARENT_ID":89,"PARENT_CATEGORY":"Food and drink"},{"ID":94,"CATEGORY NAME":"Wine","PARENT_ID":89,"PARENT_CATEGORY":"Food and drink"},{"ID":96,"CATEGORY NAME":"Dessert wine","PARENT_ID":94,"PARENT_CATEGORY":"Wine"},{"ID":100,"CATEGORY NAME":"Other wine","PARENT_ID":94,"PARENT_CATEGORY":"Wine"},{"ID":97,"CATEGORY NAME":"Red wine","PARENT_ID":94,"PARENT_CATEGORY":"Wine"},{"ID":98,"CATEGORY NAME":"Rose wine","PARENT_ID":94,"PARENT_CATEGORY":"Wine"},{"ID":99,"CATEGORY NAME":"Sparkling wine","PARENT_ID":94,"PARENT_CATEGORY":"Wine"},{"ID":95,"CATEGORY NAME":"White wine","PARENT_ID":94,"PARENT_CATEGORY":"Wine"},{"ID":101,"CATEGORY NAME":"Games consoles and toys","PARENT_ID":1,"PARENT_CATEGORY":"Root"},{"ID":881,"CATEGORY NAME":"Collectors items","PARENT_ID":101,"PARENT_CATEGORY":"Games consoles and toys"},{"ID":102,"CATEGORY NAME":"Consoles and accessories","PARENT_ID":101,"PARENT_CATEGORY":"Games consoles and toys"},{"ID":205,"CATEGORY NAME":"Gadgets","PARENT_ID":101,"PARENT_CATEGORY":"Games consoles and toys"},{"ID":106,"CATEGORY NAME":"Play toys","PARENT_ID":101,"PARENT_CATEGORY":"Games consoles and toys"},{"ID":204,"CATEGORY NAME":"Toys","PARENT_ID":101,"PARENT_CATEGORY":"Games consoles and toys"},{"ID":2178,"CATEGORY NAME":"Action Figures","PARENT_ID":204,"PARENT_CATEGORY":"Toys"},{"ID":2170,"CATEGORY NAME":"Baby","PARENT_ID":204,"PARENT_CATEGORY":"Toys"},{"ID":2011,"CATEGORY NAME":"Board Games","PARENT_ID":204,"PARENT_CATEGORY":"Toys"},{"ID":2171,"CATEGORY NAME":"Building and Construction","PARENT_ID":204,"PARENT_CATEGORY":"Toys"},{"ID":2172,"CATEGORY NAME":"Creative and Hobby","PARENT_ID":204,"PARENT_CATEGORY":"Toys"},{"ID":2177,"CATEGORY NAME":"Dolls","PARENT_ID":204,"PARENT_CATEGORY":"Toys"},{"ID":2173,"CATEGORY NAME":"Educational toys","PARENT_ID":204,"PARENT_CATEGORY":"Toys"},{"ID":2174,"CATEGORY NAME":"Electronic Toys","PARENT_ID":204,"PARENT_CATEGORY":"Toys"},{"ID":2175,"CATEGORY NAME":"Fashion","PARENT_ID":204,"PARENT_CATEGORY":"Toys"},{"ID":2176,"CATEGORY NAME":"Music and instruments","PARENT_ID":204,"PARENT_CATEGORY":"Toys"},{"ID":2012,"CATEGORY NAME":"Puzzles","PARENT_ID":204,"PARENT_CATEGORY":"Toys"},{"ID":2179,"CATEGORY NAME":"Sports and Outdoor","PARENT_ID":204,"PARENT_CATEGORY":"Toys"},{"ID":2180,"CATEGORY NAME":"Vehicles","PARENT_ID":204,"PARENT_CATEGORY":"Toys"},{"ID":105,"CATEGORY NAME":"Video games","PARENT_ID":101,"PARENT_CATEGORY":"Games consoles and toys"},{"ID":585,"CATEGORY NAME":"Gameboy","PARENT_ID":105,"PARENT_CATEGORY":"Video games"},{"ID":586,"CATEGORY NAME":"Gamecube","PARENT_ID":105,"PARENT_CATEGORY":"Video games"},{"ID":2190,"CATEGORY NAME":"Nintendo DS","PARENT_ID":105,"PARENT_CATEGORY":"Video games"},{"ID":721,"CATEGORY NAME":"PC","PARENT_ID":105,"PARENT_CATEGORY":"Video games"},{"ID":588,"CATEGORY NAME":"Playstation","PARENT_ID":105,"PARENT_CATEGORY":"Video games"},{"ID":2010,"CATEGORY NAME":"Wii","PARENT_ID":105,"PARENT_CATEGORY":"Video games"},{"ID":587,"CATEGORY NAME":"Xbox","PARENT_ID":105,"PARENT_CATEGORY":"Video games"},{"ID":107,"CATEGORY NAME":"Gifts","PARENT_ID":1,"PARENT_CATEGORY":"Root"},{"ID":799,"CATEGORY NAME":"Adult gifts","PARENT_ID":107,"PARENT_CATEGORY":"Gifts"},{"ID":108,"CATEGORY NAME":"Anniversary gifts","PARENT_ID":107,"PARENT_CATEGORY":"Gifts"},{"ID":109,"CATEGORY NAME":"Birthday gifts","PARENT_ID":107,"PARENT_CATEGORY":"Gifts"},{"ID":1891,"CATEGORY NAME":"Charitable Gifts","PARENT_ID":107,"PARENT_CATEGORY":"Gifts"},{"ID":110,"CATEGORY NAME":"Chocolate","PARENT_ID":107,"PARENT_CATEGORY":"Gifts"},{"ID":111,"CATEGORY NAME":"Experiences","PARENT_ID":107,"PARENT_CATEGORY":"Gifts"},{"ID":112,"CATEGORY NAME":"Flowers","PARENT_ID":107,"PARENT_CATEGORY":"Gifts"},{"ID":344,"CATEGORY NAME":"Gadgets","PARENT_ID":107,"PARENT_CATEGORY":"Gifts"},{"ID":343,"CATEGORY NAME":"Gifts for kids","PARENT_ID":107,"PARENT_CATEGORY":"Gifts"},{"ID":341,"CATEGORY NAME":"Gifts for men","PARENT_ID":107,"PARENT_CATEGORY":"Gifts"},{"ID":342,"CATEGORY NAME":"Gifts for women","PARENT_ID":107,"PARENT_CATEGORY":"Gifts"},{"ID":345,"CATEGORY NAME":"Gourmet gifts","PARENT_ID":107,"PARENT_CATEGORY":"Gifts"},{"ID":113,"CATEGORY NAME":"Wedding gifts","PARENT_ID":107,"PARENT_CATEGORY":"Gifts"},{"ID":114,"CATEGORY NAME":"Health and beauty","PARENT_ID":1,"PARENT_CATEGORY":"Root"},{"ID":115,"CATEGORY NAME":"Bodycare","PARENT_ID":114,"PARENT_CATEGORY":"Health and beauty"},{"ID":2313,"CATEGORY NAME":"Disability Aids","PARENT_ID":115,"PARENT_CATEGORY":"Bodycare"},{"ID":2314,"CATEGORY NAME":"Health Accessories","PARENT_ID":115,"PARENT_CATEGORY":"Bodycare"},{"ID":2312,"CATEGORY NAME":"Mobility Aids","PARENT_ID":115,"PARENT_CATEGORY":"Bodycare"},{"ID":116,"CATEGORY NAME":"Contact lenses","PARENT_ID":114,"PARENT_CATEGORY":"Health and beauty"},{"ID":117,"CATEGORY NAME":"Cosmetics","PARENT_ID":114,"PARENT_CATEGORY":"Health and beauty"},{"ID":118,"CATEGORY NAME":"Diet","PARENT_ID":114,"PARENT_CATEGORY":"Health and beauty"},{"ID":1502,"CATEGORY NAME":"Erotic","PARENT_ID":114,"PARENT_CATEGORY":"Health and beauty"},{"ID":119,"CATEGORY NAME":"Fitness equipment","PARENT_ID":114,"PARENT_CATEGORY":"Health and beauty"},{"ID":120,"CATEGORY NAME":"Haircare","PARENT_ID":114,"PARENT_CATEGORY":"Health and beauty"},{"ID":778,"CATEGORY NAME":"Shaving machines","PARENT_ID":114,"PARENT_CATEGORY":"Health and beauty"},{"ID":2350,"CATEGORY NAME":"Health and Safety","PARENT_ID":1,"PARENT_CATEGORY":"Root"},{"ID":2355,"CATEGORY NAME":"Alarms and Detectors","PARENT_ID":2350,"PARENT_CATEGORY":"Health and Safety"},{"ID":2354,"CATEGORY NAME":"Biohazard","PARENT_ID":2350,"PARENT_CATEGORY":"Health and Safety"},{"ID":2356,"CATEGORY NAME":"First Aid","PARENT_ID":2350,"PARENT_CATEGORY":"Health and Safety"},{"ID":2357,"CATEGORY NAME":"PPE","PARENT_ID":2350,"PARENT_CATEGORY":"Health and Safety"},{"ID":2351,"CATEGORY NAME":"Signage","PARENT_ID":2350,"PARENT_CATEGORY":"Health and Safety"},{"ID":2353,"CATEGORY NAME":"Spill Control","PARENT_ID":2350,"PARENT_CATEGORY":"Health and Safety"},{"ID":2352,"CATEGORY NAME":"Storage","PARENT_ID":2350,"PARENT_CATEGORY":"Health and Safety"},{"ID":121,"CATEGORY NAME":"Home and garden","PARENT_ID":1,"PARENT_CATEGORY":"Root"},{"ID":276,"CATEGORY NAME":"Baby and toddler","PARENT_ID":121,"PARENT_CATEGORY":"Home and garden"},{"ID":122,"CATEGORY NAME":"Bathroom","PARENT_ID":121,"PARENT_CATEGORY":"Home and garden"},{"ID":2315,"CATEGORY NAME":"Bathing Aids","PARENT_ID":122,"PARENT_CATEGORY":"Bathroom"},{"ID":223,"CATEGORY NAME":"Bedroom","PARENT_ID":121,"PARENT_CATEGORY":"Home and garden"},{"ID":444,"CATEGORY NAME":"Cleaning Products","PARENT_ID":121,"PARENT_CATEGORY":"Home and garden"},{"ID":251,"CATEGORY NAME":"Decorating","PARENT_ID":127,"PARENT_CATEGORY":"DIY"},{"ID":274,"CATEGORY NAME":"Electrical","PARENT_ID":127,"PARENT_CATEGORY":"DIY"},{"ID":250,"CATEGORY NAME":"DIY Accessories","PARENT_ID":127,"PARENT_CATEGORY":"DIY"},{"ID":249,"CATEGORY NAME":"Hand tools","PARENT_ID":127,"PARENT_CATEGORY":"DIY"},{"ID":273,"CATEGORY NAME":"Nails and screws","PARENT_ID":127,"PARENT_CATEGORY":"DIY"},{"ID":275,"CATEGORY NAME":"Plumbing","PARENT_ID":127,"PARENT_CATEGORY":"DIY"},{"ID":248,"CATEGORY NAME":"Power tools","PARENT_ID":127,"PARENT_CATEGORY":"DIY"},{"ID":123,"CATEGORY NAME":"Flooring","PARENT_ID":121,"PARENT_CATEGORY":"Home and garden"},{"ID":124,"CATEGORY NAME":"Furniture","PARENT_ID":121,"PARENT_CATEGORY":"Home and garden"},{"ID":442,"CATEGORY NAME":"Chairs","PARENT_ID":124,"PARENT_CATEGORY":"Furniture"},{"ID":443,"CATEGORY NAME":"Tables","PARENT_ID":124,"PARENT_CATEGORY":"Furniture"},{"ID":125,"CATEGORY NAME":"Garden and leisure","PARENT_ID":121,"PARENT_CATEGORY":"Home and garden"},{"ID":281,"CATEGORY NAME":"Garden accessories","PARENT_ID":125,"PARENT_CATEGORY":"Garden and leisure"},{"ID":279,"CATEGORY NAME":"Garden furniture","PARENT_ID":125,"PARENT_CATEGORY":"Garden and leisure"},{"ID":280,"CATEGORY NAME":"Garden leisure","PARENT_ID":125,"PARENT_CATEGORY":"Garden and leisure"},{"ID":278,"CATEGORY NAME":"Garden tools","PARENT_ID":125,"PARENT_CATEGORY":"Garden and leisure"},{"ID":181,"CATEGORY NAME":"Garden flowers","PARENT_ID":121,"PARENT_CATEGORY":"Home and garden"},{"ID":126,"CATEGORY NAME":"Gas and electricity","PARENT_ID":121,"PARENT_CATEGORY":"Home and garden"},{"ID":127,"CATEGORY NAME":"DIY","PARENT_ID":121,"PARENT_CATEGORY":"Home and garden"},{"ID":128,"CATEGORY NAME":"Heating and cooling","PARENT_ID":121,"PARENT_CATEGORY":"Home and garden"},{"ID":129,"CATEGORY NAME":"Home accessories","PARENT_ID":121,"PARENT_CATEGORY":"Home and garden"},{"ID":130,"CATEGORY NAME":"Home security","PARENT_ID":121,"PARENT_CATEGORY":"Home and garden"},{"ID":131,"CATEGORY NAME":"Household appliances","PARENT_ID":121,"PARENT_CATEGORY":"Home and garden"},{"ID":132,"CATEGORY NAME":"Can openers","PARENT_ID":131,"PARENT_CATEGORY":"Household appliances"},{"ID":133,"CATEGORY NAME":"Cookers","PARENT_ID":131,"PARENT_CATEGORY":"Household appliances"},{"ID":134,"CATEGORY NAME":"Dishwashers","PARENT_ID":131,"PARENT_CATEGORY":"Household appliances"},{"ID":135,"CATEGORY NAME":"Electric shavers","PARENT_ID":131,"PARENT_CATEGORY":"Household appliances"},{"ID":136,"CATEGORY NAME":"Freezers","PARENT_ID":131,"PARENT_CATEGORY":"Household appliances"},{"ID":138,"CATEGORY NAME":"Fridge freezers","PARENT_ID":131,"PARENT_CATEGORY":"Household appliances"},{"ID":137,"CATEGORY NAME":"Fridges","PARENT_ID":131,"PARENT_CATEGORY":"Household appliances"},{"ID":139,"CATEGORY NAME":"Grills","PARENT_ID":131,"PARENT_CATEGORY":"Household appliances"},{"ID":140,"CATEGORY NAME":"Microwaves","PARENT_ID":131,"PARENT_CATEGORY":"Household appliances"},{"ID":141,"CATEGORY NAME":"Ovens","PARENT_ID":131,"PARENT_CATEGORY":"Household appliances"},{"ID":142,"CATEGORY NAME":"Small appliances","PARENT_ID":131,"PARENT_CATEGORY":"Household appliances"},{"ID":143,"CATEGORY NAME":"Tumble dryers","PARENT_ID":131,"PARENT_CATEGORY":"Household appliances"},{"ID":144,"CATEGORY NAME":"Vacuum cleaners","PARENT_ID":131,"PARENT_CATEGORY":"Household appliances"},{"ID":145,"CATEGORY NAME":"Washer dryers","PARENT_ID":131,"PARENT_CATEGORY":"Household appliances"},{"ID":146,"CATEGORY NAME":"Washing machines","PARENT_ID":131,"PARENT_CATEGORY":"Household appliances"},{"ID":224,"CATEGORY NAME":"Kitchen","PARENT_ID":121,"PARENT_CATEGORY":"Home and garden"},{"ID":226,"CATEGORY NAME":"Accessories","PARENT_ID":224,"PARENT_CATEGORY":"Kitchen"},{"ID":225,"CATEGORY NAME":"Bar ware","PARENT_ID":224,"PARENT_CATEGORY":"Kitchen"},{"ID":231,"CATEGORY NAME":"Crockery","PARENT_ID":224,"PARENT_CATEGORY":"Kitchen"},{"ID":230,"CATEGORY NAME":"Linen","PARENT_ID":224,"PARENT_CATEGORY":"Kitchen"},{"ID":228,"CATEGORY NAME":"Pots and Pans","PARENT_ID":224,"PARENT_CATEGORY":"Kitchen"},{"ID":227,"CATEGORY NAME":"Storage","PARENT_ID":224,"PARENT_CATEGORY":"Kitchen"},{"ID":229,"CATEGORY NAME":"Utensils","PARENT_ID":224,"PARENT_CATEGORY":"Kitchen"},{"ID":247,"CATEGORY NAME":"Lighting","PARENT_ID":121,"PARENT_CATEGORY":"Home and garden"},{"ID":304,"CATEGORY NAME":"Pets","PARENT_ID":121,"PARENT_CATEGORY":"Home and garden"},{"ID":277,"CATEGORY NAME":"Seasonal","PARENT_ID":121,"PARENT_CATEGORY":"Home and garden"},{"ID":147,"CATEGORY NAME":"Money and finance","PARENT_ID":1,"PARENT_CATEGORY":"Root"},{"ID":148,"CATEGORY NAME":"Finance and loans","PARENT_ID":147,"PARENT_CATEGORY":"Money and finance"},{"ID":149,"CATEGORY NAME":"Credit cards","PARENT_ID":148,"PARENT_CATEGORY":"Finance and loans"},{"ID":150,"CATEGORY NAME":"Home loans","PARENT_ID":148,"PARENT_CATEGORY":"Finance and loans"},{"ID":151,"CATEGORY NAME":"Personal loans","PARENT_ID":148,"PARENT_CATEGORY":"Finance and loans"},{"ID":152,"CATEGORY NAME":"Secured loans","PARENT_ID":148,"PARENT_CATEGORY":"Finance and loans"},{"ID":153,"CATEGORY NAME":"Unsecured loans","PARENT_ID":148,"PARENT_CATEGORY":"Finance and loans"},{"ID":154,"CATEGORY NAME":"Insurance","PARENT_ID":147,"PARENT_CATEGORY":"Money and finance"},{"ID":156,"CATEGORY NAME":"Health insurance","PARENT_ID":154,"PARENT_CATEGORY":"Insurance"},{"ID":155,"CATEGORY NAME":"General insurance","PARENT_ID":154,"PARENT_CATEGORY":"Insurance"},{"ID":157,"CATEGORY NAME":"Life insurance","PARENT_ID":154,"PARENT_CATEGORY":"Insurance"},{"ID":158,"CATEGORY NAME":"Investments","PARENT_ID":147,"PARENT_CATEGORY":"Money and finance"},{"ID":159,"CATEGORY NAME":"Pension","PARENT_ID":147,"PARENT_CATEGORY":"Money and finance"},{"ID":160,"CATEGORY NAME":"Music","PARENT_ID":1,"PARENT_CATEGORY":"Root"},{"ID":161,"CATEGORY NAME":"CDs","PARENT_ID":160,"PARENT_CATEGORY":"Music"},{"ID":616,"CATEGORY NAME":"Children","PARENT_ID":161,"PARENT_CATEGORY":"CDs"},{"ID":728,"CATEGORY NAME":"Christmas","PARENT_ID":161,"PARENT_CATEGORY":"CDs"},{"ID":581,"CATEGORY NAME":"Classical","PARENT_ID":161,"PARENT_CATEGORY":"CDs"},{"ID":611,"CATEGORY NAME":"Country","PARENT_ID":161,"PARENT_CATEGORY":"CDs"},{"ID":612,"CATEGORY NAME":"Dance","PARENT_ID":161,"PARENT_CATEGORY":"CDs"},{"ID":582,"CATEGORY NAME":"Ethnic","PARENT_ID":161,"PARENT_CATEGORY":"CDs"},{"ID":614,"CATEGORY NAME":"Flamenco","PARENT_ID":161,"PARENT_CATEGORY":"CDs"},{"ID":609,"CATEGORY NAME":"Indie","PARENT_ID":161,"PARENT_CATEGORY":"CDs"},{"ID":617,"CATEGORY NAME":"Jazz and Blues","PARENT_ID":161,"PARENT_CATEGORY":"CDs"},{"ID":610,"CATEGORY NAME":"Ost","PARENT_ID":161,"PARENT_CATEGORY":"CDs"},{"ID":584,"CATEGORY NAME":"Pop","PARENT_ID":161,"PARENT_CATEGORY":"CDs"},{"ID":613,"CATEGORY NAME":"Rap","PARENT_ID":161,"PARENT_CATEGORY":"CDs"},{"ID":583,"CATEGORY NAME":"Rock","PARENT_ID":161,"PARENT_CATEGORY":"CDs"},{"ID":615,"CATEGORY NAME":"Soul and Funk","PARENT_ID":161,"PARENT_CATEGORY":"CDs"},{"ID":618,"CATEGORY NAME":"Vocalists","PARENT_ID":161,"PARENT_CATEGORY":"CDs"},{"ID":1058,"CATEGORY NAME":"Concert tickets","PARENT_ID":160,"PARENT_CATEGORY":"Music"},{"ID":978,"CATEGORY NAME":"MP3","PARENT_ID":160,"PARENT_CATEGORY":"Music"},{"ID":1770,"CATEGORY NAME":"Alternative","PARENT_ID":978,"PARENT_CATEGORY":"MP3"},{"ID":1777,"CATEGORY NAME":"Audiobook","PARENT_ID":978,"PARENT_CATEGORY":"MP3"},{"ID":1771,"CATEGORY NAME":"Chanson","PARENT_ID":978,"PARENT_CATEGORY":"MP3"},{"ID":1772,"CATEGORY NAME":"Classic","PARENT_ID":978,"PARENT_CATEGORY":"MP3"},{"ID":1773,"CATEGORY NAME":"Comedy","PARENT_ID":978,"PARENT_CATEGORY":"MP3"},{"ID":1774,"CATEGORY NAME":"Country","PARENT_ID":978,"PARENT_CATEGORY":"MP3"},{"ID":1775,"CATEGORY NAME":"Dance Electronic","PARENT_ID":978,"PARENT_CATEGORY":"MP3"},{"ID":1776,"CATEGORY NAME":"HipHop RnB","PARENT_ID":978,"PARENT_CATEGORY":"MP3"},{"ID":1778,"CATEGORY NAME":"Jazz","PARENT_ID":978,"PARENT_CATEGORY":"MP3"},{"ID":1779,"CATEGORY NAME":"Metal","PARENT_ID":978,"PARENT_CATEGORY":"MP3"},{"ID":1780,"CATEGORY NAME":"Pop","PARENT_ID":978,"PARENT_CATEGORY":"MP3"},{"ID":1781,"CATEGORY NAME":"Reggae","PARENT_ID":978,"PARENT_CATEGORY":"MP3"},{"ID":1782,"CATEGORY NAME":"Rock","PARENT_ID":978,"PARENT_CATEGORY":"MP3"},{"ID":1783,"CATEGORY NAME":"Soul Funk","PARENT_ID":978,"PARENT_CATEGORY":"MP3"},{"ID":1784,"CATEGORY NAME":"TV Movie","PARENT_ID":978,"PARENT_CATEGORY":"MP3"},{"ID":1785,"CATEGORY NAME":"World","PARENT_ID":978,"PARENT_CATEGORY":"MP3"},{"ID":162,"CATEGORY NAME":"Musical instruments","PARENT_ID":160,"PARENT_CATEGORY":"Music"},{"ID":261,"CATEGORY NAME":"Office","PARENT_ID":1,"PARENT_CATEGORY":"Root"},{"ID":263,"CATEGORY NAME":"Business machines","PARENT_ID":261,"PARENT_CATEGORY":"Office"},{"ID":266,"CATEGORY NAME":"Calenders and organisers","PARENT_ID":261,"PARENT_CATEGORY":"Office"},{"ID":267,"CATEGORY NAME":"Desktop accessories","PARENT_ID":261,"PARENT_CATEGORY":"Office"},{"ID":268,"CATEGORY NAME":"Envelops and post room","PARENT_ID":261,"PARENT_CATEGORY":"Office"},{"ID":265,"CATEGORY NAME":"Files and storage","PARENT_ID":261,"PARENT_CATEGORY":"Office"},{"ID":272,"CATEGORY NAME":"Labels and badges","PARENT_ID":261,"PARENT_CATEGORY":"Office"},{"ID":262,"CATEGORY NAME":"Office furniture","PARENT_ID":261,"PARENT_CATEGORY":"Office"},{"ID":269,"CATEGORY NAME":"Paper and books","PARENT_ID":261,"PARENT_CATEGORY":"Office"},{"ID":270,"CATEGORY NAME":"Pens and writing instruments","PARENT_ID":261,"PARENT_CATEGORY":"Office"},{"ID":271,"CATEGORY NAME":"Presentation and conference","PARENT_ID":261,"PARENT_CATEGORY":"Office"},{"ID":264,"CATEGORY NAME":"Toners and cartridges","PARENT_ID":261,"PARENT_CATEGORY":"Office"},{"ID":838,"CATEGORY NAME":"Photography","PARENT_ID":1,"PARENT_CATEGORY":"Root"},{"ID":840,"CATEGORY NAME":"Albums","PARENT_ID":838,"PARENT_CATEGORY":"Photography"},{"ID":841,"CATEGORY NAME":"Personalized_products","PARENT_ID":838,"PARENT_CATEGORY":"Photography"},{"ID":885,"CATEGORY NAME":"Postcard and Pictures","PARENT_ID":838,"PARENT_CATEGORY":"Photography"},{"ID":839,"CATEGORY NAME":"Prints_and_Packs","PARENT_ID":838,"PARENT_CATEGORY":"Photography"},{"ID":1687,"CATEGORY NAME":"Posters","PARENT_ID":1,"PARENT_CATEGORY":"Root"},{"ID":1690,"CATEGORY NAME":"Animals","PARENT_ID":1687,"PARENT_CATEGORY":"Posters"},{"ID":1691,"CATEGORY NAME":"Architecture","PARENT_ID":1687,"PARENT_CATEGORY":"Posters"},{"ID":1689,"CATEGORY NAME":"Celebrities","PARENT_ID":1687,"PARENT_CATEGORY":"Posters"},{"ID":1688,"CATEGORY NAME":"Entertainment","PARENT_ID":1687,"PARENT_CATEGORY":"Posters"},{"ID":1692,"CATEGORY NAME":"World Cultures","PARENT_ID":1687,"PARENT_CATEGORY":"Posters"},{"ID":1647,"CATEGORY NAME":"Real Estate","PARENT_ID":1,"PARENT_CATEGORY":"Root"},{"ID":1649,"CATEGORY NAME":"For rent","PARENT_ID":1647,"PARENT_CATEGORY":"Real Estate"},{"ID":1648,"CATEGORY NAME":"For sale","PARENT_ID":1647,"PARENT_CATEGORY":"Real Estate"},{"ID":1650,"CATEGORY NAME":"Public sale","PARENT_ID":1647,"PARENT_CATEGORY":"Real Estate"},{"ID":1,"CATEGORY NAME":"Root","PARENT_ID":"","PARENT_CATEGORY":"#N/A"},{"ID":401,"CATEGORY NAME":"Special offers","PARENT_ID":1,"PARENT_CATEGORY":"Root"},{"ID":698,"CATEGORY NAME":"Sport","PARENT_ID":1,"PARENT_CATEGORY":"Root"},{"ID":707,"CATEGORY NAME":"Accessories","PARENT_ID":698,"PARENT_CATEGORY":"Sport"},{"ID":1494,"CATEGORY NAME":"Athletics","PARENT_ID":698,"PARENT_CATEGORY":"Sport"},{"ID":701,"CATEGORY NAME":"Basketball","PARENT_ID":698,"PARENT_CATEGORY":"Sport"},{"ID":702,"CATEGORY NAME":"Cycling","PARENT_ID":698,"PARENT_CATEGORY":"Sport"},{"ID":1492,"CATEGORY NAME":"Fitness","PARENT_ID":698,"PARENT_CATEGORY":"Sport"},{"ID":705,"CATEGORY NAME":"Football","PARENT_ID":698,"PARENT_CATEGORY":"Sport"},{"ID":1476,"CATEGORY NAME":"World Cup 2006","PARENT_ID":705,"PARENT_CATEGORY":"Football"},{"ID":706,"CATEGORY NAME":"Golf","PARENT_ID":698,"PARENT_CATEGORY":"Sport"},{"ID":1493,"CATEGORY NAME":"Gymnastics","PARENT_ID":698,"PARENT_CATEGORY":"Sport"},{"ID":1038,"CATEGORY NAME":"Hunting and Fishing","PARENT_ID":698,"PARENT_CATEGORY":"Sport"},{"ID":703,"CATEGORY NAME":"Leisure","PARENT_ID":698,"PARENT_CATEGORY":"Sport"},{"ID":700,"CATEGORY NAME":"Mountain and Camping","PARENT_ID":698,"PARENT_CATEGORY":"Sport"},{"ID":758,"CATEGORY NAME":"Shoes","PARENT_ID":698,"PARENT_CATEGORY":"Sport"},{"ID":708,"CATEGORY NAME":"Skates","PARENT_ID":698,"PARENT_CATEGORY":"Sport"},{"ID":709,"CATEGORY NAME":"Snow","PARENT_ID":698,"PARENT_CATEGORY":"Sport"},{"ID":704,"CATEGORY NAME":"Tennis","PARENT_ID":698,"PARENT_CATEGORY":"Sport"},{"ID":1813,"CATEGORY NAME":"tickets","PARENT_ID":698,"PARENT_CATEGORY":"Sport"},{"ID":699,"CATEGORY NAME":"Water","PARENT_ID":698,"PARENT_CATEGORY":"Sport"},{"ID":2110,"CATEGORY NAME":"Top sellers","PARENT_ID":1,"PARENT_CATEGORY":"Root"},{"ID":163,"CATEGORY NAME":"Travel","PARENT_ID":1,"PARENT_CATEGORY":"Root"},{"ID":1812,"CATEGORY NAME":"Activities","PARENT_ID":163,"PARENT_CATEGORY":"Travel"},{"ID":164,"CATEGORY NAME":"Car hire","PARENT_ID":163,"PARENT_CATEGORY":"Travel"},{"ID":165,"CATEGORY NAME":"Cruises","PARENT_ID":163,"PARENT_CATEGORY":"Travel"},{"ID":166,"CATEGORY NAME":"Flights","PARENT_ID":163,"PARENT_CATEGORY":"Travel"},{"ID":167,"CATEGORY NAME":"Holidays","PARENT_ID":163,"PARENT_CATEGORY":"Travel"},{"ID":168,"CATEGORY NAME":"Hotels","PARENT_ID":163,"PARENT_CATEGORY":"Travel"},{"ID":1830,"CATEGORY NAME":"Last minute","PARENT_ID":163,"PARENT_CATEGORY":"Travel"},{"ID":169,"CATEGORY NAME":"Train tickets","PARENT_ID":163,"PARENT_CATEGORY":"Travel"},{"ID":170,"CATEGORY NAME":"Travel accessories","PARENT_ID":163,"PARENT_CATEGORY":"Travel"},{"ID":2,"CATEGORY NAME":"Uncategorised","PARENT_ID":1,"PARENT_CATEGORY":"Root"},{"ID":4,"CATEGORY NAME":"Books","PARENT_ID":1,"PARENT_CATEGORY":"Root"}]',
            true);
    }
}