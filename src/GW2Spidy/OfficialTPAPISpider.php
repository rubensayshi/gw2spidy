<?php

namespace GW2Spidy;

use \Exception;

use GW2Spidy\Util\CurlRequest;

use GW2Spidy\DB\ItemSubType;
use GW2Spidy\DB\ItemType;


class OfficialTPAPISpider extends BaseSpider {

    public function getListingsByIds(array $ids) {
        $ids = array_map('urlencode', $ids);

        $curl = CurlRequest::newInstance("http://api.guildwars2.com/v2/commerce/listings?ids=".implode(",", $ids)."")
             ->exec()
             ;

        $data = json_decode($curl->getResponseBody(), true);

        return $data;
    }
}
