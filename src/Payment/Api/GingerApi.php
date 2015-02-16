<?php
/**
 * Created by PhpStorm.
 * User: Zebimax
 * Date: 13.02.15
 * Time: 16:15
 */

namespace Payment\Api;

class GingerApi
{
    public $debug_mode;
    public $log_to;

    public function __construct($apiKey)
    {
        $this->api_endpoint = "https://api.gingerpayments.com";
        // $this->api_endpoint = "http://localhost:8080";
        $this->api_version  = "v1";
        $this->apiKey = $apiKey;
        $this->debug_curl   = true;
    }

    public function setSettings($api_key, $log_to, $debug_mode)
    {
        $this->debug_mode = $debug_mode;
        $this->log_to     = $log_to;
        $this->apiKey    = $api_key;
    }

    public function gingerLog($contents)
    {
        if ( $this->log_to == 'file' ) {
            $file = dirname( __FILE__ ) . '/gingerlog.txt';
            if (is_writable($file)) {
                file_put_contents( $file, date( 'Y-m-d H.i.s' ) . ": ", FILE_APPEND );

                if ( is_array( $contents ) )
                    $contents = var_export( $contents, true );
                else if ( is_object( $contents ) )
                    $contents = json_encode( $contents );

                file_put_contents( $file, $contents . "\n", FILE_APPEND );
            }
        } else {
            error_log( $contents );
        }

    }

    public function performApiCall($api_method, $payload = '', $method = 'GET')
    {
        $url = implode("/", array($this->api_endpoint, $this->api_version, $api_method));

        $curl = curl_init( $url );

        $length = 0;
        if ( $method == 'POST' ) {
            curl_setopt( $curl, CURLOPT_POST, 1 );
            // curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt( $curl, CURLOPT_POSTFIELDS, $payload );
            $length = strlen( $payload );
        }

        if ( $method == 'PUT' ) {
            curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt( $curl, CURLOPT_POSTFIELDS, $payload );
            $length = strlen( $payload );
        }

        $request_headers = array(
            "Accept: application/json",
            "Content-Type: application/json",
            "User-Agent: gingerphplib",
            "X-Ginger-Client-Info: " . php_uname(),
            "Authorization: Basic " . base64_encode( $this->apiKey . ":" ),
            "Connection: close",
            "Content-length: " . $length,
        );

        curl_setopt( $curl, CURLOPT_HTTPHEADER, $request_headers );
        curl_setopt( $curl, CURLOPT_TIMEOUT, 10 );
        // curl_setopt( $curl, CURLOPT_SSL_VERIFYHOST, 2 ); // 2 = to check the existence of a common name and also verify that it matches the hostname provided. In production environments the value of this option should be kept at 2 (default value).
        curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, 0 );
        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $curl, CURLOPT_FORBID_REUSE, 1 );
        curl_setopt( $curl, CURLOPT_FRESH_CONNECT, 1 );

        if ( $this->debug_curl ) {
            $file = dirname( __FILE__ ) . '/gingercurl.txt';
            if (is_writable($file)) {
                curl_setopt( $curl, CURLOPT_VERBOSE, 1 ); // prevent caching issues
                $file_handle = fopen( $file, "a+" );
                curl_setopt( $curl, CURLOPT_STDERR, $file_handle ); // prevent caching issues
            }
        }

        $responseString = curl_exec( $curl );

        if ( $responseString == false ) {
            $response = array( 'error' => curl_error( $curl ) );
        } else {
            $response = json_decode( $responseString, true );

            if ( !$response ) {
                $this->gingerLog( 'invalid json: JSON error code: ' . json_last_error() . "\nResponse: " . $responseString );
                $response = array( 'error' =>  'Invalid JSON' );
            }
        }
        curl_close( $curl );

        return $response;
    }

    public function gingerGetIssuers()
    {
        // API Request to GINGER to fetch the issuers
        return $this->performApiCall( "ideal/issuers/" );
    }

    public function gingerCreateIdealOrder($orders_id, $total, $issuer_id, $return_url, $description)
    {
        $post = array(
            "type"              => "payment",
            "currency"          => "EUR",
            "amount"            => 100 * round( $total, 2 ),
            "merchant_order_id" => (string)$orders_id,
            "description"       => $description,
            "return_url"        =>  $return_url,
            "transactions"      => array(array(
                "payment_method"         => "ideal",
                "payment_method_details" => array("issuer_id" => $issuer_id)
            ))
        );
        $order = json_encode( $post );
        $result = $this->performApiCall( "orders/", $order, 'POST' );

        return $result;
    }

    public function gingerCreateBanktransferOrder($orders_id, $total, $return_url, $description)
    {
        $post = array(
            "type" => "payment",
            "currency" => "EUR",
            "amount" => 100 * round( $total, 2 ),
            "merchant_order_id" => (string)$orders_id,
            "description" => $description,
            "return_url" =>  $return_url,
            "transactions" => array(
                array( "payment_method" => "sepa-debit-transfer",
                ),
            )
        );
        $order = json_encode( $post );
        $result = $this->performApiCall( "orders/", $order, 'POST' );

        return $result;
    }

    public function getOrderStatus($order_id)
    {
        $order = $this->performApiCall( "orders/" . $order_id . "/" );

        if ( !is_array( $order ) or array_key_exists( 'error', $order ) ) {
            return 'error';
        }
        else {
            return $order['status'];
        }
    }

    public function getOrderDetails ($order_id)
    {
        $order = $this->performApiCall( "orders/" . $order_id . "/" );

        if ( !is_array( $order ) or array_key_exists( 'error', $order ) ) {
            return 'error';
        }
        else {
            return $order;
        }
    }
}