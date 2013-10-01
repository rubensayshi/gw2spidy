<?php

namespace GW2Spidy;

use \Exception;

use GW2Spidy\Util\CurlRequest;

class GemExchangeSpider extends BaseSpider {
    public function getGemExchangeRate() {
        $gems  = 100000;
        $coins = 10000000;

        $curl = CurlRequest::newInstance(getAppConfig('gw2spidy.gemexchange_url') . "/api/v0/exchange/rate.json?o.type=gems&o.quantity={$gems}&m.sessionId={$this->getSession()->getSessionKey()}")
                    ->setCookie("s={$this->getSession()->getSessionKey()}")
                    ->setHeader("X-Requested-With: XMLHttpRequest")
                    ->exec()
                    ;

        $data = json_decode($curl->getResponseBody(), true);

        if (!$data) {
            throw new Exception("Failed to retrieve exchange rates.");
        }

        return array (
            'gem_to_gold' => $this->getGemToGold(),
            'gold_to_gem' => $this->getGoldToGem(),
        );
    }

    protected function getGemToGold() {
        $gems  = 100000;

        $curl = CurlRequest::newInstance(getAppConfig('gw2spidy.gemexchange_url') . "/api/v0/exchange/rate.json?o.type=gems&o.quantity={$gems}&m.sessionId={$this->getSession()->getSessionKey()}")
                    ->setCookie("s={$this->getSession()->getSessionKey()}")
                    ->setHeader("X-Requested-With: XMLHttpRequest")
                    ->exec()
                    ;

        $data = json_decode($curl->getResponseBody(), true);

        if (!$data) {
            throw new Exception("Failed to retrieve exchange rates.");
        }

        return $data['body']['coins_per_gem'];
    }

    protected function getGoldToGem() {
        $coins = 100000000;

        $curl = CurlRequest::newInstance(getAppConfig('gw2spidy.gemexchange_url') . "/api/v0/exchange/rate.json?o.type=coins&o.quantity={$coins}&m.sessionId={$this->getSession()->getSessionKey()}")
                    ->setCookie("s={$this->getSession()->getSessionKey()}")
                    ->setHeader("X-Requested-With: XMLHttpRequest")
                    ->exec()
                    ;

        $data = json_decode($curl->getResponseBody(), true);

        if (!$data) {
            throw new Exception("Failed to retrieve exchange rates.");
        }

        return $data['body']['coins_per_gem'];
    }
}