<?php

require_once('MtgoxApi.php');

/**
 * MtGox Merchant
 *
 * @author Ludovic Barreca <ludovic@tibanne.com>
 * @version 1.0.0
 * @copyright MtGox Co. Ltd.
 */
class Mtgox_Merchant
{
    const API_ORDER_CREATE = '1/generic/private/merchant/order/create';

    private $api;

    /**
     * Constructor
     *
     * @param $apiKey       Public Key for the MtGox Api
     * @param $apiSecret    Secret Key for the MtGox Api
     */
    public function __construct($apiKey, $apiSecret)
    {
        $this->api = new Mtgox_Api($apiKey, $apiSecret);
    }

    /**
     * Get API object
     *
     * @return Mtgox_Api instance
     */
    public function getApi()
    {
        return $this->api;
    }

    /**
     * Creates a new order on the gateway
     *
     * @param float $amount             Amount to ask
     * @param string $currency          Currency to use
     * @param string $return_success    Callback url on success
     * @param string $return_failure    Callback url on failure
     * @param array $options            Array of non-mandatory parameters
     */
    public function orderCreate($amount, $currency, $return_success, $return_failure, $options = array())
    {
        $requestData = array(
            'amount'         => $amount,
            'currency'       => $currency,
            'return_success' => $return_success,
            'return_failure' => $return_failure
        );

        foreach ($options as $key => $option) {
            $requestData[$key] = $option;
        }

        return $this->api->mtgoxQuery(self::API_ORDER_CREATE, $requestData);
    }
}
