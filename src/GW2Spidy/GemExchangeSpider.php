<?php

namespace GW2Spidy;

use \Exception;

use GW2Spidy\Util\CurlRequest;

class GemExchangeSpider extends BaseSpider {
    public function getGemExchangeRate() {
        if (!$this->getSession()->getGameSession()) {
            throw new Exception("Trying to get gem exchange rate with a non-game-session.");
        }

        $gems  = 100000;
        $coins = 10000000;

        $curl = CurlRequest::newInstance(getAppConfig('gw2spidy.gemexchange_url') . "/ws/rates.json?gems=100000&coins=10000000")
                    ->setCookie("s={$this->getSession()->getSessionKey()}")
                    ->setHeader("X-Requested-With: XMLHttpRequest")
                    ->exec()
                    ;

        $data = json_decode($curl->getResponseBody(), true);

        if (!$data) {
            throw new Exception("Failed to retrieve exchange rates.");
        }

        return array (
            'gem_to_gold' => $data['results']['coins']['quantity'] / $gems,
            'gold_to_gem' => $coins / $data['results']['gems']['quantity'],
        );
    }

    public function getGemExchangeVolume() {
        if (!$this->getSession()->getGameSession()) {
            throw new Exception("Trying to get gem exchange volume with a non-game-session.");
        }

        $curl = CurlRequest::newInstance(getAppConfig('gw2spidy.gemexchange_url') . "/ws/rates.json?gems=10000000000000000&coins=10000000000000000")
                    ->setCookie("s={$this->getSession()->getSessionKey()}")
                    ->setHeader("X-Requested-With: XMLHttpRequest")
                    ->exec()
                    ;

        $data = json_decode($curl->getResponseBody(), true);

        if (!$data) {
            throw new Exception("Failed to retrieve exchange rates.");
        }

        return array (
            'gem_count'  => $data['results']['gems']['quantity'],
            'gold_count' => $data['results']['coins']['quantity'],
        );
    }
}