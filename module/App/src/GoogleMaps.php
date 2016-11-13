<?php

namespace App;

/**
 * A PHP wrapper for the Google Maps Geocoding API v3.
 * @author  Narek Poghosyan
 * @url https://developers.google.com/maps/documentation/geocoding/intro
 * @package App
 * @version 0.1
 */
class GoogleMaps
{

    /**
     * Domain portion of the Google Geocoding API URL.
     */
    const URL_DOMAIN = "maps.googleapis.com";

    /**
     * Path portion of the Google Geocoding API URL.
     */
    const URL_PATH = "/maps/api/geocode/";

    /**
     * JSON response format.
     */
    const FORMAT_JSON = "json";

    /**
     * XML response format.
     */
    const FORMAT_XML = "xml";

    /**
     * Ошибок нет, адрес обработан и получен хотя бы один геокод.
     */
    const STATUS_SUCCESS = "OK";

    /**
     * Геокодирование успешно выполнено, однако результаты не найдены. Это может произойти,
     * если геокодировщику был передан несуществующий адрес (address).
     */
    const STATUS_ZERO_RESULTS = "ZERO_RESULTS";

    /**
     * Указывает на превышение квоты.
     * Over limit of 2,500 (100,000 if premier) requests per day.
     */
    const STATUS_OVER_QUERY_LIMIT = "OVER_QUERY_LIMIT";

    /**
     * Указывает на отклонение запроса.
     * Request denied, usually because of missing sensor parameter.
     */
    const STATUS_REQUEST_DENIED = "REQUEST_DENIED";

    /**
     * Как правило, указывает на отсутствие в запросе полей address, components или latlng
     */
    const STATUS_INVALID_REQUEST = "INVALID_REQUEST";

    /**
     * Указывает, что запрос не удалось обработать из-за ошибки сервера.
     * Если повторить попытку, запрос может оказаться успешным.
     */
    const STATUS_UNKNOWN_ERROR = "UNKNOWN_ERROR";

    /**
     * Ключ API
     *
     * @var string
     */
    protected $apiKey;

    /**
     * @var string
     */
    protected $format = self::FORMAT_JSON;

    /**
     * @var string
     */
    protected $language;

    /**
     * @var string
     */
    protected $region;

    /**
     * @var string
     */
    protected $address;

    /**
     * @var string
     */
    protected $placeId;

    /**
     * GoogleMaps constructor.
     *
     * @param      $apiKey
     * @param null $language
     * @param null $region
     */
    public function __construct($apiKey, $language = null, $region = null)
    {
        $this->setApiKey($apiKey)->setLanguage($language)->setRegion($region);
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @param string $apiKey
     *
     * @return $this
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @param string $format
     *
     * @return $this
     */
    public function setFormat($format)
    {
        $this->format = $format;

        return $this;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string $language
     *
     * @return $this
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * @return string
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * @param string $region
     *
     * @return $this
     */
    public function setRegion($region)
    {
        $this->region = $region;

        return $this;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $address
     *
     * @return $this
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return string
     */
    public function getPlaceId()
    {
        return $this->placeId;
    }

    /**
     * @param string $placeId
     *
     * @return $this
     */
    public function setPlaceId($placeId)
    {
        $this->placeId = $placeId;

        return $this;
    }

    /**
     * @return string
     */
    private function geocodeQueryString()
    {
        $queryString = [];
        $address = $this->getAddress();
        $queryString['address'] = $address;
        $queryString['region'] = $this->getRegion();
        $queryString['language'] = $this->getLanguage();
        $queryString = array_filter($queryString);
        $queryString['key'] = $this->getApiKey();

        return http_build_query($queryString);
    }

    /**
     * @param bool $https
     *
     * @return string
     */
    private function geocodeUrl($https = false)
    {
        if ($https || $this->getApiKey()) {
            $scheme = "https";
        } else {
            $scheme = "http";
        }
        $pathQueryString = self::URL_PATH . $this->getFormat() . "?" . $this->geocodeQueryString();
        $url = $scheme . "://" . self::URL_DOMAIN . $pathQueryString;

        return $url;
    }

    /**
     * @param null $address
     * @param bool $https
     * @param bool $raw
     *
     * @return mixed|\SimpleXMLElement|string
     */
    public function geocode($address = null, $https = false, $raw = false)
    {
        if (!is_null($address)) {
            $this->setAddress($address);
        }
        $url = $this->geocodeUrl($https);
        $response = file_get_contents($url);
        if ($raw) {
            return $response;
        } elseif ($this->getFormat() == self::FORMAT_JSON) {
            return json_decode($response, true);
        } elseif ($this->getFormat() == self::FORMAT_XML) {
            return new \SimpleXMLElement($response);
        } else {
            return $response;
        }
    }


    /**
     * @return string
     */
    private function reverseGeocodeQueryString()
    {
        $queryString = [];
        $queryString['place_id'] = $this->getPlaceId();
        $queryString['language'] = $this->getLanguage();
        $queryString = array_filter($queryString);
        $queryString['key'] = $this->getApiKey();

        return http_build_query($queryString);
    }

    /**
     * @param bool $https
     *
     * @return string
     */
    private function reverseGeocodeUrl($https = false)
    {
        if ($https || $this->getApiKey()) {
            $scheme = "https";
        } else {
            $scheme = "http";
        }
        $pathQueryString = self::URL_PATH . $this->getFormat() . "?" . $this->reverseGeocodeQueryString();
        $url = $scheme . "://" . self::URL_DOMAIN . $pathQueryString;

        return $url;
    }

    /**
     * @param null $placeId
     * @param bool $https
     * @param bool $raw
     *
     * @return mixed|\SimpleXMLElement|string
     */
    public function reverseGeocode($placeId = null, $https = false, $raw = false)
    {
        if (!is_null($placeId)) {
            $this->setPlaceId($placeId);
        }
        $url = $this->reverseGeocodeUrl($https);
        $response = file_get_contents($url);
        if ($raw) {
            return $response;
        } elseif ($this->getFormat() == self::FORMAT_JSON) {
            return json_decode($response, true);
        } elseif ($this->getFormat() == self::FORMAT_XML) {
            return new \SimpleXMLElement($response);
        } else {
            return $response;
        }
    }
}
