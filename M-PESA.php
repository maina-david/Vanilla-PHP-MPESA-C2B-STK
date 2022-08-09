<?php

class Mpesa
{
    protected $env;
    protected $shortcode;
    protected $consumer_key;
    protected $consumer_secret;
    protected $passkey;
    protected $initiatorName;
    protected $initiatorPassword;
    protected $result_url;
    protected $timeout_url;
    protected $timestamp;

    public function __construct()
    {
        $this->env = "sandbox"; // sandbox or live
        $this->shortcode = ""; // shortcode
        $this->consumer_key = ""; // consumer key
        $this->consumer_secret = ""; // consumer secret
        $this->passkey = ""; // passkey
        $this->initiatorName = ""; // initiator name
        $this->initiatorPassword = ""; // initiator password
        $this->result_url = ""; // result url
        $this->timeout_url = ""; // timeout url
        $this->timestamp = date('YmdHis'); // timestamp

    }

    public function getToken()
    {
        $url = "https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization: Basic ' . base64_encode($this->consumer_key . ':' . $this->consumer_secret))); //setting custom header
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $result = json_decode($result);
        curl_close($curl);
        return $result->access_token;
    }

    public function mpesaExpress($amount,$phone)
    {

        $token = $this->getToken();
        $url = "https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization: Bearer ' . $token)); //setting custom header
        $curl_post_data = array(
            'BusinessShortCode' => $this->shortcode,
            'Password' => $this->passkey,
            'Timestamp' => $this->timestamp,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => $amount,
            'PartyA' => $phone,
            'PartyB' => $this->shortcode,
            'PhoneNumber' => $phone,
            'CallBackURL' => $this->result_url,
            'AccountReference' => 'AccountReference',
            'TransactionDesc' => 'TransactionDesc'
        );
        $data_string = json_encode($curl_post_data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        $curl_response = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curl_response = json_decode($curl_response);
        curl_close($curl);
        return $curl_response;
    }
}

?>