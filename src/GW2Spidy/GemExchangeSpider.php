<?php

namespace GW2Spidy;

use GW2Spidy\Util\CurlRequest;
use \Exception;

class GemExchangeSpider extends BaseSpider {
    protected function getLoginToUrl() {
        return GEMEXCHANGE_URL;
    }

    public function getGemExchange() {
        $this->ensureLogin();

        $curl = CurlRequest::newInstance(GEMEXCHANGE_URL . '/ws/trends.json?type=')
                    ->exec()
                    ;

        if ($curl->getInfo('http_code') >= 400) {
            throw new Exception("Failed to retrieve gemexchange trends");
        }

        $data = json_decode($curl->getResponseBody(), true);

        return $data;
    }
}

?>
