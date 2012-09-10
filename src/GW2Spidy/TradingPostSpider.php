<?php

namespace GW2Spidy;

use \Exception;

use GW2Spidy\Util\CacheHandler;
use GW2Spidy\Util\CurlRequest;

use GW2Spidy\DB\ItemSubType;
use GW2Spidy\DB\ItemType;


class TradingPostSpider extends BaseSpider {
    const LISTING_TYPE_SELL = 'sells';
    const LISTING_TYPE_BUY  = 'buys';

    protected function getLoginToUrl() {
        return TRADINGPOST_URL;
    }

    public function getItemByExactName($name) {
        $this->ensureLogin();

        $curl = CurlRequest::newInstance(TRADINGPOST_URL . " /ws/search.json?text=".urlencode($name)."&levelmin=0&levelmax=80")
             ->exec()
             ;

        $data = json_decode($curl->getResponseBody(), true);

        foreach ($data['results'] as $item) {
            if ($item['name'] == $name) {
                return $item;
            }
        }

        return null;
    }

    public function getListingsById($id, $type = self::LISTING_TYPE_SELL) {
        $this->ensureLogin();

        // for now we can query for 'all' and get both sell and buy in the return
        //  should it stop working like that we can just query for what we want
        $queryType = 'all';
        $cacheKey  = "listings::{$id}";

        if (!($listings = $this->cache->get($cacheKey))) {
            $curl = CurlRequest::newInstance(TRADINGPOST_URL . "/ws/listings.json?id={$id}&type={$queryType}")
                 ->exec()
                 ;

            $data = json_decode($curl->getResponseBody(), true);

            if (!isset($data['listings']) || !isset($data['listings'][$type])) {
                throw new Exception("Failed to retrieve proper listingsdata from the request result");
            }

            $listings = $data['listings'][$type];

            $this->cache->set($cacheKey, $listings, MEMCACHE_COMPRESSED, 10);
        }

        return $listings;
    }

    public function getMarketData() {
        $this->ensureLogin();

        $curl = CurlRequest::newInstance(TRADINGPOST_URL)
             ->exec()
             ;

        $result = $curl->getResponseBody();

        if (preg_match("/<script>[\n ]+GW2\.market = (\{.*?\})[\n ]+<\/script>/ms", $result, $matches)) {
            $json = json_decode($matches[1], true);
            return $json['data'];
        } else {
            throw new Exception("Failed to extract GW2.market JSON from HTML");
        }
    }

    public function getItemList($type=null, $subType=null, $offset=0) {
        $this->ensureLogin();

        $typeId    = ($type instanceof ItemType)       ? $type->getId()    : $type;
        $subTypeId = ($subType instanceof ItemSubType) ? $subType->getId() : $subType;

        $url = TRADINGPOST_URL . "/ws/search.json?text=&levelmin=0&levelmax=80&offset={$offset}";

        if ($typeId !== null) {
            $url = "{$url}&type={$typeId}";
        }
        if ($subTypeId !== null) {
            $url = "{$url}&subtype={$subTypeId}";
        }

        $curl = CurlRequest::newInstance($url)
             ->exec()
             ;

        if($curl->getInfo('http_code') >= 400) {
            throw new Exception("getItemList request failed with HTTP code {$curl->getInfo('http_code')}!");
        }

        $result = $curl->getResponseBody();
        $json   = json_decode($result, true);

        if (isset($json['results']) && $json['results']) {
            return $json['results'];
        } else {
            return false;
        }
    }
}

?>
