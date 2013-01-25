<?php
/**
 * MtGox Api
 *
 * @author Ludovic Barreca <ludovic@tibanne.com>
 * @version 1.0.0
 * @copyright MtGox Co. Ltd.
 */
class Mtgox_Api
{
    const API_INFO = '1/generic/private/info';

    private $apiKey;
    private $apiSecret;

    /**
     * Constructor
     *
     * @param string $apiKey
     * @param string $apiSecret
     */
    public function __construct($apiKey, $apiSecret)
    {
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
    }

    /**
     * Check if the connection to the api works
     *
     * @return boolean
     */
    public function checkConnection()
    {
        $response = $this->query(self::API_INFO);
        var_dump($response);die;

        return $response['result'] === 'success';
    }

    /**
     * Prepare and send a query
     *
     * @staticvar null $ch
     *
     * @param string $path   api path
     * @param array  $req    parameters data
     * @return array
     * @throws Exception
     */
    public function query($path, $req = array())
    {
        $mt = explode(' ', microtime());
        $req['nonce'] = $mt[1] . substr($mt[0], 2, 6);
        $postData = http_build_query($req, '', '&');
        $headers = array(
            'Rest-Key: ' . $this->apiKey,
            'Rest-Sign: ' . base64_encode(
                hash_hmac('sha512', $postData, base64_decode($this->apiSecret), TRUE)
             ),
        );

        static $ch = NULL;

        if (is_null($ch)) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt(
                $ch, CURLOPT_USERAGENT,
                'Mozilla/4.0 (compatible; MtGox PHP client; ' . php_uname('s') . '; PHP/' . phpversion() . ')'
            );
        }

        curl_setopt($ch, CURLOPT_URL, 'https://mtgox.com/api/' . $path);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        $res = curl_exec($ch);

        if ($res === FALSE) {
            $msg = 'Could not get reply: ' . curl_error($ch);
            throw new \Exception($msg);
        }

        $dec = json_decode($res, TRUE);

        if (!$dec) {
            $msg = 'Invalid data received, please make sure connection is working and requested API exists';
            throw new \Exception($msg);
        }

        return $dec;
    }
}

