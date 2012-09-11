<?php

namespace GW2Spidy;

use GW2Spidy\Util\CurlRequest;
use \Exception;

class GemExchangeSpider extends BaseSpider {
    const GEM_RATE_TYPE_RECIEVE_GEMS  = 'ReceivingGems';
    const GEM_RATE_TYPE_RECIEVE_COINS = 'ReceivingCoins';

    protected function getLoginToUrl() {
        return GEMEXCHANGE_URL;
    }

    public function getGemExchange($type = self::GEM_RATE_TYPE_RECIEVE_COINS) {
        $this->ensureLogin();

        $curl = CurlRequest::newInstance(GEMEXCHANGE_URL . "/ws/trends.json?type={$type}")
                    ->exec()
                    ;

        $data = json_decode($curl->getResponseBody(), true);

        return $data;
    }
}

?>
