<?php

namespace GW2Spidy;

class OfficialTPAPISpider extends BaseSpider {

    public function getListingsByIds(array $ids) {
        $ids = array_map('urlencode', $ids);

        return $this->getApiData(getAppConfig("gw2spidy.gw2api_url") . "/v2/commerce/listings?ids=".implode(",", $ids)."");
    }
}
