<?php

/**
 * MtGox Response
 *
 * @author Ludovic Barreca <ludovic@tibanne.com>
 * @version 1.0.0
 * @copyright MtGox Co. Ltd.
 */
class Mtgox_Response
{
    private $secretKey;


    /**
     * Constructor
     *
     * @param string $secretKey     Your secret key for verification purposes
     */
    public function __construct($secretKey)
    {
        $this->secretKey = $secretKey;
    }

    /**
     * Check IPN data authenticity
     *
     * @return boolean
     */
    public function checkIpnAuthenticity()
    {
        $sign = hash_hmac(
            'sha512', file_get_contents("php://input"), base64_decode($this->secretKey), TRUE
        );

        $clientSign = base64_decode($_SERVER['HTTP_REST_SIGN']);
        if ($sign == $clientSign) {

            return true;
        } else {

            return false;
        }
    }
}
