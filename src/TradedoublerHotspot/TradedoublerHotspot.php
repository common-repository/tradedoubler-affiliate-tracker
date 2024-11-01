<?php

class TradedoublerHotspot {
    private $token;
    private $baseUrl;
    private $curlConnectionTimeout;
    private $curlTimeout;
    public function __construct($token)
    {
        $this->token = $token;
        $this->curlConnectionTimeout = 5;
        $this->curlTimeout = 5;
        $this->baseUrl = 'https://api.hubapi.com/';

    }

    public function updateProgram($programID, $time, $type='Wordpress'){
        if($programID == '') return;
       $resRaw = $this->request(
           "crm/v3/objects/program/$programID?idProperty=program_id_td&properties=plug_in_platform_type,last_login"
       );
        $res = json_decode($resRaw, true);
        if($res && $res['id']){
            $id = $res['id'];
            $this->requestPatch("crm/v3/objects/program/$id", ['properties' => [
                'plug_in_platform_type'=> $type,
                'last_login'=> $time,
            ]]);
        }
    }

    private function request($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl . $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->curlConnectionTimeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->curlTimeout);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer '. $this->token,
        ]);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

        $response = curl_exec($ch);

        curl_close($ch);

        return $response;
    }

    private function requestPatch($url, $values){
        $ch = curl_init();
        if ($values) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($values));
        }
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl . $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->curlConnectionTimeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->curlTimeout);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer '. $this->token,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');

        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLINFO_HEADER_OUT, 1);

        $response = curl_exec($ch);

        curl_close($ch);

        return $response;
    }
}