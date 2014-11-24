<?php

namespace GW2Spidy;

use Exception;
use GW2Spidy\Util\CurlRequest;
use GW2Spidy\Util\Singleton;

abstract class BaseSpider extends Singleton {

    /**
     * @param $url string The url for this API call.
     * @return mixed[] The API answer parsed as an array.
     * @throws Exception Thrown if a non positive HTTP Code was returned.
     */
    protected function getApiData($url)
    {
        $curl = CurlRequest::newInstance($url)
            ->exec();

        if ($curl->getInfo("http_code") != 200) {
            throw new Exception("Failed to retrieve API data.");
        }

        $data = json_decode($curl->getResponseBody(), true);
        return $data;
    }
}

?>
