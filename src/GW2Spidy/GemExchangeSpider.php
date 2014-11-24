<?php

namespace GW2Spidy;

class GemExchangeSpider extends BaseSpider {
    public function getGemExchangeRate() {
        return array (
            'gem_to_gold' => $this->getGemToGold(),
            'gold_to_gem' => $this->getGoldToGem(),
        );
    }

    protected function getGemToGold() {
        $gems  = 100000;

        $data = $this->getApiData(getAppConfig('gw2spidy.gw2api_url') . "/v2/commerce/exchange/gems?quantity={$gems}");

        return $data['coins_per_gem'];
    }

    protected function getGoldToGem() {
        $coins = 100000000;

        $data = $this->getApiData(getAppConfig('gw2spidy.gw2api_url') . "/v2/commerce/exchange/coins?quantity={$coins}");

        return $data['coins_per_gem'];
    }
}