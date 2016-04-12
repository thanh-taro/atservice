<?php

namespace atservice;

use GuzzleHttp\Client;

class Service
{
    const HOST = "http://api.atvietnam.vn/oapi/airline/";

    protected static client = null;
    protected static $username;
    protected static $password;

    public static function setAccount($username, $password)
    {
        static::$username = $username;
        static::$password = $password;
    }

    public static function getPlaces()
    {
        return static::__request('GET', 'Places', [], 'Place');
    }

    public static function getCountries()
    {
        return static::__request('GET', 'Countries', [], 'Country');
    }

    public static function getProvinces()
    {
        return static::__request('GET', 'Provinces', [], 'Province');
    }

    public static function getPriceOptions()
    {
        return static::__request('GET', 'PriceOptions', [], 'PriceOption');
    }

    public static function updatePriceOption($id, $params)
    {
        return static::__request('PATCH', 'PriceOptions', ['json' => $params]);
    }

    protected static function __request($verb, $endpoint, $params = [], $className = null)
    {
        $opt = ['auth' => [$this->username, $this->password]];
        $opt = array_merge($opt, $params);
        $res = json_decode((string) $this->__client()->request($verb, $endpoint, $opt)->getBody(), true);
        if (empty($res['value'])) {
            return [];
        }
        if (empty($className)) {
            return $res['value'];
        }
        $result = [];
        foreach ($res['value'] as $val) {
            $result[] = new $className($val);
        }
        return $result;
    }

    protected static function __client()
    {
        if (static::$client === null) {
            static::$client = new Client(['base_uri' => static::HOST]);
        }
        return static::$client;
    }
}
