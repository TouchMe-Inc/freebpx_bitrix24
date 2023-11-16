<?php

namespace Bitrix24;

use Exception;

/**
 * @author TouchMe-Inc
 */
class RestApi
{
    private const VERSION = '0000';
    private const TYPE_TRANSPORT = 'json';
    private const DEFAULT_BATCH_SIZE = 50;

    private $optional;

    /**
     * @throws Exception
     */
    public function __construct($optional)
    {
        if (!function_exists('curl_init')) {
            throw new Exception('Lib curl not found!');
        }

        $this->optional = $optional;
    }

    /**
     * @param $method string
     * @param $params array
     * @param $options array
     * @return mixed
     */
    public function call(string $method, array $params = [], array $options = [])
    {
        $url = $this->optional['HOOK_URL'] . $method . '.' . static::TYPE_TRANSPORT;

        if ($params) {
            $queryParams = http_build_query($params);
        }

        $obCurl = curl_init();

        curl_setopt($obCurl, CURLOPT_URL, $url);
        curl_setopt($obCurl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($obCurl, CURLOPT_POSTREDIR, 10);
        curl_setopt($obCurl, CURLOPT_USERAGENT, 'Bitrix24 Rest Api ' . static::VERSION);

        if (isset($queryParams)) {
            curl_setopt($obCurl, CURLOPT_POST, true);
            curl_setopt($obCurl, CURLOPT_POSTFIELDS, $queryParams);
        }

        curl_setopt(
            $obCurl, CURLOPT_FOLLOWLOCATION, (isset($options['FOLLOW_LOCATION'])) ? $options['FOLLOW_LOCATION'] : 1
        );

        if (isset($options['C_REST_IGNORE_SSL']) && $options['C_REST_IGNORE_SSL'] === true) {
            curl_setopt($obCurl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($obCurl, CURLOPT_SSL_VERIFYHOST, false);
        }

        $out = curl_exec($obCurl);

        $result = json_decode($out, true);

        curl_close($obCurl);

        return $result;
    }

    /**
     * @param $array mixed
     * @param $halt array
     * @return mixed
     *
     * @example $array:
     * $array = [
     *      'find_contact' => [
     *          'method' => 'crm.duplicate.findbycomm',
     *          'params' => [ "entity_type" => "CONTACT",  "type" => "EMAIL", "values" => array("info@bitrix24.com") ]
     *      ],
     *      'get_contact' => [
     *          'method' => 'crm.contact.get',
     *          'params' => [ "id" => '$result[find_contact][CONTACT][0]' ]
     *      ],
     *      'get_company' => [
     *          'method' => 'crm.company.get',
     *          'params' => [ "id" => '$result[get_contact][COMPANY_ID]', "select" => ["*"],]
     *      ]
     * ];
     */
    public function callBatch($array, $halt = 0)
    {
        $result = null;

        if (is_array($array)) {
            $items = 0;
            $params = [];

            foreach ($array as $key => $data) {
                if (!empty($data['method'])) {
                    $items++;
                    if (self::DEFAULT_BATCH_SIZE >= $items) {
                        $params['cmd'][$key] = $data['method'];
                        if (!empty($data['params'])) {
                            $params['cmd'][$key] .= '?' . http_build_query($data['params']);
                        }
                    }
                }
            }

            if (!empty($params)) {
                $params['halt'] = $halt;
                $result = $this->call('batch', $params);
            }
        }

        return $result;
    }

    public static function getVersion(): string
    {
        return static::VERSION;
    }
}